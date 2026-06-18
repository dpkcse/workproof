<x-layouts.app title="Edit Task"><h1 class="text-2xl font-bold">Edit task</h1><form class="mt-6 rounded bg-white p-6 shadow" method="post" action="{{ url('/tasks/'.$task->id) }}">@method('PUT')@include('tenant.tasks._form')</form></x-layouts.app>

<section class="mt-6 rounded bg-indigo-50 p-4 text-sm">
    <h2 class="font-semibold">AI task cleaner</h2>
    <p class="text-gray-600">Paste rough task notes and request a suggestion. AI output is not saved until you copy it into the form.</p>
    <form class="mt-3" method="POST" action="/ai/task-cleaner">@csrf<textarea class="w-full rounded border p-2" name="raw_text" rows="3" placeholder="Rough task notes"></textarea><button class="mt-2 rounded bg-indigo-700 px-3 py-2 text-white">Generate task suggestion</button></form>
</section>
