@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Edit Data Pegawai</h5>
        </div>

        <div class="card-body">
            <form id="formEmployee" method="POST" action="{{ route('employee.update', $employee->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h5>Edit Pegawai</h5>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jabatan</label>
                        <select name="position" id="position" class="form-control">
                            <option value="">Pilih</option>
                            <option value="manager" {{ $employee->position == 'manager' ? 'selected' : '' }}>Manager
                            </option>
                            <option value="staff" {{ $employee->position == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ $employee->position == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tanggal Lahir</label>
                        <input type="text" name="birth_date" id="birth_date" class="form-control"
                            value="{{ $employee->birth_date }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Foto</label>
                        <div id="photoDropzone" class="dropzone"></div>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control">{{ $employee->address }}</textarea>
                    </div>

                    <input type="hidden" name="photo" id="photo" value="{{ $employee->photo }}">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#position').select2({
            width: '100%'
        });

        $('#birth_date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        Dropzone.autoDiscover = false;

        let uploadedFile = "{{ $employee->photo }}";

        let myDropzone = new Dropzone("#photoDropzone", {
            url: "{{ route('employee.upload') }}",
            paramName: "photo",
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            init: function() {
                @if ($employee->photo)
                    let mockFile = {
                        name: "Foto Lama",
                        size: 12345
                    };

                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "{{ $employee->photoUrl() }}");
                    this.emit("complete", mockFile);

                    this.files.push(mockFile);
                @endif
            },

            success: function(file, response) {
                uploadedFile = response.file;
                $('#photo').val(uploadedFile);
            },

            removedfile: function(file) {
                uploadedFile = null;
                $('#photo').val('');

                file.previewElement.remove();
            }
        });

        $("#formEmployee").validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                position: {
                    required: true
                },
                birth_date: {
                    required: true
                }
            },

            submitHandler: function(form) {
                if (!uploadedFile) {
                    alert('Foto wajib diupload');
                    return false;
                }

                $('#photo').val(uploadedFile);

                form.submit();
            }
        });
    </script>
@endpush
