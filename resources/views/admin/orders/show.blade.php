<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">Order No.：{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>Order List</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>Customer Name：</td>
        <td>{{ $order->user->name }}</td>
        <td>Pay At：</td>
        <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
      </tr>
      <tr>
        <td>payment method：</td>
        <td>{{ $order->payment_method }}</td>
        <td>Payment No.：</td>
        <td>{{ $order->payment_no }}</td>
      </tr>
      <tr>
        <td>Shipping Address</td>
        <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
      </tr>
      <tr>
        <td rowspan="{{ $order->items->count() + 1 }}">Product List</td>
        <td>Item Name</td>
        <td>Price</td>
        <td>Qty</td>
      </tr>
      @foreach($order->items as $item)
      <tr>
        <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
        <td>${{ $item->price }}</td>
        <td>{{ $item->amount }}</td>
      </tr>
      @endforeach
      <tr>
        <td>Total Price：</td>
        <td>${{ $order->total_amount }}</td>

        <!-- Add shipment status here -->
        <td>Shipment status：</td>
        <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
      </tr>
      <!-- Order shipment -->
      <!-- Show delivery form if the order is not shipped -->
      @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
      <tr>
        <td colspan="4">
          <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">

            {{ csrf_field() }}
            <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
              <label for="express_company" class="control-label">Express Company</label>
              <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="Input express company">
              @if($errors->has('express_company'))
                @foreach($errors->get('express_company') as $msg)
                  <span class="help-block">{{ $msg }}</span>
                @endforeach
              @endif
            </div>
            <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
              <label for="express_no" class="control-label">Tracking No.</label>
              <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="Input Tracking No.">
              @if($errors->has('express_no'))
                @foreach($errors->get('express_no') as $msg)
                  <span class="help-block">{{ $msg }}</span>
                @endforeach
              @endif
            </div>
            <button type="submit" class="btn btn-success" id="ship-btn">Save</button>
          </form>
        </td>
      </tr>
      @else
      <!-- Or show the express company and the express No -->
      <tr>
        <td>Express Company：</td>
        <td>{{ $order->ship_data['express_company'] }}</td>
        <td>Tracking No.：</td>
        <td>{{ $order->ship_data['express_no'] }}</td>
      </tr>
      @endif
      </tbody>
    </table>
  </div>
</div>
