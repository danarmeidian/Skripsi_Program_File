<?php

error_reporting(~E_NOTICE);
session_start();
include 'config.php';
include 'includes/db.php';
$db = new DB($config['server'], $config['username'], $config['password'], $config['database_name']);
include 'includes/des_class.php';
include 'includes/paging.php';
include 'composer/vendor/autoload.php';

function is_able($mod)
{
    $role = array(
        'admin' => array(
            'user',
            'des',
            'hasil',
            'alpha'          
        ),
        'owner' => array(
            // 'user',
            // 'produk',
            'produk_keluar',
            // 'des',
            'hasil',
            'cetak_hasil'
        ),
        'kasir' => array(
            // 'user',
            'produk',
            'jual',
            // 'des',
            // 'hasil',
        ),
        'guest' => array(),
    );
    if (!_session('level'))
        $_SESSION['level'] = 'guest';
    if (!isset($role[_session('level')]))
        $_SESSION['level'] = 'guest';
    $level = strtolower(_session('level'));
    return in_array($mod, (array)$role[$level]);
}

function is_hidden($mod)
{
    return (is_able($mod)) ? '' : 'hidden';
}

function _post($key, $val = null)
{
    global $_POST;
    if (isset($_POST[$key]))
        return $_POST[$key];
    else
        return $val;
}

function _get($key, $val = null)
{
    global $_GET;
    if (isset($_GET[$key]))
        return $_GET[$key];
    else
        return $val;
}

function _session($key, $val = null)
{
    global $_SESSION;
    if (isset($_SESSION[$key]))
        return $_SESSION[$key];
    else
        return $val;
}

$mod = _get('m');
$act = _get('act');

function br_to_enter($text)
{
    return str_replace("\r\n", '<br />', $text);
}

function kode_oto($field, $table, $prefix, $length)
{
    global $db;
    $var = $db->get_var("SELECT $field FROM $table WHERE $field REGEXP '{$prefix}[0-9]{{$length}}' ORDER BY $field DESC");
    if ($var) {
        return $prefix . substr(str_repeat('0', $length) . (substr($var, -$length) + 1), -$length);
    } else {
        return $prefix . str_repeat('0', $length - 1) . 1;
    }
}

function set_value($key = null, $default = null)
{
    global $_POST;
    if (isset($_POST[$key]))
        return $_POST[$key];

    if (isset($_GET[$key]))
        return $_GET[$key];

    return $default;
}

$rows = $db->get_results("SELECT * FROM tb_produk ORDER BY kode_produk");
foreach ($rows as $row) {
    $PRODUK[$row->kode_produk] = $row;
}

function get_produk_option($selected = 0)
{
    global $PRODUK;
    $a = '';
    foreach ($PRODUK as $key => $val) {
        if ($key == $selected)
            $a .= "<option value='$key' selected data-harga='$val->harga'>$val->nama_produk</option>";
        else
            $a .= "<option value='$key' data-harga='$val->harga'>$val->nama_produk</option>";
    }
    return $a;
}

function esc_field($str)
{
    if ($str)
        return addslashes($str);
}

function redirect_js($url)
{
    echo '<script type="text/javascript">window.location.replace("' . $url . '");</script>';
}

function alert($url)
{
    echo '<script type="text/javascript">alert("' . $url . '");</script>';
}

function print_msg($msg, $type = 'danger')
{
    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        ' . $msg . '
    </div>';
}

function dd($str)
{
    echo '<pre>' . print_r($str, 1) . '</pre>';
}

function get_level_option($selected = '')
{
    $arr = array(
        'admin' => 'Admin',
        'kasir' => 'Kasir',
        'pemilik toko' => 'Pemilik Toko',
    );
    $a = '';
    foreach ($arr as $key => $val) {
        if ($selected == $key)
            $a .= "<option value='$key' selected>$val</option>";
        else
            $a .= "<option value='$key'>$val</option>";
    }
    return $a;
}

function tgl_indo($date)
{
    $tanggal = explode('-', $date);

    $array_bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $bulan = $array_bulan[$tanggal[1] * 1];

    return $tanggal[2] . ' ' . $bulan . ' ' . $tanggal[0];
}

function if_null($content, $default = null)
{
    if (isset($content))
        return $content;
    return $default;
}
