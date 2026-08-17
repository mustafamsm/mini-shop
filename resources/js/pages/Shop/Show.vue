<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ShopLayout from '@/layouts/ShopLayout.vue'
import CartController from '@/actions/App/Http/Controllers/CartController'
import type { Product } from '@/types/models'

const props = defineProps<{ product: Product }>()

const selectedVariantId = ref(props.product.variants?.[0]?.id ?? null)
const quantity = ref(1)

function addToCart() {
  if (!selectedVariantId.value) {
    return
}

  router.post(CartController.store().url, {
    product_variant_id: selectedVariantId.value,
    quantity: quantity.value,
  }, { preserveScroll: true })
}
</script>

<template>
  <Head :title="product.name" />
  <ShopLayout>
    <div class="grid grid-cols-2 gap-12">
      <div class="aspect-square bg-white rounded overflow-hidden">
        <img v-if="product.images?.[0]" :src="product.images[0].path" :alt="product.name" class="w-full h-full object-cover" />
      </div>
      <div>
        <h1 class="text-3xl font-semibold mb-2">{{ product.name }}</h1>
        <p class="text-xl mb-6">${{ Number(product.base_price).toFixed(2) }}</p>

        <select v-if="product.variants && product.variants.length > 1" v-model="selectedVariantId" class="border rounded px-3 py-2 mb-4">
          <option v-for="v in product.variants" :key="v.id" :value="v.id">{{ v.name ?? v.sku }}</option>
        </select>

        <div class="flex items-center gap-4 mb-6">
          <input type="number" v-model.number="quantity" min="1" class="border rounded px-3 py-2 w-20" />
          <button @click="addToCart" class="bg-black text-white px-6 py-2 rounded">Add to cart</button>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>
