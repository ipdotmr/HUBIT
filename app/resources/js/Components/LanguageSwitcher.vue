<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const currentLocale = ref(locale.value || document.documentElement.lang || 'en');

const languages = [
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' },
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
];

const switchLanguage = (newLocale) => {
    // Update vue-i18n locale
    locale.value = newLocale;
    
    // Save to localStorage
    localStorage.setItem('locale', newLocale);
    
    // Update HTML attributes
    currentLocale.value = newLocale;
    document.documentElement.lang = newLocale;
    document.documentElement.dir = newLocale === 'ar' ? 'rtl' : 'ltr';
    
    // Also update backend session
    router.post('/language', { locale: newLocale }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            window.location.reload();
        },
    });
};

onMounted(() => {
    // Set initial direction
    if (currentLocale.value === 'ar') {
        document.documentElement.dir = 'rtl';
    }
    
    // Sync with vue-i18n
    locale.value = currentLocale.value;
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
