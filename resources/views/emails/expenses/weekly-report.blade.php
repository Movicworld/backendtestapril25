<x-mail::message>
# Weekly Expense Report

Hi {{ $admin->name }},

Here's the expense summary for **{{ $company->name }}** for the past week.

<x-mail::panel>
**Total Expenses:** ${{ number_format($total, 2) }}
**Number of Transactions:** {{ $count }}
</x-mail::panel>

## Breakdown by Category

<x-mail::table>
| Category | Total |
|:---------|------:|
@foreach($byCategory as $category => $amount)
| {{ $category }} | ${{ number_format($amount, 2) }} |
@endforeach
</x-mail::table>

Thanks,
{{ config('app.name') }}
</x-mail::message>
