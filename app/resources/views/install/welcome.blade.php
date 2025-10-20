@extends('install.layout')

@section('content')
    <div class="grid gap-8 p-8 lg:grid-cols-[2fr,1fr] lg:gap-12">
        <div>
            <h2 class="text-2xl font-semibold text-white sm:text-3xl">{{ __('Welcome to your HUBIT installation') }}</h2>
            <p class="mt-4 text-base text-slate-300">
                {{ __('This guided installer will walk you through the essential steps required to launch HUBIT securely. Before any files are modified, the system performs a preflight check to ensure your server meets the requirements.') }}
            </p>

            <dl class="mt-8 space-y-4">
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </span>
                    <div>
                        <dt class="text-lg font-semibold text-white">{{ __('Preflight Checks') }}</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-slate-300">{{ __('Validate PHP version, extensions, and filesystem permissions to guarantee compatibility.') }}</dd>
                    </div>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                        </svg>
                    </span>
                    <div>
                        <dt class="text-lg font-semibold text-white">{{ __('Environment & Database') }}</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-slate-300">{{ __('Write a production-ready .env file with secure defaults and MySQL credentials.') }}</dd>
                    </div>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75A2.25 2.25 0 0014.25 4.5h-4.5A2.25 2.25 0 007.5 6.75v3.75m9 0h2.25A2.25 2.25 0 0121 12.75v6.75H3v-6.75A2.25 2.25 0 015.25 10.5H7.5m9 0H7.5" />
                        </svg>
                    </span>
                    <div>
                        <dt class="text-lg font-semibold text-white">{{ __('Administrator Creation') }}</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-slate-300">{{ __('Create the first administrator account with best-practice password rules and automatic role assignment.') }}</dd>
                    </div>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 4.5h15v15h-15z" />
                        </svg>
                    </span>
                    <div>
                        <dt class="text-lg font-semibold text-white">{{ __('One-Time Locking') }}</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-slate-300">{{ __('After success the installer locks itself, returning a 404 response until the marker file is removed manually.') }}</dd>
                    </div>
                </div>
            </dl>
        </div>
        <div class="flex flex-col justify-between rounded-3xl border border-white/10 bg-slate-900/60 p-6 text-sm">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('Before you start') }}</h3>
                <ul class="mt-4 space-y-3 text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.75a.75.75 0 10-1.5 0v3.5a.75.75 0 00.22.53l2 2a.75.75 0 101.06-1.06l-1.78-1.78V6.25z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Ensure your server runs PHP :version or higher.', ['version' => config('installer.min_php')]) }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 7.75A.75.75 0 019.75 7h.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-.5A.75.75 0 019 12.25v-4.5zm1 6.75a.75.75 0 100 1.5.75.75 0 000-1.5z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Keep your MySQL credentials handy—they are required to run migrations and seeders.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm2.78-9.72a.75.75 0 00-1.06 0L9 11.99 7.28 10.3a.75.75 0 10-1.06 1.06l2.25 2.25a.75.75 0 001.06 0l3.25-3.25a.75.75 0 000-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Confirm write access to your project root, storage, bootstrap/cache, and database directories.') }}</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('install.checks') }}" class="inline-flex w-full items-center justify-center rounded-full bg-brand px-6 py-3 text-base font-semibold text-slate-950 shadow-lg shadow-brand/40 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-brand">
                    {{ __('Start Preflight Checks') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5L21 12l-7.5 7.5m7.5-7.5H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection
