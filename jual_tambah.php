<div class="page-header">
    <h1>Tambah penjualan</h1>
</div>
<?php
if (!isset($_SESSION['jual']))
    jual_baru();
function jual_baru()
{
    $_SESSION['jual'] = array(
        'faktur' => kode_oto('faktur', 'tb_jual', 'FJ-' . date('Ymd-'), 3),
        'tanggal' => date('Y-m-d'),
        'kode_pelanggan' => '',

        'grantotal' => 0,
        'bayar' => 0,
        'items' => array(),
        'item' => [
            'harga' => 0,
            'jumlah' => 0,
            'subtotal' => 0,
            'laba' => 0,
        ]
    );
}
if ($act == 'baru') {
    jual_baru();
} else if ($act == 'hapus_item') {
    unset($_SESSION['jual']['items'][$_GET['ID']]);
    print_msg('Barang dihapus!', 'success');
}

$jual = $_SESSION['jual'];

if ($_POST) {
    $jual['faktur'] = if_null($_POST['jual']['faktur'], kode_oto('faktur', 'tb_jual', 'FJ-' . date('Ymd-'), 3));
    $jual['tanggal'] = if_null($_POST['jual']['tanggal'], date('Y-m-d'));
    $jual['bayar'] = if_null($_POST['jual']['bayar']);

    if (_post('tambah_produk')) {
        if (!$_POST['item']['kode_produk']) {
            print_msg('Pilih produk!');
        } else if ($_POST['item']['jumlah'] <= 0) {
            print_msg('Masukkan jumlah min 1');
        } else {

            //echo '<pre>' . print_r($_POST, 1) .'</pre>';
            $produk = $db->get_row("SELECT * FROM tb_produk WHERE kode_produk='" . $_POST['item']['kode_produk'] . "'");
            $jual['items'][$_POST['item']['kode_produk']] = array(
                'kode_produk' => $_POST['item']['kode_produk'],
                'nama_produk' => $produk->nama_produk,
                'harga' => str_replace('.', '', $_POST['item']['harga']),
                'jumlah' => str_replace('.', '', $_POST['item']['jumlah']),
                'subtotal' => str_replace('.', '', $_POST['item']['subtotal']),
                'laba' => str_replace('.', '', $_POST['item']['laba']),
            );
            print_msg('Barang ditambahkan!', 'success');
        }
    } else if (_post('simpan_transaksi')) {
        $jual['grantotal'] = 0;
        foreach ((array)$jual['items'] as $key => $val) {
            $jual['grantotal'] += $val['subtotal'];
        }

        $jual['kembali'] = $jual['bayar'] - $jual['grantotal'];

        if ($db->get_row("SELECT * FROM tb_jual WHERE faktur='$jual[faktur]'")) {
            print_msg('Faktur sudah ada!');
        } else if (!$jual['items']) {
            print_msg('Belum ada produk yang dijual!');
        } else if ($jual['kembali'] < 0) {
            print_msg('Pembayaran kurang!');
        } else {
            $db->query("INSERT INTO tb_jual (faktur, tanggal, grantotal, bayar, kembali, kode_user)
                VALUES('$jual[faktur]', '$jual[tanggal]', '$jual[grantotal]', '$jual[bayar]', '$jual[kembali]', '{$_SESSION['ID']}')");

            $id_jual = $db->insert_id;

            foreach ($jual['items'] as $item) {
                $db->query("INSERT INTO tb_jual_detail (id_jual, kode_produk, harga_jual, jumlah, subtotal, aktual)
                    VALUES ('$id_jual', '$item[kode_produk]', '$item[harga]', '$item[jumlah]', '$item[subtotal]', '$item[jumlah]')");
            }
            // Menambahkan data aktual ke tb_hasil
            $db->query(" INSERT INTO tb_hasil (kode_produk, tanggal, aktual)
                    SELECT jd.kode_produk, j.tanggal AS tanggal, SUM(jd.jumlah) AS aktual
                    FROM tb_jual_detail jd JOIN tb_jual j ON jd.id_jual = j.id_jual
                    WHERE j.id_jual = '$id_jual' GROUP BY jd.kode_produk, j.tanggal
                    ON DUPLICATE KEY UPDATE aktual = VALUES(aktual)");                
            jual_baru();
            $jual = $_SESSION['jual'];

            print_msg('Data Tersimpan');
        }
    }
}

$jual['grantotal'] = 0;
$jual['laba_total'] = 0;
foreach ((array)$jual['items'] as $key => $val) {
    $jual['grantotal'] += $val['subtotal'];
    $jual['laba_total'] += $val['jumlah'];
}

$jual['kembali'] = $jual['bayar'] - $jual['grantotal'];
//echo $jual['kembali'];
$_SESSION['jual'] = $jual;

?>
<div class="pull-right alert alert-success" style="font-size: 30px;"><span class="fa fa-shopping-cart"></span> Rp <?= number_format($jual['grantotal']) ?></div>
<form method="post" action="?m=jual_tambah">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Faktur <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="jual[faktur]" value="<?= $jual['faktur'] ?>" />
            </div>
            <div class="mb-3">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="jual[tanggal]" value="<?= $jual['tanggal'] ?>" />
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="form-group">
                <label>Produk</label>
                <select class="form-select" name="item[kode_produk]" id="kode_produk">
                    <option value="">Pilih produk</option>
                    <?= get_produk_option($jual['item']['kode_produk']) ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Harga jual</label>
                <input class="form-control number" type="text" name="item[harga]" id="harga" value="<?= $jual['item']['harga'] ?>" readonly="" />
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label>Jumlah</label>
                <input class="form-control number" type="text" name="item[jumlah]" id="jumlah" value="<?= $jual['item']['jumlah'] ?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Subtotal</label>
                <input class="form-control number" type="text" name="item[subtotal]" id="subtotal" readonly="" value="<?= $jual['item']['subtotal'] ?>" />
                <input class="form-control" type="hidden" name="item[laba]" id="laba" readonly="" value="<?= $jual['item']['laba'] ?>" />
            </div>
        </div>
        <div class="col-md-2"><br>
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="btn btn-block btn-primary" name="tambah_produk" value="1"><span class="fa fa-plus"></span> Tambahkan</button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <?php
            $no = 1;
            foreach ((array)$jual['items'] as $item) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        [<?= $item['kode_produk'] ?>] <?= $item['nama_produk'] ?>
                        <a class="btn btn-sm btn-danger pull-right" href="?m=jual_tambah&act=hapus_item&ID=<?= $item['kode_produk'] ?>" onclick="return confirm('Hapus item?')"><span class="fa fa-trash"></span></a>
                    </td>
                    <td><?= number_format($item['harga']) ?></td>
                    <td><?= number_format($item['jumlah']) ?></td>
                    <td><?= number_format($item['subtotal']) ?></td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td colspan="4">Total</td>
                <td><input class="form-control number" type="text" name="jual[grantotal]" id="grantotal" value="<?= $jual['grantotal'] ?>" readonly="" /></td>
            </tr>
            <tr>
                <td colspan="4">Bayar</td>
                <td><input class="form-control number" type="text" name="jual[bayar]" id="bayar" value="<?= $jual['bayar'] ?>" /></td>
            </tr>
            <tr>
                <td colspan="4">Kembali</td>
                <td><input class="form-control number" type="text" name="jual[kembali]" id="kembali" value="<?= $jual['kembali'] ?>" readonly="" /></td>
            </tr>
        </table>
    </div>
    <div class="form-group">
        <button class="btn btn-primary" name="simpan_transaksi" value="1"><span class="fa fa-save"></span> Simpan Transaksi</button>
        <a class="btn btn-success" href="?m=jual_tambah&act=baru"><span class="fa fa-refresh"></span> Transaksi baru</a>
        <a class="btn btn-danger" href="?m=jual"><span class="fa fa-arrow-left"></span> Kembali</a>
    </div>
</form>
<script>
    $(function() {
        hitung();
        $("#kode_produk").on("select2:select", function(e) {
            $('#harga').focus();
        });

        $('#kode_produk').change(function() {
            $('#harga').val($(this).find(':selected').data('harga'));
            $('#laba').val($(this).find(':selected').data('laba'));
            hitung();
        });

        $('#harga').keyup(function() {
            hitung();
        });

        $('#jumlah').keyup(function() {
            hitung();
        });

        $('#bayar').keyup(function() {
            hitung();
        });

        function hitung() {
            $('#subtotal').val($('#harga').val() * $('#jumlah').val());
            $('#kembali').val($('#bayar').val() - $('#grantotal').val());
        }
    })
</script>