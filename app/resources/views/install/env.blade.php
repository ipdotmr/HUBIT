@extends('install.layout')

@section('content')
    <form method="POST" action="{{ route('install.env.save') }}" class="grid gap-8 p-8 lg:grid-cols-[2fr,1fr] lg:gap-12">
        @csrf
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-white sm:text-3xl">{{ __('Environment configuration') }}</h2>
                <p class="mt-2 text-sm text-slate-300">{{ __('Provide the base application details and database credentials. The installer will write a secure .env file using these values.') }}</p>
            </div>

            @if($hasExistingEnv)
                <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-4 text-sm text-amber-100">
                    {{ __('An existing .env file was detected. Saving this form will overwrite it using the values below.') }}
                </div>
            @endif

            <div class="space-y-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="app_name" class="block text-sm font-medium text-white">{{ __('Application Name') }}</label>
                        <input id="app_name" name="app_name" type="text" required value="{{ old('app_name', $defaults['app_name']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" autocomplete="off">
                    </div>
                    <div class="space-y-2">
                        <label for="app_url" class="block text-sm font-medium text-white">{{ __('Application URL') }}</label>
                        <input id="app_url" name="app_url" type="url" required value="{{ old('app_url', $defaults['app_url']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" placeholder="https://example.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-white">{{ __('MySQL database') }}</h3>
                    <p class="text-xs text-slate-400">{{ __('The installer keeps production on MySQL. Ensure the database is created and reachable.') }}</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="db_host" class="block text-sm font-medium text-white">{{ __('Host') }}</label>
                        <input id="db_host" name="db_host" type="text" required value="{{ old('db_host', $defaults['db_host']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div class="space-y-2">
                        <label for="db_port" class="block text-sm font-medium text-white">{{ __('Port') }}</label>
                        <input id="db_port" name="db_port" type="number" min="1" required value="{{ old('db_port', $defaults['db_port']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div class="space-y-2">
                        <label for="db_database" class="block text-sm font-medium text-white">{{ __('Database') }}</label>
                        <input id="db_database" name="db_database" type="text" required value="{{ old('db_database', $defaults['db_database']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div class="space-y-2">
                        <label for="db_username" class="block text-sm font-medium text-white">{{ __('Username') }}</label>
                        <input id="db_username" name="db_username" type="text" required value="{{ old('db_username', $defaults['db_username']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="db_password" class="block text-sm font-medium text-white">{{ __('Password') }}</label>
                    <input id="db_password" name="db_password" type="password" value="{{ old('db_password', $defaults['db_password']) }}" class="block w-full rounded-xl border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-3xl border border-white/10 bg-slate-900/60 p-6 text-sm">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('What happens next?') }}</h3>
                <ul class="mt-4 space-y-3 text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-7.25V6.75a.75.75 0 011.5 0v4l1.72 1.72a.75.75 0 11-1.06 1.06l-2.16-2.16a.75.75 0 01-.2-.52z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('The installer writes a locked .env file using safe quoting and clears cached configuration.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.78-10.53a.75.75 0 00-1.06-1.06L9 7.94 7.78 6.72a.75.75 0 10-1.06 1.06L7.94 9l-1.22 1.22a.75.75 0 101.06 1.06L9 10.06l1.22 1.22a.75.75 0 101.06-1.06L10.06 9l1.22-1.22z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('Your MySQL credentials remain in production while automated tests continue to use isolated SQLite databases.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.22-10.78a.75.75 0 00-1.06 0L7.5 8.44 6.28 7.22a.75.75 0 00-1.06 1.06L6.44 9.5l-1.22 1.22a.75.75 0 101.06 1.06L7.5 10.56l1.22 1.22a.75.75 0 101.06-1.06L8.56 9.5l1.22-1.22a.75.75 0 000-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>{{ __('After saving you will configure the first administrator and run migrations.') }}</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 space-y-3">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-brand px-6 py-3 text-base font-semibold text-slate-950 shadow-lg shadow-brand/40 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-brand">
                    {{ __('Save environment') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>

                <a href="{{ route('install.checks') }}" class="block text-center text-xs font-medium text-slate-400 underline-offset-4 hover:text-white hover:underline">{{ __('Back to preflight checks') }}</a>
            </div>
        </div>
    </form>
@endsection
