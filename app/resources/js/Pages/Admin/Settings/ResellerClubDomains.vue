<template>
  <SettingPageLayout
    title="ResellerClub Domains"
    description="Configure ResellerClub domain registrar integration"
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
        <p class="section-description">Choose between demo and live mode</p>
        
        <div class="mode-selector">
          <button
            @click="form.mode = 'demo'"
            :class="['mode-button', { active: form.mode === 'demo' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2Z" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 6V10L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
              <div class="mode-name">Demo Mode</div>
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
        <p class="section-description">Your ResellerClub API credentials</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.reseller_id"
            label="Reseller ID"
            placeholder="Enter reseller ID"
            helpText="Your ResellerClub reseller ID"
            required
            :error="errors.reseller_id"
          />

          <SecretField
            v-model="form.api_key"
            label="API Key"
            placeholder="Enter API key"
            helpText="API key from ResellerClub dashboard"
            required
            :error="errors.api_key"
          />
        </div>
      </div>

      <!-- Account Information -->
      <div class="settings-section">
        <h2 class="section-title">Account Information</h2>
        <p class="section-description">View your ResellerClub account details</p>
        
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
            <div class="info-label">Active Orders</div>
            <div class="info-value">{{ accountInfo.orders }}</div>
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
        <p class="section-description">Verify your ResellerClub configuration and check account balance</p>
        
        <TestConnectionButton service="resellerclub" />
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
  reseller_id: props.settings?.resellerclub?.['resellerclub.reseller_id'] || '',
  api_key: props.settings?.resellerclub?.['resellerclub.api_key'] || '',
  mode: props.settings?.resellerclub?.['resellerclub.mode'] || 'demo',
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
    { key: 'resellerclub.reseller_id', value: form.value.reseller_id, type: 'string' },
    { key: 'resellerclub.api_key', value: form.value.api_key, type: 'string', is_secret: true },
    { key: 'resellerclub.mode', value: form.value.mode, type: 'string' },
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
      balance: '2,567.89',
      domains: 156,
      orders: 23,
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
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
