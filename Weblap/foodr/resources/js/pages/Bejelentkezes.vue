<script setup>
import { Head } from '@inertiajs/vue3'
import { Form } from '@primevue/forms';
</script>

<template>
  <Head title="Bejelentkezés" />
  <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-200 p-8">

        <h1 class="text-2xl font-semibold text-slate-900">
          Bejelentkezés | FoodR
        </h1>
        <form class="mt-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">
              Email cím
            </label>
            <input
              type="email"
              placeholder="email@pelda.hu"
              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2
                     focus:border-brand-500 focus:ring-2 focus:ring-brand-200
                     outline-none"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">
              Jelszó
            </label>
            <input
              type="password"
              placeholder="••••••••"
              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2
                     focus:border-brand-500 focus:ring-2 focus:ring-brand-200
                     outline-none"
            />
          </div>

          <button
            type="submit"
            class="w-full rounded-xl bg-brand-600 py-2 font-semibold text-white
                   hover:bg-brand-700 transition"
          >
            Bejelentkezés
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
          Nincs még fiókod?
          <span class="text-brand-600 font-medium cursor-pointer hover:underline">
            Regisztráció
          </span>
        </p>
<button
  class="bg-brand-500 hover:bg-brand-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
>
  Submit
</button>
      </div>
    </div>
  </div>
</template>
