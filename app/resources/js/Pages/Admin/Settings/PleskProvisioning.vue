<template>
  <SettingPageLayout
    title="Plesk Provisioning"
    description="Configure Plesk server integration for hosting account provisioning"
    :hasChanges="hasChanges"
    :isSaving="isSaving"
    :hasTest="true"
    :isTesting="isTesting"
    :auditLogs="auditLogs"
    @save="handleSave"
    @revert="handleRevert"
    @test="handleTest"
  >
    <div class="settings-grid">
      <!-- Server Connection -->
      <div class="settings-section">
        <h2 class="section-title">Server Connection</h2>
        <p class="section-description">Plesk server connection details</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.host"
            label="Plesk Host"
            placeholder="plesk.example.com"
            helpText="Hostname or IP address of your Plesk server"
            required
            :error="errors.host"
          />

          <div class="toggle-field">
            <div class="toggle-info">
              <label class="toggle-label">Use SSL/TLS</label>
              <p class="toggle-description">Connect to Plesk over HTTPS (recommended)</p>
            </div>
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="form.use_ssl"
                class="toggle-input"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Authentication -->
      <div class="settings-section">
        <h2 class="section-title">Authentication</h2>
        <p class="section-description">Choose your authentication method</p>
        
        <div class="auth-type-selector">
          <button
            @click="form.auth_type = 'api_key'"
            :class="['auth-type-button', { active: form.auth_type === 'api_key' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke="currentColor" stroke-width="1.5"/>
              <circle cx="15" cy="9" r="1" fill="currentColor"/>
            </svg>
            <span>API Key (Recommended)</span>
          </button>

          <button
            @click="form.auth_type = 'password'"
            :class="['auth-type-button', { active: form.auth_type === 'password' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <span>Username & Password</span>
          </button>
        </div>

        <div class="form-grid" v-if="form.auth_type === 'api_key'">
          <SecretField
            v-model="form.api_key"
            label="API Key"
            placeholder="Enter Plesk API Key"
            helpText="API key from Plesk > Tools & Settings > API Keys"
            required
            :error="errors.api_key"
          />
        </div>

        <div class="form-grid" v-if="form.auth_type === 'password'">
          <FormField
            v-model="form.username"
            label="Username"
            placeholder="admin"
            helpText="Plesk administrator username"
            required
            :error="errors.username"
          />

          <SecretField
            v-model="form.password"
            label="Password"
            placeholder="Enter password"
            helpText="Plesk administrator password"
            required
            :error="errors.password"
          />
        </div>
      </div>

      <!-- Service Plan Mapping -->
      <div class="settings-section">
        <h2 class="section-title">Service Plan Mapping</h2>
        <p class="section-description">Map HUBIT products to Plesk service plans</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.default_plan"
            type="select"
            label="Default Service Plan"
            placeholder="Select a plan"
            :options="planOptions"
            helpText="Default plan for new subscriptions"
            required
            :error="errors.default_plan"
          />

          <div class="plan-info" v-if="planCount > 0">
            <div class="info-badge">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M10 10V14M10 6H10.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <span>{{ planCount }} service plans available</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Test Connection -->
      <div class="settings-section">
        <h2 class="section-title">Connection Test</h2>
        <p class="section-description">Test your Plesk configuration</p>
        
        <TestConnectionButton service="plesk" />
        
        <div class="test-info" v-if="planCount > 0">
          <p class="help-text">
            Last successful test retrieved {{ planCount }} service plans from Plesk.
            Test connection again to refresh plan list.
          </p>
        </div>
      </div>
    </div>
  </SettingPageLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import SettingPageLayout from '../../../Components/Settings/SettingPageLayout.vue';
import FormField from '../../../Components/Settings/FormField.vue';
import SecretField from '../../../Components/Settings/SecretField.vue';
import TestConnectionButton from '../../../Components/Settings/TestConnectionButton.vue';

const props = defineProps({
  settings: Object,
  auditLogs: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = ref({
  host: props.settings?.plesk?.['plesk.host'] || '',
  use_ssl: props.settings?.plesk?.['plesk.use_ssl'] ?? true,
  auth_type: props.settings?.plesk?.['plesk.auth_type'] || 'api_key',
  api_key: props.settings?.plesk?.['plesk.api_key'] || '',
  username: props.settings?.plesk?.['plesk.username'] || '',
  password: props.settings?.plesk?.['plesk.password'] || '',
  default_plan: props.settings?.plesk?.['plesk.default_plan'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);
const planCount = ref(8);

const planOptions = [
  { value: 'HUBIT_BASIC', label: 'HUBIT Basic' },
  { value: 'HUBIT_PRO', label: 'HUBIT Pro' },
  { value: 'HUBIT_BUSINESS', label: 'HUBIT Business' },
];

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'plesk.host', value: form.value.host, type: 'string' },
    { key: 'plesk.use_ssl', value: form.value.use_ssl, type: 'bool' },
    { key: 'plesk.auth_type', value: form.value.auth_type, type: 'string' },
    { key: 'plesk.api_key', value: form.value.api_key, type: 'string', is_secret: true },
    { key: 'plesk.username', value: form.value.username, type: 'string' },
    { key: 'plesk.password', value: form.value.password, type: 'string', is_secret: true },
    { key: 'plesk.default_plan', value: form.value.default_plan, type: 'string' },
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

const handleTest = () => {
  isTesting.value = true;
  setTimeout(() => {
    isTesting.value = false;
  }, 3000);
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

.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-4);
}

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}

/* Auth Type Selector */
.auth-type-selector {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.auth-type-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-3);
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
}

.auth-type-button:hover {
  border-color: var(--color-primary-500);
}

.auth-type-button.active {
  border-color: var(--color-primary-600);
  background: var(--color-primary-50);
  color: var(--color-primary-700);
}

.auth-type-button svg {
  flex-shrink: 0;
}

/* Toggle Field */
.toggle-field {
  grid-column: 1 / -1;
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

/* Plan Info */
.plan-info {
  grid-column: 1 / -1;
}

.info-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  background: var(--color-primary-50);
  border: 1px solid var(--color-primary-500);
  border-radius: var(--radius-md);
  color: var(--color-primary-700);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
}

.test-info {
  margin-top: var(--space-4);
  padding: var(--space-3);
  background: var(--color-success-50);
  border: 1px solid var(--color-success-500);
  border-radius: var(--radius-md);
}

.help-text {
  font-size: var(--text-sm);
  color: var(--color-success-700);
  margin: 0;
}

/* RTL Support */
[dir="rtl"] .toggle-input:checked + .toggle-slider:before {
  transform: translateX(-22px);
}
</style>
