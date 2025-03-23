@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('stok/create') }}">Add</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barang Name</th>
                        <th>User</th>
                        <th>Stock Date</th>
                        <th>Stock Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    $(document).ready(function() {
        var dataStok = $('#table_stok').DataTable({
            serverSide: true, 
            ajax: {
                "url": "{{ url('stok/list') }}",
                "dataType": "json",
                "type": "POST",
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "barang_nama", className: "", orderable: true, searchable: true },
                { data: "user_id", className: "", orderable: true, searchable: true },
                { data: "stok_tanggal", className: "text-center", orderable: true, searchable: true },
                { data: "stok_jumlah", className: "text-right", orderable: true, searchable: true },
                { data: "action", className: "", orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush