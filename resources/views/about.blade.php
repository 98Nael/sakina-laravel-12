<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/brand/sakina-favicon.svg">
    <title>About | Sakina Health</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_10%,rgba(245,158,11,0.16),transparent_42%),radial-gradient(circle_at_15%_80%,rgba(16,185,129,0.14),transparent_45%)]"></div>

    <header class="relative z-10 mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
        <a href="/" class="inline-flex items-center">
            <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-11 w-auto" />
        </a>
        <div class="flex items-center gap-3">
            <a href="/contact" class="rounded-lg border border-cyan-600/40 px-3 py-2 text-sm text-cyan-200 hover:border-cyan-400 hover:text-white">Contact</a>
            <a href="/" class="rounded-lg border border-slate-600 px-3 py-2 text-sm text-slate-200 hover:border-slate-400 hover:text-white">Back Home</a>
        </div>
    </header>

    <main class="relative z-10 mx-auto w-full max-w-6xl px-6 pb-20 pt-6">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
            <p class="text-xs uppercase tracking-wider text-amber-300">{{ $aboutContent->subtitle ?: 'About Platform' }}</p>
            <h1 class="mt-3 text-4xl font-extrabold text-white">{{ $aboutContent->title }}</h1>
            <p class="mt-5 max-w-3xl text-base leading-7 text-slate-300">
                {{ $aboutContent->content }}
            </p>
        </section>

        <section class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 p-5">
                <h2 class="text-sm font-semibold text-cyan-200">Operational Clarity</h2>
                <p class="mt-2 text-sm text-slate-200">Dashboards and structured flows for day-to-day decisions.</p>
            </article>
            <article class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-5">
                <h2 class="text-sm font-semibold text-emerald-200">Clinical Continuity</h2>
                <p class="mt-2 text-sm text-slate-200">Appointments, records, and prescriptions in one place.</p>
            </article>
            <article class="rounded-2xl border border-amber-400/30 bg-amber-400/10 p-5">
                <h2 class="text-sm font-semibold text-amber-200">Secure Governance</h2>
                <p class="mt-2 text-sm text-slate-200">Role-based access and approval-driven controls.</p>
            </article>
        </section>
    </main>
</body>
</html>
