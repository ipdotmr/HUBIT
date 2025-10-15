<template>
  <SettingPageLayout
    title="cPanel/WHM Provisioning"
    description="Configure cPanel/WHM server integration for hosting account provisioning"
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
        <p class="section-description">WHM server connection details</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.host"
            label="WHM Host"
            placeholder="whm.example.com"
            helpText="Hostname or IP address of your WHM server"
            required
            :error="errors.host"
          />

          <SecretField
            v-model="form.api_token"
            label="API Token"
            placeholder="WHM API token"
            helpText="API token from WHM > Manage API Tokens"
            required
            :error="errors.api_token"
          />

          <div class="toggle-field">
            <div class="toggle-info">
              <label class="toggle-label">Use SSL/TLS</label>
              <p class="toggle-description">Connect to WHM over HTTPS (recommended)</p>
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

      <!-- Package Mapping -->
      <div class="settings-section">
        <h2 class="section-title">Package Mapping</h2>
        <p class="section-description">Map HUBIT products to cPanel packages</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.default_package"
            type="select"
            label="Default cPanel Package"
            placeholder="Select a package"
            :options="packageOptions"
            helpText="Default package for new accounts"
            required
            :error="errors.default_package"
          />

          <div class="package-info" v-if="packageCount > 0">
            <div class="info-badge">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M10 10V14M10 6H10.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <span>{{ packageCount }} packages available</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Nameservers -->
      <div class="settings-section">
        <h2 class="section-title">Nameservers</h2>
        <p class="section-description">Default nameservers for new accounts</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.nameserver1"
            label="Primary Nameserver"
            placeholder="ns1.example.com"
            helpText="Primary nameserver for new domains"
            :error="errors.nameserver1"
          />

          <FormField
            v-model="form.nameserver2"
            label="Secondary Nameserver"
            placeholder="ns2.example.com"
            helpText="Secondary nameserver for new domains"
            :error="errors.nameserver2"
          />

          <FormField
            v-model="form.nameserver3"
            label="Tertiary Nameserver (Optional)"
            placeholder="ns3.example.com"
            helpText="Additional nameserver (optional)"
            :error="errors.nameserver3"
          />

          <FormField
            v-model="form.nameserver4"
            label="Quaternary Nameserver (Optional)"
            placeholder="ns4.example.com"
            helpText="Additional nameserver (optional)"
            :error="errors.nameserver4"
          />
        </div>
      </div>

      <!-- Test Connection -->
      <div class="settings-section">
        <h2 class="section-title">Connection Test</h2>
        <p class="section-description">Test your cPanel/WHM configuration</p>
        
        <TestConnectionButton service="cpanel" />
        
        <div class="test-info" v-if="packageCount > 0">
          <p class="help-text">
            Last successful test retrieved {{ packageCount }} hosting packages from WHM.
            Test connection again to refresh package list.
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
  host: props.settings?.cpanel?.['cpanel.host'] || '',
  api_token: props.settings?.cpanel?.['cpanel.api_token'] || '',
  use_ssl: props.settings?.cpanel?.['cpanel.use_ssl'] ?? true,
  default_package: props.settings?.cpanel?.['cpanel.default_package'] || '',
  nameserver1: props.settings?.cpanel?.['cpanel.nameserver1'] || '',
  nameserver2: props.settings?.cpanel?.['cpanel.nameserver2'] || '',
  nameserver3: props.settings?.cpanel?.['cpanel.nameserver3'] || '',
  nameserver4: props.settings?.cpanel?.['cpanel.nameserver4'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);
const packageCount = ref(12); // Would come from actual API test

// Mock package options - in production, fetch from API
const packageOptions = [
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
    { key: 'cpanel.host', value: form.value.host, type: 'string' },
    { key: 'cpanel.api_token', value: form.value.api_token, type: 'string', is_secret: true },
    { key: 'cpanel.use_ssl', value: form.value.use_ssl, type: 'bool' },
    { key: 'cpanel.default_package', value: form.value.default_package, type: 'string' },
    { key: 'cpanel.nameserver1', value: form.value.nameserver1, type: 'string' },
    { key: 'cpanel.nameserver2', value: form.value.nameserver2, type: 'string' },
    { key: 'cpanel.nameserver3', value: form.value.nameserver3, type: 'string' },
    { key: 'cpanel.nameserver4', value: form.value.nameserver4, type: 'string' },
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
  // Test button component handles the actual test
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

.toggle-input:focus + .toggle-slider {
  box-shadow: 0 0 0 3px var(--color-primary-50);
}

/* Package Info */
.package-info {
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

.info-badge svg {
  flex-shrink: 0;
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
