<?php
$tanggal_awal = set_value('tanggal_awal', date('Y-m-01'));
$tanggal_akhir = set_value('tanggal_akhir', date('Y-m-d'));
$from = " FROM tb_jual_detail d INNER JOIN tb_produk p ON p.kode_produk=d.kode_produk INNER JOIN tb_jual j ON j.id_jual=d.id_jual INNER JOIN tb_user u ON u.kode_user=j.kode_user";
$where = " WHERE tanggal>='$tanggal_awal' AND tanggal<='$tanggal_akhir'";
$rows = $db->get_results("SELECT * $from $where ORDER BY tanggal DESC");
$no = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .double-underline {
            border-bottom: 2px solid black;
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
<h2 center>Laporan Penjualan Ramen Kaizenka</h2>
<p>(<?= tgl_indo($tanggal_awal) ?> - <?= tgl_indo($tanggal_akhir) ?>)</p>
<table class="table">
    <thead>
        <tr class="nw">
            <th class="double-underline">No</th>
            <th class="double-underline">Tanggal</th>
            <th class="double-underline">Kode</th>
            <th class="double-underline">Nama</th>
            <th class="double-underline">Jumlah</th>
            <th class="double-underline">Harga</th>
            <th class="double-underline">Total</th>
            <th class="double-underline">Operator</th>
        </tr>
    </thead>
    <?php $total = 0;
    foreach ($rows as $row) : $total += $row->subtotal;  ?>
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
    <?php endforeach ?>
    <tr>
        <td colspan="6">Total</td>
        <td><?= number_format($total) ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
</html>