@extends('layouts.template')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List of items</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/barang/import') }}')" class="btn btn-info">Import Goods</button>
            <a href="{{ url('/barang/export_excel') }}" class="btn btn-primary"><i class="fa fa-fileexcel"></i>Export Barang (XLSX)</a>
            <a href="{{ url('/barang/export_pdf') }}" class="btn btn-primary"><i class="fa fa-filepdf"></i>Export Barang (PDF)</a>
            <button onclick="modalAction('{{ url('barang/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Add Ajax</button>
        </div>
    </div>
    <div class="card-body">
        <!-- for Data filter -->
        <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select name="filter_kategori" class="form-control form-control-sm filter_kategori">
                                <option value="">- All -</option>
                                @isset($kategori)
                                    @foreach($kategori as $l)
                                        <option value="{{ $l->category_id }}">{{ $l->category_name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <small class="form-text text-muted">Item Category</small>
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

        <table class="table table-bordered table-sm table-striped table-hover" id="table-barang">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Purchase Price</th>
                    <th>Selling Price</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = ''){
        $('#myModal').load(url,function(){
            $('#myModal').modal('show');
        });
    }

    var tableItem;
$(document).ready(function(){
    tableItem = $('#table-barang').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{{ url('barang/list') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
                d.filter_kategori = $('.filter_kategori').val();
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
                data: "barang_kode",
                className: "",
                width: "10%",
                orderable: true,
                searchable: true
            },
            {
                data: "barang_nama",
                className: "",
                width: "37%",
                orderable: true,
                searchable: true
            },
            {
                data: "harga_beli",
                className: "",
                width: "10%",
                orderable: true,
                searchable: false,
                render: function(data, type, row){
                    return new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "harga_jual",
                className: "",
                width: "10%",
                orderable: true,
                searchable: false,
                render: function(data, type, row){
                    return new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "category.category_name",
                className: "",
                width: "14%",
                orderable: true,
                searchable: false,
                render: function(data, type, row) {
                    // Handle both object and direct name cases
                    if (typeof data === 'object') {
                        return data.category_name || '';
                    }
                    return data || '';
                }
            },
            {
                data: "action",
                className: "text-center",
                width: "14%",
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#table-barang_filter input').unbind().bind().on('keyup', function(e){
        if(e.keyCode == 13){ // enter key
            tableItem.search(this.value).draw();
        }
    });

    $('.filter_kategori').change(function(){
        tableItem.draw();
    });
});
</script>
@endpush
