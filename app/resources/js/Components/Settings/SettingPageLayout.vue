<template>
  <div class="setting-page">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">{{ title }}</h1>
        <p v-if="description" class="page-description">{{ description }}</p>
      </div>
      <slot name="header-actions"></slot>
    </div>

    <!-- Main Content -->
    <div class="page-content">
      <div class="content-wrapper">
        <slot></slot>
      </div>

      <!-- Audit Drawer (optional) -->
      <transition name="slide-left">
        <div v-if="showAudit && auditLogs.length > 0" class="audit-drawer">
          <div class="audit-header">
            <h3 class="audit-title">Recent Changes</h3>
            <button @click="showAudit = false" class="close-button" aria-label="Close audit log">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="audit-list">
            <div v-for="log in auditLogs.slice(0, 10)" :key="log.id" class="audit-item">
              <div class="audit-meta">
                <span class="audit-user">{{ log.changed_by?.name || 'System' }}</span>
                <span class="audit-time">{{ formatTime(log.created_at) }}</span>
              </div>
              <div class="audit-change">
                <span class="audit-key">{{ log.key }}</span>
                <div class="audit-values">
                  <span class="audit-old">{{ log.old_value || '(empty)' }}</span>
                  <svg class="audit-arrow" width="16" height="16" viewBox="0 0 16 16">
                    <path d="M5 8H11M11 8L8 5M11 8L8 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <span class="audit-new">{{ log.new_value }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Sticky Save Bar -->
    <transition name="slide-up">
      <div v-if="hasChanges" class="save-bar">
        <div class="save-bar-content">
          <div class="save-bar-message">
            <svg class="warning-icon" width="20" height="20" viewBox="0 0 20 20">
              <path d="M10 6V10M10 14H10.01M19 10C19 14.9706 14.9706 19 10 19C5.02944 19 1 14.9706 1 10C1 5.02944 5.02944 1 10 1C14.9706 1 19 5.02944 19 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>You have unsaved changes</span>
          </div>
          <div class="save-bar-actions">
            <button @click="$emit('revert')" class="btn btn-secondary" :disabled="isSaving || isTesting">
              Revert
            </button>
            <button v-if="hasTest" @click="$emit('test')" class="btn btn-secondary" :disabled="isSaving || isTesting">
              <span v-if="isTesting" class="spinner"></span>
              <span v-else>Test Connection</span>
            </button>
            <button @click="$emit('save')" class="btn btn-primary" :disabled="isSaving || isTesting">
              <span v-if="isSaving" class="spinner"></span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: null
  },
  hasChanges: {
    type: Boolean,
    default: false
  },
  isSaving: {
    type: Boolean,
    default: false
  },
  isTesting: {
    type: Boolean,
    default: false
  },
  hasTest: {
    type: Boolean,
    default: false
  },
  auditLogs: {
    type: Array,
    default: () => []
  }
});

defineEmits(['save', 'revert', 'test']);

const showAudit = ref(props.auditLogs.length > 0);

const formatTime = (timestamp) => {
  const date = new Date(timestamp);
  const now = new Date();
  const diff = now - date;
  
  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
  return date.toLocaleDateString();
};
</script>

<style scoped>
.setting-page {
  min-height: 100vh;
  background: var(--color-background);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: var(--space-8) var(--space-6);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-surface);
}

.page-title {
  font-size: var(--text-3xl);
  font-weight: var(--font-bold);
  color: var(--color-text);
  margin: 0;
}

.page-description {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin: var(--space-2) 0 0;
}

.page-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-6);
  padding: var(--space-6);
  padding-bottom: calc(var(--space-6) + 80px);
}

@media (min-width: 1280px) {
  .page-content {
    grid-template-columns: 1fr 320px;
  }
}

.content-wrapper {
  max-width: 900px;
}

/* Audit Drawer */
.audit-drawer {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  max-height: calc(100vh - 200px);
  overflow-y: auto;
  position: sticky;
  top: var(--space-6);
}

.audit-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-4);
}

.audit-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin: 0;
}

.close-button {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: var(--space-1);
  border-radius: var(--radius-md);
  transition: all var(--transition-fast);
}

.close-button:hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.audit-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.audit-item {
  padding: var(--space-3);
  background: var(--color-background);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.audit-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-2);
  font-size: var(--text-xs);
}

.audit-user {
  font-weight: var(--font-medium);
  color: var(--color-text);
}

.audit-time {
  color: var(--color-text-muted);
}

.audit-change {
  font-size: var(--text-sm);
}

.audit-key {
  font-weight: var(--font-medium);
  color: var(--color-text);
  display: block;
  margin-bottom: var(--space-1);
}

.audit-values {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  color: var(--color-text-muted);
  font-size: var(--text-xs);
}

.audit-arrow {
  color: var(--color-primary-600);
  flex-shrink: 0;
}

.audit-old, .audit-new {
  padding: 2px 6px;
  background: var(--color-surface);
  border-radius: var(--radius-sm);
  font-family: var(--font-mono);
}

/* Sticky Save Bar */
.save-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--color-surface);
  border-top: 1px solid var(--color-border-strong);
  box-shadow: var(--shadow-xl);
  z-index: 40;
}

.save-bar-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: var(--space-4) var(--space-6);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.save-bar-message {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  color: var(--color-warning-700);
  font-weight: var(--font-medium);
}

.warning-icon {
  color: var(--color-warning-500);
}

.save-bar-actions {
  display: flex;
  gap: var(--space-3);
}

/* Buttons */
.btn {
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--transition-fast);
  border: none;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: var(--color-primary-600);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: var(--color-primary-700);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-secondary {
  background: var(--color-surface);
  color: var(--color-text);
  border: 1px solid var(--color-border);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--color-surface-hover);
  border-color: var(--color-border-strong);
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

/* Transitions */
.slide-up-enter-active, .slide-up-leave-active {
  transition: transform var(--transition-base);
}

.slide-up-enter-from {
  transform: translateY(100%);
}

.slide-up-leave-to {
  transform: translateY(100%);
}

.slide-left-enter-active, .slide-left-leave-active {
  transition: transform var(--transition-base), opacity var(--transition-base);
}

.slide-left-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-left-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* RTL Support */
[dir="rtl"] .audit-values {
  flex-direction: row-reverse;
}

[dir="rtl"] .audit-arrow {
  transform: scaleX(-1);
}

[dir="rtl"] .slide-left-enter-from,
[dir="rtl"] .slide-left-leave-to {
  transform: translateX(-100%);
}
</style>
