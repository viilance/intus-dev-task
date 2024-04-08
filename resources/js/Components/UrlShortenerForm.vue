<template>
    <form @submit.prevent="handleSubmit" class="flex flex-col items-center justify-center space-y-4 p-6">
        <TextInput
            id="short_url"
            type="text"
            placeholder="Enter URL"
            class="mt-1 block w-full max-w-xl"
            v-model="url"
            required
            autofocus
            autocomplete="name"
        />

        <PrimaryButton type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Shorten URL
        </PrimaryButton>

        <div v-if="shortenedUrl" class="alert alert-success">
            Shortened URL: <a :href="shortenedUrl" target="_blank">{{ shortenedUrl }}</a>
        </div>

        <div v-if="errorMessage" class="alert alert-danger">
            {{ errorMessage }}
        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import UrlService from "@/Services/UrlService.js";

const url = ref('');
const shortenedUrl = ref(null);
const errorMessage = ref(null);

const handleSubmit = async () => {
    try {
        const response = await UrlService.shortenUrl(url.value);
        shortenedUrl.value = response.shortUrl;
        errorMessage.value = null;
    } catch (error) {
        errorMessage.value = error.response.data.error || 'An error occurred';
        shortenedUrl.value = null;
    }
}
</script>
