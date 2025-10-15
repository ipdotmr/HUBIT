/**
 * Theme Management Composable
 * Handles theme switching, persistence, and RTL
 */

import { ref, computed, watch, onMounted } from 'vue';
import { themes, getThemeById, getDefaultTheme } from '../config/themes';

const currentTheme = ref(null);
const isRTL = ref(false);

export function useTheme() {
  /**
   * Initialize theme from localStorage or default
   */
  const initializeTheme = () => {
    const savedThemeId = localStorage.getItem('hubit_theme');
    const savedRTL = localStorage.getItem('hubit_rtl') === 'true';
    
    currentTheme.value = savedThemeId ? getThemeById(savedThemeId) : getDefaultTheme();
    isRTL.value = savedRTL;
    
    applyTheme();
  };

  /**
   * Apply theme to document
   */
  const applyTheme = () => {
    const root = document.documentElement;
    
    root.setAttribute('data-theme', currentTheme.value.id);
    
    root.setAttribute('dir', isRTL.value ? 'rtl' : 'ltr');
    
    loadThemeCSS(currentTheme.value.id);
  };

  /**
   * Load theme CSS file
   */
  const loadThemeCSS = (themeId) => {
    const existingLinks = document.querySelectorAll('link[data-theme-css]');
    existingLinks.forEach(link => link.remove());
    
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `/themes/${themeId}.css`;
    link.setAttribute('data-theme-css', 'true');
    document.head.appendChild(link);
  };

  /**
   * Set theme by ID
   */
  const setTheme = (themeId) => {
    const theme = getThemeById(themeId);
    if (theme) {
      currentTheme.value = theme;
      localStorage.setItem('hubit_theme', themeId);
      applyTheme();
    }
  };

  /**
   * Toggle RTL mode
   */
  const toggleRTL = () => {
    isRTL.value = !isRTL.value;
    localStorage.setItem('hubit_rtl', isRTL.value);
    applyTheme();
  };

  /**
   * Set RTL mode
   */
  const setRTL = (enabled) => {
    isRTL.value = enabled;
    localStorage.setItem('hubit_rtl', enabled);
    applyTheme();
  };

  /**
   * Get all available themes
   */
  const availableThemes = computed(() => themes);

  /**
   * Check if current theme is dark
   */
  const isDark = computed(() => {
    return currentTheme.value?.category === 'dark';
  });

  onMounted(() => {
    if (!currentTheme.value) {
      initializeTheme();
    }
  });

  return {
    currentTheme,
    isRTL,
    isDark,
    availableThemes,
    setTheme,
    toggleRTL,
    setRTL,
    initializeTheme,
  };
}
