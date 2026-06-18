<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><script src="https://cdn.tailwindcss.com"></script><title>{{ $title ?? 'Workspace' }} · NAXAS WorkProof</title></head>
<body class="bg-slate-50 text-slate-900">
<div class="flex min-h-screen">
    <aside class="w-64 border-r bg-white p-6">
        <div class="text-lg font-bold">NAXAS WorkProof</div>
        <nav class="mt-8 space-y-2 text-sm">
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.dashboard') }}">Dashboard</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.tasks.index') }}">Tasks</a>
            <span class="block rounded px-3 py-2 text-slate-400">Daily Reports — coming soon</span>
            <span class="block rounded px-3 py-2 text-slate-400">Proof Review — coming soon</span>
            <span class="block rounded px-3 py-2 text-slate-400">Approvals — coming soon</span>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.projects.index') }}">Projects</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.task-categories.index') }}">Task Categories</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.teams.index') }}">Teams</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.users.index') }}">Users</a>
            <span class="block rounded px-3 py-2 text-slate-400">Reports — coming soon</span>
            <span class="block rounded px-3 py-2 text-slate-400">AI Summary — coming soon</span>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.settings.company') }}">Settings</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ route('tenant.billing') }}">Billing</a>
        </nav>
    </aside>
    <main class="flex-1 p-8">{{ $slot }}</main>
</div>
</body>
</html>
