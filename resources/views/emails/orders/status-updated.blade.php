@component('mail::message')
# Order update

Your order **{{ $order->order_number }}** status changed:

**{{ ucfirst($oldStatus) }} → {{ ucfirst($order->status) }}**

@component('mail::button', ['url' => route('orders.show', $order)])
View Order
@endcomponent

Thanks,<br>
Foundry Goods
@endcomponent
