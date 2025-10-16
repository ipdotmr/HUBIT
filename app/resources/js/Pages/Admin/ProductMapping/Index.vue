<template>
  <AuthenticatedLayout>
    <Head title="Product Mapping" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-semibold mb-6">Product → Package Mapping</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
              Map HUBIT products to provisioner packages (cPanel/Plesk) for automated provisioning.
            </p>

            <!-- Products Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Provisioner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Package Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Server</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="product in products" :key="product.id">
                    <td class="px-6 py-4">
                      <div>
                        <p class="font-medium">{{ product.name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ product.product_group?.name || 'Hosting' }}</p>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <select
                        v-model="mappings[product.id].provisioner"
                        @change="updateMapping(product.id)"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 text-sm"
                      >
                        <option value="">None</option>
                        <option v-for="(label, key) in provisioners" :key="key" :value="key">{{ label }}</option>
                      </select>
                    </td>
                    <td class="px-6 py-4">
                      <input
                        v-model="mappings[product.id].package_name"
                        @blur="updateMapping(product.id)"
                        type="text"
                        :placeholder="getMappingPlaceholder(mappings[product.id].provisioner)"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 text-sm"
                      />
                    </td>
                    <td class="px-6 py-4">
                      <input
                        v-model="mappings[product.id].server_id"
                        @blur="updateMapping(product.id)"
                        type="number"
                        placeholder="Server ID (optional)"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 text-sm"
                      />
                    </td>
                    <td class="px-6 py-4">
                      <button
                        @click="testProvision(product.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                      >
                        Test
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">Configuration Guide</h3>
              <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                <li><strong>cPanel:</strong> Enter the package name exactly as defined in WHM (e.g., "HUBIT_BASIC", "HUBIT_PRO")</li>
                <li><strong>Plesk:</strong> Enter the service plan name (e.g., "Web Hosting Basic", "Reseller Plan")</li>
                <li><strong>Server ID:</strong> Optional. Use when you have multiple servers and want to specify which one to provision on.</li>
                <li><strong>Test:</strong> Click "Test" to verify the provisioning works without creating a real service.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  products: Array,
  provisioners: Object
})

const mappings = reactive({})

onMounted(() => {
  props.products.forEach(product => {
    mappings[product.id] = {
      provisioner: product.provisioner || '',
      package_name: product.provision_config?.package || '',
      server_id: product.provision_config?.server_id || null,
      meta: product.provision_config?.meta || {}
    }
  })
})

const updateMapping = (productId) => {
  const mapping = mappings[productId]
  
  router.put(route('managit.products.mapping.update', productId), mapping, {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Mapping updated')
    }
  })
}

const testProvision = async (productId) => {
  const domain = prompt('Enter a test domain name:')
  if (!domain) return

  try {
    const response = await axios.post(route('managit.products.mapping.test', productId), { domain })
    alert(response.data.message || 'Test completed')
  } catch (error) {
    alert(error.response?.data?.message || 'Test failed')
  }
}

const getMappingPlaceholder = (provisioner) => {
  const placeholders = {
    'cpanel': 'e.g., HUBIT_BASIC',
    'plesk': 'e.g., Web Hosting Basic'
  }
  return placeholders[provisioner] || 'Package name'
}
</script>
