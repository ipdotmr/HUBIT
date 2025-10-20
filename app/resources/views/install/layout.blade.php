<!DOCTYPE html>
@php
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $direction = in_array(app()->getLocale(), $rtlLocales, true) ? 'rtl' : 'ltr';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('HUBIT Installer') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,500,600,700">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#38bdf8',
                            dark: '#0f172a',
                            accent: '#22c55e',
                        },
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="relative">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-dark/70 via-slate-950 to-slate-950"></div>
        <div class="relative z-10 flex min-h-screen flex-col px-4 py-10 sm:px-6 lg:px-8">
            <header class="mx-auto w-full max-w-4xl text-center">
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                    <div class="text-left">
                        <p class="text-sm font-semibold uppercase tracking-widest text-brand/80">{{ __('Installation Wizard') }}</p>
                        <h1 class="mt-1 text-3xl font-bold text-white sm:text-4xl">{{ __('HUBIT Setup Assistant') }}</h1>
                    </div>
                    <div class="rounded-full bg-white/5 px-4 py-2 text-sm text-white shadow-lg backdrop-blur">
                        <span class="font-semibold">{{ __('Environment') }}</span>
                        <span class="mx-2 text-slate-400">&bull;</span>
                        <span class="font-semibold">{{ __('Administrator') }}</span>
                        <span class="mx-2 text-slate-400">&bull;</span>
                        <span class="font-semibold">{{ __('Finish') }}</span>
                    </div>
                </div>
                <p class="mt-6 text-base text-slate-300">
                    {{ __('Follow the guided steps to configure HUBIT securely. System checks run before modifying your environment.') }}
                </p>
            </header>

            <main class="mx-auto mt-10 w-full max-w-4xl flex-1">
                @if(session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-4 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-500/10 p-4 text-sm text-rose-200">
                        <ul class="list-disc space-y-1 ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl shadow-brand-dark/40 backdrop-blur">
                    @yield('content')
                </section>
            </main>

            <footer class="mx-auto mt-12 w-full max-w-4xl text-center text-xs text-slate-400">
                <p>&copy; {{ now()->year }} HUBIT. {{ __('All rights reserved.') }}</p>
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
