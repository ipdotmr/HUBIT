<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    settings: Object,
    auditLogs: Array
});
</script>

<template>
    <Head title="Localization - Settings" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="mb-4">
                <h3 class="mb-0">Localization</h3>
                <p class="text-muted">Configure language, timezone, and regional settings</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Regional Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Language</label>
                            <select class="form-select">
                                <option value="en">English</option>
                                <option value="ar">Arabic (العربية)</option>
                                <option value="fr">French (Français)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Timezone</label>
                            <select class="form-select">
                                <option value="UTC">UTC</option>
                                <option value="Africa/Nouakchott">Africa/Nouakchott</option>
                                <option value="Europe/Paris">Europe/Paris</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Format</label>
                            <select class="form-select">
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time Format</label>
                            <select class="form-select">
                                <option value="24">24-hour</option>
                                <option value="12">12-hour (AM/PM)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
