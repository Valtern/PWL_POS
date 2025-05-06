@extends('layouts.template')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List of Users</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/user/import') }}')" class="btn btn-info">Import Users</button>
            <a href="{{ url('/user/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Users (XLSX)</a>
            <a href="{{ url('/user/export_pdf') }}" class="btn btn-primary"><i class="fa fa-file-pdf"></i> Export Users (PDF)</a>
            <button onclick="modalAction('{{ url('/user/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Add Ajax User</button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter by Level -->
        <div id="filter" class="form-horizontal filter-level p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_level" class="col-md-2 col-form-label">Level</label>
                        <div class="col-md-6">
                            <select name="filter_level" class="form-control form-control-sm filter_level">
                                <option value="">- All -</option>
                                @isset($levels)
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl->level_id }}">{{ $lvl->level_nama }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <small class="form-text text-muted">User Role Level</small>
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

        <table class="table table-bordered table-sm table-striped table-hover" id="table-user">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Level</th>
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

    let tableUser;
    $(document).ready(function() {
        tableUser = $('#table-user').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('user/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.filter_level = $('.filter_level').val();
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false },
                { data: "username", name: "username", width: "25%" },
                { data: "nama", name: "nama", width: "30%" },
                { data: "level.level_nama", name: "level_nama", width: "25%" },
                { data: "action", name: "action", className: "text-center", width: "15%", orderable: false, searchable: false }
            ]
        });

        // Search on Enter key
        $('#table-user_filter input').unbind().bind('keyup', function(e) {
            if (e.keyCode === 13) {
                tableUser.search(this.value).draw();
            }
        });

        // Redraw on level filter change
        $('.filter_level').change(function() {
            tableUser.draw();
        });
    });
</script>
@endpush
