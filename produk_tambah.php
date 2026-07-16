<div class="page-header">
    <h1>Tambah Produk</h1>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php if ($_POST) include 'aksi.php' ?>
        <form method="post">
            <div class="mb-3">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode_produk" value="<?= set_value('kode_produk', kode_oto('kode_produk', 'tb_produk', 'J', 2)) ?>" />
            </div>
            <div class="mb-3">
                <label>Nama <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama_produk" value="<?= set_value('nama_produk') ?>" />
            </div>
            <div class="mb-3">
                <label>Harga <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="harga" value="<?= set_value('harga') ?>" />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary"><span class="fa fa-save"></span> Simpan</button>
                <a class="btn btn-danger" href="?m=produk"><span class="fa fa-arrow-left"></span> Kembali</a>
            </div>
        </form>
    </div>
</div>