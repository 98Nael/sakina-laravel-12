<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/brand/sakina-favicon.svg">
    <title>Sakina Health Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(245,158,11,0.18),transparent_40%),radial-gradient(circle_at_90%_10%,rgba(14,165,233,0.16),transparent_45%),radial-gradient(circle_at_50%_85%,rgba(16,185,129,0.15),transparent_40%)]"></div>

        <header class="relative z-10 mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6">
            <a href="/" class="inline-flex items-center">
                <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-11 w-auto" />
            </a>
            <nav class="hidden items-center gap-6 text-sm text-slate-300 md:flex">
                <a href="/about" class="hover:text-white">About</a>
                <a href="/contact" class="hover:text-white">Contact</a>
                <a href="/doctor/login" class="hover:text-white">Doctor</a>
                <a href="/patient/login" class="hover:text-white">Patient</a>
                <a href="/admin/login" class="hover:text-white">Admin</a>
            </nav>
        </header>

        <main class="relative z-10 mx-auto grid w-full max-w-7xl grid-cols-1 gap-10 px-6 pb-14 pt-8 lg:grid-cols-2 lg:items-center">
            <section>
                <p class="mb-4 inline-flex rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-amber-300">
                    Healthcare Platform
                </p>
                <h1 class="text-4xl font-extrabold leading-tight text-white sm:text-5xl">
                    Clinical operations, patient care, and administration in one secure platform.
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-slate-300">
                    Sakina brings doctors, patients, and administrators into a single modern workflow for appointments,
                    records, approvals, and communication with clarity and control.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('get-started') }}" class="rounded-xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:from-cyan-400 hover:to-emerald-400">
                        Get Started
                    </a>
                    <a href="/about" class="rounded-xl border border-slate-600 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-slate-400 hover:text-white">
                        Learn More
                    </a>
                    <a href="/contact" class="rounded-xl border border-cyan-500/40 px-5 py-3 text-sm font-semibold text-cyan-200 transition hover:border-cyan-400 hover:text-white">
                        Contact Us
                    </a>
                    <a href="#privacy-policy" class="rounded-xl border border-violet-500/40 px-5 py-3 text-sm font-semibold text-violet-200 transition hover:border-violet-400 hover:text-white">
                        Privacy Policy
                    </a>
                </div>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 p-4">
                        <p class="text-xs text-cyan-200">Admin Control</p>
                        <p class="mt-2 text-sm font-semibold text-white">Users, approvals, reports, and settings.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-4">
                        <p class="text-xs text-emerald-200">Doctor Workspace</p>
                        <p class="mt-2 text-sm font-semibold text-white">Manage patients, records, and prescriptions.</p>
                    </div>
                    <div class="rounded-2xl border border-amber-400/30 bg-amber-400/10 p-4">
                        <p class="text-xs text-amber-200">Patient Portal</p>
                        <p class="mt-2 text-sm font-semibold text-white">Appointments, history, and medical follow-up.</p>
                    </div>
                    <div class="rounded-2xl border border-violet-400/30 bg-violet-400/10 p-4">
                        <p class="text-xs text-violet-200">Secure by Role</p>
                        <p class="mt-2 text-sm font-semibold text-white">Dedicated flows for each role.</p>
                    </div>
                </div>
            </section>
        </main>

        <section class="relative z-10 mx-auto w-full max-w-7xl px-6 pb-14">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-cyan-300">Doctors</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">Meet Our Verified Doctors</h2>
                    <p class="mt-2 text-sm text-slate-300">Trusted specialists ready to support patients with accurate care.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($doctors as $doctor)
                    <article class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-xl backdrop-blur">
                        <div class="flex items-start gap-3">
                            <img
                                src="{{ $doctor['photo_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($doctor['name']) . '&background=0f172a&color=ffffff' }}"
                                alt="{{ $doctor['name'] }}"
                                class="h-14 w-14 rounded-xl border border-white/10 object-cover"
                            >
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-bold text-white">{{ $doctor['name'] }}</h3>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-cyan-300">{{ $doctor['specialization'] }}</p>
                                <p class="mt-1 truncate text-xs text-slate-400">{{ $doctor['hospital_name'] ?: 'Sakina Partner Hospital' }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-300">
                            {{ \Illuminate\Support\Str::limit($doctor['bio'] ?: 'Experienced doctor delivering patient-centered care with modern clinical standards.', 130) }}
                        </p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300 sm:col-span-2 lg:col-span-3">
                        No doctors available yet. Add verified doctors from admin panel to display them here.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="privacy-policy" class="relative z-10 mx-auto w-full max-w-7xl px-6 pb-14">
            <div class="rounded-3xl border border-violet-400/20 bg-violet-500/10 p-6 shadow-2xl backdrop-blur">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-violet-200">Legal</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-white">{{ $privacyPolicy->title }}</h2>
                    </div>
                    <span class="rounded-full border border-violet-300/40 bg-violet-500/10 px-3 py-1 text-xs font-semibold text-violet-100">
                        Updated Content
                    </span>
                </div>

                <p class="whitespace-pre-line text-sm leading-7 text-slate-200">
                    {{ $privacyPolicy->content }}
                </p>
            </div>
        </section>

        <footer class="relative z-10 border-t border-white/10 bg-slate-950/90">
            <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-8 px-6 py-12 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-10 w-auto" />
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        Integrated digital healthcare platform for clinics, doctors, and patients with secure role-based access.
                    </p>
                    <div class="mt-4 inline-flex rounded-full border border-cyan-400/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">
                        Trusted Care Network
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-white">Quick Links</h4>
                    <ul class="mt-4 space-y-2 text-sm text-slate-300">
                        <li><a href="/" class="hover:text-white">Home</a></li>
                        <li><a href="/about" class="hover:text-white">About</a></li>
                        <li><a href="/contact" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('get-started') }}" class="hover:text-white">Get Started</a></li>
                        <li><a href="#privacy-policy" class="hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-white">Contact</h4>
                    <ul class="mt-4 space-y-3 text-sm text-slate-300">
                        <li><span class="text-slate-500">Phone:</span> {{ $contactInfo['phone'] }}</li>
                        <li><span class="text-slate-500">Email:</span> {{ $contactInfo['email'] }}</li>
                        <li><span class="text-slate-500">Address:</span> {{ $contactInfo['address'] }}</li>
                        <li><span class="text-slate-500">Hours:</span> {{ $contactInfo['hours'] }}</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-white">Social Media</h4>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        @foreach($socialLinks as $item)
                            @php
                                $platform = strtolower($item['platform'] ?? $item->platform ?? 'social');
                                $label = $item['label'] ?? $item->label ?? ucfirst($platform);
                                $url = $item['url'] ?? $item->url ?? '#';
                                $icon = 'https://ui-avatars.com/api/?name=' . urlencode(substr($label, 0, 2)) . '&background=0f172a&color=ffffff&size=48&rounded=true';
                            @endphp
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-2.5 py-2 text-xs font-semibold text-slate-200 transition hover:border-cyan-400/40 hover:bg-cyan-400/10 hover:text-white"
                            >
                                <img src="{{ $icon }}" alt="{{ $label }}" class="h-6 w-6 rounded-md border border-white/10 object-cover">
                                <span class="truncate">{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Manage social links from Admin Settings.</p>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 px-6 py-4 text-xs text-slate-400 md:flex-row">
                    <p>&copy; {{ date('Y') }} Sakina Health. All rights reserved.</p>
                    <p>Designed for modern healthcare operations.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
