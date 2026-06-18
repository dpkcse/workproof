<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Forgot Password | NAXAS WorkProof</title>
</head>
<body class="bg-slate-50 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-lg flex-col justify-center px-6 py-12">
        <section class="rounded-3xl bg-white p-8 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">Account recovery</p>
            <h1 class="mt-4 text-3xl font-black">Forgot password?</h1>
            <p class="mt-3 text-slate-600">Password reset email delivery is not configured yet. Please contact your workspace administrator or platform support to reset your password.</p>
            <a class="mt-6 inline-flex rounded-xl bg-slate-950 px-5 py-3 font-bold text-white hover:bg-cyan-700" href="{{ url('/login') }}">Back to login</a>
        </section>
    </main>
</body>
</html>
