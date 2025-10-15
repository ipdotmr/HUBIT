<template>
  <SettingPageLayout
    title="Stripe Payments"
    description="Configure Stripe payment gateway integration"
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
      <!-- Mode Selection -->
      <div class="settings-section">
        <h2 class="section-title">Mode</h2>
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
              <div class="mode-desc">Use test API keys</div>
            </div>
          </button>

          <button
            @click="form.mode = 'live'"
            :class="['mode-button', { active: form.mode === 'live' }]"
            type="button"
          >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M16.88 9.1A4 4 0 0118 12v1a4 4 0 01-8 0v-1c0-1.105.447-2.104 1.17-2.828M6.17 6.172A4 4 0 0110 4h0a4 4 0 014 4v1a4 4 0 11-8 0V8c0-.69.174-1.34.48-1.91z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <div>
              <div class="mode-name">Live Mode</div>
              <div class="mode-desc">Use live API keys</div>
            </div>
          </button>
        </div>
      </div>

      <!-- API Keys Section -->
      <div class="settings-section">
        <h2 class="section-title">API Keys</h2>
        <p class="section-description">Your Stripe API credentials</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.publishable_key"
            label="Publishable Key"
            placeholder="pk_test_... or pk_live_..."
            helpText="Public key for client-side Stripe.js"
            required
            :error="errors.publishable_key"
          />

          <SecretField
            v-model="form.secret_key"
            label="Secret Key"
            placeholder="sk_test_... or sk_live_..."
            helpText="Private key for server-side API calls"
            required
            :error="errors.secret_key"
          />
        </div>
      </div>

      <!-- Webhook Configuration -->
      <div class="settings-section">
        <h2 class="section-title">Webhooks</h2>
        <p class="section-description">Configure Stripe webhook endpoints</p>
        
        <div class="webhook-info">
          <div class="info-item">
            <label class="info-label">Webhook URL</label>
            <div class="webhook-url">
              <code>{{ webhookUrl }}</code>
              <button @click="copyWebhookUrl" class="copy-button" type="button">
                <svg v-if="urlCopied" width="16" height="16" viewBox="0 0 16 16">
                  <path d="M13.657 4.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L7 9.636l5.243-5.343a1 1 0 011.414 0z" fill="currentColor"/>
                </svg>
                <svg v-else width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M6 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H6z" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M3 5H2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2v-1" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </div>

          <SecretField
            v-model="form.webhook_secret"
            label="Webhook Signing Secret"
            placeholder="whsec_..."
            helpText="Used to verify webhook signatures"
            :error="errors.webhook_secret"
          />
        </div>
      </div>

      <!-- Test Connection -->
      <div class="settings-section">
        <h2 class="section-title">Connection Test</h2>
        <p class="section-description">Verify your Stripe configuration</p>
        
        <TestConnectionButton service="stripe" />
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
  mode: props.settings?.stripe?.['stripe.mode'] || 'test',
  publishable_key: props.settings?.stripe?.['stripe.publishable_key'] || '',
  secret_key: props.settings?.stripe?.['stripe.secret_key'] || '',
  webhook_secret: props.settings?.stripe?.['stripe.webhook_secret'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);
const urlCopied = ref(false);

const webhookUrl = computed(() => {
  return `${window.location.origin}/webhooks/stripe`;
});

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'stripe.mode', value: form.value.mode, type: 'string' },
    { key: 'stripe.publishable_key', value: form.value.publishable_key, type: 'string' },
    { key: 'stripe.secret_key', value: form.value.secret_key, type: 'string', is_secret: true },
    { key: 'stripe.webhook_secret', value: form.value.webhook_secret, type: 'string', is_secret: true },
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

const copyWebhookUrl = async () => {
  try {
    await navigator.clipboard.writeText(webhookUrl.value);
    urlCopied.value = true;
    setTimeout(() => {
      urlCopied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
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

/* Webhook Info */
.webhook-info {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.info-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--color-text);
}

.webhook-url {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
}

.webhook-url code {
  flex: 1;
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  color: var(--color-text);
}

.copy-button {
  padding: var(--space-1);
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  border-radius: var(--radius-sm);
  transition: all var(--transition-fast);
  display: flex;
  align-items: center;
  justify-content: center;
}

.copy-button:hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}
</style>
