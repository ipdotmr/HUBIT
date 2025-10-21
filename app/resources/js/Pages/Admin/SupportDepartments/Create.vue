<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const activeTab = ref('en');

const form = useForm({
    name_en: '',
    name_ar: '',
    name_fr: '',
    description_en: '',
    description_ar: '',
    description_fr: '',
    email: '',
    is_active: true,
    sort_order: 0,
});

const submit = () => {
    form.post(route('managit.support-departments.store'));
};
</script>

<template>
    <Head title="Add Support Department" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.support-departments.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Departments
                            </Link>
                        </div>
                        <h4 class="page-title">Add Support Department</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Department Email</label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.email }"
                                        id="email"
                                        placeholder="support@example.com"
                                    >
                                    <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="sort_order" class="form-label">Sort Order *</label>
                                    <input 
                                        v-model.number="form.sort_order" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.sort_order }"
                                        id="sort_order"
                                        min="0"
                                        required
                                    >
                                    <div v-if="form.errors.sort_order" class="invalid-feedback">{{ form.errors.sort_order }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="is_active" class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input 
                                            v-model="form.is_active" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="is_active"
                                        >
                                        <label class="form-check-label" for="is_active">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'en'"
                                        :class="['nav-link', { active: activeTab === 'en' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> English
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'ar'"
                                        :class="['nav-link', { active: activeTab === 'ar' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> Arabic
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'fr'"
                                        :class="['nav-link', { active: activeTab === 'fr' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> French
                                    </button>
                                </li>
                            </ul>

                            <div v-show="activeTab === 'en'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="name_en" class="form-label">Department Name (English) *</label>
                                        <input 
                                            v-model="form.name_en" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.name_en }"
                                            id="name_en"
                                            required
                                        >
                                        <div v-if="form.errors.name_en" class="invalid-feedback">{{ form.errors.name_en }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="description_en" class="form-label">Description (English)</label>
                                        <textarea 
                                            v-model="form.description_en" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.description_en }"
                                            id="description_en"
                                            rows="4"
                                        ></textarea>
                                        <div v-if="form.errors.description_en" class="invalid-feedback">{{ form.errors.description_en }}</div>
                                    </div>
                                </div>
                            </div>

                            <div v-show="activeTab === 'ar'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="name_ar" class="form-label">Department Name (Arabic) *</label>
                                        <input 
                                            v-model="form.name_ar" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.name_ar }"
                                            id="name_ar"
                                            dir="rtl"
                                            required
                                        >
                                        <div v-if="form.errors.name_ar" class="invalid-feedback">{{ form.errors.name_ar }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="description_ar" class="form-label">Description (Arabic)</label>
                                        <textarea 
                                            v-model="form.description_ar" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.description_ar }"
                                            id="description_ar"
                                            rows="4"
                                            dir="rtl"
                                        ></textarea>
                                        <div v-if="form.errors.description_ar" class="invalid-feedback">{{ form.errors.description_ar }}</div>
                                    </div>
                                </div>
                            </div>

                            <div v-show="activeTab === 'fr'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="name_fr" class="form-label">Department Name (French) *</label>
                                        <input 
                                            v-model="form.name_fr" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.name_fr }"
                                            id="name_fr"
                                            required
                                        >
                                        <div v-if="form.errors.name_fr" class="invalid-feedback">{{ form.errors.name_fr }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="description_fr" class="form-label">Description (French)</label>
                                        <textarea 
                                            v-model="form.description_fr" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.description_fr }"
                                            id="description_fr"
                                            rows="4"
                                        ></textarea>
                                        <div v-if="form.errors.description_fr" class="invalid-feedback">{{ form.errors.description_fr }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Department' }}
                                    </button>
                                    <Link :href="route('managit.support-departments.index')" class="btn btn-secondary ms-2">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
