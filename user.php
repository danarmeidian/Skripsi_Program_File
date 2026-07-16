<div class="page-header">
    <h1>User</h1>
</div>
<div class="card card-default">
    <div class="card-header">
        <form class="row row-cols-lg-auto g-1">
            <input type="hidden" name="m" value="user" />
            <div class="col">
                <input class="form-control" type="text" placeholder="Pencarian. . ." name="q" value="<?= _get('q') ?>" />
            </div>
            <div class="col">
                <button class="btn btn-success"><span class="fa fa-search"></span> Cari</button>
            </div>
            <div class="col">
                <a class="btn btn-primary" href="?m=user_tambah"><span class="fa fa-plus"></span> Tambah</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped m-0">
            <thead>
                <tr class="nw">
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>User</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $q = esc_field(_get('q'));

            $rows = $db->get_results("SELECT * FROM tb_user WHERE nama_user LIKE '%$q%' ORDER BY kode_user");
            $no = 0;

            foreach ($rows as $row) : ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->kode_user ?></td>
                    <td><?= $row->nama_user ?></td>
                    <td><?= $row->user ?></td>
                    <td><?= $row->level ?></td>
                    <td class="nw">
                        <a class="btn btn-sm btn-warning" href="?m=user_ubah&ID=<?= $row->kode_user ?>"><span class="fa fa-edit"></span></a>
                        <a class="btn btn-sm btn-danger" href="aksi.php?act=user_hapus&ID=<?= $row->kode_user ?>" onclick="return confirm('Hapus data?')"><span class="fa fa-trash"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>