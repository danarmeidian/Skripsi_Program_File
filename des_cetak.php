<?php
$_POST = $_SESSION['post'];
$n_periode = $_POST['n_periode'];
$kode_produk = $_POST['kode_produk'];
$alpha = $db->get_var("SELECT alpha FROM tb_alpha WHERE kode_produk='$kode_produk' ORDER BY mape LIMIT 1");

$rows = $db->get_results("SELECT tanggal, SUM(jumlah) AS jumlah FROM tb_penjualan d INNER JOIN tb_produk j ON j.kode_produk=d.kode_produk WHERE d.kode_produk='$kode_produk' GROUP BY d.kode_produk, YEAR(tanggal), MONTH(tanggal) ORDER BY tanggal");
$penjualan = array();
foreach ($rows as $row) {
    $penjualan[$row->tanggal] = $row->jumlah * 1;
    $last_periode = $row->tanggal;
}

$f = new DES($penjualan, $alpha, $n_periode);
?>

<div class="panel panel-primary">
    <div class="card-header">
        <h3 class="panel-title">Perhitungan <?= $PRODUK[$kode_produk] ?> (Alpha: <?= $alpha ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover text-right">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Actual (Yt)</th>
                    <th>S't</th>
                    <th>S"t</th>
                    <th>at</th>
                    <th>bt</th>
                    <th>Forecast</th>
                    <th>e</th>
                    <th>|e|</th>
                    <th>e<sup>2</sup></th>
                    <th>[e]/yt</th>
                </tr>
            </thead>
            <?php
            $categories = array();
            $series = array();
            $categories = array();
            $series[0]['name'] = 'Aktual';
            $series[1]['name'] = 'Forecast';
            foreach ($f->yt as $key => $val) :
                $series[0]['data'][] = $val * 1;
                $series[1]['data'][] = isset($f->ft[$key]) ? round($f->ft[$key]) : null;
                $categories[] = date('M Y', strtotime($key)); ?>
                <tr>
                    <td><?= date('M Y', strtotime($key)) ?></td>
                    <td><?= round($val, 4) ?></td>
                    <td><?= round($f->st[$key], 4)  ?></td>
                    <td><?= round($f->sst[$key], 4)  ?></td>
                    <td><?= isset($f->at[$key]) ? round($f->at[$key], 4) : '' ?></td>
                    <td><?= isset($f->bt[$key]) ? round($f->bt[$key], 4) : '' ?></td>
                    <td><?= isset($f->ft[$key]) ? round($f->ft[$key]) : '' ?></td>
                    <td><?= isset($f->e[$key]) ? round($f->e[$key], 4) : '' ?></td>
                    <td><?= isset($f->e_abs[$key]) ?  round($f->e_abs[$key], 4) : '' ?></td>
                    <td><?= isset($f->e2[$key]) ?  round($f->e2[$key], 4) : '' ?></td>
                    <td><?= isset($f->e_abs_yt[$key]) ? round($f->e_abs_yt[$key], 4)  : '' ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <div class="panel-body">
        MSE (Mean Square Error) : <?= number_format($f->mse, 2) ?><br />
        RMSE (Root Mean Square Error) : <?= number_format($f->rmse, 2) ?><br />
        MAD (Mean Absolute Deviation) : <?= number_format($f->mad, 2) ?><br />
        MAPE (Mean Absolute Percent Error) : <?= number_format($f->mape, 2) ?>%<br />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Periode</th>
                    <th>at</th>
                    <th>bt</th>
                    <th>Ft</th>
                </tr>
            </thead>
            <?php
            $next_periode = $last_periode;
            foreach ($f->ft_next as $key => $val) :
                $next_periode = date('Y-m-d', strtotime($next_periode . " 1 months"));
                $categories[] =  date('M Y', strtotime($next_periode));
                $series[1]['data'][] = round($val * 1) ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= date('M Y', strtotime($next_periode)) ?></td>
                    <td><?= round($f->last_at, 2) ?></td>
                    <td><?= round($f->last_bt * ($key + 1), 2) ?></td>
                    <td><?= round($val) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <div class="card-footer">
        <div id="container" style="height: 500px; min-width: 500px"></div>
        <script type="text/javascript">
            Highcharts.chart('container', {
                title: {
                    text: 'Grafik Perbandingan Aktual dan Forecasting'
                },

                xAxis: {
                    categories: ["Jan-2020", "Feb-2020", "Mar-2020", "Apr-2020", "May-2020", "Jun-2020", "Jul-2020", "Aug-2020", "Sep-2020", "Oct-2020", "Nov-2020", "Dec-2020", "Jan-2021", "Feb-2021", "Mar-2021"]
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
                        penjualanLabels: {
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