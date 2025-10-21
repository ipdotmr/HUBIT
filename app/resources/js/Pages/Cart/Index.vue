<template>
  <OsenLayout>
    <Head title="Shopping Cart" />

    <template #header>
      <div class="row">
        <div class="col-12">
          <div class="page-title-box">
            <h4 class="page-title">Shopping Cart</h4>
          </div>
        </div>
      </div>
    </template>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <button
                v-if="cart.items && cart.items.length > 0"
                @click="clearCart"
                class="btn btn-sm btn-outline-danger"
              >
                <i class="ti ti-trash me-1"></i>
                Clear Cart
              </button>
            </div>

            <!-- Empty Cart -->
            <div v-if="!cart.items || cart.items.length === 0" class="text-center py-5">
              <i class="ti ti-shopping-cart-off display-4 text-muted mb-3"></i>
              <h4 class="text-muted">Your cart is empty</h4>
              <p class="text-muted">Browse our products and add items to your cart.</p>
              <Link
                :href="route('products.index')"
                class="btn btn-primary mt-3"
              >
                <i class="ti ti-shopping-bag me-1"></i>
                Browse Products
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
                        <p class="text-sm text-muted">
                          <span v-if="item.type === 'domain'">
                            {{ item.years }} {{ item.years > 1 ? 'years' : 'year' }} - {{ item.config?.registrar || 'Namecheap' }}
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
              <div class="border-top pt-4 mt-4">
                <div class="mb-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>{{ formatPrice(totals.subtotal, cart.currency) }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Tax:</span>
                    <span>{{ formatPrice(totals.tax, cart.currency) }}</span>
                  </div>
                  <div class="d-flex justify-content-between fs-5 fw-bold">
                    <span>Total:</span>
                    <span class="text-primary">{{ formatPrice(totals.total, cart.currency) }}</span>
                  </div>
                </div>

                <button
                  @click="checkout"
                  class="btn btn-primary w-100"
                >
                  <i class="ti ti-credit-card me-1"></i>
                  Proceed to Checkout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </OsenLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import OsenLayout from '@/Layouts/OsenLayout.vue'

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
  if (confirm('Are you sure you want to clear your cart?')) {
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
