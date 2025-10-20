@extends('install.layout')

@section('content')
    <div class="grid gap-8 p-8 lg:grid-cols-[2fr,1fr] lg:gap-12">
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-white sm:text-3xl">{{ __('Administrator account') }}</h2>
                <p class="mt-2 text-sm text-slate-300">{{ __('Create the first administrator who will manage HUBIT after installation. Passwords follow Laravel’s default strength rules.') }}</p>
            </div>

            <form method="POST" action="{{ route('install.admin.save') }}" class="space-y-6">
                @csrf
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-white">{{ __('Full name') }}</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $admin['name'] ?? '') }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" autocomplete="off">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-white">{{ __('Email address') }}</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $admin['email'] ?? '') }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-white">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" required class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" autocomplete="new-password">
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-white">{{ __('Confirm password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" autocomplete="new-password">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-brand">
                        {{ __('Save administrator') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="flex flex-col justify-between rounded-3xl border border-white/10 bg-slate-900/60 p-6 text-sm">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white">{{ __('Run installation') }}</h3>
                <p class="text-slate-300">{{ __('When you are ready, run the installer to generate the application key, run migrations, seed the first administrator, and optimise configuration caches.') }}</p>

                <ul class="mt-4 space-y-3 text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9.25V6.75a1 1 0 112 0v4.5a1 1 0 01-.293.707l-2 2a1 1 0 11-1.414-1.414l1.707-1.707z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Installer creates the admin role when available or toggles an is_admin flag if supported.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7.28 7.22a.75.75 0 011.06 0L10 8.94l1.66-1.72a.75.75 0 111.08 1.04L11.06 10l1.72 1.66a.75.75 0 11-1.04 1.08L10 11.06l-1.66 1.72a.75.75 0 11-1.08-1.04L8.94 10 7.28 8.34a.75.75 0 010-1.12z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('A lock file is written to :path and routes are disabled until it is removed manually.', ['path' => config('installer.marker_path')]) }}</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 space-y-3">
                @if (! $envWritten)
                    <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-4 text-xs text-amber-100">
                        {{ __('Environment settings have not been saved yet. Complete the previous step before running the installer.') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('install.run') }}">
                    @csrf
                    <button type="submit" @class([
                            'inline-flex w-full items-center justify-center rounded-full px-6 py-3 text-base font-semibold shadow-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900',
                            'bg-brand text-slate-950 shadow-brand/40 hover:bg-sky-400 focus:ring-brand' => $envWritten && ! empty($admin),
                            'cursor-not-allowed bg-slate-700/60 text-slate-400' => ! ($envWritten && ! empty($admin)),
                        ]) @disabled(! ($envWritten && ! empty($admin)))>
                        {{ __('Run installation now') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5L21 12l-7.5 7.5M3 12h18" />
                        </svg>
                    </button>
                </form>

                <a href="{{ route('install.env') }}" class="block text-center text-xs font-medium text-slate-400 underline-offset-4 hover:text-white hover:underline">{{ __('Back to environment step') }}</a>
            </div>
        </div>
    </div>
@endsection
