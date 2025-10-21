<template>
  <WhmcsAdminLayout>
    <Head title="Product Mapping" />

    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-0">Product → Package Mapping</h3>
          <p class="text-muted">Map HUBIT products to provisioner packages (cPanel/Plesk) for automated provisioning</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">

            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th>Provisioner</th>
                  <th>Package Name</th>
                  <th>Server</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in products" :key="product.id">
                  <td>
                    <div>
                      <strong>{{ product.name }}</strong>
                      <br><small class="text-muted">{{ product.product_group?.name || 'Hosting' }}</small>
                    </div>
                  </td>
                  <td>
                    <select
                      v-model="mappings[product.id].provisioner"
                      @change="updateMapping(product.id)"
                      class="form-select form-select-sm"
                    >
                      <option value="">None</option>
                      <option v-for="(label, key) in provisioners" :key="key" :value="key">{{ label }}</option>
                    </select>
                  </td>
                  <td>
                    <input
                      v-model="mappings[product.id].package_name"
                      @blur="updateMapping(product.id)"
                      type="text"
                      :placeholder="getMappingPlaceholder(mappings[product.id].provisioner)"
                      class="form-control form-control-sm"
                    />
                  </td>
                  <td>
                    <input
                      v-model="mappings[product.id].server_id"
                      @blur="updateMapping(product.id)"
                      type="number"
                      placeholder="Server ID (optional)"
                      class="form-control form-control-sm"
                    />
                  </td>
                  <td>
                    <button
                      @click="testProvision(product.id)"
                      class="btn btn-sm btn-primary"
                    >
                      <i class="fas fa-vial me-1"></i>Test
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>


      <!-- Help Text -->
      <div class="alert alert-info mt-4">
        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Configuration Guide</h5>
        <ul class="mb-0">
          <li><strong>cPanel:</strong> Enter the package name exactly as defined in WHM (e.g., "HUBIT_BASIC", "HUBIT_PRO")</li>
          <li><strong>Plesk:</strong> Enter the service plan name (e.g., "Web Hosting Basic", "Reseller Plan")</li>
          <li><strong>Server ID:</strong> Optional. Use when you have multiple servers and want to specify which one to provision on.</li>
          <li><strong>Test:</strong> Click "Test" to verify the provisioning works without creating a real service.</li>
        </ul>
      </div>
    </div>
  </WhmcsAdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue'

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
