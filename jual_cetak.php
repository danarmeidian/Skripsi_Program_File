<?php
$tanggal_awal = set_value('tanggal_awal', date('Y-m-01'));
$tanggal_akhir = set_value('tanggal_akhir', date('Y-m-d'));
$from = " FROM tb_jual j INNER JOIN tb_user u ON u.kode_user=j.kode_user";
$where = " WHERE tanggal>='$tanggal_awal' AND tanggal<='$tanggal_akhir'";
$rows = $db->get_results("SELECT * $from $where ORDER BY id_jual DESC");
$no = 0;
?>
<h1>Data penjualan (<?= tgl_indo($tanggal_awal) ?> - <?= tgl_indo($tanggal_akhir) ?>)</h1>
<table class="table">
    <thead>
        <tr class="nw">
            <th>No</th>
            <th>No Transaksi</th>
            <th>Tanggal</th>
            <th>Grantotal</th>
            <th>Operator</th>
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
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3">Total</td>
        <td><?= number_format($total) ?></td>
        <td colspan="3"></td>
    </tr>
</table>