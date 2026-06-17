<x-layouts.platform title="Platform Dashboard">
    <h1 class="text-3xl font-bold">Platform Dashboard</h1>
    <div class="mt-8 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow"><p>Total workspaces</p><strong class="text-3xl">{{ $totalWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Trial</p><strong class="text-3xl">{{ $trialWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Active</p><strong class="text-3xl">{{ $activeWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Suspended</p><strong class="text-3xl">{{ $suspendedWorkspaces }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Total users</p><strong class="text-3xl">{{ $totalUsers }}</strong></div>
        <div class="rounded bg-white p-5 shadow"><p>Public plans</p><strong class="text-3xl">{{ $totalPublicPlans }}</strong></div>
    </div>
    <section class="mt-8 rounded bg-white p-6 shadow"><h2 class="text-xl font-semibold">Subscriptions by status</h2><ul class="mt-4">@forelse($subscriptionsByStatus as $status => $total)<li>{{ $status }}: {{ $total }}</li>@empty<li>No subscriptions yet.</li>@endforelse</ul></section>
    <section class="mt-8 rounded bg-white p-6 shadow"><h2 class="text-xl font-semibold">Recent workspace signups</h2><ul class="mt-4 space-y-2">@foreach($recentWorkspaces as $workspace)<li><a class="text-cyan-700" href="{{ route('platform.workspaces.show', $workspace) }}">{{ $workspace->name }}</a> · {{ $workspace->owner?->email ?? 'No owner' }}</li>@endforeach</ul></section>
</x-layouts.platform>
