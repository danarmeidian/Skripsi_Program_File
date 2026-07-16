<?php
require_once 'functions.php';

/** LOGIN */
if ($mod == 'login') {
    $user = esc_field($_POST['user']);
    $pass = esc_field($_POST['pass']);

    $row = $db->get_row("SELECT * FROM tb_user WHERE user='$user' AND pass='$pass'");
    if ($row) {
        $_SESSION['login'] = $row->user;
        $_SESSION['level'] = $row->level;
        $_SESSION['ID'] = $row->kode_user;
        redirect_js("index.php");
    } else {
        print_msg("Username dan Password Tidak Sesuai.");
    }
} elseif ($act == 'logout') {
    unset($_SESSION['login'], $_SESSION['level'], $_SESSION['ID']);
    header("location:index.php?m=login");
} else if ($mod == 'password') {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $pass3 = $_POST['pass3'];

    $row = $db->get_row("SELECT * FROM tb_user WHERE user='$_SESSION[login]' AND pass='$pass1'");

    if ($pass1 == '' || $pass2 == '' || $pass3 == '')
        print_msg('Field bertanda * harus diisi.');
    elseif (!$row)
        print_msg('Password lama salah.');
    elseif ($pass2 != $pass3)
        print_msg('Password baru dan konfirmasi password baru tidak sama.');
    else {
        $db->query("UPDATE tb_user SET pass='$pass2' WHERE user='$_SESSION[login]'");
        print_msg('Password berhasil diubah.', 'success');
    }
}

/** produk */
elseif ($mod == 'produk_tambah') {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    if ($kode_produk == '' || $nama_produk == '' || $harga == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_produk WHERE kode_produk='$kode_produk'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_produk (kode_produk, nama_produk, harga) VALUES ('$kode_produk', '$nama_produk', '$harga')");
        redirect_js("index.php?m=produk");
    }
} else if ($mod == 'produk_ubah') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];

    if ($nama_produk == '' || $harga == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    else {
        $db->query("UPDATE tb_produk SET nama_produk='$nama_produk', harga='$harga' WHERE kode_produk='$_GET[ID]'");
        redirect_js("index.php?m=produk");
    }
} else if ($act == 'produk_hapus') {
    $db->query("DELETE FROM tb_jual_detail WHERE kode_produk='$_GET[ID]'");
    $db->query("DELETE FROM tb_produk WHERE kode_produk='$_GET[ID]'");
    header("location:index.php?m=produk");
}

/** penjualan */
else if ($act == 'jual_hapus') {
    $id_jual = $_GET['ID'];
    $db->query("DELETE FROM tb_jual WHERE id_jual='$id_jual'");
    $db->query("DELETE FROM tb_jual_detail WHERE id_jual='$id_jual'");
    header("location:index.php?m=jual");
}

/** user */
elseif ($mod == 'user_tambah') {
    $kode_user = $_POST['kode_user'];
    $nama_user = $_POST['nama_user'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $level = $_POST['level'];

    if ($kode_user == '' || $user == '' || $pass == '' || $nama_user == '' || $level == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    elseif ($db->get_row("SELECT * FROM tb_user WHERE kode_user='$kode_user'")) {
        print_msg("Kode sudah ada!");
    } elseif ($db->get_row("SELECT * FROM tb_user WHERE user='$user'")) {
        print_msg("User sudah ada!");
    } else {
        $db->query("INSERT INTO tb_user (kode_user, user, pass, nama_user, level) 
                                    VALUES ('$kode_user', '$user', '$pass', '$nama_user', '$level')");
        redirect_js("index.php?m=user");
    }
} else if ($mod == 'user_ubah') {
    $kode_user = $_POST['kode_user'];
    $nama_user = $_POST['nama_user'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $level = $_POST['level'];

    if ($kode_user == '' || $user == '' || $pass == '' || $nama_user == '' || $level == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    elseif ($db->get_row("SELECT * FROM tb_user WHERE user='$user' AND kode_user<>'$_GET[ID]'")) {
        print_msg("User sudah ada!");
    } else {
        $db->query("UPDATE tb_user SET 
                user='$user', 
                pass='$pass', 
                nama_user='$nama_user',                                         
                level='$level'
            WHERE kode_user='$_GET[ID]'");
        redirect_js("index.php?m=user");
    }
} else if ($act == 'user_hapus') {
    $db->query("DELETE FROM tb_user WHERE kode_user='$_GET[ID]'");
    header("location:index.php?m=user");
}
