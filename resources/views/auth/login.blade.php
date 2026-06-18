<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | NAXAS WorkProof</title>
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <section class="grid w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl md:grid-cols-2">
            <div class="hidden bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 p-10 text-white md:block">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">NAXAS WorkProof</p>
                <h1 class="mt-8 text-4xl font-black leading-tight">Welcome back to your workspace command center.</h1>
                <p class="mt-5 text-slate-200">Sign in to review dashboards, daily reports, task proof, approvals, and team accountability in one secure place.</p>
                <div class="mt-10 rounded-2xl border border-white/10 bg-white/10 p-5">
                    <p class="text-sm text-slate-200">Session-based authentication keeps your workspace access protected while preserving your active workspace context.</p>
                </div>
            </div>

            <div class="p-8 sm:p-10">
                <div class="md:hidden">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">NAXAS WorkProof</p>
                </div>

                <h2 class="mt-4 text-3xl font-black text-slate-950 md:mt-0">Log in</h2>
                <p class="mt-2 text-sm text-slate-600">Use your workspace account email and password.</p>

                @if (session('status'))
                    <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">Please fix the following:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="mt-8 space-y-5" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700" for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" placeholder="you@company.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <label class="block text-sm font-semibold text-slate-700" for="password">Password</label>
                            <a class="text-sm font-semibold text-cyan-700 hover:text-cyan-900" href="{{ Route::has('password.request') ? route('password.request') : url('/forgot-password') }}">Forgot password?</a>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-700" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span>Remember me on this device</span>
                    </label>

                    <button class="w-full rounded-xl bg-slate-950 px-5 py-3 font-bold text-white shadow-lg shadow-slate-900/20 transition hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-200" type="submit">
                        Sign in
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-600">
                    Need a workspace?
                    @if (Route::has('register'))
                        <a class="font-bold text-cyan-700 hover:text-cyan-900" href="{{ route('register') }}">Start a free trial</a>
                    @else
                        <a class="font-bold text-cyan-700 hover:text-cyan-900" href="/register">Start a free trial</a>
                    @endif
                </p>
            </div>
        </section>
    </main>
</body>
</html>
