@php
    $passed = $passed ?? false;
@endphp
<div @class([
        'flex items-center justify-between rounded-2xl border px-4 py-3 text-sm font-medium',
        'border-emerald-400/30 bg-emerald-500/10 text-emerald-200' => $passed,
        'border-rose-400/30 bg-rose-500/10 text-rose-200' => ! $passed,
    ])>
    <span>{{ strtoupper($label) }}</span>
    <span class="inline-flex items-center gap-1">
        @if ($passed)
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.253 7.253a1 1 0 01-1.414 0L3.296 9.215a1 1 0 011.414-1.414l3.327 3.326 6.546-6.546a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
        @else
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-500/20 text-rose-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm2.28-10.53a.75.75 0 10-1.06-1.06L10 7.94 8.78 6.72a.75.75 0 10-1.06 1.06L8.94 9l-1.22 1.22a.75.75 0 101.06 1.06L10 10.06l1.22 1.22a.75.75 0 101.06-1.06L11.06 9l1.22-1.22z" clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </span>
</div>
