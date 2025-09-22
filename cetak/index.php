<?php

include "../../setting/kon.php";
include "../../setting/function.php";
include "../../setting/variable.php";


$sqld = "select * from ps_pendaftaran_online where id=\"$gid\" and id_jadwal_dokter =\"$gid2\" and hapus =\"0\"";
$data = mysqli_query($db_result, $sqld);
$ndata = mysqli_num_rows($data);
if ($ndata > 0) {
    $fdata = mysqli_fetch_assoc($data);


    if ($fdata['id_asuransi'] == '0') {
        $jenis_pasien = "Pasien Umum/Asuransi";
    } else if ($fdata['id_asuransi'] == '1') {
        $jenis_pasien = "Pasien BPJS";
    }

    date_default_timezone_set('Asia/Jakarta');
    $jam = date("H:i:s");
    $tgl_daftar = TglFormat1($fdata['tgl_daftar']);
    // $jam = substr($fdata['tgl_insert'], 11, 18);
    $waktu = "$tgl_daftar $jam";

    $no_identitas = !empty($fdata['no_identitas']) ? $fdata['no_identitas'] : $fdata['nomor_rm'];
    $pasien = $fdata['nama_pasien'] . " - " . $no_identitas;

    $kode_booking = $fdata['kode_booking'];
    $no_antri = $fdata['no_antri'];

    $jenis_daftar = ($fdata['jenis_daftar'] == '2') ? "Go Show" : "";

    $id_poli = $fdata['id_instalasi'];
    $nama_poli = SelPoli($id_poli)[$id_poli]['nama_poli'];
    $id_jadwal_dokter = $fdata['id_jadwal_dokter'];
    $sJadwalDokter = JadwalDokter();
    $id_pegawai = $sJadwalDokter[$id_jadwal_dokter]['id_pegawai'];
    $sdokter = SelPegawai();
    $gelar_depan = $sdokter[$id_pegawai]['gelar_depan'];
    $gelar_belakang = $sdokter[$id_pegawai]['gelar_belakang'];
    $nama_pegawai = $sdokter[$id_pegawai]['nama_pegawai'];
    $nama_dokter = NamaGabung($nama_pegawai, $gelar_depan, $gelar_belakang);

    $poli = "$nama_poli - $nama_dokter";
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Rumah Sakit Universitas Muhammadiyah Malang</title>
    <meta name="robots" content="noindex, nofollow">
</head>
<!-- <body onload=""> -->

<body onload="window.print();">

    <style>
        @media print {
            @page {
                size: portrait !important;
            }

            * {
                margin: 10px;
                padding: 0;
            }

            body {
                font-size: 25px;
                line-height: 2.0;
            }

            p {
                line-height: 1.5;
            }
        }
    </style>

    <center>
        <div style="text-transform: uppercase;">
            <b><?= $jenis_pasien ?></b>
        </div>
        <div><b>RSU Universitas Muhammadiyah Malang</b></div>
        <div><?= $waktu ?></div>
        <div><?= $pasien ?></div>
        <hr style="border: 0; border-top: 5px solid black; margin: 20px 100px;">
        <div>Kode Booking : <b><?= $kode_booking ?></b></div>
        <div><b>Harap Check-in 30 menit sebelum praktek</b></div>
        <p style="font-size: 200px; margin: 0px 0;"><?= $no_antri ?></p>
        <div><b><?= $jenis_daftar ?></b></div>
        <hr style="border: 0; border-top: 5px solid black; margin: 20px 100px;">
        <div><b><?= $poli ?></b></div>
    </center>

</body>

</html>