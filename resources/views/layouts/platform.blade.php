<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><script src="https://cdn.tailwindcss.com"></script><title>{{ $title ?? 'Platform' }} · NAXAS WorkProof</title></head>
<body class="bg-slate-100 text-slate-900">
<div class="flex min-h-screen">
    <aside class="w-64 bg-slate-950 p-6 text-white">
        <div class="text-lg font-bold">NAXAS Platform</div>
        <nav class="mt-8 space-y-2 text-sm">
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.dashboard') }}">Platform Dashboard</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.workspaces.index') }}">Workspaces</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.plans.index') }}">Plans</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.subscriptions.index') }}">Subscriptions</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.payments.index') }}">Payments</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.ai-usage.index') }}">AI Usage</a>
            <a class="block rounded px-3 py-2 hover:bg-white/10" href="{{ route('platform.support.index') }}">Support</a>
        </nav>
    </aside>
    <main class="flex-1 p-8">
        @if(session('status'))<div class="mb-6 rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>@endif
        {{ $slot }}
    </main>
</div>
</body>
</html>
