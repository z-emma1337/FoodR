<script setup>
defineProps({
    disabled: Boolean,
    loading: Boolean
})
</script>

<template>
    <button type="submit" :disabled="disabled || loading" class="w-full rounded-3xl bg-brand-700 py-2.5 font-semibold text-accent-200
           shadow-xl shadow-brand-800/40
           transition-transform duration-300
           hover:scale-105
           focus:outline-none focus:ring-4 focus:ring-brand-500/40
           active:scale-95
           disabled:bg-brand-400 disabled:text-accent-200 
           disabled:cursor-not-allowed 
           disabled:shadow-none
disabled:hover:scale-100           cursor-pointer">
        <span v-if="loading"
            class="inline-block h-5 w-5 border-2 border-accent-200 border-t-transparent rounded-full animate-spin"></span>
        <slot v-else />
    </button>
</template>
