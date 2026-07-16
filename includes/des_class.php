<?php
class DES
{
    public $yt = array();
    public $alpha;
    public $n_periode;
    public $st = array();
    public $sst = array();
    public $at = array();
    public $bt = array();
    public $ft = array();
    public $last_at = 0;
    public $last_bt = 0;
    public $e = array();
    public $e_abs = array();
    public $e2 = array();
    public $e_abs_yt = array();
    public $ft_next = array();
    public $mse = 0;
    public $mape = 0;
    public $rmse = 0;
    public $mad = 0;

    function __construct($yt, $alpha, $n_periode)
    {
        $this->yt = $yt;
        $this->alpha = $alpha;
        $this->n_periode = $n_periode;

        $this->hitung();
    }
    function hitung()
    {
        $a = 1;
        $st_temp = null;
        $sst_temp = null;
        foreach ($this->yt as $k => $v) {
            if ($a == 1) {
                $this->st[$k] = $v;
                $this->sst[$k] = $v;
                $this->at[$k] = $v;
                $this->bt[$k] = $this->alpha / (1 - $this->alpha) * ($this->st[$k] - $this->sst[$k]);
            } else {
                $this->st[$k] = $this->alpha * $v + (1 - $this->alpha) * $st_temp;
                $this->sst[$k] = $this->alpha * $this->st[$k] + (1 - $this->alpha) * $sst_temp;
                $this->at[$k] = 2 * $this->st[$k] - $this->sst[$k];
                $this->bt[$k] = $this->alpha / (1 - $this->alpha) * ($this->st[$k] - $this->sst[$k]);
                $this->ft[$k] = $this->last_at + $this->last_bt;
                $this->e[$k] = $this->yt[$k] - $this->ft[$k];
                $this->e_abs[$k] = abs($this->e[$k]);
                $this->e2[$k] = pow($this->e[$k], 2);
                $this->e_abs_yt[$k] = abs($this->e[$k]) / $this->yt[$k];
            }
            $this->last_at = $this->at[$k];
            $this->last_bt = $this->bt[$k];
            $st_temp = $this->st[$k];
            $sst_temp = $this->sst[$k];
            $a++;
        }

        for ($a = 1; $a <= $this->n_periode; $a++) {
            $this->ft_next[] = $this->last_at + $this->last_bt * $a;
        }

        $n = count($this->yt);
        $this->mse = $n == 0 ? 0 : array_sum($this->e2) / $n;
        $this->rmse = $n == 0 ? 0 : sqrt(array_sum($this->e2) / $n);
        $this->mad =  $n == 0 ? 0 : array_sum($this->e_abs) / $n;
        $this->mape =  $n == 0 ? 0 : array_sum($this->e_abs_yt) / $n * 100;
    }
}
