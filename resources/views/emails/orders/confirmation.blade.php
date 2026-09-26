@component('mail::message')
# Thanks for your order, {{ $order->user->name }}!

Order **{{ $order->order_number }}** has been placed.

@component('mail::table')
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->unit_price, 2) }} |
@endforeach
@endcomponent

**Total: ${{ number_format($order->total, 2) }}**

@component('mail::button', ['url' => route('orders.show', $order)])
View Order
@endcomponent

Thanks,<br>
Foundry Goods
@endcomponent