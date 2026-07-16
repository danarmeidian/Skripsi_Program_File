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

<body onload="window.print()">
    <div class="wrapper">
        <?php
        $row = $db->get_row("SELECT * FROM tb_penjualan INNER JOIN tb_produk ON tb_produk.kode_produk=tb_penjualan.kode_produk WHERE id_penjualan='$_GET[ID]'");
        ?>
        <h1>Ramen Kaizenka</h1>
        <h4>Jl. RE. Martadinata, Cijoho, Kec. Kuningan, Kabupaten Kuningan, Jawa Barat 45513</h4>
        <h2>NOTA PENJUALAN</h2>
        <table>
            <tr>
                <td>Tanggal</td>
                <td>: <?= $row->tanggal ?></td>
            </tr>
            <tr>
                <td>Kode</td>
                <td>: <?= $row->kode_produk ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <?= $row->nama_produk ?></td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>: <?= $row->jumlah ?></td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>: <?= number_format($row->harga_jual) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td>: <?= number_format($row->total) ?></td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td>: <?= number_format($row->harga_jual) ?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td>: <?= number_format($row->kembali) ?></td>
            </tr>
        </table>
        <h4></h4>
        <h5>TERIMAKASIH ATAS KUNJUNGAN ANDA</h5>
        <!-- <table>
            <tr>
                <th></th>
            </tr>
        </table> -->
    </div>
</body>

</html>