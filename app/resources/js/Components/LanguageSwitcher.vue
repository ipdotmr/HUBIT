<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const currentLocale = ref(document.documentElement.lang || 'en');

const languages = [
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' },
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
];

const switchLanguage = (locale) => {
    router.post('/language', { locale }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            currentLocale.value = locale;
            document.documentElement.lang = locale;
            document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
            window.location.reload();
        },
    });
};

onMounted(() => {
    if (currentLocale.value === 'ar') {
        document.documentElement.dir = 'rtl';
    }
});
</script>

<template>
    <div class="dropdown">
        <button 
            class="btn btn-sm btn-light dropdown-toggle" 
            type="button" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
        >
            <i class="ti ti-language me-1"></i>
            {{ languages.find(l => l.code === currentLocale)?.flag }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li v-for="lang in languages" :key="lang.code">
                <a 
                    class="dropdown-item" 
                    :class="{ 'active': currentLocale === lang.code }"
                    href="#"
                    @click.prevent="switchLanguage(lang.code)"
                >
                    <span class="me-2">{{ lang.flag }}</span>
                    {{ lang.name }}
                </a>
            </li>
        </ul>
    </div>
</template>
