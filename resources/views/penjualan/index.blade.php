@extends('layouts.master')

@section('title')
    Daftar Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Penjualan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered table-penjualan">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Kode Member</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Diskon</th>
                            <th>Total Bayar</th>
                            <th>Kasir</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                            <th>Aksi</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @includeIf('penjualan.detail')
@endsection

@push('scripts')
    <script>
        let table, table1;

        $(function() {
            table = $('.table-penjualan').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('transaksi.data') }}',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'created_at',
                        name: 'tanggal',
                        render: function(data) {
                            if (!data) return '';
                            const d = new Date(data);
                            const day = String(d.getDate()).padStart(2, '0');
                            const month = String(d.getMonth() + 1).padStart(2, '0');
                            const year = d.getFullYear();
                            return `${day}-${month}-${year}`;
                        }
                    },
                    {
                        data: 'kode_member'
                    },
                    {
                        data: 'total_item'
                    },
                    {
                        data: 'total_harga'
                    },
                    {
                        data: 'diskon'
                    },
                    {
                        data: 'bayar'
                    },
                    {
                        data: 'kasir'
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return data ?? '';
                        }
                    }
                ]
            });

            table1 = $('.table-detail').DataTable({
                processing: true,
                bSort: false,
                dom: 'Brt',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kode_produk'
                    },
                    {
                        data: 'nama_produk'
                    },
                    {
                        data: 'harga_jual'
                    },
                    {
                        data: 'jumlah'
                    },
                    {
                        data: 'subtotal'
                    },
                    {
                        data: 'aksi',
                        searchable: false,
                        sortable: false,
                        render: function(data, type, row) {
                            let url = '{{ route('transaksi.nota_kecil_recap', ':id') }}'.replace(
                                ':id',
                                row.id_penjualan);
                            return `
                                <div class="btn-group">
                                    <a href="${url}" target="_blank" class="btn btn-info btn-xs">
                                        <i class="fa fa-file-text-o"></i> Lihat Nota
                                    </a>
                                </div>
                            `;
                        }
                    }
                ]
            });
        });

        function showDetail(url) {
            $('#modal-detail').modal('show');
            table1.ajax.url(url).load();
        }

        function deleteData(url) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                        '_token': $('[name=csrf-token]').attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }
    </script>
@endpush
