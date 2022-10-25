<!-- style tambahan -->
<style>
    .teks::-webkit-input-placeholder {
        font-style: italic;
        color: darkgoldenrod;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h4 class="h4 mb-4 text-gray-800"><?= $title; ?></h4>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form create PO</h5>
                </div>
                <div class="card-body">
                    <div id="approveForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Acknowledge 2</label>
                                    <input type="text" name="ack2" id="ack2" class="form-control teks" placeholder="wajib diisi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Acknowledge 3</label>
                                    <input type="text" name="ack3" id="ack3" class="form-control teks" placeholder="wajib diisi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Acknowledge 4</label>
                                    <input type="text" name="ack4" id="ack4" class="form-control teks" placeholder="wajib diisi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Acknowledge 5</label>
                                    <input type="text" name="ack5" id="ack5" class="form-control teks" placeholder="wajib diisi">
                                </div>
                            </div>
                        </div>
                        <button id="next" class="btn btn-primary next1 float-right">NEXT <span class="fas fa-fw fa-angles-right"></span></button>
                    </div>
                    <!-- form header -->
                    <div id="box1">
                        <form id="formHeader">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="deliverto">Delivery to</label>
                                            <select name="namapt" id="namapt" class="form-control namapt" style="width: 100%;">
                                                <option value=""></option>
                                                <?php foreach ($pt as $p) : ?>
                                                    <option value="<?= $p['id_perusahaan']; ?>"><?= $p['atasnama'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="Tgl. PO">Tgl. PO</label>
                                            <input type="text" name="tglpo" id="tglpo" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label for="Delivery Required">Delivery Required</label>
                                            <input type="date" name="tglkrm" id="tglkrm" class="form-control" value="<?= date('Y-m-d',  strtotime("+2 day", strtotime(date("Y-m-d")))); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="keteragan">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan" cols="30" rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <button class="btn btn-warning back1"><span class="fas fa-fw fa-angles-left"></span> BACK</button>
                        <button id="next" class="btn btn-primary next2 float-right">NEXT <span class="fas fa-fw fa-angles-right"></span></button>
                    </div>
                    <!-- form detail item -->
                    <div id="box2">
                        <form id="form1" class="mt-3">
                            <div class="card">
                                <h6 class="card-header card-title">Item-1</h6>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-5">
                                            <label for="">SKU/ Description</label>
                                            <select name="sku" id="sku1" class="form-control sku1" style="width: 100%;">
                                                <option value="">SKU/ Model</option>
                                                <?php foreach ($bilom as $bi) : ?>
                                                    <option value="<?= $bi['id']; ?>"><?= $bi['sku_xb'] . ' | ' . $bi['sku_mk']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="qty">QTY</label>
                                            <input type="text" name="qty" id="qty1" class="form-control">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="harga">Harga</label>
                                            <input type="text" name="harga" id="harga1" class="form-control harga1">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="harga">Amount</label>
                                            <input type="text" name="amount" id="amount1" class="form-control amount1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="col mb-3">
                            <button class="btn btn-warning back2 m-1"><span class="fas fa-fw fa-angles-left"></span> BACK</button>
                            <button class="btn btn-success simpan float-right m-1"><span class="fas fa-fw fa-save"></span> SAVE</button>
                            <button class="btn btn-primary tambah float-right m-1"><span class="fas fa-fw fa-plus"></span> Add Item</button>
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
<script>
    $(document).ready(function() {
        $(".namapt").select2({
            placeholder: '= Pilih Perusahaan =',
            allowClear: 'true',
            width: 'resolve'
        });
    });
    $(document).ready(function() {
        // $("#box1").hide();
        // $("#box2").hide();
        // $(".next1").click(function() {
        //     var act1 = $("#ack2").val();
        //     var act2 = $("#ack3").val();
        //     var act3 = $("#ack4").val();
        //     var act4 = $("#ack5").val();

        //     if (act1 == '' || act2 == '' || act3 == '' || act4 == '') {
        //         swal.fire(
        //             'Galat',
        //             'Semua kolom Acknowledge 2-5 harus terisi',
        //             'warning'
        //         );
        //     } else {
        //         $("#approveForm").hide();
        //         $("#box1").show();
        //     }
        // });

        // $(".next2").click(function() {
        //     var namapt = $("#namapt").val();
        //     var tglpo = $("#tglpo").val();
        //     var tglkrm = $("#tglkrm").val();
        //     var keterangan = $("#keterangan").val();
        //     if (namapt == '' || tglpo == '' || tglkrm == '') {
        //         swal.fire(
        //             'Galat',
        //             'Semua kolom dalam Form wajib diisi',
        //             'warning'
        //         );
        //     } else {
        //         $("#box1").hide();
        //         $("#box2").show();
        //     }
        // });

        // $(".back2").click(function() {
        //     $("#box1").show();
        //     $("#box2").hide();
        // });
        // $(".back1").click(function() {
        //     $("#box1").hide();
        //     $("#approveForm").show();
        // });
    });
    $(document).ready(function() {
        $(".sku1").select2({
            allowClear: true,
            placeholder: 'SKU XB | SKU MK'
        });
        $("#sku1").change(function() {
            var sku = $("#sku1").val();
            // alert(sku)
            $.ajax({
                url: "<?= base_url('adminpo/getSkuXb') ?>",
                method: 'POST',
                dataType: "json",
                data: {
                    id: sku
                },
                success: function(data) {
                    var dump = data;
                    $("#harga1").val(dump.harga);
                    // console.log(data);
                }
            });
        });

        $("#qty1").keyup(function() {
            var harga   = $("#harga1").val();
            var qty     = $("#qty1").val();
            
            amount = parseInt(qty) * parseInt(harga);

            $("#amount1").val(amount);
        });
        $("#harga1").keyup(function() {
            var harga   = $("#harga1").val();
            var qty     = $("#qty1").val();
            
            amount = parseInt(qty) * parseInt(harga);

            $("#amount1").val(amount);
        });

        var index = 1;
        $(".tambah").click(function() {
            index++;
            var form = "form" + index;
            
        });
    });
</script>