<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description,
    display_order: props.category.display_order,
    is_published: props.category.is_published,
});

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
};

const submit = () => {
    form.put(route('managit.knowledgebase.update', props.category.id));
};
</script>

<template>
    <Head title="Edit Knowledgebase Category" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.knowledgebase.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back
                            </Link>
                        </div>
                        <h4 class="page-title">Edit Category: {{ category.name }}</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    @input="generateSlug"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.name }"
                                    required
                                />
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input
                                    id="slug"
                                    type="text"
                                    v-model="form.slug"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.slug }"
                                    required
                                />
                                <small class="text-muted">URL-friendly version of the name</small>
                                <div v-if="form.errors.slug" class="invalid-feedback">{{ form.errors.slug }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="form-control"
                                    rows="3"
                                ></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="display_order" class="form-label">Display Order <span class="text-danger">*</span></label>
                                <input
                                    id="display_order"
                                    type="number"
                                    v-model="form.display_order"
                                    class="form-control"
                                    required
                                    min="0"
                                />
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input
                                        id="is_published"
                                        type="checkbox"
                                        v-model="form.is_published"
                                        class="form-check-input"
                                    />
                                    <label for="is_published" class="form-check-label">
                                        Published (visible to clients)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <Link :href="route('managit.knowledgebase.index')" class="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing">Update Category</span>
                                    <span v-else>Updating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
