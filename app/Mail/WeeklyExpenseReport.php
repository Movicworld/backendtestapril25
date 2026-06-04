<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $admin,
        public readonly Company $company,
        public readonly Collection $expenses,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Weekly Expense Report – {$this->company->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expenses.weekly-report',
            with: [
                'total'      => $this->expenses->sum('amount'),
                'count'      => $this->expenses->count(),
                'byCategory' => $this->expenses->groupBy('category')->map->sum('amount'),
            ]
        );
    }
}
