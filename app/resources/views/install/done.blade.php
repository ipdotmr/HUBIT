@extends('install.layout')

@section('content')
    <div class="grid gap-8 p-8 lg:grid-cols-[2fr,1fr] lg:gap-12">
        <div class="space-y-6">
            <div>
                <h2 class="text-3xl font-semibold text-white">{{ __('Installation complete') }}</h2>
                <p class="mt-2 text-base text-slate-300">{{ __('HUBIT is now ready to use. The installer has been locked for security and future visits to /install will return a 404 response.') }}</p>
            </div>

            <div class="rounded-3xl border border-emerald-400/30 bg-emerald-500/10 p-6 text-slate-100 shadow-lg shadow-emerald-500/20">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zM10.5 14.19l-2.22-2.22a.75.75 0 10-1.06 1.06l2.75 2.75a.75.75 0 001.06 0l6-6a.75.75 0 10-1.06-1.06l-5.47 5.47z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ __('You’re all set!') }}</h3>
                        <p class="text-sm text-emerald-100/80">{{ __('Migrations were executed, caches optimised, and your administrator account is ready.') }}</p>
                    </div>
                </div>
                <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9.25V6.75a1 1 0 112 0v4.5a1 1 0 01-.293.707l-2 2a1 1 0 11-1.414-1.414l1.707-1.707z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Log in with the administrator credentials you configured to access the dashboard.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.78-10.53a.75.75 0 00-1.06-1.06L9 7.94 7.78 6.72a.75.75 0 10-1.06 1.06L7.94 9l-1.22 1.22a.75.75 0 101.06 1.06L9 10.06l1.22 1.22a.75.75 0 101.06-1.06L10.06 9l1.22-1.22z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('To rerun the installer (not recommended), remove the marker file at :path.', ['path' => config('installer.marker_path')]) }}</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-full bg-brand px-6 py-3 text-base font-semibold text-slate-950 shadow-lg shadow-brand/40 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-brand">
                    {{ __('Go to application') }}
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-brand">
                    {{ __('Sign in as administrator') }}
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6 text-sm text-slate-300">
            <h3 class="text-lg font-semibold text-white">{{ __('Next steps') }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ __('Review the deployment checklist in the README to finalise your production environment and enable queues, mail, and monitoring.') }}</p>
            <div class="mt-6 space-y-3 text-xs">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p>{{ __('Remember to update DNS, SSL, and SMTP settings before onboarding customers.') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p>{{ __('Consider enabling Horizon and queue workers, then restart them after deployments using the runbook commands.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
