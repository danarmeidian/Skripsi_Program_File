<?php
$tanggal_awal = set_value('tanggal_awal', date('Y-m-01'));
$tanggal_akhir = set_value('tanggal_akhir', date('Y-m-d'));

$q = esc_field(_get('q'));
$pg = new Paging();
$limit = 25;
$offset = $pg->get_offset($limit, _get('page'));

$from = " FROM tb_jual_detail d INNER JOIN tb_produk p ON p.kode_produk=d.kode_produk INNER JOIN tb_jual j ON j.id_jual=d.id_jual INNER JOIN tb_user u ON u.kode_user=j.kode_user";

$where = " WHERE (nama_produk LIKE '%$q%' OR p.kode_produk LIKE '%$q%' OR nama_user LIKE '%$q%') AND tanggal>='$tanggal_awal' AND tanggal<='$tanggal_akhir'";

$rows = $db->get_results("SELECT * $from $where ORDER BY tanggal DESC LIMIT $offset, $limit");
$no = $offset;

$jumrec = $db->get_var("SELECT COUNT(*) $from $where");

?>
<div class="page-header">
    <h1>Laporan Penjualan (<?= tgl_indo($tanggal_awal) ?> - <?= tgl_indo($tanggal_akhir) ?>)</h1>
</div>
<div class="card mb-3">
    <div class="card-header">
        <form class="row row-cols-lg-auto g-1">
            <input type="hidden" name="m" value="produk_keluar" />
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
            <div class="form-group ">
                <a class="btn btn-secondary" href="cetak.php?<?= $_SERVER['QUERY_STRING'] ?>" target="_blank"><span class="fa fa-print"></span> Cetak</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped m-0">
            <thead>
                <tr class="nw">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <?php
            $total = 0;
            $total_laba = 0;
            foreach ($rows as $row) : $total += $row->grantotal;  ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->tanggal ?></td>
                    <td><?= $row->kode_produk ?></td>
                    <td><?= $row->nama_produk ?></td>
                    <td><?= number_format($row->jumlah) ?></td>
                    <td><?= number_format($row->harga) ?></td>
                    <td><?= number_format($row->subtotal) ?></td>
                    <td><?= $row->nama_user ?></td>
                </tr>
            <?php endforeach?>
        </table>
    </div>
    <div class="card-footer">
        <?= $pg->show("m=produk_keluar&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&q=$q&page=", $jumrec, $limit, _get('page')) ?>
    </div>
</div>