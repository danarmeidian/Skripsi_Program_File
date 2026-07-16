<?php
$_SESSION['post'] = $_POST;
$kode_produk = $_POST['kode_produk'];
$alpha = $db->get_var("SELECT alpha FROM tb_alpha WHERE kode_produk='$kode_produk' ORDER BY mape LIMIT 1");
$harga_satuan = $db->get_var("SELECT harga FROM tb_produk WHERE kode_produk = '$kode_produk'");
$rows = $db->get_results("SELECT DATE_FORMAT(tanggal, '%Y-%m-01') AS bulan, SUM(jumlah) AS jumlah FROM tb_jual j 
            INNER JOIN tb_jual_detail d ON d.id_jual=j.id_jual 
            INNER JOIN tb_produk p ON p.kode_produk=d.kode_produk 
            WHERE d.kode_produk='$kode_produk' 
            GROUP BY p.kode_produk, YEAR(tanggal), MONTH(tanggal) 
            ORDER BY tanggal");
$penjualan = array();
foreach ($rows as $row) {
    $penjualan[$row->bulan] = $row->jumlah * 1;
    $last_periode = $row->bulan;
}

$f = new DES($penjualan, $alpha, $n_periode);

// Check for existing forecast for the next period
$next_periode = date('Y-m-d', strtotime($last_periode . " 1 months")); 
$existing_forecast = $db->get_row("SELECT * FROM tb_hasil WHERE kode_produk = '$kode_produk' AND tanggal = '$next_periode'");

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
foreach ($f->ft_next as $key => $val) :
    $next_periode = date('Y-m-d', strtotime($next_periode . " 1 months"));
    $categories[] =  date('M Y', strtotime($next_periode));
    $periode = date('M Y', strtotime($next_periode));
    $hasil = $val;
    $series[1]['data'][] = round($val * 1);

    // Insert only if no existing forecast for the period
    if (!$existing_forecast) {
        $db->query("INSERT INTO tb_hasil (kode_produk, tanggal, hasil) VALUES ('$kode_produk', '$next_periode', '$val')");
    }

endforeach;
?>
<div class="card mb-3">
    <div class="card-header">
        <strong>Perhitungan <?= $PRODUK[$kode_produk]->nama_produk ?> (Alpha : <?= $alpha ?>)</strong>
    </div>
    <div class="card-body">
        Berdasarkan perhitungan didapatkan hasil :<br />
        Alpha : <?= $alpha ?><br />
        Periode : <?= $periode ?><br />
        Hasil Peramalan : <?= round($hasil, 2) ?><br />
        MAPE : <?= round($f->mape, 2) ?>%<br />
    </div>
    <div class="card-body">
        <div id="container" style="height: 500px; min-width: 500px"></div>
        <script type="text/javascript">
            Highcharts.chart('container', {
                title: {
                    text: 'Grafik Perbandingan Aktual dan Forecasting'
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
    <div class="card-footer">
        <!-- <a class="btn btn-secondary" href="cetak.php?m=des" target="_blank"><span class="fa fa-print"></span> Cetak </a> -->
        <a class="btn btn-primary" href="#detail" data-bs-toggle="collapse"><span class="fa fa-eye"></span> Detail Perhitungan </a>
    </div>
    <div class="card-body collapse" id="detail">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-right mb-0">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Aktual (Xt)</th>
                        <th>S't</th>
                        <th>S"t</th>
                        <th>at</th>
                        <th>bt</th>
                        <th>Ft</th>
                        <!-- <th>e</th>
                        <th>|e|</th>
                        <th>e<sup>2</sup></th> -->
                        <th>|PE|</th>
                    </tr>
                </thead>
                <?php foreach ($f->yt as $key => $val) :  ?>
                    <tr>
                        <td><?= date('M Y', strtotime($key)) ?></td>
                        <td><?= round($val, 4) ?></td>
                        <td><?= round($f->st[$key], 4)  ?></td>
                        <td><?= round($f->sst[$key], 4)  ?></td>
                        <td><?= isset($f->at[$key]) ? round($f->at[$key], 4) : '' ?></td>
                        <td><?= isset($f->bt[$key]) ? round($f->bt[$key], 4) : '' ?></td>
                        <td><?= isset($f->ft[$key]) ? round($f->ft[$key]) : '' ?></td>
                        <!-- <td><?= isset($f->e[$key]) ? round($f->e[$key], 4) : '' ?></td>
                        <td><?= isset($f->e_abs[$key]) ?  round($f->e_abs[$key], 4) : '' ?></td>
                        <td><?= isset($f->e2[$key]) ?  round($f->e2[$key], 4) : '' ?></td> -->
                        <td><?= isset($f->e_abs_yt[$key]) ? round($f->e_abs_yt[$key], 4)  : '' ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <div class="card-body">
            <!-- MSE (Mean Square Error) : <?= number_format($f->mse, 2) ?><br />
            RMSE (Root Mean Square Error) : <?= number_format($f->rmse, 2) ?><br />
            MAD (Mean Absolute Deviation) : <?= number_format($f->mad, 2) ?><br /> -->
            <h6>MAPE (Mean Absolute Percent Error) : <?= number_format($f->mape, 2) ?>%</h6><br />
        </div>
        <div class="table-responsive">
        <h6>Hasil Peramalan Penjualan <?= $PRODUK[$kode_produk]->nama_produk ?> Periode Selanjutnya Adalah :</h6>
            <table class="table table-bordered table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Peramalan (Ft)</th>
                        <th>Estimasi Pendapatan</th> <!-- Menambahkan kolom estimasi pendapatan -->
                    </tr>
                </thead>
                <?php
                $next_periode = $last_periode;
                foreach ($f->ft_next as $key => $val) :
                    $next_periode = date('Y-m-d', strtotime($next_periode . " 1 months"));  
                    $estimasi_pendapatan = round($val) * $harga_satuan; // Hitung estimasi pendapatan
                    ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= date('M Y', strtotime($next_periode)) ?></td>
                        <td><?= round($val) ?> Produk</td>
                        <td>Rp <?= number_format($estimasi_pendapatan, 0, ',', '.') ?></td> <!-- Menampilkan estimasi pendapatan -->
                    </tr>
                <?php endforeach ?>
            </table><br><br>
            <h6>Interval MAPE</h6>
            <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>MAPE</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><10%</td>
                        <td>Sangat Baik</td>
                    </tr>
                    <tr><td>10% - 20%</td>
                        <td>Baik</td>
                    </tr>
                    </tr>
                    <tr><td>20% - 50%</td>
                        <td>Cukup Baik</td>
                    </tr>
                    </tr>
                    <tr><td>>50%</td>
                        <td>Buruk</td>
                    </tr>
            </table>
        </div>
    </div>
</div>