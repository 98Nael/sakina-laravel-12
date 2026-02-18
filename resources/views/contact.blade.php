<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/brand/sakina-favicon.svg">
    <title>Contact Us | Sakina Health</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_10%_15%,rgba(14,165,233,0.16),transparent_42%),radial-gradient(circle_at_85%_80%,rgba(245,158,11,0.16),transparent_44%)]"></div>

    <header class="relative z-10 mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
        <a href="/" class="inline-flex items-center">
            <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-11 w-auto" />
        </a>
        <nav class="flex items-center gap-3">
            <a href="/about" class="rounded-lg border border-slate-600 px-3 py-2 text-sm text-slate-200 hover:border-slate-400 hover:text-white">About</a>
            <a href="/" class="rounded-lg border border-slate-600 px-3 py-2 text-sm text-slate-200 hover:border-slate-400 hover:text-white">Home</a>
        </nav>
    </header>

    <main class="relative z-10 mx-auto w-full max-w-6xl px-6 pb-20 pt-4">
        <section class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
            <p class="text-xs uppercase tracking-wider text-amber-300">Contact</p>
            <h1 class="mt-3 text-4xl font-extrabold text-white">Contact Us</h1>
            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                Reach our support and operations team for onboarding, technical questions, and collaboration requests.
            </p>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border border-slate-700 bg-slate-900/70 p-6 lg:col-span-2">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-300/40 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-slate-200">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2 text-slate-100 outline-none focus:border-cyan-400" />
                            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-slate-200">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2 text-slate-100 outline-none focus:border-cyan-400" />
                            @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="mb-1 block text-sm font-medium text-slate-200">Subject</label>
                        <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2 text-slate-100 outline-none focus:border-cyan-400" />
                        @error('subject') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="mb-1 block text-sm font-medium text-slate-200">Message</label>
                        <textarea id="message" name="message" rows="6" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2 text-slate-100 outline-none focus:border-cyan-400">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:from-cyan-400 hover:to-emerald-400">
                        Send Message
                    </button>
                </form>
            </section>

            <aside class="rounded-2xl border border-slate-700 bg-slate-900/70 p-6">
                <h2 class="text-lg font-semibold text-white">Support Channels</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <p><span class="font-semibold text-slate-100">Email:</span> support@sakina.com</p>
                    <p><span class="font-semibold text-slate-100">Phone:</span> +20 100 000 0000</p>
                    <p><span class="font-semibold text-slate-100">Hours:</span> Sun - Thu, 9:00 AM - 5:00 PM</p>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
