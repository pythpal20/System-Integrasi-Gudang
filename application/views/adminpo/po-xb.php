<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h4 class="h4 mb-1 text-gray-800"><?= $title; ?></h4>
    <hr class="sidebar-divider">
    <div class="row">
        <div class="col-lg-12 mb-2">
            <button class="btn btn-sm btn-rounded btn-primary tambah"><span class="fas fa-fw fa-plus"></span> PO Baru</button>
            <button class="btn btn-sm btn-rounded btn-success export float-right"><span class="fas fa-fw fa-file-export"></span> Export Data</button>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" style="background-color: #FFEFD5">
                            <a class="nav-link active" id="mwk-tab" data-toggle="tab" href="#mwk" role="tab" aria-controls="mwk" aria-selected="true">Mutli Wahana Kencana</a>
                        </li>
                        <li class="nav-item" style="background-color: #E6E6FA">
                            <a class="nav-link" id="mwm-tab" data-toggle="tab" href="#mwm" role="tab" aria-controls="mwm" aria-selected="false">Multi Wahana Makmur</a>
                        </li>
                        <li class="nav-item" style="background-color: #E0FFFF;">
                            <a class="nav-link" id="bak-tab" data-toggle="tab" href="#bak" role="tab" aria-controls="bak" aria-selected="false">Batavia Adimarga Kencana</a>
                        </li>
                        <li class="nav-item" style="background-color: #FFE4E1;">
                            <a class="nav-link" id="fci-tab" data-toggle="tab" href="#fci" role="tab" aria-controls="fci" aria-selected="false">Food Container Indonesia</a>
                        </li>
                        <li class="nav-item bg-gray-500">
                            <a class="nav-link" id="dtm-tab" data-toggle="tab" href="#dtm" role="tab" aria-controls="dtm" aria-selected="false">Dewata Titian Mas</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="mwk" role="tabpanel" aria-labelledby="mwk-tab">
                            <table class="table-responsive table" id="tablemwk" data-toggle="tablemwk" data-show-toggle="true" data-show-refresh="true" data-show-pagination-switch="true" data-show-columns="true" data-mobile-responsive="true" data-check-on-init="true" data-advanced-search="true" data-id-table="advancedTable" data-show-print="true" data-show-columns-toggle-all="true"></table>
                        </div>
                        <div class="tab-pane fade" id="mwm" role="tabpanel" aria-labelledby="mwm-tab">
                            <table class="table-responsive table" id="tablemwm" data-toggle="tablemwm" data-show-toggle="true" data-show-refresh="true" data-show-pagination-switch="true" data-show-columns="true" data-mobile-responsive="true" data-check-on-init="true" data-advanced-search="true" data-id-table="advancedTable" data-show-print="true" data-show-columns-toggle-all="true"></table>
                        </div>
                        <div class="tab-pane fade" id="bak" role="tabpanel" aria-labelledby="bak-tab">
                            <table class="table-responsive table" id="tablebak" data-toggle="tablebak" data-show-toggle="true" data-show-refresh="true" data-show-pagination-switch="true" data-show-columns="true" data-mobile-responsive="true" data-check-on-init="true" data-advanced-search="true" data-id-table="advancedTable" data-show-print="true" data-show-columns-toggle-all="true"></table>
                        </div>
                        <div class="tab-pane fade" id="fci" role="tabpanel" aria-labelledby="fci-tab">
                            <table class="table-responsive table" id="tablefci" data-toggle="tablefci" data-show-toggle="true" data-show-refresh="true" data-show-pagination-switch="true" data-show-columns="true" data-mobile-responsive="true" data-check-on-init="true" data-advanced-search="true" data-id-table="advancedTable" data-show-print="true" data-show-columns-toggle-all="true"></table>
                        </div>
                        <div class="tab-pane fade" id="dtm" role="tabpanel" aria-labelledby="dtm-tab">
                            <table class="table-responsive table" id="tabledtm" data-toggle="tabledtm" data-show-toggle="true" data-show-refresh="true" data-show-pagination-switch="true" data-show-columns="true" data-mobile-responsive="true" data-check-on-init="true" data-advanced-search="true" data-id-table="advancedTable" data-show-print="true" data-show-columns-toggle-all="true"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
<!-- start modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data PO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= base_url('adminpo/export'); ?>">
                    <div class="form-group">
                        <label for="namapt">Nama Perusahaan</label>
                        <select name="namapt" id="namapt" class="js-example-responsive form-control namapt" style="width: 100%">
                            <option value="">~ Pilih PT ~</option>
                            <?php foreach ($pt as $p) : ?>
                                <option value="<?= $p['id_perusahaan']; ?>"><?= $p['atasnama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggalawal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tglawal" name="tglawal">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggalakhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tglakhir" name="tglakhir">
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Download</button></form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Javascript -->
<script>
    $(document).ready(function() {
        $(".export").click(() => {
            $("#exportModal").modal('show');
        });

        $(".namapt").select2({
            placeholder: '= Pilih Perusahaan =',
            width: 'resolve',
            allowClear: 'true'
        });

        $(".tambah").click(() => {
            document.location.href="<?= base_url('adminpo/tambahpo');?>";
        });
    });
</script>