<?php
$row = $db->get_row("SELECT * FROM tb_produk WHERE kode_produk='$_GET[ID]'");
?>
<div class="page-header">
    <h1>Ubah Produk</h1>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php if ($_POST) include 'aksi.php' ?>
        <form method="post">
            <div class="mb-3">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode_produk" readonly="readonly" value="<?= $row->kode_produk ?>" />
            </div>
            <div class="mb-3">
                <label>Nama <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama_produk" value="<?= set_value('nama_produk', $row->nama_produk) ?>" />
            </div>
            <div class="mb-3">
                <label>Harga <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="harga" value="<?= set_value('harga', $row->harga) ?>" />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary"><span class="fa fa-save"></span> Simpan</button>
                <a class="btn btn-danger" href="?m=produk"><span class="fa fa-arrow-left"></span> Kembali</a>
            </div>
        </form>
    </div>
</div>