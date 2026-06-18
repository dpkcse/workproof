<x-layouts.platform title="Platform Dashboard">
    <h1 class="text-3xl font-bold">Platform Dashboard</h1>
    <div class="mt-8 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow"><p>Total workspaces</p><strong class="text-3xl">{{ $totalWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Trial</p><strong class="text-3xl">{{ $trialWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Active</p><strong class="text-3xl">{{ $activeWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Suspended</p><strong class="text-3xl">{{ $suspendedWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Total users</p><strong class="text-3xl">{{ $totalUsers }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Public plans</p><strong class="text-3xl">{{ $totalPublicPlans }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Manual MRR</p><strong class="text-3xl">{{ number_format($monthlyRecurringRevenue, 2) }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Active subscriptions</p><strong class="text-3xl">{{ $activeSubscriptions }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Past due subscriptions</p><strong class="text-3xl">{{ $pastDueSubscriptions }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Overdue invoices</p><strong class="text-3xl">{{ $overdueInvoices }}</strong></div>
    </div>
    <section class="mt-8 rounded bg-white p-6 shadow"><h2 class="text-xl font-semibold">Subscriptions by status</h2><ul class="mt-4">@forelse($subscriptionsByStatus as $status => $total)<li>{{ $status }}: {{ $total }}</li>@empty<li>No subscriptions yet.</li>@endforelse</ul></section>
    <section class="mt-8 rounded bg-white p-6 shadow"><h2 class="text-xl font-semibold">Recent workspace signups</h2><ul class="mt-4 space-y-2">@foreach($recentWorkspaces as $workspace)<li><a class="text-cyan-700" href="{{ route('platform.workspaces.show', $workspace) }}">{{ $workspace->name }}</a> · {{ $workspace->owner?->email ?? 'No owner' }}</li>@endforeach</ul></section>
<section class="mt-8 rounded bg-white p-6 shadow"><h2 class="text-xl font-semibold">Recent payments</h2><ul class="mt-4 space-y-2">@forelse($recentPayments as $payment)<li>{{ $payment->payment_number }} · {{ $payment->workspace?->name }} · {{ $payment->status }} · {{ $payment->amount }}</li>@empty<li>No payments yet.</li>@endforelse</ul></section>
</x-layouts.platform>
