<template>
  <div class="theme-switcher">
    <!-- Theme Dropdown -->
    <div class="theme-dropdown">
      <button
        @click="isOpen = !isOpen"
        class="theme-button"
        :aria-label="$t('theme.switch')"
      >
        <div class="theme-preview" :style="{ backgroundColor: currentTheme?.primary }"></div>
        <span class="theme-name">{{ currentTheme?.name }}</span>
        <svg
          class="chevron"
          :class="{ 'rotate-180': isOpen }"
          width="16"
          height="16"
          viewBox="0 0 16 16"
          fill="none"
        >
          <path
            d="M4 6L8 10L12 6"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
      >
        <div v-if="isOpen" class="theme-menu">
          <div class="theme-section">
            <div class="section-label">{{ $t('theme.light_themes') }}</div>
            <button
              v-for="theme in lightThemes"
              :key="theme.id"
              @click="selectTheme(theme.id)"
              class="theme-option"
              :class="{ active: currentTheme?.id === theme.id }"
            >
              <div class="theme-color" :style="{ backgroundColor: theme.primary }"></div>
              <div class="theme-info">
                <div class="theme-option-name">{{ theme.name }}</div>
                <div class="theme-description">{{ theme.description }}</div>
              </div>
              <svg
                v-if="currentTheme?.id === theme.id"
                class="check-icon"
                width="16"
                height="16"
                viewBox="0 0 16 16"
                fill="none"
              >
                <path
                  d="M13.3334 4L6.00002 11.3333L2.66669 8"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </div>

          <div class="theme-section">
            <div class="section-label">{{ $t('theme.dark_themes') }}</div>
            <button
              v-for="theme in darkThemes"
              :key="theme.id"
              @click="selectTheme(theme.id)"
              class="theme-option"
              :class="{ active: currentTheme?.id === theme.id }"
            >
              <div class="theme-color" :style="{ backgroundColor: theme.primary }"></div>
              <div class="theme-info">
                <div class="theme-option-name">{{ theme.name }}</div>
                <div class="theme-description">{{ theme.description }}</div>
              </div>
              <svg
                v-if="currentTheme?.id === theme.id"
                class="check-icon"
                width="16"
                height="16"
                viewBox="0 0 16 16"
                fill="none"
              >
                <path
                  d="M13.3334 4L6.00002 11.3333L2.66669 8"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </div>

          <!-- RTL Toggle -->
          <div class="theme-section border-t">
            <button
              @click="handleRTLToggle"
              class="rtl-toggle"
            >
              <div class="rtl-info">
                <div class="rtl-label">{{ $t('theme.rtl_mode') }}</div>
                <div class="rtl-description">{{ $t('theme.rtl_description') }}</div>
              </div>
              <div class="toggle-switch" :class="{ active: isRTL }">
                <div class="toggle-slider"></div>
              </div>
            </button>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useTheme } from '../composables/useTheme';

const {
  currentTheme,
  isRTL,
  availableThemes,
  setTheme,
  toggleRTL,
  initializeTheme,
} = useTheme();

const isOpen = ref(false);

// Split themes by category
const lightThemes = computed(() => {
  return availableThemes.value.filter(theme => theme.category === 'light');
});

const darkThemes = computed(() => {
  return availableThemes.value.filter(theme => theme.category === 'dark');
});

// Select theme
const selectTheme = (themeId) => {
  setTheme(themeId);
  isOpen.value = false;
};

// Toggle RTL
const handleRTLToggle = () => {
  toggleRTL();
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.theme-switcher');
  if (!dropdown) {
    isOpen.value = false;
  }
};

onMounted(() => {
  initializeTheme();
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.theme-switcher {
  position: relative;
}

.theme-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  font-size: var(--text-sm);
  color: var(--color-text);
}

.theme-button:hover {
  background: var(--color-surface-hover);
  border-color: var(--color-border-strong);
}

.theme-preview {
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  box-shadow: var(--shadow-sm);
}

.theme-name {
  font-weight: var(--font-medium);
}

.chevron {
  transition: transform var(--transition-fast);
  color: var(--color-text-muted);
}

.theme-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  width: 320px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  z-index: 50;
  overflow: hidden;
}

.theme-section {
  padding: 0.75rem;
}

.theme-section.border-t {
  border-top: 1px solid var(--color-border);
}

.section-label {
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
  padding: 0 0.5rem;
}

.theme-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem;
  background: transparent;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-align: left;
}

.theme-option:hover {
  background: var(--color-surface-hover);
}

.theme-option.active {
  background: var(--color-primary-50);
}

.theme-color {
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  flex-shrink: 0;
}

.theme-info {
  flex: 1;
}

.theme-option-name {
  font-weight: var(--font-medium);
  color: var(--color-text);
  font-size: var(--text-sm);
}

.theme-description {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  margin-top: 0.125rem;
}

.check-icon {
  color: var(--color-primary-600);
  flex-shrink: 0;
}

.rtl-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 0.75rem;
  background: transparent;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-align: left;
}

.rtl-toggle:hover {
  background: var(--color-surface-hover);
}

.rtl-info {
  flex: 1;
}

.rtl-label {
  font-weight: var(--font-medium);
  color: var(--color-text);
  font-size: var(--text-sm);
}

.rtl-description {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  margin-top: 0.125rem;
}

.toggle-switch {
  width: 2.75rem;
  height: 1.5rem;
  background: var(--color-border-strong);
  border-radius: var(--radius-full);
  position: relative;
  transition: all var(--transition-base);
}

.toggle-switch.active {
  background: var(--color-primary-600);
}

.toggle-slider {
  position: absolute;
  top: 0.125rem;
  left: 0.125rem;
  width: 1.25rem;
  height: 1.25rem;
  background: white;
  border-radius: 50%;
  transition: transform var(--transition-base);
  box-shadow: var(--shadow-sm);
}

.toggle-switch.active .toggle-slider {
  transform: translateX(1.25rem);
}

/* RTL adjustments */
[dir="rtl"] .theme-menu {
  right: auto;
  left: 0;
}

[dir="rtl"] .toggle-switch.active .toggle-slider {
  transform: translateX(-1.25rem);
}
</style>
