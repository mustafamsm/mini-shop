<script setup lang="ts">
import CartController from '@/actions/App/Http/Controllers/CartController'
import { ArrowLeft, Check, Minus, Package, Plus, ShoppingBag } from '@lucide/vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { computed, ref } from 'vue'
import ShopLayout from '@/layouts/ShopLayout.vue'
import type { Product } from '@/types/models'

const props = defineProps<{ product: Product }>()

const selectedVariantId = ref(props.product.variants?.[0]?.id ?? null)
const quantity = ref(1)
const selectedImageIndex = ref(0)
const failedImageIds = ref(new Set<number>())

const selectedVariant = computed(() =>
  props.product.variants?.find((v) => v.id === selectedVariantId.value),
)

const selectedPrice = computed(() =>
  Number(selectedVariant.value?.price_override ?? props.product.base_price),
)

const selectedImage = computed(() => props.product.image_urls?.[selectedImageIndex.value])

function hasImage(imageId: number) {
  return !failedImageIds.value.has(imageId)
}

function markImageUnavailable(imageId: number) {
  failedImageIds.value = new Set(failedImageIds.value).add(imageId)
}

function selectVariant(variantId: number) {
  selectedVariantId.value = variantId
  quantity.value = Math.min(quantity.value, selectedVariant.value?.stock ?? 1)
}

function decreaseQuantity() {
  quantity.value = Math.max(1, quantity.value - 1)
}

function increaseQuantity() {
  quantity.value = Math.min(selectedVariant.value?.stock ?? 1, quantity.value + 1)
}

function addToCart() {
  if (!selectedVariantId.value) {
    toast.error('Please select a product variant.')
    return
  }

  if (!selectedVariant.value) {
    toast.error('Selected variant is unavailable.')
    return
  }

  if (quantity.value < 1) {
    quantity.value = 1
    toast.error('Quantity must be at least 1.')
    return
  }

  if (quantity.value > selectedVariant.value.stock) {
    quantity.value = selectedVariant.value.stock
    toast.error(`Only ${selectedVariant.value.stock} item(s) left in stock.`)
    return
  }

  router.post(
    CartController.store().url,
    {
      product_variant_id: selectedVariantId.value,
      quantity: quantity.value,
    },
    {
      preserveScroll: true,
      onError: (errors) => {
        const validationErrors = Object.values(errors ?? {}).flat().filter(Boolean)
        const message = validationErrors.length
          ? validationErrors.join(' ')
          : 'Unable to add item to cart.'

        toast.error(message)
      },
    },
  )
}
</script>

