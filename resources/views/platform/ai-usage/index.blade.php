<x-layouts.platform title="AI Usage">
    <h1 class="text-3xl font-bold">AI Usage</h1>
    <section class="mt-6 rounded bg-white p-6 shadow">
        @if($aiUsageTableExists)
            <form class="mb-4 grid gap-3 md:grid-cols-6" method="GET">
                <select class="rounded border p-2" name="workspace_id"><option value="">All workspaces</option>@foreach($workspaces as $workspace)<option value="{{ $workspace->id }}" @selected(request('workspace_id')==$workspace->id)>{{ $workspace->name }}</option>@endforeach</select>
                <input class="rounded border p-2" name="feature" value="{{ request('feature') }}" placeholder="Feature">
                <input class="rounded border p-2" name="provider" value="{{ request('provider') }}" placeholder="Provider">
                <select class="rounded border p-2" name="status"><option value="">Any status</option>@foreach(['success','failed','blocked'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
                <input class="rounded border p-2" type="date" name="date" value="{{ request('date') }}">
                <button class="rounded bg-slate-900 px-4 py-2 text-white">Filter</button>
            </form>
            <table class="w-full text-left text-sm"><thead><tr><th>Time</th><th>Workspace</th><th>Feature</th><th>Provider</th><th>Status</th><th>Tokens</th><th>Cost</th><th>Error</th></tr></thead><tbody>@foreach($usageLogs as $log)<tr class="border-t"><td>{{ $log->created_at }}</td><td>{{ $log->workspace?->name ?? $log->workspace_id }}</td><td>{{ $log->feature }}</td><td>{{ $log->provider }} / {{ $log->model }}</td><td>{{ $log->status }}</td><td>{{ ($log->input_tokens ?? 0) + ($log->output_tokens ?? 0) }}</td><td>{{ $log->estimated_cost }}</td><td>{{ $log->error_message }}</td></tr>@endforeach</tbody></table>
            <div class="mt-4">{{ $usageLogs->links() }}</div>
        @else<p>AI usage logging will be added in Phase 4.</p>@endif
    </section>
</x-layouts.platform>
