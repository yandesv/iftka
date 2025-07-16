<?php
    $koneksi = mysqli_connect("localhost:3306", "root", "", "webift");

    if(!$koneksi)
    {
        die("koneksi Gagal".mysqli_connect_error());
    }

    function query($query)
    {
      global $koneksi;
       $result = mysqli_query($koneksi, $query);
       
       $rows = [];

       while ($row = mysqli_fetch_assoc($result) )
       {
         $rows[] = $row;
       }

       return $rows;
    }


    function tambahmahasiswa($data)
    {
        global $koneksi;

        $nama = $data["nama"];
        
        $nim = $data["nim"];
        $jurusan = $data["jurusan"];
        $nohp = $data["nohp"];

        $file = $_FILES['foto']['name'];
        $namafile = date ('dmy_hm'). '_' . $file;
        $temp = $_FILES['foto']['tmp_name'];
        $folder = 'images/';
        $path = $folder . $namafile;

        if(move_uploaded_file($temp, $path))
{
    $query = "INSERT INTO mahasiswa (foto, nama, nim, jurusan, nohp) 
              VALUES ('$namafile', '$nama', '$nim', '$jurusan', '$nohp')";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

    }

    function hapusdata($id)
    {
        global $koneksi;
        $query = "DELETE FROM mahasiswa where id=$id";
        mysqli_query($koneksi,$query);

        return mysqli_affected_rows($koneksi);
    }
    
    function ubahdata($data, $id)
{
    global $koneksi;

    $nama = $data["nama"];
    $nim = $data["nim"];
    $jurusan = $data["jurusan"];
    $nohp = $data["nohp"];

    $namafile = ''; // default kosong

    // Cek apakah user upload foto baru
    if ($_FILES['foto']['error'] === 0) {
        $file = $_FILES['foto']['name'];
        $namafile = date('dmy_hm') . '_' . $file;
        $temp = $_FILES['foto']['tmp_name'];
        $folder = 'images/';
        $path = $folder . $namafile;

        move_uploaded_file($temp, $path);

        // Jika foto diupload
        $query = "UPDATE mahasiswa SET
            foto = '$namafile',
            nama = '$nama',
            nim = '$nim',
            jurusan = '$jurusan',
            nohp = '$nohp'
            WHERE id = $id";
    } else {
        // Jika tidak upload foto
        $query = "UPDATE mahasiswa SET
            nama = '$nama',
            nim = '$nim',
            jurusan = '$jurusan',
            nohp = '$nohp'
            WHERE id = $id";
    }

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}


function register($data) {
    global $koneksi;

    $username = strtolower(stripslashes($data["username"]));
    $password1 = mysqli_real_escape_string($koneksi, $data["password1"]);
    $password2 = mysqli_real_escape_string($koneksi, $data["password2"]);

    // Cek username sudah ada atau belum
    $result = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'",);
    if (mysqli_fetch_assoc($result)) {
        return "Username sudah terdaftar!";
    }

    // Cek konfirmasi password
    if ($password1 !== $password2) {
        return "Konfirmasi password tidak sesuai!";
    }

    // Enkripsi password
    $password_hash = password_hash($password1, PASSWORD_DEFAULT);

    // Tambahkan ke database
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password_hash')";
    if (mysqli_query($koneksi, $query)) {
        return "Register Berhasil!";
    } else {
        return "Register Gagal!";
    }
}

?>