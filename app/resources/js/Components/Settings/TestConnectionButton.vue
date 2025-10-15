<template>
  <button
    @click="handleTest"
    :disabled="disabled || isTesting"
    :class="['test-button', statusClass]"
    type="button"
  >
    <span v-if="isTesting" class="spinner"></span>
    <svg v-else-if="testResult?.success" class="icon-success" width="20" height="20" viewBox="0 0 20 20">
      <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
    </svg>
    <svg v-else-if="testResult?.success === false" class="icon-error" width="20" height="20" viewBox="0 0 20 20">
      <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
    </svg>
    <svg v-else class="icon-test" width="20" height="20" viewBox="0 0 20 20" fill="none">
      <path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="1.5"/>
      <path d="M10 6V10L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    
    <span class="button-text">
      {{ buttonText }}
    </span>
    
    <span v-if="testResult?.latency_ms" class="latency">
      {{ testResult.latency_ms }}ms
    </span>
  </button>
  
  <div v-if="testResult?.message" :class="['test-message', testResult.success ? 'success' : 'error']">
    {{ testResult.message }}
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  service: {
    type: String,
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const isTesting = ref(false);
const testResult = ref(null);

const statusClass = computed(() => {
  if (isTesting.value) return 'testing';
  if (testResult.value?.success === true) return 'success';
  if (testResult.value?.success === false) return 'error';
  return '';
});

const buttonText = computed(() => {
  if (isTesting.value) return 'Testing...';
  if (testResult.value?.success === true) return 'Connected';
  if (testResult.value?.success === false) return 'Failed';
  return 'Test Connection';
});

const handleTest = async () => {
  isTesting.value = true;
  testResult.value = null;
  
  const startTime = performance.now();
  
  try {
    const response = await axios.post(`/managit/settings/test/${props.service}`);
    const latency = Math.round(performance.now() - startTime);
    
    testResult.value = {
      ...response.data,
      latency_ms: response.data.latency_ms || latency
    };
  } catch (error) {
    testResult.value = {
      success: false,
      message: error.response?.data?.message || error.message || 'Connection test failed',
      latency_ms: Math.round(performance.now() - startTime)
    };
  } finally {
    isTesting.value = false;
    
    // Clear result after 10 seconds
    setTimeout(() => {
      testResult.value = null;
    }, 10000);
  }
};
</script>

<style scoped>
.test-button {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-4);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--color-text);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.test-button:hover:not(:disabled) {
  background: var(--color-surface-hover);
  border-color: var(--color-border-strong);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.test-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.test-button.testing {
  border-color: var(--color-primary-500);
  color: var(--color-primary-700);
}

.test-button.success {
  border-color: var(--color-success-500);
  color: var(--color-success-700);
  background: var(--color-success-50);
}

.test-button.error {
  border-color: var(--color-error-500);
  color: var(--color-error-700);
  background: var(--color-error-50);
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid currentColor;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

.icon-test,
.icon-success,
.icon-error {
  flex-shrink: 0;
}

.icon-success {
  color: var(--color-success-700);
}

.icon-error {
  color: var(--color-error-700);
}

.button-text {
  flex: 1;
}

.latency {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  font-family: var(--font-mono);
  padding: 2px 6px;
  background: var(--color-background);
  border-radius: var(--radius-sm);
}

.test-message {
  margin-top: var(--space-2);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
}

.test-message.success {
  background: var(--color-success-50);
  color: var(--color-success-700);
  border: 1px solid var(--color-success-500);
}

.test-message.error {
  background: var(--color-error-50);
  color: var(--color-error-700);
  border: 1px solid var(--color-error-500);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
