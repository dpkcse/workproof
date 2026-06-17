<x-layouts.platform title="Payments">
    <h1 class="text-3xl font-bold">Payments</h1>
    <section class="mt-6 rounded bg-white p-6 shadow"><p class="text-slate-700">Manual billing is ready for Phase 1 review. Payment gateway will be added in a later phase.</p>@if($paymentsTableExists)<ul class="mt-4 space-y-2">@foreach($payments as $payment)<li>{{ json_encode($payment) }}</li>@endforeach</ul>@else<p class="mt-4 font-semibold">Payment gateway will be added in later phase.</p>@endif</section>
</x-layouts.platform>
