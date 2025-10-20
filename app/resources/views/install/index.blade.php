@extends('install.layout')

@section('content')
    <div class="bg-white/90 backdrop-blur shadow-xl rounded-3xl overflow-hidden ring-1 ring-slate-200">
        <div class="grid lg:grid-cols-[260px,1fr]">
            <aside class="bg-slate-900 text-white p-6 space-y-6">
                <div>
                    <p class="text-sm uppercase tracking-wide text-slate-300">HUBIT Installer</p>
                    <h1 class="mt-2 text-2xl font-semibold">Let’s get you set up</h1>
                </div>
                <nav class="space-y-3" aria-label="Installation steps">
                    <div class="flex items-start gap-3" data-step-indicator="1">
                        <span class="mt-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-300 font-medium">1</span>
                        <div>
                            <p class="font-semibold">Pre-check</p>
                            <p class="text-sm text-slate-300">Validate server requirements.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 opacity-60" data-step-indicator="2">
                        <span class="mt-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-500/20 text-slate-300 font-medium">2</span>
                        <div>
                            <p class="font-semibold">Configure</p>
                            <p class="text-sm text-slate-300">Generate your environment file.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 opacity-60" data-step-indicator="3">
                        <span class="mt-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-500/20 text-slate-300 font-medium">3</span>
                        <div>
                            <p class="font-semibold">Install</p>
                            <p class="text-sm text-slate-300">Run migrations & finalize.</p>
                        </div>
                    </div>
                </nav>
            </aside>
            <main class="p-8 lg:p-10 space-y-10" data-installer-container
                  data-can-continue="{{ $canContinue ? 'true' : 'false' }}">
                <section id="step-1" class="space-y-6">
                    <header>
                        <p class="text-sm font-medium text-emerald-600">Step 1</p>
                        <h2 class="text-2xl font-semibold text-slate-900">Pre-check</h2>
                        <p class="mt-2 text-slate-600">Review the server & application requirements below. Resolve any ❌ items before continuing.</p>
                    </header>
                    <div class="grid gap-3">
                        @foreach ($checks as $check)
                            <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white/70 px-4 py-3"
                                 data-check="{{ $check['key'] }}" data-status="{{ $check['status'] }}">
                                <span class="text-xl" aria-hidden="true">
                                    @switch($check['status'])
                                        @case('pass')
                                            ✅
                                            @break
                                        @case('fail')
                                            ❌
                                            @break
                                        @case('warn')
                                            ⚠️
                                            @break
                                        @default
                                            ⏳
                                    @endswitch
                                </span>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900">{{ $check['label'] }}</p>
                                    <p class="text-sm text-slate-600">{{ $check['message'] }}</p>
                                </div>
                                @if (!($check['required'] ?? true))
                                    <span class="text-xs uppercase tracking-wide text-slate-400">Optional</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4">
                        <p class="text-sm text-slate-600">Required checks must be green before you continue.</p>
                        <button id="btn-precheck-continue" type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:cursor-not-allowed disabled:bg-emerald-600/50"
                                @if (! $canContinue) disabled @endif>
                            Continue
                        </button>
                    </div>
                </section>

                <section id="step-2" class="space-y-6 hidden" aria-hidden="true">
                    <header>
                        <p class="text-sm font-medium text-emerald-600">Step 2</p>
                        <h2 class="text-2xl font-semibold text-slate-900">Configure environment</h2>
                        <p class="mt-2 text-slate-600">Provide your application, database, queue, and mail settings. Passwords are never shown once saved.</p>
                    </header>
                    <form id="installer-config-form" class="space-y-8">
                        @csrf
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">Application</h3>
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">App Name</span>
                                        <input type="text" name="app_name" value="{{ old('app_name', 'HUBIT') }}" required
                                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">App URL</span>
                                        <input type="url" name="app_url" value="{{ old('app_url', config('app.url')) }}" required
                                               placeholder="https://example.com"
                                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                    </label>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Locale</span>
                                            <input type="text" name="app_locale" value="{{ old('app_locale', config('app.locale')) }}" required
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Fallback Locale</span>
                                            <input type="text" name="app_fallback_locale" value="{{ old('app_fallback_locale', config('app.fallback_locale')) }}" required
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                    </div>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Timezone</span>
                                        <input type="text" name="app_timezone" value="{{ old('app_timezone', config('app.timezone')) }}" required
                                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                    </label>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Environment</span>
                                            <select name="app_env" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                                <option value="production" selected>production</option>
                                                <option value="staging">staging</option>
                                                <option value="local">local</option>
                                            </select>
                                        </label>
                                        <label class="flex items-center gap-3 mt-6">
                                            <input type="checkbox" name="app_debug" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                            <span class="text-sm text-slate-700">Enable debug</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">Database</h3>
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Driver</span>
                                        <select name="db_driver" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <option value="mysql" selected>mysql</option>
                                            <option value="pgsql">pgsql</option>
                                            <option value="sqlsrv">sqlsrv</option>
                                        </select>
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Host</span>
                                        <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required
                                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                    </label>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Port</span>
                                            <input type="number" name="db_port" value="{{ old('db_port', 3306) }}" required
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Database</span>
                                            <input type="text" name="db_database" value="{{ old('db_database') }}" required
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                    </div>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Username</span>
                                            <input type="text" name="db_username" value="{{ old('db_username') }}" required
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Password</span>
                                            <input type="password" name="db_password"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" autocomplete="new-password" />
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-slate-100 px-3 py-2">
                                        <p class="text-sm text-slate-600" id="db-check-message">Database test has not been run.</p>
                                        <button type="button" id="btn-test-database"
                                                class="inline-flex items-center gap-2 rounded-full border border-emerald-600 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                                            Test connection
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">Cache & Queue</h3>
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Cache driver</span>
                                        <select name="cache_driver" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <option value="file" selected>file</option>
                                            <option value="redis">redis</option>
                                        </select>
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Queue connection</span>
                                        <select name="queue_connection" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <option value="sync" selected>sync</option>
                                            <option value="database">database</option>
                                            <option value="redis">redis</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">Mail</h3>
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Mailer</span>
                                        <select name="mail_mailer" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                            <option value="log" selected>log</option>
                                            <option value="smtp">smtp</option>
                                        </select>
                                    </label>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Host</span>
                                            <input type="text" name="mail_host" value="{{ old('mail_host') }}"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Port</span>
                                            <input type="number" name="mail_port" value="{{ old('mail_port') }}"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                    </div>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Username</span>
                                            <input type="text" name="mail_username" value="{{ old('mail_username') }}"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Password</span>
                                            <input type="password" name="mail_password"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" autocomplete="new-password" />
                                        </label>
                                    </div>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Encryption</span>
                                        <input type="text" name="mail_encryption" value="{{ old('mail_encryption') }}"
                                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                    </label>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">From address</span>
                                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address') }}"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">From name</span>
                                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name') }}"
                                                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-slate-900">Administrator</h3>
                            <p class="text-sm text-slate-600">We’ll create an admin account if no users exist. You can provide a password or let the installer generate one.</p>
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700">Name</span>
                                    <input type="text" name="admin_name" value="{{ old('admin_name', 'Administrator') }}" required
                                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                </label>
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700">Email</span>
                                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                </label>
                            </div>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Password (optional)</span>
                                <input type="password" name="admin_password" autocomplete="new-password"
                                       class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                                       placeholder="Leave blank to auto-generate" />
                            </label>
                        </div>

                        <div id="config-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <p class="text-sm text-slate-600">We back up any existing .env before writing.</p>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:cursor-not-allowed disabled:bg-emerald-600/50">
                                Save & Continue
                            </button>
                        </div>
                    </form>
                </section>

                @include('install.run')
            </main>
        </div>
    </div>
@endsection
