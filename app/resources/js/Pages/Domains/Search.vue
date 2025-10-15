<template>
  <AuthenticatedLayout>
    <Head :title="$t('domains.search')" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-semibold mb-6">{{ $t('domains.search_title') }}</h2>

            <!-- Search Form -->
            <form @submit.prevent="search" class="mb-8">
              <div class="flex gap-4">
                <div class="flex-1">
                  <input
                    v-model="form.fqdn"
                    type="text"
                    :placeholder="$t('domains.enter_domain')"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                    required
                  />
                </div>
                
                <select
                  v-model="form.registrar"
                  class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                >
                  <option value="">{{ $t('domains.any_registrar') }}</option>
                  <option value="namecheap">Namecheap</option>
                  <option value="namecom">Name.com</option>
                  <option value="coccaep">Coccaep (.mr)</option>
                </select>

                <button
                  type="submit"
                  :disabled="searching"
                  class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                  {{ searching ? $t('common.searching') : $t('common.search') }}
                </button>
              </div>
            </form>

            <!-- Search Results -->
            <div v-if="result" class="mt-8">
              <div v-if="result.error" class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                {{ result.error }}
              </div>

              <div v-else-if="result.available" class="space-y-4">
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded flex items-center justify-between">
                  <div>
                    <p class="font-semibold">{{ form.fqdn }} {{ $t('domains.available') }}</p>
                    <p v-if="result.price" class="text-sm">{{ $t('domains.price') }}: {{ formatPrice(result.price) }}</p>
                  </div>
                  <button
                    @click="addToCart(form.fqdn, result.price, result.registrar)"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                  >
                    {{ $t('cart.add_to_cart') }}
                  </button>
                </div>

                <!-- Suggestions -->
                <div v-if="result.suggestions && result.suggestions.length > 0" class="mt-6">
                  <h3 class="text-lg font-semibold mb-3">{{ $t('domains.suggestions') }}</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                      v-for="suggestion in result.suggestions"
                      :key="suggestion.fqdn"
                      class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 hover:border-indigo-500 dark:hover:border-indigo-600 transition"
                    >
                      <p class="font-semibold">{{ suggestion.fqdn }}</p>
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatPrice(suggestion.price) }}</p>
                      <button
                        @click="addToCart(suggestion.fqdn, suggestion.price, result.registrar)"
                        class="mt-2 w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                      >
                        {{ $t('cart.add_to_cart') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded">
                {{ form.fqdn }} {{ $t('domains.not_available') }}
              </div>
            </div>

            <!-- TLD Pricing Table -->
            <div class="mt-12">
              <h3 class="text-lg font-semibold mb-4">{{ $t('domains.pricing_table') }}</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">TLD</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $t('domains.register') }}</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $t('domains.renew') }}</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $t('domains.transfer') }}</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="(prices, tld) in pricing" :key="tld">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">.{{ tld }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">${{ prices.register }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">${{ prices.renew }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">${{ prices.transfer }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const form = ref({
  fqdn: '',
  registrar: ''
})

const searching = ref(false)
const result = ref(null)
const pricing = ref({})

const search = async () => {
  searching.value = true
  result.value = null

  try {
    const response = await axios.post(route('domains.search.query'), form.value)
    result.value = response.data
  } catch (error) {
    result.value = {
      error: error.response?.data?.message || 'Search failed'
    }
  } finally {
    searching.value = false
  }
}

const addToCart = async (fqdn, price, registrar) => {
  router.post(route('cart.add-domain'), {
    fqdn,
    years: 1,
    registrar: registrar || 'namecheap',
    price
  }, {
    onSuccess: () => {
      router.visit(route('cart.index'))
    }
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price || 0)
}

onMounted(async () => {
  try {
    const response = await axios.get(route('domains.pricing'))
    pricing.value = response.data
  } catch (error) {
    console.error('Failed to load pricing:', error)
  }
})
</script>
