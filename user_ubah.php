<?php
$row = $db->get_row("SELECT * FROM tb_user WHERE kode_user='$_GET[ID]'");
?>
<div class="page-header">
    <h1>Ubah user</h1>
</div>
<form method="post">
    <div class="row">
        <div class="col-sm-6">
            <?php if ($_POST) include 'aksi.php' ?>
            <div class="mb-3">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode_user" value="<?= set_value('kode_user', $row->kode_user) ?>" readonly="" />
            </div>
            <div class="mb-3">
                <label>Nama <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama_user" value="<?= set_value('nama_user', $row->nama_user) ?>" />
            </div>
            <div class="mb-3">
                <label>Username <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="user" value="<?= set_value('user', $row->user) ?>" />
            </div>
            <div class="mb-3">
                <label>Pass <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="pass" value="<?= set_value('pass', $row->pass) ?>" />
            </div>
            <div class="mb-3">
                <label>Level <span class="text-danger">*</span></label>
                <select class="form-select" name="level">
                    <?= get_level_option('level', $row->level) ?>
                </select>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary"><span class="fa fa-save"></span> Simpan</button>
                <a class="btn btn-danger" href="?m=user"><span class="fa fa-arrow-left"></span> Kembali</a>
            </div>
        </div>
    </div>
</form>