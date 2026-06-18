<x-layouts.app :title="$title ?? 'Report'">
<h1 class="text-2xl font-bold">{{ $title ?? 'Report' }}</h1>
<p class="mt-2 text-sm text-slate-600">Workspace-scoped, paginated report. Use exports for large data sets.</p>
<div class="mt-4 rounded bg-white p-4 shadow overflow-x-auto">
<table class="w-full text-sm"><thead><tr><th class="text-left">Record</th><th class="text-left">Status</th><th class="text-left">Date</th></tr></thead><tbody>
@foreach($rows as $row)<tr class="border-t"><td class="py-2">{{ $row->task_number ?? $row->title ?? $row->name ?? $row->id }}</td><td>{{ $row->status ?? $row->score ?? '' }}</td><td>{{ optional($row->due_date ?? $row->report_date ?? $row->score_date ?? $row->created_at)->format('Y-m-d') }}</td></tr>@endforeach
</tbody></table><div class="mt-4">{{ $rows->links() }}</div></div></x-layouts.app>
