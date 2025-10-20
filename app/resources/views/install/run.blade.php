<section id="step-3" class="space-y-6 hidden" aria-hidden="true">
    <header>
        <p class="text-sm font-medium text-emerald-600">Step 3</p>
        <h2 class="text-2xl font-semibold text-slate-900">Run installer</h2>
        <p class="mt-2 text-slate-600">We’ll execute database migrations, link storage, and finalize your installation. Progress appears below.</p>
    </header>
    <div class="rounded-2xl border border-slate-200 bg-white/80">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Installation log</p>
                    <p class="text-xs text-slate-500">Updates live as each step completes.</p>
                </div>
                <button id="btn-start-install" type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white shadow focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                    Start installation
                </button>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <ol class="space-y-3 text-sm" id="install-steps" aria-live="polite">
                <li data-run-step="clear_cache" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Clear caches</p>
                        <p class="text-xs text-slate-500">config:clear, cache:clear, route:clear, view:clear</p>
                    </div>
                </li>
                <li data-run-step="migrate" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Run database migrations</p>
                        <p class="text-xs text-slate-500">php artisan migrate --force</p>
                    </div>
                </li>
                <li data-run-step="storage_link" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Create storage link</p>
                        <p class="text-xs text-slate-500">php artisan storage:link</p>
                    </div>
                </li>
                <li data-run-step="optimize" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Optimize application</p>
                        <p class="text-xs text-slate-500">php artisan optimize</p>
                    </div>
                </li>
                <li data-run-step="create_admin" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Create admin user</p>
                        <p class="text-xs text-slate-500">Ensure at least one admin account exists</p>
                    </div>
                </li>
                <li data-run-step="test_mail" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Send test mail</p>
                        <p class="text-xs text-slate-500">Optional SMTP verification</p>
                    </div>
                </li>
                <li data-run-step="queue_restart" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Restart queues</p>
                        <p class="text-xs text-slate-500">queue:restart & horizon:terminate</p>
                    </div>
                </li>
                <li data-run-step="lock" class="flex items-start gap-3">
                    <span class="mt-1 h-3 w-3 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <div>
                        <p class="font-medium text-slate-900">Finalize & lock installer</p>
                        <p class="text-xs text-slate-500">Create storage/framework/install.lock</p>
                    </div>
                </li>
            </ol>
            <div id="install-log"
                 class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-slate-950/90 p-4 font-mono text-xs text-emerald-200"
                 role="log" aria-live="polite"></div>
            <div id="install-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
            <div id="install-success" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                <p class="font-semibold">Installation complete!</p>
                <p class="text-xs text-emerald-700">You can now sign in to HUBIT. Keep any generated password safe—it will only be shown once.</p>
                <div class="mt-3 flex flex-wrap gap-3" id="admin-credentials" aria-live="polite"></div>
                <a href="{{ url('/') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Go to application</a>
            </div>
            <div class="hidden" id="install-retry">
                <button type="button" class="inline-flex items-center gap-2 rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-white" data-retry-button>Retry step</button>
            </div>
        </div>
    </div>
</section>
