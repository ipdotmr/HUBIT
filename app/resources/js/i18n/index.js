import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';
import fr from './locales/fr.json';

function getBrowserLocale() {
    const browserLang = navigator.language || navigator.userLanguage;
    const lang = browserLang.split('-')[0]; // Get just the language code (e.g., 'en' from 'en-US')
    
    if (['en', 'ar', 'fr'].includes(lang)) {
        return lang;
    }
    
    return 'en';
}

const savedLocale = localStorage.getItem('locale');
const defaultLocale = savedLocale || getBrowserLocale();

const i18n = createI18n({
    legacy: false, // Use Composition API mode
    locale: defaultLocale,
    fallbackLocale: 'en',
    messages: {
        en,
        ar,
        fr
    }
});

export default i18n;
