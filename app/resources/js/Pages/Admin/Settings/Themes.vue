<template>
  <WhmcsAdminLayout>
    <SettingPageLayout
    title="Themes & UI"
    description="Customize the visual appearance and user interface"
    :hasChanges="hasChanges"
    :isSaving="isSaving"
    :auditLogs="auditLogs"
    @save="handleSave"
    @revert="handleRevert"
  >
    <div class="settings-grid">
      <!-- Theme Selection Section -->
      <div class="settings-section">
        <h2 class="section-title">Theme Selection</h2>
        <p class="section-description">Choose a theme for your HUBIT installation</p>
        
        <div class="themes-grid">
          <div
            v-for="theme in availableThemes"
            :key="theme.id"
            @click="selectTheme(theme.id)"
            :class="['theme-card', { active: form.default_theme === theme.id }]"
            role="button"
            tabindex="0"
            :aria-label="`Select ${theme.name} theme`"
          >
            <div class="theme-preview" :style="{ backgroundColor: theme.primary }">
              <div class="preview-content">
                <div class="preview-header" :style="{ backgroundColor: theme.primary }"></div>
                <div class="preview-body">
                  <div class="preview-box"></div>
                  <div class="preview-box"></div>
                </div>
              </div>
            </div>
            <div class="theme-info">
              <div class="theme-name">{{ theme.name }}</div>
              <div class="theme-description">{{ theme.description }}</div>
            </div>
            <div v-if="form.default_theme === theme.id" class="theme-check">
              <svg width="20" height="20" viewBox="0 0 20 20">
                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- UI Preferences Section -->
      <div class="settings-section">
        <h2 class="section-title">UI Preferences</h2>
        <p class="section-description">Customize user interface behavior</p>
        
        <div class="form-grid">
          <div class="toggle-field">
            <div class="toggle-info">
              <label class="toggle-label">Allow User Theme Selection</label>
              <p class="toggle-description">Let users choose their own theme preference</p>
            </div>
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="form.allow_user_selection"
                class="toggle-input"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-field">
            <div class="toggle-info">
              <label class="toggle-label">Enable RTL Support</label>
              <p class="toggle-description">Support right-to-left languages (Arabic, Hebrew)</p>
            </div>
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="form.rtl_enabled"
                class="toggle-input"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Custom CSS Section -->
      <div class="settings-section">
        <h2 class="section-title">Custom CSS</h2>
        <p class="section-description">Add custom CSS to override theme styles</p>
        
        <FormField
          v-model="form.custom_css"
          type="textarea"
          label="Custom CSS Code"
          placeholder="/* Add your custom CSS here */"
          :rows="10"
          helpText="Advanced: CSS will be injected after theme styles"
          :error="errors.custom_css"
        />
      </div>
    </div>
    </SettingPageLayout>
  </WhmcsAdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import SettingPageLayout from '../../../Components/Settings/SettingPageLayout.vue';
import FormField from '../../../Components/Settings/FormField.vue';
import { themes } from '../../../config/themes';

const props = defineProps({
  settings: Object,
  auditLogs: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = ref({
  default_theme: props.settings?.theme?.['theme.default'] || 'ipmr',
  allow_user_selection: props.settings?.theme?.['theme.allow_user_selection'] ?? true,
  rtl_enabled: props.settings?.theme?.['theme.rtl_enabled'] ?? true,
  custom_css: props.settings?.theme?.['theme.custom_css'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);

const availableThemes = ref(themes);

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const selectTheme = (themeId) => {
  form.value.default_theme = themeId;
};

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'theme.default', value: form.value.default_theme, type: 'string' },
    { key: 'theme.allow_user_selection', value: form.value.allow_user_selection, type: 'bool' },
    { key: 'theme.rtl_enabled', value: form.value.rtl_enabled, type: 'bool' },
    { key: 'theme.custom_css', value: form.value.custom_css, type: 'string' },
  ];
  
  router.post('/managit/settings', { settings }, {
    onSuccess: () => {
      originalForm.value = JSON.parse(JSON.stringify(form.value));
      isSaving.value = false;
    },
    onError: () => {
      isSaving.value = false;
    }
  });
};

const handleRevert = () => {
  form.value = JSON.parse(JSON.stringify(originalForm.value));
};
</script>

<style scoped>
.settings-grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-8);
}

.settings-section {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
}

.section-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin: 0 0 var(--space-2);
}

.section-description {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin: 0 0 var(--space-6);
}

/* Themes Grid */
.themes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: var(--space-4);
}

.theme-card {
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-4);
  cursor: pointer;
  transition: all var(--transition-fast);
  position: relative;
}

.theme-card:hover {
  border-color: var(--color-primary-500);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.theme-card.active {
  border-color: var(--color-primary-600);
  background: var(--color-primary-50);
}

.theme-preview {
  width: 100%;
  height: 120px;
  border-radius: var(--radius-md);
  overflow: hidden;
  margin-bottom: var(--space-3);
  position: relative;
}

.preview-content {
  padding: var(--space-2);
}

.preview-header {
  height: 20px;
  border-radius: var(--radius-sm);
  margin-bottom: var(--space-2);
  opacity: 0.9;
}

.preview-body {
  display: flex;
  gap: var(--space-2);
}

.preview-box {
  flex: 1;
  height: 60px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: var(--radius-sm);
}

.theme-info {
  text-align: center;
}

.theme-name {
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin-bottom: var(--space-1);
}

.theme-description {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
}

.theme-check {
  position: absolute;
  top: var(--space-2);
  right: var(--space-2);
  width: 28px;
  height: 28px;
  background: var(--color-primary-600);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Toggle Fields */
.form-grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.toggle-field {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-4);
  background: var(--color-background);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.toggle-info {
  flex: 1;
}

.toggle-label {
  font-weight: var(--font-medium);
  color: var(--color-text);
  display: block;
  margin-bottom: var(--space-1);
}

.toggle-description {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin: 0;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 26px;
  flex-shrink: 0;
}

.toggle-input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--color-border-strong);
  transition: var(--transition-base);
  border-radius: var(--radius-full);
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background: white;
  transition: var(--transition-base);
  border-radius: 50%;
}

.toggle-input:checked + .toggle-slider {
  background: var(--color-primary-600);
}

.toggle-input:checked + .toggle-slider:before {
  transform: translateX(22px);
}

.toggle-input:focus + .toggle-slider {
  box-shadow: 0 0 0 3px var(--color-primary-50);
}

/* RTL Support */
[dir="rtl"] .theme-check {
  right: auto;
  left: var(--space-2);
}

[dir="rtl"] .toggle-input:checked + .toggle-slider:before {
  transform: translateX(-22px);
}
</style>
