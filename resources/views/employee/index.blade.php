@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Pegawai</h5>
            <a href="{{ route('employee.create') }}" class="btn btn-primary">Tambah Pegawai</a>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search_name" class="form-control" placeholder="Cari nama...">
                </div>

                <div class="col-md-4">
                    <select id="filter_position" class="form-control select2">
                        <option value="all">Semua Jabatan</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Tanggal Lahir</span>
                        <input type="text" class="form-control" id="filter_date" placeholder="Pilih tanggal">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="alert">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <table id="employeeTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Jabatan</th>
                        <th>Tanggal Lahir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {

            $('#filter_position').select2();

            $('#filter_date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            let startDate = '';
            let endDate = '';

            $('#filter_date').on('apply.daterangepicker', function(ev, picker) {
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');

                $(this).val(startDate + ' - ' + endDate);

                table.draw();
            });

            $('#filter_date').on('cancel.daterangepicker', function() {
                $(this).val('');
                startDate = '';
                endDate = '';

                table.draw();
            });

            let table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '/employee/list',
                    data: function(d) {
                        d.search = $('#search_name').val();
                        d.position = $('#filter_position').val();
                        d.start_date = startDate;
                        d.end_date = endDate;
                    }
                },
                columns: [{
                        data: 'photo',
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'birth_date'
                    },
                    {
                        data: 'action',
                        orderable: false
                    }
                ]
            });

            $('#search_name, #filter_position').on('keyup change', function() {
                table.draw();
            });

            $(document).on('click', '.btn-delete', function() {
                const name = $(this).data('name');
                const deleteUrl = $(this).data('route');

                Swal.fire({
                    title: 'Yakin?',
                    text: "Data " + name + " akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.',
                                        'success')
                                    .then(() => table.draw());
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                            }
                        });

                    }
                });
            });
        });
    </script>
@endpush
