<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><script src="https://cdn.tailwindcss.com"></script><title>{{ $title ?? 'Workspace' }} · NAXAS WorkProof</title></head>
<body class="bg-slate-50 text-slate-900">
<div class="flex min-h-screen">
    <aside class="w-64 border-r bg-white p-6">
        <div class="text-lg font-bold">NAXAS WorkProof</div>
        <nav class="mt-8 space-y-2 text-sm">
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/dashboard') }}">Dashboard</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/tasks') }}">Tasks</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/daily-reports') }}">Daily Reports</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/proof-review') }}">Proof Review</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/approvals') }}">Approvals</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/projects') }}">Projects</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/task-categories') }}">Task Categories</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/teams') }}">Teams</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/users') }}">Users</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/missing-reports') }}">Missing Reports</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/manager-dashboard') }}">Manager Dashboard</a>
            <span class="block rounded px-3 py-2 text-slate-400">AI Summary — coming soon</span>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/settings/company') }}">Settings</a>
            <a class="block rounded px-3 py-2 hover:bg-slate-100" href="{{ url('/billing') }}">Billing</a>

            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="block w-full rounded px-3 py-2 text-left hover:bg-slate-100" type="submit">Logout</button>
            </form>
        </nav>
    </aside>
    <main class="flex-1 p-8">{{ $slot }}</main>
</div>
</body>
</html>