<template>
  <Head :title="product.name" />
  <ShopLayout>
    <div class="mb-8">
      <Link href="/" class="inline-flex items-center gap-2 text-sm font-medium text-neutral-500 transition hover:text-neutral-900">
        <ArrowLeft class="size-4" aria-hidden="true" />
        Back to shop
      </Link>
    </div>

    <div class="grid gap-10 lg:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)] lg:gap-16">
      <section aria-label="Product gallery" class="min-w-0">
        <div class="grid gap-3 sm:grid-cols-[88px_minmax(0,1fr)]">
          <div v-if="product.image_urls?.length" class="order-2 flex gap-3 overflow-x-auto sm:order-1 sm:flex-col">
            <button
              v-for="(image, index) in product.image_urls"
              :key="image.id"
              type="button"
              class="size-20 shrink-0 overflow-hidden rounded-xl bg-neutral-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 sm:size-[88px]"
              :class="selectedImageIndex === index ? 'ring-2 ring-neutral-900 ring-offset-2' : 'ring-1 ring-neutral-200 hover:ring-neutral-400'"
              :aria-label="`View image ${index + 1} of ${product.image_urls.length}`"
              :aria-pressed="selectedImageIndex === index"
              @click="selectedImageIndex = index"
            >
              <img
                v-if="hasImage(image.id)"
                :src="image.thumb_url || image.url"
                :alt="`${product.name} thumbnail ${index + 1}`"
                class="h-full w-full object-cover"
                loading="lazy"
                @error="markImageUnavailable(image.id)"
              />
              <Package v-else class="mx-auto size-6 text-neutral-400" aria-hidden="true" />
            </button>
          </div>

          <div class="order-1 aspect-[4/5] overflow-hidden rounded-[28px] bg-neutral-100 shadow-sm ring-1 ring-neutral-200 sm:order-2">
            <img
              v-if="selectedImage && hasImage(selectedImage.id)"
              :src="selectedImage.url || selectedImage.thumb_url"
              :alt="product.name"
              class="h-full w-full object-cover"
              @error="markImageUnavailable(selectedImage.id)"
            />
            <div v-else class="flex h-full flex-col items-center justify-center gap-4 px-8 text-center text-neutral-400" role="img" :aria-label="`${product.name} image unavailable`">
              <Package class="size-14" stroke-width="1.1" aria-hidden="true" />
              <span class="text-xs font-medium uppercase tracking-[0.18em]">Image unavailable</span>
            </div>
          </div>
        </div>
      </section>

      <section class="flex flex-col justify-center lg:py-6">
        <div class="mb-5 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">
          <span>{{ product.category?.name ?? 'Foundry Goods' }}</span>
          <span class="size-1 rounded-full bg-neutral-400" aria-hidden="true" />
          <span>Everyday collection</span>
        </div>

        <h1 class="max-w-xl text-4xl font-semibold leading-[1.05] tracking-tight text-neutral-950 sm:text-5xl">
          {{ product.name }}
        </h1>
        <p class="mt-5 text-2xl font-medium tracking-tight text-neutral-900">
          ${{ selectedPrice.toFixed(2) }}
        </p>

        <p v-if="product.description" class="mt-6 max-w-lg text-base leading-7 text-neutral-600">
          {{ product.description }}
        </p>

        <div class="my-8 h-px bg-neutral-200" />

        <div v-if="product.variants && product.variants.length > 1" class="mb-7">
          <div class="mb-3 flex items-center justify-between gap-4">
            <span class="text-sm font-semibold text-neutral-900">Choose an option</span>
            <span class="text-xs text-neutral-500">{{ selectedVariant?.name ?? selectedVariant?.sku }}</span>
          </div>
          <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
            <button
              v-for="variant in product.variants"
              :key="variant.id"
              type="button"
              class="rounded-xl border px-3 py-3 text-left text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2"
              :class="selectedVariantId === variant.id ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-200 bg-white text-neutral-700 hover:border-neutral-500'"
              :disabled="variant.stock <= 0"
              :aria-pressed="selectedVariantId === variant.id"
              @click="selectVariant(variant.id)"
            >
              <span class="block font-medium">{{ variant.name ?? variant.sku }}</span>
              <span class="mt-1 block text-xs" :class="selectedVariantId === variant.id ? 'text-neutral-300' : 'text-neutral-500'">
                {{ variant.stock > 0 ? `${variant.stock} available` : 'Sold out' }}
              </span>
            </button>
          </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
          <div class="inline-flex h-12 items-center justify-between rounded-xl border border-neutral-200 bg-white px-2 sm:w-36">
            <button type="button" class="flex size-9 items-center justify-center rounded-lg text-neutral-700 transition hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Decrease quantity" :disabled="quantity <= 1" @click="decreaseQuantity">
              <Minus class="size-4" aria-hidden="true" />
            </button>
            <span class="min-w-8 text-center text-sm font-semibold text-neutral-900" aria-live="polite">{{ quantity }}</span>
            <button type="button" class="flex size-9 items-center justify-center rounded-lg text-neutral-700 transition hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Increase quantity" :disabled="quantity >= (selectedVariant?.stock ?? 0)" @click="increaseQuantity">
              <Plus class="size-4" aria-hidden="true" />
            </button>
          </div>
          <button
            type="button"
            class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-xl bg-neutral-950 px-6 text-sm font-semibold text-white transition hover:bg-neutral-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:bg-neutral-300"
            :disabled="!selectedVariant || selectedVariant.stock <= 0"
            @click="addToCart"
          >
            <ShoppingBag class="size-4" aria-hidden="true" />
            {{ selectedVariant?.stock ? 'Add to cart' : 'Sold out' }}
          </button>
        </div>

        <div class="mt-6 grid gap-3 border-t border-neutral-200 pt-5 text-sm text-neutral-600 sm:grid-cols-2">
          <div class="flex items-center gap-2">
            <Check class="size-4 text-neutral-900" aria-hidden="true" />
            Thoughtful materials
          </div>
          <div class="flex items-center gap-2">
            <Check class="size-4 text-neutral-900" aria-hidden="true" />
            Made for daily use
          </div>
        </div>
        </section>
      </div>
    

  </ShopLayout>
</template>
