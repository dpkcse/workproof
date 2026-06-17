<x-layouts.platform title="AI Usage">
    <h1 class="text-3xl font-bold">AI Usage</h1>
    <section class="mt-6 rounded bg-white p-6 shadow">@if($aiUsageTableExists)<ul class="space-y-2">@foreach($usageLogs as $log)<li>{{ json_encode($log) }}</li>@endforeach</ul>@else<p>AI usage logging will be added in Phase 4.</p>@endif</section>
</x-layouts.platform>
