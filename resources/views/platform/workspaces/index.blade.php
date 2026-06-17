<x-layouts.platform title="Workspaces">
    <h1 class="text-3xl font-bold">Workspaces</h1>
    <form class="mt-6 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-4" method="GET">
        <input class="rounded border p-2" name="search" value="{{ request('search') }}" placeholder="Name, slug, owner email">
        <select class="rounded border p-2" name="status"><option value="">Any status</option>@foreach(['trial','active','suspended','cancelled','expired'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
        <select class="rounded border p-2" name="edition"><option value="">Any edition</option>@foreach(['saas','private','enterprise'] as $edition)<option value="{{ $edition }}" @selected(request('edition')===$edition)>{{ ucfirst($edition) }}</option>@endforeach</select>
        <button class="rounded bg-slate-900 px-4 py-2 text-white">Filter</button>
    </form>
    <div class="mt-6 overflow-hidden rounded bg-white shadow"><table class="w-full text-left text-sm"><thead class="bg-slate-100"><tr><th class="p-3">Workspace</th><th>Owner</th><th>Status</th><th>Edition</th><th>Trial ends</th><th></th></tr></thead><tbody>@foreach($workspaces as $workspace)<tr class="border-t"><td class="p-3"><strong>{{ $workspace->name }}</strong><br><span class="text-slate-500">{{ $workspace->slug }}</span></td><td>{{ $workspace->owner?->email ?? '—' }}</td><td><span class="rounded bg-slate-100 px-2 py-1">{{ $workspace->status }}</span></td><td>{{ $workspace->edition }}</td><td>{{ $workspace->trial_ends_at?->toDateString() ?? '—' }}</td><td><a class="text-cyan-700" href="{{ route('platform.workspaces.show', $workspace) }}">View</a></td></tr>@endforeach</tbody></table></div>
    <div class="mt-6">{{ $workspaces->links() }}</div>
</x-layouts.platform>
