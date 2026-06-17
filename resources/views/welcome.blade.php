<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Naxas WorkProof is an all-in-one work management, CRM, reporting, and AI-powered team monitoring system.">
    <title>Naxas WorkProof | Work Management & Team Monitoring Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    boxShadow: { soft: '0 24px 80px rgba(15, 23, 42, 0.12)' }
                }
            }
        }
    </script>
</head>
<body class="bg-white font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/85 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-bold tracking-tight text-slate-950">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-rose-500 to-blue-600 text-white shadow-lg shadow-rose-200">NW</span>
                <span class="text-lg">Naxas WorkProof</span>
            </a>
            <div class="hidden items-center gap-7 text-sm font-medium text-slate-600 lg:flex">
                <a class="hover:text-rose-600" href="#home">Home</a>
                <a class="hover:text-rose-600" href="#features">Features</a>
                <a class="hover:text-rose-600" href="#crm">CRM</a>
                <a class="hover:text-rose-600" href="#monitoring">Task Monitoring</a>
                <a class="hover:text-rose-600" href="#reports">Reports</a>
                <a class="hover:text-rose-600" href="#pricing">Pricing</a>
                <a class="hover:text-rose-600" href="#contact">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden rounded-full px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:inline-flex">Login</a>
                <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5">Get Started</a>
            </div>
        </nav>
    </header>

    <main id="home" class="overflow-hidden">
        <section class="relative isolate px-5 pb-20 pt-16 lg:px-8 lg:pb-28 lg:pt-24">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_10%_20%,rgba(244,63,94,.18),transparent_28%),radial-gradient(circle_at_85%_10%,rgba(37,99,235,.18),transparent_30%),linear-gradient(180deg,#fff_0%,#fff7f9_45%,#eff6ff_100%)]"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[1.02fr_.98fr]">
                <div>
                    <p class="inline-flex rounded-full border border-rose-200 bg-white/70 px-4 py-2 text-sm font-semibold text-rose-600 shadow-sm">AI-powered operations command center</p>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">All-in-One Work Management & Team Monitoring Software</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">Track daily tasks, monitor employee performance, manage CRM leads, follow up clients, and generate reports from one simple system.</p>
                    <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-rose-500 to-blue-600 px-7 py-4 text-center font-bold text-white shadow-xl shadow-rose-200 transition hover:-translate-y-1">Start Free Trial</a>
                        <a href="#demo" class="rounded-full border border-slate-200 bg-white px-7 py-4 text-center font-bold text-slate-800 shadow-sm hover:border-blue-200 hover:text-blue-700">View Demo</a>
                    </div>
                </div>
                <div id="demo" class="relative">
                    <div class="rounded-[2rem] border border-white/70 bg-white/85 p-4 shadow-soft backdrop-blur">
                        <div class="rounded-[1.5rem] bg-slate-950 p-5 text-white">
                            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                <div><p class="text-xs text-slate-400">Manager Dashboard</p><h2 class="font-bold">Today’s WorkProof</h2></div>
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300">Live</span>
                            </div>
                            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                                @foreach ([['42','Tasks done','rose'],['18','Follow-ups','blue'],['7','Delayed','amber']] as $stat)
                                    <div class="rounded-2xl bg-white/10 p-4"><p class="text-2xl font-black">{{ $stat[0] }}</p><p class="text-xs text-slate-300">{{ $stat[1] }}</p></div>
                                @endforeach
                            </div>
                            <div class="mt-5 space-y-3">
                                @foreach (['PR00152 follow-up scheduled with client','Sarah completed 8 assigned tasks','AI summary: Sales team needs 3 urgent callbacks'] as $item)
                                    <div class="flex items-center gap-3 rounded-2xl bg-white p-3 text-sm text-slate-700"><span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r from-rose-500 to-blue-600"></span>{{ $item }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-10 right-2 w-44 rounded-[2rem] border border-white bg-white p-3 shadow-2xl sm:right-8">
                        <div class="rounded-[1.4rem] bg-gradient-to-b from-blue-50 to-rose-50 p-4"><p class="text-xs font-bold text-slate-500">Mobile updates</p><p class="mt-8 text-3xl font-black text-slate-950">96%</p><p class="text-xs text-slate-500">accountability score</p></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
            <div class="flex flex-wrap justify-center gap-3 rounded-[2rem] border border-slate-100 bg-white p-5 shadow-soft">
                @foreach (['Digital Marketing Teams','Sales Teams','Service Businesses','Pick & Drop Operations','Agencies','SMEs','Remote Teams'] as $useCase)
                    <span class="rounded-full bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600">{{ $useCase }}</span>
                @endforeach
            </div>
        </section>

        <section id="features" class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="max-w-3xl"><p class="font-bold text-blue-600">One Platform</p><h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Manage daily work without chasing updates.</h2></div>
            <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach (['Daily Task List','Pending Task Tracking','Employee-wise Monitoring','CRM Lead Follow-up','Client Activity History','Manager Dashboard'] as $feature)
                    <article class="rounded-[1.75rem] border border-slate-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-soft"><div class="mb-5 h-12 w-12 rounded-2xl bg-gradient-to-br from-rose-100 to-blue-100"></div><h3 class="text-xl font-bold">{{ $feature }}</h3><p class="mt-3 text-slate-600">Keep every assignment, update, owner, and result visible in a manager-ready workflow.</p></article>
                @endforeach
            </div>
        </section>

        <section id="crm" class="bg-gradient-to-br from-rose-50 via-white to-blue-50 py-20"><div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-2 lg:px-8"><div><p class="font-bold text-rose-600">CRM & Prospect Management</p><h2 class="mt-3 text-3xl font-black sm:text-5xl">Turn prospects into accountable follow-up pipelines.</h2><p class="mt-5 text-lg text-slate-600">Create leads, schedule follow-ups, assign tasks, track activity timelines, convert customers, and search prospect codes like PR00152 instantly.</p></div><div class="grid gap-4 sm:grid-cols-2">@foreach (['Lead/prospect creation','Follow-up scheduling','Task assignment','Activity timeline','Customer conversion','Searchable prospect code'] as $item)<div class="rounded-3xl bg-white p-6 font-bold shadow-sm">{{ $item }}</div>@endforeach</div></div></section>

        <section id="reports" class="mx-auto max-w-7xl px-5 py-20 lg:px-8"><div class="grid gap-10 lg:grid-cols-2"><div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-soft"><h2 class="text-3xl font-black">Reporting & Accountability</h2><div class="mt-6 space-y-4">@foreach (['Daily work report','Pending task report','Employee performance','Lead conversion report','Activity logs','Export options'] as $report)<div class="rounded-2xl bg-white/10 p-4">{{ $report }}</div>@endforeach</div></div><div id="monitoring" class="self-center"><p class="font-bold text-blue-600">Task Monitoring</p><h2 class="mt-3 text-3xl font-black sm:text-5xl">See what is done, pending, delayed, and ready for review.</h2><p class="mt-5 text-lg text-slate-600">Naxas WorkProof creates clear accountability across departments, field operations, sales teams, agencies, and remote staff.</p></div></div></section>

        <section class="mx-auto max-w-7xl px-5 py-10 lg:px-8"><div class="grid gap-4 rounded-[2rem] bg-gradient-to-r from-rose-500 to-blue-600 p-6 text-white shadow-soft sm:grid-cols-4">@foreach (['3x Better Team Visibility','60% Less Manual Follow-up','40% Faster Reporting','100% Task Accountability'] as $stat)<div class="rounded-3xl bg-white/15 p-6 text-center font-black">{{ $stat }}</div>@endforeach</div></section>

        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8"><div class="rounded-[2rem] bg-slate-50 p-8 lg:p-12"><p class="font-bold text-rose-600">AI Assistant</p><h2 class="mt-3 text-3xl font-black sm:text-5xl">AI Assistant for Smarter Work Management</h2><div class="mt-8 grid gap-4 md:grid-cols-5">@foreach (['Summarize daily updates','Detect pending or delayed tasks','Suggest follow-up actions','Generate manager reports','Answer regular business questions'] as $ai)<div class="rounded-3xl bg-white p-5 text-sm font-bold shadow-sm">{{ $ai }}</div>@endforeach</div></div></section>

        <section id="pricing" class="bg-slate-950 py-20 text-white"><div class="mx-auto max-w-7xl px-5 lg:px-8"><div class="text-center"><p class="font-bold text-blue-300">Pricing Preview</p><h2 class="mt-3 text-3xl font-black sm:text-5xl">Plans for every growing team.</h2></div><div class="mt-10 grid gap-5 md:grid-cols-3">@foreach ([['Starter','$19/mo'],['Business','$49/mo'],['Enterprise','Custom']] as $plan)<article class="rounded-[2rem] border border-white/10 bg-white/10 p-7"><h3 class="text-2xl font-black">{{ $plan[0] }}</h3><p class="mt-4 text-4xl font-black">{{ $plan[1] }}</p><ul class="mt-6 space-y-3 text-slate-200"><li>Task monitoring</li><li>CRM follow-ups</li><li>Reports dashboard</li></ul></article>@endforeach</div></div></section>

        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8"><div class="grid gap-5 md:grid-cols-3">@foreach (['WorkProof gave our managers a single place to review daily output.','Our sales follow-ups stopped falling through the cracks.','Reporting that took hours is now ready before meetings.'] as $quote)<blockquote class="rounded-[2rem] border border-slate-100 bg-white p-7 shadow-sm"><p class="text-slate-700">“{{ $quote }}”</p><footer class="mt-5 font-bold text-slate-950">Business Manager</footer></blockquote>@endforeach</div></section>

        <section id="contact" class="px-5 pb-20 lg:px-8"><div class="mx-auto max-w-7xl rounded-[2.5rem] bg-gradient-to-br from-rose-500 to-blue-600 p-10 text-center text-white shadow-soft lg:p-16"><h2 class="text-3xl font-black sm:text-5xl">Ready to Monitor Your Team and Grow Faster?</h2><a href="{{ route('register') }}" class="mt-8 inline-flex rounded-full bg-white px-8 py-4 font-black text-slate-950 shadow-lg">Get Started Today</a></div></section>
    </main>

    <footer class="border-t border-slate-100 bg-white px-5 py-10 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-8 text-sm text-slate-600 md:grid-cols-4">
            <div><h3 class="font-black text-slate-950">Naxas WorkProof</h3><p class="mt-3">Business task, team monitoring, CRM, reporting, and AI-powered work management.</p></div>
            <div><h4 class="font-bold text-slate-950">Quick links</h4><p class="mt-3">Home · Features · Pricing · Contact</p></div>
            <div><h4 class="font-bold text-slate-950">Product</h4><p class="mt-3">CRM · Task Monitoring · Reports · AI Assistant</p></div>
            <div><h4 class="font-bold text-slate-950">Contact</h4><p class="mt-3">hello@naxasworkproof.test<br>© {{ date('Y') }} Naxas WorkProof</p></div>
        </div>
    </footer>
</body>
</html>
