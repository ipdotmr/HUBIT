@extends('install.layout')

@section('content')
    <div class="grid gap-8 p-8 lg:grid-cols-[2fr,1fr] lg:gap-12">
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-white sm:text-3xl">{{ __('Preflight checks') }}</h2>
                <p class="mt-2 text-sm text-slate-300">{{ __('Review the server requirements below. Critical checks must pass before proceeding to environment configuration.') }}</p>
            </div>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ __('Core requirements') }}</h3>
                    <div class="mt-3 space-y-3">
                        @include('install.partials.check', ['check' => $phpCheck])
                        @include('install.partials.check', ['check' => $databaseCheck, 'warning' => ! $databaseCheck['status']])
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white">{{ __('PHP extensions') }}</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($extensionChecks as $check)
                            @include('install.partials.badge', ['label' => $check['label'], 'passed' => $check['status']])
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white">{{ __('Filesystem permissions') }}</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($permissionChecks as $check)
                            @include('install.partials.check', ['check' => $check])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-3xl border border-white/10 bg-slate-900/60 p-6 text-sm">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('Status summary') }}</h3>
                <p class="mt-3 text-slate-300">
                    {{ __('All critical checks must succeed before you can configure the environment. Database connectivity will be tested again after your credentials are saved.') }}
                </p>

                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-300">{{ __('PHP version') }}</dt>
                        <dd class="text-right">
                            @include('install.partials.status-chip', ['passed' => $phpCheck['status'], 'label' => $phpCheck['details']])
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-300">{{ __('Extensions') }}</dt>
                        <dd class="text-right">
                            @include('install.partials.status-chip', ['passed' => $extensionChecks->every(fn ($check) => $check['status']), 'label' => $extensionChecks->where('status', true)->count().'/'.$extensionChecks->count()])
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-300">{{ __('Permissions') }}</dt>
                        <dd class="text-right">
                            @include('install.partials.status-chip', ['passed' => $permissionChecks->every(fn ($check) => $check['status']), 'label' => $permissionChecks->where('status', true)->count().'/'.$permissionChecks->count()])
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-300">{{ __('Database ping') }}</dt>
                        <dd class="text-right">
                            @include('install.partials.status-chip', ['passed' => $databaseCheck['status'], 'label' => $databaseCheck['status'] ? __('OK') : __('Pending')])
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="mt-8 space-y-4">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-xs text-slate-300">
                    <p>{{ $databaseCheck['details'] }}</p>
                </div>

                <a @class([
                        'inline-flex w-full items-center justify-center rounded-full px-6 py-3 text-base font-semibold shadow-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900',
                        'bg-brand text-slate-950 shadow-brand/40 hover:bg-sky-400 focus:ring-brand' => $allPassed,
                        'bg-slate-700/60 text-slate-400 cursor-not-allowed' => ! $allPassed,
                    ]) href="{{ $allPassed ? route('install.env') : '#' }}">
                    {{ $allPassed ? __('Continue to environment setup') : __('Resolve the pending checks to continue') }}
                    @if ($allPassed)
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5L21 12l-7.5 7.5m7.5-7.5H3" />
                        </svg>
                    @endif
                </a>

                <a href="{{ route('install.welcome') }}" class="block text-center text-xs font-medium text-slate-400 underline-offset-4 hover:text-white hover:underline">
                    {{ __('Back to introduction') }}
                </a>
            </div>
        </div>
    </div>
@endsection
