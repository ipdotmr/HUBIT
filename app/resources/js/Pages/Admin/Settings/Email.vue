<template>
  <SettingPageLayout
    title="Email & SMTP"
    description="Configure email server and delivery settings"
    :hasChanges="hasChanges"
    :isSaving="isSaving"
    :hasTest="true"
    :isTesting="isTesting"
    :auditLogs="auditLogs"
    @save="handleSave"
    @revert="handleRevert"
    @test="handleTestEmail"
  >
    <div class="settings-grid">
      <!-- Server Configuration -->
      <div class="settings-section">
        <h2 class="section-title">SMTP Server</h2>
        <p class="section-description">Mail server connection settings</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.mailer"
            type="select"
            label="Mail Driver"
            :options="mailerOptions"
            helpText="Choose your mail delivery method"
            required
            :error="errors.mailer"
          />

          <FormField
            v-model="form.host"
            label="SMTP Host"
            placeholder="smtp.example.com"
            helpText="SMTP server hostname"
            required
            :error="errors.host"
          />

          <FormField
            v-model.number="form.port"
            type="number"
            label="SMTP Port"
            placeholder="587"
            helpText="Usually 587 (TLS) or 465 (SSL)"
            required
            :error="errors.port"
          />

          <FormField
            v-model="form.username"
            label="Username"
            placeholder="user@example.com"
            helpText="SMTP authentication username"
            :error="errors.username"
          />

          <SecretField
            v-model="form.password"
            label="Password"
            placeholder="SMTP password"
            helpText="SMTP authentication password"
            :error="errors.password"
          />

          <FormField
            v-model="form.encryption"
            type="select"
            label="Encryption"
            :options="encryptionOptions"
            helpText="TLS recommended for port 587"
            required
            :error="errors.encryption"
          />
        </div>
      </div>

      <!-- Sender Information -->
      <div class="settings-section">
        <h2 class="section-title">Sender Information</h2>
        <p class="section-description">Default sender details for outgoing emails</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.from_address"
            type="email"
            label="From Email Address"
            placeholder="noreply@example.com"
            helpText="Default sender email address"
            required
            :error="errors.from_address"
          />

          <FormField
            v-model="form.from_name"
            label="From Name"
            placeholder="HUBIT Notifications"
            helpText="Default sender name"
            required
            :error="errors.from_name"
          />
        </div>
      </div>

      <!-- Test Email -->
      <div class="settings-section">
        <h2 class="section-title">Test Email Configuration</h2>
        <p class="section-description">Send a test email to verify your configuration</p>
        
        <div class="test-email-section">
          <FormField
            v-model="testEmail"
            type="email"
            label="Test Email Address"
            placeholder="your-email@example.com"
            helpText="Enter your email address to receive a test message"
          />

          <button
            @click="handleTestEmail"
            :disabled="!testEmail || isTesting"
            class="test-email-button"
            type="button"
          >
            <span v-if="isTesting" class="spinner"></span>
            <svg v-else width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ isTesting ? 'Sending Test Email...' : 'Send Test Email' }}</span>
          </button>

          <div v-if="testResult" :class="['test-result', testResult.success ? 'success' : 'error']">
            <svg v-if="testResult.success" width="20" height="20" viewBox="0 0 20 20">
              <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
            </svg>
            <svg v-else width="20" height="20" viewBox="0 0 20 20">
              <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
            </svg>
            <div>
              <div class="result-message">{{ testResult.message }}</div>
              <div v-if="testResult.latency_ms" class="result-latency">{{ testResult.latency_ms }}ms</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SettingPageLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import SettingPageLayout from '../../../Components/Settings/SettingPageLayout.vue';
import FormField from '../../../Components/Settings/FormField.vue';
import SecretField from '../../../Components/Settings/SecretField.vue';

const props = defineProps({
  settings: Object,
  auditLogs: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = ref({
  mailer: props.settings?.email?.['mail.mailer'] || 'smtp',
  host: props.settings?.email?.['mail.host'] || '',
  port: props.settings?.email?.['mail.port'] || 587,
  username: props.settings?.email?.['mail.username'] || '',
  password: props.settings?.email?.['mail.password'] || '',
  encryption: props.settings?.email?.['mail.encryption'] || 'tls',
  from_address: props.settings?.email?.['mail.from_address'] || '',
  from_name: props.settings?.email?.['mail.from_name'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);
const testEmail = ref('');
const testResult = ref(null);

const mailerOptions = [
  { value: 'smtp', label: 'SMTP' },
  { value: 'sendmail', label: 'Sendmail' },
  { value: 'ses', label: 'Amazon SES' },
  { value: 'mailgun', label: 'Mailgun' },
];

const encryptionOptions = [
  { value: 'tls', label: 'TLS (Recommended)' },
  { value: 'ssl', label: 'SSL' },
  { value: null, label: 'None (Not Recommended)' },
];

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'mail.mailer', value: form.value.mailer, type: 'string' },
    { key: 'mail.host', value: form.value.host, type: 'string' },
    { key: 'mail.port', value: form.value.port, type: 'int' },
    { key: 'mail.username', value: form.value.username, type: 'string' },
    { key: 'mail.password', value: form.value.password, type: 'string', is_secret: true },
    { key: 'mail.encryption', value: form.value.encryption, type: 'string' },
    { key: 'mail.from_address', value: form.value.from_address, type: 'string' },
    { key: 'mail.from_name', value: form.value.from_name, type: 'string' },
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

const handleTestEmail = async () => {
  if (!testEmail.value) return;
  
  isTesting.value = true;
  testResult.value = null;
  
  const startTime = performance.now();
  
  try {
    const response = await axios.post('/managit/settings/test/smtp', {
      email: testEmail.value
    });
    
    testResult.value = {
      success: true,
      message: response.data.message || 'Test email sent successfully!',
      latency_ms: Math.round(performance.now() - startTime)
    };
  } catch (error) {
    testResult.value = {
      success: false,
      message: error.response?.data?.message || error.message || 'Failed to send test email',
      latency_ms: Math.round(performance.now() - startTime)
    };
  } finally {
    isTesting.value = false;
    
    setTimeout(() => {
      testResult.value = null;
    }, 10000);
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

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}

/* Test Email Section */
.test-email-section {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.test-email-button {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4);
  background: var(--color-primary-600);
  color: white;
  border: none;
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--transition-fast);
  align-self: flex-start;
}

.test-email-button:hover:not(:disabled) {
  background: var(--color-primary-700);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.test-email-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid currentColor;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.test-result {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
}

.test-result.success {
  background: var(--color-success-50);
  color: var(--color-success-700);
  border: 1px solid var(--color-success-500);
}

.test-result.error {
  background: var(--color-error-50);
  color: var(--color-error-700);
  border: 1px solid var(--color-error-500);
}

.test-result svg {
  flex-shrink: 0;
}

.result-message {
  font-weight: var(--font-medium);
}

.result-latency {
  font-size: var(--text-xs);
  opacity: 0.8;
  font-family: var(--font-mono);
}
</style>
