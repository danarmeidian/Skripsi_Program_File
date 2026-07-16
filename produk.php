<div class="page-header">
    <h1>Produk</h1>
</div>
<div class="card mb-3">
    <div class="card-header">
        <form class="row row-cols-lg-auto g-1">
            <input type="hidden" name="m" value="produk" />
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian. . ." name="q" value="<?= _get('q') ?>" />
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="fa fa-refresh"></span> Refresh</button>
            </div>
            <div class="form-group">
                <a class="btn btn-primary" href="?m=produk_tambah"><span class="fa fa-plus"></span> Tambah</a>
            </div>
        </form>
    </div>
    <table class="table table-bordered table-hover table-striped m-0">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <?php
        $q = esc_field(_get('q'));
        $rows = $db->get_results("SELECT * FROM tb_produk WHERE nama_produk LIKE '%$q%' ORDER BY kode_produk");
        $no = 0;
        foreach ($rows as $row) :  ?>
            <tr>
                <td><?= $row->kode_produk ?></td>
                <td><?= $row->nama_produk ?></td>
                <td><?= number_format($row->harga) ?></td>
                <td>
                    <a class="btn btn-sm btn-warning" href="?m=produk_ubah&ID=<?= $row->kode_produk ?>"><span class="fa fa-edit"></span></a>
                    <a class="btn btn-sm btn-danger" href="aksi.php?act=produk_hapus&ID=<?= $row->kode_produk ?>" onclick="return confirm('Hapus data?')"><span class="fa fa-trash"></span></a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>