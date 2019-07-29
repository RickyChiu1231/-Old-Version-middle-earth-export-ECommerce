@extends('layouts.app')
@section('title', 'Shipping Addresses list')

@section('content')
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="card panel-default">
        <div class="card-header">Shipping Addresses list
            <a href="{{ route('user_addresses.create') }}" class="float-right">Add new Shipping Address</a>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Name</th>
              <th>Address</th>
              <th>PostCode</th>
              <th>Phone</th>
              <th>Action</th>
            </tr>
            </thead>s
            <tbody>
            @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->zip }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                  <a href="{{ route('user_addresses.edit', ['user_address' => $address->id]) }}" class="btn btn-primary">Edit</a>

                <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">Delete</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @section('scriptsAfterJs')
<script>
$(document).ready(function() {
  // Deletebtn click event
  $('.btn-del-address').click(function() {

    var id = $(this).data('id');
    // use sweetalert
    swal({
        title: "Are you sure to delete this address？",
        icon: "warning",
        buttons: ['Cancel', 'Yes'],
        dangerMode: true,
      })
    .then(function(willDelete) {

      if (!willDelete) {
        return;
      }
      axios.delete('/user_addresses/' + id)
        .then(function () {
          location.reload();
        })
    });
  });
});
</script>
@endsection
@endsection
