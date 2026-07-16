<?php
// Pastikan untuk menghubungkan dengan database terlebih dahulu
// Misalnya menggunakan koneksi $db yang sudah ada sebelumnya

// Ambil parameter pencarian dari URL
$q = esc_field(_get('q'));

// Query untuk mendapatkan data sesuai pencarian
$query = "SELECT b.kode_produk, b.nama_produk, DATE_FORMAT(h.tanggal, '%Y-%m-01') AS bulan, 
           h.hasil, b.harga, (h.hasil * COALESCE(b.harga, 0)) AS pendapatan
      FROM tb_hasil h 
      INNER JOIN tb_produk b ON b.kode_produk = h.kode_produk 
      WHERE b.nama_produk LIKE '%$q%' 
      GROUP BY b.kode_produk, bulan";

// Eksekusi query
$rows = $db->get_results($query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Daftar Tabel</title>
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
</head>
<body>
    <h2 >Daftar Tabel Peramalan</h2>
    <table class="table">
        <thead>
            <tr>
                <th class="double-underline">Kode</th>
                <th class="double-underline">Nama</th>
                <th class="double-underline">Periode</th>
                <th class="double-underline">Peramalan</th>
                <th class="double-underline">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($rows)) {
                echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan.</td></tr>";
            } else {
                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td>{$row->kode_produk}</td>";
                    echo "<td>{$row->nama_produk}</td>";
                    echo "<td>" . date('M-Y', strtotime($row->bulan)) . "</td>";
                    echo "<td>{$row->hasil} Produk</td>";
                    echo "<td>Rp. " . number_format($row->pendapatan, 0, ',', '.'). "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>