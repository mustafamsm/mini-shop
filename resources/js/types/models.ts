export interface Category {
    id: number
    name: string
    slug: string
    parent_id: number | null
}
export interface ProductImage {
    id: number
    path: string
    sort_order: number
}

export interface ProductVariant {
    id: number
    sku: string
    name: string | null
    price_override: number | null
    stock: number
}

export interface Product {
    id: number
    category_id: number
    category?: Category
    name: string
    slug: string
    description: string | null
    base_price: number
    is_active: boolean
    images?: ProductImage[]
    variants?: ProductVariant[]
}

export interface CartItem {
    id: number
    cart_id: number
    product_variant_id: number
    quantity: number
    product_variant?: ProductVariant & { product?: Product }
}

export interface Cart {
    id: number
    user_id: number | null
    session_id: string | null
    items: CartItem[]
}

export type OrderStatus = 'pending' | 'paid' | 'processing' | 'shipped' | 'delivered' | 'cancelled'

export interface OrderItem {
    id: number
    order_id: number
    product_variant_id: number
    product_name: string
    quantity: number
    unit_price: number
}

export interface Order {
    id: number
    order_number: string
    user_id: number
    address_id: number
    status: OrderStatus
    subtotal: number
    tax: number
    shipping: number
    total: number
    items?: OrderItem[]
    created_at: string
}

// Laravel paginator shape (from ->paginate())
export interface Paginated<T> {
    data: T[]
    total: number
    links: { url: string | null; label: string; active: boolean }[]
}

export interface DashboardStats {
    total_revenue: number
    orders_today: number
    low_stock_variants: number
}

export interface RevenueByDay {
    date: string
    total: number
}
