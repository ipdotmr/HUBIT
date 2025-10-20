@php
    $passed = $passed ?? false;
    $label = $label ?? ($passed ? __('OK') : __('Pending'));
@endphp
<span @class([
        'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide',
        'bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/40' => $passed,
        'bg-rose-500/15 text-rose-200 ring-1 ring-rose-400/40' => ! $passed,
    ])>
    @if ($passed)
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.253 7.253a1 1 0 01-1.414 0L3.296 9.215a1 1 0 011.414-1.414l3.327 3.326 6.546-6.546a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    @else
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-9.25a.75.75 0 00-1.5 0v3.5a.75.75 0 001.5 0v-3.5zm-.75 5.5a.88.88 0 100 1.75.88.88 0 000-1.75z" clip-rule="evenodd" />
        </svg>
    @endif
    <span>{{ $label }}</span>
</span>
