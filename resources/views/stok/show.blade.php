@extends('layouts.template') 

@section('content')
  <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools"></div>
      </div>
      <div class="card-body">
        @empty ($stok)
            <div class="alert alert-danger alert-dismissible">
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                The data you are looking for is not found.
            </div>
        @else
            <table class="table table-bordered table-striped table-hover table-sm">
                <tr>
                    <th>ID</th>
                    <td>{{ $stok->stok_id }}</td>
                </tr>
                <tr>
                    <th>Barang</th>
                    <td>{{ $stok->barang->barang_nama }}</td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>{{ $stok->stok_jumlah }}</td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td>{{ $stok->stok_tanggal ? $stok->stok_tanggal->format('d-m-Y') : 'No Date Available' }}</td>
                </tr>
            </table>
        @endempty
        <a href="{{ url('stok') }}" class="btn btn-sm btn-default mt-2">Return</a>
    </div>
  </div>
@endsection

@push('css')
@endpush

@push('js') 
@endpush
