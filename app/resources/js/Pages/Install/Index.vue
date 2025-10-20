<script setup>
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    appName: {
        type: String,
        default: 'HUBIT',
    },
});

const loading = ref(false);
const error = ref(null);
const checks = ref([]);

const severityOrder = {
    required: 0,
    optional: 1,
};

const sortedChecks = computed(() =>
    [...checks.value].sort((a, b) => {
        const severityScoreA = severityOrder[a.severity ?? 'required'] ?? 99;
        const severityScoreB = severityOrder[b.severity ?? 'required'] ?? 99;

        if (severityScoreA !== severityScoreB) {
            return severityScoreA - severityScoreB;
        }

        if (a.status === b.status) {
            return a.label.localeCompare(b.label);
        }

        return a.status ? 1 : -1;
    }),
);

const requiredFailures = computed(() =>
    sortedChecks.value.filter((check) => (check.severity ?? 'required') === 'required' && !check.status),
);

const optionalFailures = computed(() =>
    sortedChecks.value.filter((check) => (check.severity ?? 'required') === 'optional' && !check.status),
);

const summary = computed(() => {
    if (loading.value && !checks.value.length) {
        return {
            state: 'loading',
            heading: 'Checking system requirements…',
            message: 'Hang tight while we verify your environment.',
        };
    }

    if (error.value) {
        return {
            state: 'error',
            heading: 'Unable to complete preflight checks',
            message: error.value,
        };
    }

    if (requiredFailures.value.length) {
        return {
            state: 'fail',
            heading: 'Action required before continuing',
            message: 'Resolve the failed requirements below and re-run the checks.',
        };
    }

    if (optionalFailures.value.length) {
        return {
            state: 'warn',
            heading: 'Almost ready — review the recommendations',
            message: 'Optional improvements are listed below. You can continue once you are comfortable.',
        };
    }

    return {
        state: 'pass',
        heading: 'All systems ready',
        message: "Your environment meets HUBIT's installation prerequisites.",
    };
});

const stateStyles = {
    loading: 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-950/40 dark:text-sky-200 dark:ring-sky-900',
    error: 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950/40 dark:text-rose-200 dark:ring-rose-900',
    fail: 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950/40 dark:text-rose-200 dark:ring-rose-900',
    warn: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-200 dark:ring-amber-900',
    pass: 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-200 dark:ring-emerald-900',
};

const runPreflight = async () => {
    loading.value = true;
    error.value = null;

    try {
        const { data } = await axios.post('/api/install/preflight');

        checks.value = Array.isArray(data?.checks) ? data.checks : [];
    } catch (err) {
        error.value =
            err?.response?.data?.message ??
            err?.message ??
            'We could not connect to the preflight endpoint. Check your network connection and try again.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    runPreflight();
});
</script>

