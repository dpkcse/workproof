<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Workspace Setup | NAXAS WorkProof</title>
</head>
<body class="bg-slate-50 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-2xl flex-col justify-center px-6 py-12">
        <section class="rounded-3xl bg-white p-8 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">Workspace required</p>
            <h1 class="mt-4 text-3xl font-black">Set up your workspace</h1>
            @if (session('status'))
                <div class="mt-5 rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm text-cyan-900">{{ session('status') }}</div>
            @endif
            <p class="mt-5 text-slate-600">Your account does not currently have an active workspace membership. Create a workspace trial or ask your workspace administrator to invite you.</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                @if (Route::has('register'))
                    <a class="rounded-xl bg-slate-950 px-5 py-3 text-center font-bold text-white hover:bg-cyan-700" href="{{ route('register') }}">Create workspace</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full rounded-xl border border-slate-300 px-5 py-3 font-bold text-slate-700 hover:bg-slate-100" type="submit">Log out</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
