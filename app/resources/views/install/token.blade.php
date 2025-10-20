@extends('install.layout')

@section('content')
    <div class="max-w-xl mx-auto bg-white/90 backdrop-blur shadow-xl rounded-3xl ring-1 ring-slate-200 p-10 space-y-6">
        <header class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Installer access required</h1>
            <p class="text-sm text-slate-600">Provide the one-time installer token from your hosting control panel to continue.</p>
        </header>
        @if (!empty($error))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $error }}</div>
        @endif
        <form method="GET" action="{{ route('install.index') }}" class="space-y-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Installer token</span>
                <input type="password" name="token" required autocomplete="one-time-code"
                       class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
            </label>
            <button type="submit" class="w-full rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                Continue
            </button>
        </form>
        <p class="text-xs text-center text-slate-500">Need help? Contact your administrator to retrieve the INSTALL_TOKEN value.</p>
    </div>
@endsection