<template>
    <Head title="Installer" />

    <div class="min-h-screen bg-slate-950 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-950 py-16 text-slate-100">
        <div class="mx-auto w-full max-w-5xl px-6">
            <header class="mb-10 flex flex-col gap-3 text-center">
                <p class="text-sm font-medium uppercase tracking-[0.35em] text-sky-400">
                    HUBIT Installer
                </p>
                <h1 class="text-3xl font-semibold text-white md:text-4xl">
                    Welcome to the {{ props.appName }} setup assistant
                </h1>
                <p class="mx-auto max-w-2xl text-sm text-slate-300 md:text-base">
                    We will run a quick system preflight check before continuing with the installation. Review the
                    results below and resolve any issues that are highlighted in red.
                </p>
            </header>

            <section
                :class="[
                    'mb-10 rounded-2xl border border-transparent p-6 shadow-lg ring-1 transition',
                    stateStyles[summary.state],
                ]"
            >
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <div class="flex items-center gap-3 text-base font-medium md:text-lg">
                            <span
                                v-if="summary.state === 'pass'"
                                aria-hidden="true"
                                class="flex size-8 items-center justify-center rounded-full bg-emerald-100/80 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span
                                v-else-if="summary.state === 'warn'"
                                aria-hidden="true"
                                class="flex size-8 items-center justify-center rounded-full bg-amber-100/80 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.451 11.479c.75 1.333-.213 2.995-1.743 2.995H3.55c-1.53 0-2.493-1.662-1.743-2.995L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-.25-5.75a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span
                                v-else-if="summary.state === 'fail'"
                                aria-hidden="true"
                                class="flex size-8 items-center justify-center rounded-full bg-rose-100/80 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span
                                v-else-if="summary.state === 'error'"
                                aria-hidden="true"
                                class="flex size-8 items-center justify-center rounded-full bg-rose-100/80 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5a1 1 0 012 0v5a1 1 0 01-2 0V5zm1 10a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span
                                v-else
                                aria-hidden="true"
                                class="flex size-8 items-center justify-center rounded-full bg-sky-100/80 text-sky-700 dark:bg-sky-900/40 dark:text-sky-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 animate-spin">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992m-4.992 0l3.535-3.535m-3.535 3.535L19.558 12.88M11.977 4.652V2.005m0 2.647L8.442 3.117m3.535 1.535L7.44 7.689M4.5 12h-2.647m2.647 0L3.117 8.465m1.383 3.535L7.69 16.56m4.287 2.788v2.647m0-2.647l3.535 1.535m-3.535-1.535 3.535-3.535" />
                                </svg>
                            </span>
                            <span>{{ summary.heading }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-200/80 md:text-base">
                            {{ summary.message }}
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="loading"
                            @click="runPreflight"
                        >
                            <svg
                                v-if="loading"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-4 animate-spin"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 4.5 20 8m0 0-3.5 3.5M20 8H4m3.5-3.5L4 8m0 0 3.5 3.5M4 8h16m-7.5 8.5L12 20m0 0-1.5-3.5M12 20v-8"
                                />
                            </svg>
                            <span>{{ loading ? 'Re-running…' : 'Re-run checks' }}</span>
                        </button>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div
                    v-for="check in sortedChecks"
                    :key="check.key"
                    class="flex items-start justify-between gap-4 rounded-2xl border border-white/5 bg-white/5 p-4 text-left shadow-sm transition hover:border-white/10 hover:bg-white/10"
                    :class="{
                        'border-rose-400/60 bg-rose-500/10 hover:bg-rose-500/15': !check.status && (check.severity ?? 'required') === 'required',
                        'border-amber-400/60 bg-amber-500/10 hover:bg-amber-500/15': !check.status && (check.severity ?? 'required') === 'optional',
                        'border-emerald-500/40 bg-emerald-500/5 hover:bg-emerald-500/10': check.status,
                    }"
                >
                    <div class="flex gap-3">
                        <span
                            aria-hidden="true"
                            class="mt-1 flex size-8 items-center justify-center rounded-full"
                            :class="{
                                'bg-emerald-500/20 text-emerald-300': check.status,
                                'bg-rose-500/20 text-rose-200': !check.status && (check.severity ?? 'required') === 'required',
                                'bg-amber-500/20 text-amber-200': !check.status && (check.severity ?? 'required') === 'optional',
                            }"
                        >
                            <svg
                                v-if="check.status"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5a1 1 0 012 0v5a1 1 0 01-2 0V5zm1 10a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </span>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-base font-semibold text-white md:text-lg">
                                    {{ check.label }}
                                </h2>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium uppercase tracking-wide"
                                    :class="{
                                        'bg-white/10 text-slate-200': (check.severity ?? 'required') === 'required',
                                        'bg-amber-400/20 text-amber-100': (check.severity ?? 'required') === 'optional',
                                    }"
                                >
                                    {{ (check.severity ?? 'required') === 'required' ? 'Required' : 'Optional' }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-200/80 md:text-base">
                                {{ check.message }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center text-sm font-medium">
                        <span
                            :class="{
                                'text-emerald-300': check.status,
                                'text-rose-200': !check.status && (check.severity ?? 'required') === 'required',
                                'text-amber-200': !check.status && (check.severity ?? 'required') === 'optional',
                            }"
                        >
                            {{ check.status ? 'Ready' : 'Needs attention' }}
                        </span>
                    </div>
                </div>

                <div v-if="!sortedChecks.length && !loading" class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-sm text-slate-200/80">
                    No checks have been run yet. Click "Re-run checks" to get started.
                </div>
            </section>
        </div>
    </div>
</template>
