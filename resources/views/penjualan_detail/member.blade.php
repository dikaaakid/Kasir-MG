<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih Member</h4>
            </div>
            <div class="modal-body">
                <!-- Card Container -->
                <div class="row">
                    @foreach ($member as $key => $item)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm">
                                <img src="https://via.placeholder.com/150" class="card-img-top" alt="Member Image">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->nama }}</h5>
                                    <p class="card-text">
                                        <strong>Telepon:</strong> {{ $item->telepon }}<br>
                                        <strong>Alamat:</strong> {{ $item->alamat }}
                                    </p>
                                    <button class="btn btn-primary btn-sm btn-block"
                                            onclick="pilihMember('{{ $item->id_member }}', '{{ $item->kode_member }}')">
                                        <i class="fa fa-check-circle"></i> Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
