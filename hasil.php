<?php
// Misal, $role diambil dari sesi pengguna yang login
$role = $_SESSION['level'] ?? ''; // Bisa 'admin', 'pemilik', atau lainnya

if (_get('act') == 'hapus' && _get('kode_produk') && _get('bulan')) {
    if ($role === 'admin') { // Hanya admin yang bisa menghapus
        $kode_produk = esc_field(_get('kode_produk'));
        $bulan = esc_field(_get('bulan'));

        // Menghapus data dari tb_hasil berdasarkan kode_produk dan bulan
        $delete_query = $db->query("DELETE FROM tb_hasil 
                                    WHERE kode_produk = '$kode_produk' 
                                    AND DATE_FORMAT(tanggal, '%Y-%m-01') = '$bulan'");

        // Cek apakah query berhasil
        if ($delete_query) {
            print_msg('Data berhasil dihapus!', 'success');
        } else {
            print_msg('Gagal menghapus data.', 'danger');
        }
    } else {
        print_msg('Anda tidak memiliki izin untuk menghapus data.', 'danger');
    }

    // Redirect untuk refresh halaman setelah penghapusan
    header("Location: ?m=hasil");
    exit;
}
?>
<div class="page-header">
    <h1>Hasil Peramalan</h1>
</div>
<div class="card mb-3">
    <div class="card-header">
        <form class="row row-cols-lg-auto g-1">
            <input type="hidden" name="m" value="hasil" />
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian. . ." 
                name="q" value="<?= _get('q') ?>" />
            </div>
            <div class="col">
                <button class="btn btn-success"><span class="fa fa-search"></span> Cari</button>
            </div>
            <div class="form-group">
                <a href="?m=hasil" class="btn btn-success"><span class="fa fa-refresh"></span> Refresh</a> 
            </div>
            <?php if ($role === 'owner') : // Tombol Cetak hanya untuk pemilik ?>
                <div class="form-group">
                    <a class="btn btn-secondary" href="cetak.php?m=hasil&q=<?= urlencode(_get('q')) ?>" target="_blank">
                    <span class="fa fa-print"></span> Cetak</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <table class="table table-bordered table-hover table-striped m-0">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Periode</th>
                <?php if ($role === 'admin') : // Kolom tambahan hanya untuk admin ?>
                    <th>Aktual</th>
                <?php endif; ?>
                <th>Peramalan</th>
                <th>Pendapatan</th>
                <?php if ($role === 'admin') : // Kolom tambahan hanya untuk admin ?>
                    <th>Akurasi (%)</th>
                    <th>Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php
$q = esc_field(_get('q'));
$rows = $db->get_results("SELECT 
    b.kode_produk, 
    b.nama_produk, 
    b.harga AS harga_satuan, 
    DATE_FORMAT(h.tanggal, '%Y-%m-01') AS bulan, 
    h.hasil AS total_peramalan,
    (h.hasil * b.harga) AS estimasi_pendapatan,
    SUM(h.aktual) AS total_aktual,
    CASE 
        WHEN SUM(h.aktual) = 0 THEN 0 -- Mencegah pembagian nol
        ELSE 100 - (ABS(SUM(h.aktual) - h.hasil) / SUM(h.aktual) * 100)
    END AS akurasi
FROM tb_hasil h 
INNER JOIN tb_produk b ON b.kode_produk = h.kode_produk 
WHERE b.nama_produk LIKE '%$q%'
GROUP BY b.kode_produk, bulan");
 if (empty($rows)) {
?>
                <tr>
                    <td colspan="<?= $role === 'admin' ? 9 : 5 ?>" class="text-center">Data tidak ditemukan.</td>
                </tr>
                <?php
            } else {
                foreach ($rows as $row) : ?>
                    <tr>
                        <td><?= $row->kode_produk ?></td>
                        <td><?= $row->nama_produk ?></td>
                        <td><?= date('M-Y', strtotime($row->bulan)) ?></td>
                        <?php if ($role === 'admin') : ?>
                            <td><?= $row->total_aktual ?> Produk</td>
                        <?php endif; ?>
                        <td><?= $row->total_peramalan ?> Produk</td>
                        <td>Rp. <?= number_format($row->estimasi_pendapatan, 0, ',', '.') ?></td>
                        <?php if ($role === 'admin') : ?>
                            <td><?= number_format($row->akurasi, 2) ?>%</td>
                            <td>
                                <a href="?m=hasil&act=hapus&kode_produk=<?= $row->kode_produk ?>&bulan=<?= $row->bulan ?>" 
                                    class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
                                    <span class="fa fa-trash"></span>
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach;
            } ?>
        </tbody>
    </table>
</div>
