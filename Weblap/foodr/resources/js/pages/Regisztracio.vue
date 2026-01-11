<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const showPassword = ref(false);
</script>

<template>
    <Head title="Regisztráció" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-md">
            <div
                class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200"
            >
                <h1 class="text-2xl font-semibold text-slate-900">
                    Regisztráció | FoodR
                </h1>

                <form class="mt-6 space-y-4">
                    <!-- USERNAME -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Felhasználónév
                        </label>
                        <input
                            type="text"
                            placeholder="pelda123"
                            class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500"
                        />
                    </div>
                    <!-- EMAIL -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Email cím
                        </label>
                        <input
                            type="email"
                            placeholder="email@pelda.hu"
                            class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500"
                        />
                    </div>

                    <!-- PASSWORD -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700">
                            Jelszó
                        </label>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-brand-500"
                        >
                            <component
                                :is="showPassword ? EyeOff : Eye"
                                class="h-5 w-5"
                            />
                        </button>
                    </div>

                    <!-- PASSWORD CONFIRMATION -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700">
                            Jelszó megerősítése
                        </label>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-brand-500"
                        >
                            <component
                                :is="showPassword ? EyeOff : Eye"
                                class="h-5 w-5"
                            />
                        </button>
                    </div>
                    <!-- BUTTON -->
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-brand-600 py-2 font-semibold text-white transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-white focus:outline-none"
                    >
                        Regisztráció
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Már van fiókod?
                    <Link
                        href="/bejelentkezes"
                        class="cursor-pointer font-medium text-brand-600 hover:underline"
                    >
                        Bejelentkezés
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
