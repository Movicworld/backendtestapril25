<?php

namespace App\Jobs;

use App\Enums\Role;
use App\Mail\WeeklyExpenseReport;
use App\Models\Company;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ExpenseRepositoryInterface $expenses): void
    {
        Company::with('users')->chunk(50, function ($companies) use ($expenses) {
            foreach ($companies as $company) {
                $admins = $company->users->where('role', Role::Admin);

                if ($admins->isEmpty()) {
                    continue;
                }

                $weeklyExpenses = $expenses->allForCompany($company->id)
                    ->filter(fn($e) => $e->created_at->gte(Carbon::now()->subWeek()));

                foreach ($admins as $admin) {
                    Mail::to($admin->email)->queue(
                        new WeeklyExpenseReport($admin, $company, $weeklyExpenses)
                    );
                }
            }
        });
    }
}
