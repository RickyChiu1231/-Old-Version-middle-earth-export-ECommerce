@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>Price</label><em>$</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">Total Sell <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">Total Review <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="Rating {{ $product->rating }}">Rating <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>Select</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
      @foreach($product->skus as $sku)
       <label
      class="btn sku-btn"
      data-price="{{ $sku->price }}"
      data-stock="{{ $sku->stock }}"
      data-toggle="tooltip"
      title="{{ $sku->description }}"
      data-placement="bottom">
    <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
        </label>
        @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>Qty</label><input type="text" class="form-control form-control-sm" value="1"><span></span><span class="stock"></span></div>
        <div class="buttons">
          @if($favored)
            <button class="btn btn-danger btn-disfavor">Dislike</button>
          @else
            <button class="btn btn-success btn-favor">❤ Like</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">Add to Cart</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">Product Specification</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">User Review</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
      $('.sku-btn').click(function () {
        $('.product-info .price span').text($(this).data('price'));
        $('.product-info .stock').text('Instock：' + $(this).data('stock') + '件');
      });
      // 监听收藏按钮的点击事件
      $('.btn-favor').click(function () {
        // 发起一个 post ajax 请求，请求 url 通过后端的 route() 函数生成。
        axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
          .then(function () { // 请求成功会执行这个回调
            swal('Success', '', 'success')
              .then(function () {  // 这里加了一个 then() 方法
                location.reload();
              });
          }, function(error) { // 请求失败会执行这个回调
            // 如果返回码是 401 代表没登录
            if (error.response && error.response.status === 401) {
              swal('Please sign in', '', 'error');
            } else if (error.response && error.response.data.msg) {
              // 其他有 msg 字段的情况，将 msg 提示给用户
              swal(error.response.data.msg, '', 'error');
            }  else {
              // 其他情况应该是系统挂了
              swal('system error', '', 'error');
            }
          });
      });
      $('.btn-disfavor').click(function () {
        axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
          .then(function () {
            swal('Dislike Success', '', 'success')
              .then(function () {
                location.reload();
              });
          });
      });
      // 加入购物车按钮点击事件
      $('.btn-add-to-cart').click(function () {
        // 请求加入购物车接口
        axios.post('{{ route('cart.add') }}', {
          sku_id: $('label.active input[name=skus]').val(),
          amount: $('.cart_amount input').val(),
        })
          .then(function () { // 请求成功执行此回调
            swal('Item successfully add to cart', '', 'success')
            .then(function() {
          location.href = '{{ route('cart.index') }}';
        });
          }, function (error) { // 请求失败执行此回调
            if (error.response.status === 401) {
              // http 状态码为 401 代表用户未登陆
              swal('Please sign in', '', 'error');
            } else if (error.response.status === 422) {
              // http 状态码为 422 代表用户输入校验失败
              var html = '<div>';
              _.each(error.response.data.errors, function (errors) {
                _.each(errors, function (error) {
                  html += error+'<br>';
                })
              });
              html += '</div>';
              swal({content: $(html)[0], icon: 'error'})
            } else {
              // 其他情况应该是系统挂了
              swal('system error', '', 'error');
            }
          })
      });
    });
  </script>
@endsection
