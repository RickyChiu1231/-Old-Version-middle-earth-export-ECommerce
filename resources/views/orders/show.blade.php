@extends('layouts.app')
@section('title', 'Order Detail')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h4>Order Info</h4>
  </div>
  <div class="card-body">
    <table class="table">
      <thead>
      <tr>
        <th>Product Info</th>
        <th class="text-center">Unit Price</th>
        <th class="text-center">Qty</th>
        <th class="text-right item-amount">Total</th>
      </tr>
      </thead>
      @foreach($order->items as $index => $item)
        <tr>
          <td class="product-info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                <img src="{{ $item->product->image_url }}">
              </a>
            </div>
            <div>
              <span class="product-title">
                 <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
              </span>
              <span class="sku-title">{{ $item->productSku->title }}</span>
            </div>
          </td>
          <td class="sku-price text-center vertical-middle">${{ $item->price }}</td>
          <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
          <td class="item-amount text-right vertical-middle">${{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
        </tr>
      @endforeach
      <tr><td colspan="4"></td></tr>
    </table>
    <div class="order-bottom">
      <div class="order-info">
        <div class="line"><div class="line-label">Shipping Address：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
        <div class="line"><div class="line-label">Order Remark：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
        <div class="line"><div class="line-label">Order No.：</div><div class="line-value">{{ $order->no }}</div></div>
        <!-- Output shipment status -->
        <div class="line">
          <div class="line-label">Shipment status：</div>
          <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
        </div>
        <!-- Show if there is shipment information -->
        @if($order->ship_data)
        <div class="line">
          <div class="line-label">Shipment Detail：</div>
          <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
        </div>
        @endif
      </div>
      <div class="order-summary text-right">
        <div class="total-amount">
          <span>Total Price：</span>
          <div class="value">${{ $order->total_amount }}</div>
        </div>
        <div>
          <span>Order status：</span>
          <div class="value">
            @if($order->paid_at)
              @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                Paid
              @else
                {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
              @endif
            @elseif($order->closed)
              Order close
            @else
              Unpaid
            @endif
          </div>
        </div>

        <!-- Pay btn start -->
@if(!$order->paid_at && !$order->closed)
<div class="payment-buttons">
  <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">Alipay</a>
</div>
@endif
<!-- pay btn end -->
<!-- Show Confirmed Goods Receipt button if the order's shipping status is Shipped -->
        @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
        <div class="receive-button">
          <button type="button" id="btn-receive" class="btn btn-sm btn-success">Confirm Receipt</button>
        </div>
        @endif
@section('scriptsAfterJs')
<script>
  $(document).ready(function() {
    // 确认收货按钮点击事件
    $('#btn-receive').click(function() {
      // 弹出确认框
      swal({
        title: "确认已经收到商品？",
        icon: "warning",
        dangerMode: true,
        buttons: ['取消', '确认收到'],
      })
      .then(function(ret) {
        // 如果点击取消按钮则不做任何操作
        if (!ret) {
          return;
        }
        // ajax 提交确认操作
        axios.post('{{ route('orders.received', [$order->id]) }}')
          .then(function () {
            // 刷新页面
            location.reload();
          })
      });
    });

  });
</script>
@endsection
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection
