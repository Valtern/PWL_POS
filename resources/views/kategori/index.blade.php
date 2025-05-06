@extends('layouts.template')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List of Categories</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-info">Import Categories</button>
            <a href="{{ url('/kategori/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Categories (XLSX)</a>
            <a href="{{ url('/kategori/export_pdf') }}" class="btn btn-primary"><i class="fa fa-file-pdf"></i> Export Categories (PDF)</a>
            <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Add Ajax Category</button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-sm table-striped table-hover" id="table-kategori">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
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

let tableKategori;
$(document).ready(function() {
    tableKategori = $('#table-kategori').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('kategori/list') }}",
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', width: '5%', orderable: false, searchable: false },
            { data: 'kategori_kode', name: 'kategori_kode', width: '20%' },
            { data: 'kategori_nama', name: 'kategori_nama', width: '50%' },
            { data: 'action', name: 'action', className: 'text-center', width: '25%', orderable: false, searchable: false }
        ]
    });

    // Search on Enter key
    $('#table-kategori_filter input').unbind().bind('keyup', function(e) {
        if (e.keyCode === 13) {
            tableKategori.search(this.value).draw();
        }
    });
});
</script>
@endpush
