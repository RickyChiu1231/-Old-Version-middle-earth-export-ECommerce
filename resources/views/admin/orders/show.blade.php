<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">Order No.：{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>Buyer：</td>
        <td>{{ $order->user->name }}</td>
        <td>Paid At：</td>
        <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
      </tr>
      <tr>
        <td>Payment Method：</td>
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
          <td>￥{{ $item->price }}</td>
          <td>{{ $item->amount }}</td>
        </tr>
      @endforeach
      <tr>
        <td>Total Price：</td>
        <td>￥{{ $order->total_amount }}</td>
        <td>Shipment Status：</td>
        <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
      </tr>


      @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)

        @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_SUCCESS)
        <tr>
          <td colspan="4">
            <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">

              {{ csrf_field() }}
              <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                <label for="express_company" class="control-label">Express Company</label>
                <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="Input Express Company Name">
                @if($errors->has('express_company'))
                  @foreach($errors->get('express_company') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                <label for="express_no" class="control-label">Shipment No.</label>
                <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="Input Tracking No.">
                @if($errors->has('express_no'))
                  @foreach($errors->get('express_no') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <button type="submit" class="btn btn-success" id="ship-btn">Ship</button>
            </form>
          </td>
        </tr>

        @endif
      @else

        <tr>
          <td>Express Company：</td>
          <td>{{ $order->ship_data['express_company'] }}</td>
          <td>Tracking No.：</td>
          <td>{{ $order->ship_data['express_no'] }}</td>
        </tr>
      @endif

      @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
        <tr>
          <td>Rufund status：</td>
          <td colspan="2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}，Reason：{{ $order->extra['refund_reason'] }}</td>
          <td>
            <!-- Show processing button if the order refund status is already applied -->
            @if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED)
              <button class="btn btn-sm btn-success" id="btn-refund-agree">Agree</button>
              <button class="btn btn-sm btn-danger" id="btn-refund-disagree">Disagree</button>
            @endif
          </td>
        </tr>
      @endif
      </tbody>
    </table>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Disagree button click event
    $('#btn-refund-disagree').click(function() {
      swal({
        title: 'Enter the reason for rejecting the refund',
        input: 'text',
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
        showLoaderOnConfirm: true,
        preConfirm: function(inputValue) {
          if (!inputValue) {
            swal('Rejecting Reason cannot be null', '', 'error')
            return false;
          }
          // Laravel-Admin does not have axios, requesting using jQuery's ajax method
          return $.ajax({
            url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
            type: 'POST',
            data: JSON.stringify({   // Turn the request into a JSON string
              agree: false,  // Refuse application
              reason: inputValue,
              _token: LA.token,
            }),
            contentType: 'application/json',  // The requested data format is JSON
          });
        },
        allowOutsideClick: () => !swal.isLoading()
      }).then(function (ret) {
        // If the user clicks the "Cancel" button, no action is taken.
        if (ret.dismiss === 'cancel') {
          return;
        }
        swal({
          title: 'Success',
          type: 'success'
        }).then(function() {
          // reload page
          location.reload();
        });
      });
    });

    // Agree button click event
    $('#btn-refund-agree').click(function() {
      swal({
        title: 'Are you sure to refund the order?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return $.ajax({
            url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
            type: 'POST',
            data: JSON.stringify({
              agree: true, // Means agrees to refund
              _token: LA.token,
            }),
            contentType: 'application/json',
          });
        }
      }).then(function (ret) {
        // If the user clicks the "Cancel" button, no action is taken.
        if (ret.dismiss === 'cancel') {
          return;
        }
        swal({
          title: 'Success',
          type: 'success'
        }).then(function() {
          // reload the page
          location.reload();
        });
      });
    });
  });
</script>
