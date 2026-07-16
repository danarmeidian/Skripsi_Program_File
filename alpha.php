<div class="page-header">
    <h1>Perhitungan Alpha</h1>
</div>
<?php foreach ($PRODUK as $kode_produk => $produk) : ?>
    <div class="card mb-3">
        <div class="card-header">
            Nilai Alpha Terbaik <?= $produk->nama_produk ?>
        </div>
        <table class="table table-bordered table-hover table-striped m-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Alpha</th>
                    <th>MAPE</th>
                </tr>
            </thead>
            <?php
            $rows = $db->get_results("SELECT tanggal, SUM(jumlah) AS jumlah FROM tb_jual j INNER JOIN tb_jual_detail d ON d.id_jual=j.id_jual WHERE kode_produk='$kode_produk' GROUP BY YEAR(tanggal), MONTH(tanggal) ORDER BY tanggal");
            $penjualan = array();
            foreach ($rows as $row) {
                $penjualan[$row->tanggal] = $row->jumlah * 1;
                $last_periode = $row->tanggal;
            }

            $db->query("DELETE FROM tb_alpha WHERE kode_produk='$kode_produk'");
            for ($alpha = 0.1; $alpha <= 0.9; $alpha += 0.1) {
                $f = new DES($penjualan, $alpha, 1);
                $db->query("INSERT INTO tb_alpha (kode_produk, alpha, mape) VALUES ('$kode_produk', '$alpha', '{$f->mape}')");
            }
            $q = esc_field(_get('q'));
            $rows = $db->get_results("SELECT * FROM tb_alpha WHERE kode_produk='$kode_produk' ORDER BY mape");
            $no = 0;
            $terbaik = $db->get_row("SELECT * FROM tb_alpha WHERE kode_produk='$kode_produk' ORDER BY mape LIMIT 1");
            foreach ($rows as $row) :  ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->alpha ?></td>
                    <td><?= round($row->mape, 4) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <div class="card-footer">
            Alpha Terbaik: <?= $terbaik->alpha ?>, dengan hasil MAPE: <?= round($terbaik->mape, 4) ?>
        </div>
    </div>
<?php endforeach ?>