<script setup>
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'
</script>

<template>

    <Head title="Bejelentkezés" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-md">
            <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
                <h1 class="text-2xl font-semibold text-slate-900">
                    Bejelentkezés | FoodR
                </h1>

                <form class="mt-6 space-y-4">
                    <!-- EMAIL -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Email cím
                        </label>
                        <input type="email" placeholder="email@pelda.hu"
                            class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500" />
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Jelszó
                        </label>

                        <div class="relative mt-1">
                            <input :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                                class="focus-glow w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500" />

                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center text-slate-400 hover:text-brand-500">
                                <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                    <!-- BUTTON -->
                    <button type="submit"
                        class="w-full rounded-xl bg-brand-600 py-2 font-semibold text-white transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-white focus:outline-none">
                        Bejelentkezés
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Nincs még fiókod?
                    <Link href="/regisztracio" class="cursor-pointer font-medium text-brand-600 hover:underline">
                        Regisztráció </Link>
                </p>
            </div>
        </div>
    </div>
</template>
