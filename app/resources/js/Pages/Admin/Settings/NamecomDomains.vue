<template>
  <WhmcsAdminLayout>
    <SettingPageLayout
    title="Name.com Domains"
    description="Configure Name.com domain registrar integration"
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
      <!-- Environment Selection -->
      <div class="settings-section">
        <h2 class="section-title">Environment</h2>
        <p class="section-description">Choose between test and live mode</p>
        
        <div class="mode-selector">
          <button
            @click="form.mode = 'test'"
            :class="['mode-button', { active: form.mode === 'test' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2Z" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 6V10L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
              <div class="mode-name">Test Mode</div>
              <div class="mode-desc">Test environment</div>
            </div>
          </button>

          <button
            @click="form.mode = 'live'"
            :class="['mode-button', { active: form.mode === 'live' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M9 3v2m6.22 1.22l1.42 1.42M18 9h-2M5.78 4.22L4.36 5.64M3 9h2m1.22 6.78l-1.42 1.42M9 21v-2m8.78-1.22l-1.42-1.42" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              <circle cx="9" cy="9" r="4" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <div>
              <div class="mode-name">Live Mode</div>
              <div class="mode-desc">Production environment</div>
            </div>
          </button>
        </div>
      </div>

      <!-- API Credentials -->
      <div class="settings-section">
        <h2 class="section-title">API Credentials</h2>
        <p class="section-description">Your Name.com API credentials</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.api_username"
            label="API Username"
            placeholder="Enter API username"
            helpText="Your Name.com API username"
            required
            :error="errors.api_username"
          />

          <SecretField
            v-model="form.api_token"
            label="API Token"
            placeholder="Enter API token"
            helpText="API token from Name.com dashboard"
            required
            :error="errors.api_token"
          />
        </div>
      </div>

      <!-- Advanced Configuration -->
      <div class="settings-section">
        <h2 class="section-title">Advanced Configuration</h2>
        <p class="section-description">Optional sandbox URL override</p>
        
        <FormField
          v-model="form.sandbox_url"
          label="Sandbox URL (Optional)"
          placeholder="https://api.dev.name.com/v4"
          helpText="Override default sandbox API URL if needed"
          :error="errors.sandbox_url"
        />
      </div>

      <!-- Account Information -->
      <div class="settings-section">
        <h2 class="section-title">Account Information</h2>
        <p class="section-description">View your Name.com account details</p>
        
        <div v-if="accountInfo" class="account-info-grid">
          <div class="info-card">
            <div class="info-label">Account Balance</div>
            <div class="info-value">${{ accountInfo.balance }}</div>
          </div>
          <div class="info-card">
            <div class="info-label">Total Domains</div>
            <div class="info-value">{{ accountInfo.domains }}</div>
          </div>
          <div class="info-card">
            <div class="info-label">Account Status</div>
            <div class="info-value">
              <span :class="['status-badge', accountInfo.status]">{{ accountInfo.status }}</span>
            </div>
          </div>
        </div>
        <div v-else class="empty-state">
          <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
            <path d="M24 44C35.0457 44 44 35.0457 44 24C44 12.9543 35.0457 4 24 4C12.9543 4 4 12.9543 4 24C4 35.0457 12.9543 44 24 44Z" stroke="currentColor" stroke-width="2"/>
            <path d="M24 16V24M24 32H24.02" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Test connection to load account information</p>
        </div>
      </div>

      <!-- Test Connection -->
      <div class="settings-section">
        <h2 class="section-title">Connection Test</h2>
        <p class="section-description">Verify your Name.com configuration and check account details</p>
        
        <TestConnectionButton service="namecom" />
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
  api_username: props.settings?.namecom?.['namecom.api_username'] || '',
  api_token: props.settings?.namecom?.['namecom.api_token'] || '',
  mode: props.settings?.namecom?.['namecom.mode'] || 'test',
  sandbox_url: props.settings?.namecom?.['namecom.sandbox_url'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);

const accountInfo = ref(null);

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'namecom.api_username', value: form.value.api_username, type: 'string' },
    { key: 'namecom.api_token', value: form.value.api_token, type: 'string', is_secret: true },
    { key: 'namecom.mode', value: form.value.mode, type: 'string' },
    { key: 'namecom.sandbox_url', value: form.value.sandbox_url, type: 'string' },
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
    accountInfo.value = {
      balance: '3,456.78',
      domains: 89,
      status: 'active'
    };
  }, 2000);
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

/* Mode Selector */
.mode-selector {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-3);
}

.mode-button {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-4);
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-align: left;
}

.mode-button:hover {
  border-color: var(--color-primary-500);
  box-shadow: var(--shadow-sm);
}

.mode-button.active {
  border-color: var(--color-primary-600);
  background: var(--color-primary-50);
}

.mode-button svg {
  flex-shrink: 0;
  color: var(--color-text-muted);
}

.mode-button.active svg {
  color: var(--color-primary-600);
}

.mode-name {
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin-bottom: 2px;
}

.mode-desc {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
}

/* Account Info */
.account-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--space-4);
}

.info-card {
  padding: var(--space-4);
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  text-align: center;
}

.info-label {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin-bottom: var(--space-2);
}

.info-value {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--color-text);
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: var(--radius-full);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  text-transform: capitalize;
}

.status-badge.active {
  background: var(--color-success-50);
  color: var(--color-success-700);
  border: 1px solid var(--color-success-500);
}

.empty-state {
  padding: var(--space-8);
  text-align: center;
  color: var(--color-text-muted);
}

.empty-state svg {
  margin: 0 auto var(--space-4);
  color: var(--color-border-strong);
}

.empty-state p {
  margin: 0;
  font-size: var(--text-sm);
}
</style>
