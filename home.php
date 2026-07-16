<div class="page-header">
    <h1>Beranda</h1>
</div>
<?php
if (_get('migrate')) {
    $db->query("TRUNCATE tb_jual");
    $db->query("TRUNCATE tb_jual_detail");
    for ($i = 1; $i <= 300; $i+=3) {
        $tanggal = $db->get_var("SELECT DATE(NOW()) + INTERVAL - 301 + $i DAY");
        $faktur = kode_oto('faktur', 'tb_jual', 'FJ-' . date('Ymd-', strtotime($tanggal)), 3);
        $db->query("INSERT INTO tb_jual (faktur, tanggal) VALUES ('$faktur', '$tanggal')");
        $db->query("UPDATE tb_jual SET kode_user=(SELECT kode_user FROM tb_user ORDER BY RAND() LIMIT 1)");
        $limit = rand(1, 3);
        $id_jual = $db->insert_id;
        $jumlah = rand(1, 5);
        foreach ($db->get_results("SELECT * FROM tb_produk ORDER BY RAND() LIMIT $limit") as $row) {
            $db->query("INSERT INTO tb_jual_detail (id_jual, kode_produk, harga_jual, jumlah, subtotal) VALUES ('$id_jual', '$row->kode_produk', '$row->harga', $jumlah, $row->harga * $jumlah)");
        }
    }
    $db->query("UPDATE tb_jual SET grantotal = (SELECT SUM(subtotal) FROM tb_jual_detail WHERE id_jual=tb_jual.id_jual)");
    $db->query("UPDATE tb_jual SET bayar = CEIL(grantotal/50000) * 50000");
    $db->query("UPDATE tb_jual SET kembali = bayar-grantotal");
}

?>
<?php foreach ($PRODUK as $kode_produk => $produk) : ?>
    <div class="card mb-3">
        <div class="card-header">
            Grafik <?= $produk->nama_produk ?>
        </div>
        <div class="card-body">
            <?php
            $currentYear = date('Y');
            $sql = "SELECT tanggal, SUM(jumlah) AS jumlah 
                    FROM tb_jual j 
                    INNER JOIN tb_jual_detail d ON d.id_jual=j.id_jual 
                    WHERE YEAR(tanggal) = '$currentYear' 
                    AND kode_produk='$kode_produk' 
                    GROUP BY MONTH(tanggal) 
                    ORDER BY tanggal";
            $rows = $db->get_results($sql);
            $alpha = $db->get_var("SELECT alpha FROM tb_alpha WHERE kode_produk='$kode_produk' ORDER BY mape LIMIT 1");
            $n_periode = 1;
            $penjualan = array();
            $last_periode = null;

            foreach ($rows as $row) {
                $penjualan[$row->tanggal] = $row->jumlah * 1;
                $last_periode = $row->tanggal;
            }

            $f = new DES($penjualan, $alpha, $n_periode);

            $categories = array();
            $series = array();
            $categories = array();
            $series[0]['name'] = 'Aktual';
            $series[1]['name'] = 'Forecast';
            foreach ($f->yt as $key => $val) :
                $series[0]['data'][] = $val * 1;
                $series[1]['data'][] = isset($f->ft[$key]) ? round($f->ft[$key]) : null;
                $categories[] = date('M Y', strtotime($key));
            endforeach;

            $next_periode = $last_periode;

            $periode = '';
            $hasil = '';
            foreach ($f->ft_next as $key => $val) :
                $next_periode = date('Y-m-d', strtotime($next_periode . " 1 months"));
                $categories[] =  date('M Y', strtotime($next_periode));
                $periode = date('M Y', strtotime($next_periode));
                $hasil = $val;
                $series[1]['data'][] = round($val * 1);
            endforeach;
            ?>

            <div id="container_<?= $kode_produk ?>" style="height: 500px; min-width: 500px"></div>
            <script type="text/javascript">
                Highcharts.chart('container_<?= $kode_produk ?>', {
                    title: {
                        text: 'Grafik Penjualan dan Peramalan 1 Tahun Terakhir'
                    },

                    xAxis: {
                        categories: <?= json_encode($categories) ?>
                    },

                    yAxis: {
                        title: {
                            text: 'Jumlah'
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },

                    series: <?= json_encode($series) ?>,

                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'
                                }
                            }
                        }]
                    }
                });
            </script>
        </div>
    </div>
<?php endforeach ?>