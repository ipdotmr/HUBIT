<template>
  <AuthenticatedLayout>
    <Head :title="$t('cart.title')" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold">{{ $t('cart.title') }}</h2>
              <button
                v-if="cart.items && cart.items.length > 0"
                @click="clearCart"
                class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
              >
                {{ $t('cart.clear_cart') }}
              </button>
            </div>

            <!-- Empty Cart -->
            <div v-if="!cart.items || cart.items.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p class="mt-4 text-gray-500 dark:text-gray-400">{{ $t('cart.empty') }}</p>
              <Link
                :href="route('domains.search')"
                class="mt-4 inline-block px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                {{ $t('domains.search_domains') }}
              </Link>
            </div>

            <!-- Cart Items -->
            <div v-else>
              <div class="space-y-4 mb-6">
                <div
                  v-for="item in cart.items"
                  :key="item.id"
                  class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex justify-between items-center"
                >
                  <div class="flex-1">
                    <div class="flex items-center gap-3">
                      <!-- Icon based on type -->
                      <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded">
                        <svg v-if="item.type === 'domain'" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <svg v-else class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                      </div>

                      <div>
                        <p class="font-semibold">
                          <span v-if="item.type === 'domain'">{{ item.fqdn }}</span>
                          <span v-else>{{ item.config?.plan || 'Hosting Plan' }}</span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                          <span v-if="item.type === 'domain'">
                            {{ item.years }} {{ item.years > 1 ? $t('common.years') : $t('common.year') }} - {{ item.config?.registrar || 'Namecheap' }}
                          </span>
                          <span v-else>
                            {{ item.config?.cycle || 'monthly' }} - {{ item.sku }}
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="flex items-center gap-4">
                    <div class="text-right">
                      <p class="font-semibold">{{ formatPrice(item.price, item.currency) }}</p>
                    </div>
                    <button
                      @click="removeItem(item.id)"
                      class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Totals -->
              <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="space-y-2 mb-4">
                  <div class="flex justify-between text-sm">
                    <span>{{ $t('cart.subtotal') }}:</span>
                    <span>{{ formatPrice(totals.subtotal, cart.currency) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span>{{ $t('cart.tax') }}:</span>
                    <span>{{ formatPrice(totals.tax, cart.currency) }}</span>
                  </div>
                  <div class="flex justify-between text-lg font-semibold">
                    <span>{{ $t('cart.total') }}:</span>
                    <span>{{ formatPrice(totals.total, cart.currency) }}</span>
                  </div>
                </div>

                <button
                  @click="checkout"
                  class="w-full px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  {{ $t('cart.checkout') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  cart: Object,
  totals: Object
})

const removeItem = (itemId) => {
  router.delete(route('cart.remove', itemId), {
    preserveScroll: true
  })
}

const clearCart = () => {
  if (confirm(t('cart.confirm_clear'))) {
    router.post(route('cart.clear'), {}, {
      preserveScroll: true
    })
  }
}

const checkout = () => {
  router.post(route('cart.checkout'))
}

const formatPrice = (price, currency = 'MRU') => {
  const currencySymbols = {
    'MRU': 'MRU',
    'USD': '$',
    'EUR': '€'
  }
  
  return `${currencySymbols[currency] || currency} ${Number(price || 0).toFixed(2)}`
}
</script>
