<form action="{{ url('/barang/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Item Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label>
                    <a href="{{ asset('template_barang.xlsx') }}" class="btn btn-info btn-sm" download><i class="fa fa-file-excel"></i> Download</a>
                    <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Select File</label>
                    {{-- <input type="file" name="file_barang" id="file_barang" class="form-control" accept=".xlsx" required> --}}
                    <input type="file" name="file_barang" id="file_barang" class="form-control"
       accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    <small id="error-file_barang" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-import").validate({
        rules: {
            file_barang: {
                required: true,
                extension: "xlsx"
            },
        },
        submitHandler: function(form) {
            var formData = new FormData(form); // Make the form into FormData to handle the file

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData, // Data sent in the form of FormData
                processData: false, // setting processData and contentType to false, to handle the file
                contentType: false,
                success: function(response) {
                    if (response.status) { // if successful
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Successful',
                            text: response.message
                        });
                        tableItem.ajax.reload(); // Reload datatable
                    } else { // if there is an error
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'An Error Occurred',
                            text: response.message
                        });
                    }
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
