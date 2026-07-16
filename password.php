<div class="page-header">
    <h1>Ubah Password</h1>
</div>
<div class="row">
    <div class="col-sm-5">
        <?php if ($_POST) include 'aksi.php' ?>
        <form method="post">
            <div class="mb-3">
                <label>Password Lama <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="pass1" />
            </div>
            <div class="mb-3">
                <label>Password Baru <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="pass2" />
            </div>
            <div class="mb-3">
                <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="pass3" />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary"><span class="fa fa-save"></span> Simpan</button>
            </div>
        </form>
    </div>
</div>