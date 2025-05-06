@extends('layouts.template')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List of Stock Items</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stock</button>
            <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stock (XLSX)</a>
            <a href="{{ url('/stok/export_pdf') }}" class="btn btn-primary"><i class="fa fa-file-pdf"></i> Export Stock (PDF)</a>
            <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Add Ajax Stock</button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter by Item -->
        <div id="filter" class="form-horizontal filter-barang p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_barang" class="col-md-2 col-form-label">Item</label>
                        <div class="col-md-6">
                            <select name="filter_barang" class="form-control form-control-sm filter_barang">
                                <option value="">- All -</option>
                                @isset($barang)
                                    @foreach($barang as $brg)
                                        <option value="{{ $brg->barang_id }}">{{ $brg->barang_nama }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <small class="form-text text-muted">Filter by Item</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    let tableStok;
    $(document).ready(function() {
        tableStok = $('#table-stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('stok/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.filter_barang = $('.filter_barang').val();
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    className: "text-center",
                    width: "5%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "barang.barang_nama",
                    name: "barang_nama",
                    width: "25%",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "user.nama",
                    name: "user_nama",
                    width: "20%",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "stok_tanggal",
                    name: "stok_tanggal",
                    width: "20%",
                    render: function(data) {
                        return new Date(data).toLocaleString();
                    }
                },
                {
                    data: "stok_jumlah",
                    name: "stok_jumlah",
                    className: "text-right",
                    width: "15%",
                    render: function(data) {
                        return new Intl.NumberFormat().format(data);
                    }
                },
                {
                    data: "action",
                    name: "action",
                    className: "text-center",
                    width: "15%",
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[3, 'desc']] // Default sort by date
        });

        // Search on Enter key
        $('#table-stok_filter input').unbind().bind('keyup', function(e) {
            if (e.keyCode === 13) {
                tableStok.search(this.value).draw();
            }
        });

        // Redraw on item filter change
        $('.filter_barang').change(function() {
            tableStok.draw();
        });
    });
</script>
@endpush
