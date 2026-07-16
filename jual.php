<?php
$tanggal_awal = set_value('tanggal_awal', date('Y-m-01'));
$tanggal_akhir = set_value('tanggal_akhir', date('Y-m-d'));

$q = esc_field(_get('q'));
$pg = new Paging();
$limit = 25;
$offset = $pg->get_offset($limit, _get('page'));

$from = " FROM tb_jual j INNER JOIN tb_user u ON u.kode_user=j.kode_user";

$where = " WHERE (faktur LIKE '%$q%') AND tanggal>='$tanggal_awal' AND tanggal<='$tanggal_akhir'";

$rows = $db->get_results("SELECT * $from $where ORDER BY id_jual DESC LIMIT $offset, $limit");
$no = $offset;

$jumrec = $db->get_var("SELECT COUNT(*) $from $where");

?>
<div class="page-header">
    <h1>Data penjualan (<?= tgl_indo($tanggal_awal) ?> - <?= tgl_indo($tanggal_akhir) ?>)</h1>
</div>
<div class="card mb-3">
    <div class="card-header">
        <form class="row row-cols-lg-auto g-1">
            <input type="hidden" name="m" value="jual" />
            <input type="hidden" name="page" value="<?= _get('page') ?>" />
            <div class="form-group">
                <input class="form-control" type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>" />
            </div>
            <div class="form-group">
                <input class="form-control" type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>" />
            </div>
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian. . ." name="q" value="<?= _get('q') ?>" />
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="fa fa-search"></span> Cari</button>
            </div>
            <div class="form-group">
                <a class="btn btn-primary <?= is_hidden('jual_tambah') ?>" href="?m=jual_tambah"><span class="fa fa-plus"></span> Tambah</a>
            </div>
            <!-- <div class="form-group ">
                <a class="btn btn-secondary" href="cetak.php?<?= $_SERVER['QUERY_STRING'] ?>" target="_blank"><span class="fa fa-print"></span> Cetak</a>
            </div> -->
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped m-0">
            <thead>
                <tr class="nw">
                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>Tanggal</th>
                    <th>Grantotal</th>
                    <th>Operator</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $total = 0;
            $total_laba = 0;
            foreach ($rows as $row) : $total += $row->grantotal;  ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->faktur ?></td>
                    <td><?= $row->tanggal ?></td>
                    <td><?= number_format($row->grantotal) ?></td>
                    <td><?= $row->nama_user ?></td>
                    <td>
                        <a class="btn btn-sm btn-warning" href="?m=barang_ubah&ID=<?= $row->kode_barang ?>" hidden><span class="fa fa-edit"></span></a>
                        <a class="btn btn-sm btn-danger hidden" href="aksi.php?act=jual_hapus&ID=<?= $row->id_jual ?>&faktur=<?= $row->faktur ?>" onclick="return confirm('Hapus data?')"><span class="fa fa-trash"></span></a>
                        <a class="btn btn-sm btn-secondary" href="jual_nota.php?&ID=<?= $row->id_jual ?>" target="_blank"><span class="fa fa-print"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3">Total</td>
                <td><?= number_format($total) ?></td>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <?= $pg->show("m=jual&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&q=$q&page=", $jumrec, $limit, _get('page')) ?>
    </div>
</div>