<div class="page-header">
    <h1>Peramalan Double Exponential Smoothing</h1>
</div>
<div class="card mb-3">
    <div class="card-header">
        Pengaturan
    </div>
    <div class="card-body">
        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Produk <span class="text-danger">*</span></label>
                        <select class="form-select" name="kode_produk">
                            <?= get_produk_option(set_value('kode_produk')) ?>
                        </select>
                    </div>
                    <div class="mb-3" hidden>
                        <label>Periode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="n_periode" value="<?= set_value('n_periode', 1) ?>" />
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary"> <span class="fa fa-signal"></span> Hitung</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
if ($_POST) {
    $n_periode = $_POST['n_periode'];
    $kode_produk = $_POST['kode_produk'];

    if ($n_periode == '' || $kode_produk == '') {
        print_msg('Field bertanda * tidak boleh kosong!');
    } else {
        // Check if a record with the same periode and kode_produk already exists
        $existing_record = $db->get_row("SELECT * FROM tb_hasil WHERE kode_produk = '$kode_produk' AND tanggal = DATE_ADD(LAST_DAY(NOW()), INTERVAL 0 DAY)"); 

        if (!$existing_record) { 
            // If no record exists, proceed with the calculation and insertion
            include 'des_hasil.php'; 
        } else {
            print_msg('Data peramalan untuk periode ini sudah ada.', 'warning');
        }
    }
}

?>