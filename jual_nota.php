<?php include 'functions.php'; ?>
<!doctype html>
<html>

<head>
    <title>Cetak Laporan</title>
    <style>
        body {
            font-family: Verdana;
            font-size: 13px;
        }

        h1,
        h2,
        h4,
        h5 {
            margin: 0;
            text-align: center;
        }

        h4 {
            border-bottom: 4px double black;
        }

        .wrapper {
            margin: 0 auto;
            max-width: 400px;
            font-family: consolas;
        }
    </style>
    <script src="assets/js/highcharts.js"></script>
</head>

<body onload="//window.print()">
    <div class="wrapper">
        <?php
        $row = $db->get_row("SELECT * FROM tb_jual j INNER JOIN tb_user u ON u.kode_user=j.kode_user WHERE id_jual='$_GET[ID]'");
        ?>
        <h1>Ramen Kaizenka</h1>
        <h4>Jl. RE. Martadinata, Cijoho, Kec. Kuningan, Kabupaten Kuningan, Jawa Barat 45513 </h4>
        <h2>NOTA PENJUALAN</h2>
        <table style="width: 100%;">
            <tr>
                <td>Nota</td>
                <td>: <?= $row->faktur ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: <?= $row->tanggal ?></td>
            </tr>
            <tr>
                <td>Operator</td>
                <td>: <?= $row->nama_user ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr />
                    <table style="width: 100%;">
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                        <?php
                        $details = $db->get_results("SELECT * FROM tb_jual_detail d INNER JOIN tb_produk p ON p.kode_produk=d.kode_produk WHERE id_jual='$_GET[ID]'");
                        foreach ($details as $detail) : ?>
                            <tr>
                                <td><?= $detail->nama_produk ?></td>
                                <td><?= $detail->jumlah ?></td>
                                <td><?= number_format($detail->harga) ?></td>
                                <td><?= number_format($detail->subtotal) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                    <hr />
                </td>
            </tr>
            <tr>
                <td>Total</td>
                <td>: <?= number_format($row->grantotal) ?></td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td>: <?= number_format($row->bayar) ?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td>: <?= number_format($row->kembali) ?></td>
            </tr>
        </table>
        <h4></h4>
        <h5>TERIMAKASIH ATAS KUNJUNGAN ANDA</h5>
    </div>
</body>

</html>