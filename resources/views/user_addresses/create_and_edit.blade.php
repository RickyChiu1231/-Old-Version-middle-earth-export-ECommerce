@extends('layouts.app')
@section('title', ($address->id ? 'Edit': 'Add') . 'Shipping Address')

@section('content')
<div class="row">
<div class="col-md-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h2 class="text-center">
  {{ $address->id ? 'Edit': 'Add' }} Shipping Address
    </h2>
  </div>
  <div class="card-body">

    @if (count($errors) > 0)
      <div class="alert alert-danger">
        <h4>Error：</h4>
        <ul>
          @foreach ($errors->all() as $error)
            <li><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <user-addresses-create-and-edit inline-template>
        @if($address->id)
    <form class="form-horizontal" role="form" action="{{ route('user_addresses.update', ['user_address' => $address->id]) }}" method="post">
      {{ method_field('PUT') }}
  @else
      <form class="form-horizontal" role="form" action="{{ route('user_addresses.store') }}" method="post">
        @endif
        <!-- 引入 csrf token 字段 -->
      {{ csrf_field() }}
      <!-- 注意这里多了 @change -->
        <select-district :init-value="{{ json_encode([old('province', $address->province), old('city', $address->city), old('district', $address->district)]) }}" @change="onDistrictChanged" inline-template>
          <div class="form-group row">
            <label class="col-form-label col-sm-2 text-md-right">Select Province-City-District</label>
            <div class="col-sm-3">
              <select class="form-control" v-model="provinceId">
                <option value="">Select province</option>
                <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
              </select>
            </div>
            <div class="col-sm-3">
              <select class="form-control" v-model="cityId">
                <option value="">Select city</option>
                <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
              </select>
            </div>
            <div class="col-sm-3">
              <select class="form-control" v-model="districtId">
                <option value="">select district</option>
                <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
              </select>
            </div>
          </div>
        </select-district>
        <!-- 插入了 3 个隐藏的字段 -->
        <!-- 通过 v-model 与 user-addresses-create-and-edit 组件里的值关联起来 -->
        <!-- 当组件中的值变化时，这里的值也会跟着变 -->
        <input type="hidden" name="province" v-model="province">
        <input type="hidden" name="city" v-model="city">
        <input type="hidden" name="district" v-model="district">
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Detail Address</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Postcode</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="zip" value="{{ old('zip', $address->zip) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Name</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Phone</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
          </div>
        </div>
        <div class="form-group row text-center">
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </div>
      </form>
    </user-addresses-create-and-edit>
  </div>
</div>
</div>
</div>
@endsection
