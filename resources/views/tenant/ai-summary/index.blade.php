<x-layouts.app title="AI Summary">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">AI Summary</h1>
        <p class="text-sm text-gray-600">AI output is suggestion only and must be reviewed by a manager or owner before important action.</p>
    </div>
    <section class="rounded bg-white p-6 shadow">
        @forelse($summaries as $summary)
            <article class="border-b py-4 last:border-b-0">
                <div class="flex items-center justify-between"><h2 class="font-semibold">{{ $summary->title ?? $summary->summary_type }}</h2><span class="text-xs text-gray-500">{{ $summary->generated_at?->diffForHumans() }}</span></div>
                <p class="mt-2 whitespace-pre-line text-sm">{{ $summary->content }}</p>
                <p class="mt-2 text-xs text-gray-500">Provider: {{ $summary->provider ?? 'n/a' }} · Model: {{ $summary->model ?? 'n/a' }} · Sources: {{ collect($summary->source_ids ?? [])->join(', ') ?: 'n/a' }}</p>
            </article>
        @empty
            <p>No AI summaries generated yet.</p>
        @endforelse
        <div class="mt-4">{{ $summaries->links() }}</div>
    </section>
</x-layouts.app>
