<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from "vue";

const props = defineProps({
    user: Object,
    csrf_token: String
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    avatar: null,
    gallery: [],
});

const filepondAvatarInput = ref(null); // Reference the input to clear the files later
const filepondGalleryInput = ref(null); // Reference the input to clear the files later

const submit = () => {
    form
        .transform((data) => {
            return {
                ...data,
                gallery: data.gallery.map(item => item.serverId) // Pluck only the serverIds
            }
        })
        .put(route('update-user-info'), {
            onSuccess: () => {
                filepondAvatarInput.value.removeFiles();
                filepondGalleryInput.value.removeFiles();
            },
        });
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Name</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                v-model="form.name"
                            />
                            <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
                        </div>
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Email</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                v-model="form.email"
                            />
                            <div v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</div>
                        </div>
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Avatar</span>
                            </label>
                            <FilePond
                                name="avatar"
                                ref="filepondAvatarInput"
                                class-name="my-pond"
                                allow-multiple="false"
                                accepted-file-types="image/*"
                                @init="handleFilePondInit"
                                @processfile="handleFilePondAvatarProcess"
                                @removefile="handleFilePondAvatarRemoveFile"
                            />
                        </div>
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Gallery</span>
                            </label>
                            <FilePond
                                name="gallery"
                                ref="filepondGalleryInput"
                                class-name="my-pond"
                                allow-multiple="true"
                                accepted-file-types="image/*"
                                @init="handleFilePondInit"
                                @processfile="handleFilePondGalleryProcess"
                                @removefile="handleFilePondGalleryRemoveFile"
                            />
                            <div v-if="form.errors">
                                <div v-for="(error, index) in form.errors" class="text-red-500">{{ error }}</div>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center mt-3 p-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition w-full"
                            :disabled="form.processing"
                        >
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
// Import filepond
import vueFilePond, { setOptions } from 'vue-filepond';

// Import filepond plugins
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';

// Import filepond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);

export default {
    name: 'Dashboard',
    methods: {
        handleFilePondInit: function () {
            // Set global options on filepond init
            setOptions({
                credits: false,
                server: {
                    url: '/filepond',
                    headers: {
                        'X-CSRF-TOKEN': this.props.csrf_token,
                    }
                }
            });
        },
        handleFilePondAvatarProcess: function (error, file) {
            // Set the server id from response
            this.form.avatar = file.serverId;
        },
        handleFilePondAvatarRemoveFile: function (error, file) {
            // Remove the server id on file remove
            this.form.avatar = null;
        },
        handleFilePondGalleryProcess: function (error, file) {
            // Set the server id from response
            this.form.gallery.push({id: file.id, serverId: file.serverId});
        },
        handleFilePondGalleryRemoveFile: function (error, file) {
            // Remove the server id on file remove
            this.form.gallery = this.form.gallery.filter(item => item.id !== file.id);
        },
    },
};
</script>
