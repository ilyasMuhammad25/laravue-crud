<!-- resources/js/Pages/News/Partials/NewsFormModal.vue -->
<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { defineProps, defineEmits } from 'vue';

const { item, show, maxWidth, closeable } = defineProps({
    item: [Array, Object],
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: '2xl' },
    closeable: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const form = useForm({
    title: item?.title ?? '',
    content: item?.content ?? "",
    file_image: null, // Initialize as null
});

const toFormData = (data) => {
    const formData = new FormData();
    Object.keys(data).forEach((key) => {
        formData.append(key, data[key]);
    });
    return formData;
};

const submit = () => {
    const formData = toFormData(form);

    if (form.id) {
		form.post(route('admin.staffs.update', form.id), {
            data: formData,
            onSuccess: () => emit('close'),
            forceFormData: true,
        });
    } else {
        form.post(route('news.store'), {
            data: formData,
            onSuccess: () => emit('close'),
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="emit('close')">
        <SectionCard class="rounded-none dark:rounded-none">
            <div class="flex items-center justify-end w-full px-3 pt-3 cursor-pointer" @click="emit('close')">
                <Icon name="close" class="h-7" />
            </div>
            <header class="border-b border-secondary/10 dark:border-secondary mb-5 -mt-5">
                <h1 class="px-14 mb-2 text-dark text-xl font-bold inline-flex items-center">
                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-info-light/20 mr-4">
                        <Icon name="user" class="h-7" />
                    </span>
                    {{ item?.id ? __('Edit News') : __('New News') }}
                </h1>
            </header>

            <main class="px-14 pb-7">
                <ValidationErrors class="my-5" />
                <div>
                    <div class="md:grid grid-cols-1 md:grid-cols-2 gap-x-5">
                        <TextInput label="Title" :placeholder="__('Enter title')" id="title" v-model="form.title" :error="form.errors.title" class="w-full" />
                        <TextInput label="Content" :placeholder="__('Enter content')" id="content" v-model="form.content" :error="form.errors.content" class="w-full" />
                        <TextInput label="Image" type="file" id="file_image" @change="(e) => form.file_image = e.target.files[0]" :error="form.errors.file_image" class="w-full" />
                    </div>
                </div>
            </main>

            <footer class="border-t border-secondary/10 dark:border-secondary pb-5">
                <div class="mt-3 flex items-center justify-between px-14">
                    <Button :processing="form.processing" :disabled="form.processing" intent="primary" as="button" class="mt-5" @click="submit">{{ __('Save') }}</Button>
                </div>
            </footer>
        </SectionCard>
    </Modal>
</template>
