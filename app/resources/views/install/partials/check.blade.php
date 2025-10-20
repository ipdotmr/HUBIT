@php
    $passed = $check['status'] ?? false;
    $label = $check['label'] ?? '';
    $details = $check['details'] ?? null;
    $warning = $warning ?? false;
@endphp
<div @class([
        'rounded-2xl border px-4 py-4 text-sm transition',
        'border-emerald-400/30 bg-emerald-500/10 text-emerald-100' => $passed,
        'border-amber-400/30 bg-amber-500/10 text-amber-100' => ! $passed && $warning,
        'border-rose-400/30 bg-rose-500/10 text-rose-100' => ! $passed && ! $warning,
    ])>
    <div class="flex items-center justify-between gap-3">
        <span class="font-medium text-white">{{ $label }}</span>
        @include('install.partials.status-chip', ['passed' => $passed, 'label' => $passed ? __('Ready') : __('Action required')])
    </div>
    @if ($details)
        <p class="mt-2 text-xs leading-relaxed text-slate-200/80">
            {{ $details }}
        </p>
    @endif
</div>
