<?php

include "../../setting/kon.php";
include "../../setting/function.php";
include "../../setting/variable.php";

$gid  = $_GET['gid'] ?? null;
$gid2 = $_GET['gid2'] ?? null;
$sqld = "select * from ps_pendaftaran_online where id=\"$gid\" and id_jadwal_dokter =\"$gid2\" and hapus =\"0\"";
$data = mysqli_query($db_result, $sqld);
$ndata = mysqli_num_rows($data);
if ($ndata > 0) {
    $fdata = mysqli_fetch_assoc($data);

    $id_asuransi = $fdata['id_asuransi'];
    if ($id_asuransi == '0') {
        $jenis_pasien = "Pasien Umum";
        $no_asuransi = "";
    } else if ($id_asuransi == '1') {
        $jenis_pasien = "Pasien BPJS";
        $no_asuransi = "/ " . $fdata['nomor_asuransi'];
    }

    date_default_timezone_set('Asia/Jakarta');
    $jam = date("H:i:s");
    $tgl_daftar1 = TglFormat4($fdata['tgl_daftar']);
    $tgl_daftar2 = TglFormat1($fdata['tgl_daftar']);
    // $jam = substr($fdata['tgl_insert'], 11, 18);
    $waktu = "$tgl_daftar1 Pukul $jam";

    $no_rm = !empty($fdata['nomor_rm']) ? "/ " . $fdata['nomor_rm'] : "";
    $pasien = $fdata['nama_pasien'] . " $no_rm" . " $no_asuransi";

    $kode_booking = $fdata['kode_booking'];

    $id_jadwal_dokter = $fdata['id_jadwal_dokter'];
    $sJadwalDokter = JadwalDokter();
    $jam_awal = $sJadwalDokter[$id_jadwal_dokter]['jam_awal'];
    $jam_akhir = $sJadwalDokter[$id_jadwal_dokter]['jam_akhir'];
    if ($jam_awal <= "12:00" && $id_asuransi == '1') {
        $abjad = "A-";
    } else if ($jam_awal > "12:00" && $id_asuransi == '1') {
        $abjad = "B-";
    } else if ($jam_awal <= "12:00" && $id_asuransi == '0') {
        $abjad = "C-";
    } else if ($jam_awal > "12:00" && $id_asuransi == '0') {
        $abjad = "D-";
    }
    $no_antri = "$abjad" . $fdata['no_antri'];

    $jenis_daftar = ($fdata['jenis_daftar'] == '2') ? "Admisi/TPP" : "";

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
                margin: 5px;
                padding: 0;
            }

            body {
                font-size: 25px;
                line-height: 1.0;
            }

            p {
                line-height: 1.0;
                font-size: 23px;
            }
        }
    </style>

    <center>
        <div>Faskes Tingkat Lanjut</div>
        <div>RSU Universitas Muhammadiyah Malang</div>
        <div><b><?php echo $jenis_daftar ?></b></div>
        <hr style="border: 0; border-top: 5px solid black; margin: 20px 100px;">
        <div>Kode Booking : <b><?php echo $kode_booking ?></b></div>
        <p style="font-size: 150px; margin: 0px 0;"><?php echo $no_antri ?></p>
        <div><?php echo $pasien ?></div>
        <p><?php echo $jenis_pasien ?></p>
        <hr style="border: 0; border-top: 5px solid black; margin: 20px 100px;">
        <div><b><?php echo $poli ?></b></div>
        <div>Jam Praktek : <?php echo " $tgl_daftar2 " . substr($jam_awal, 0, 5) . " - " . substr($jam_akhir, 0, 5); ?></div>
        <br>
        <p><b>Harap Check-in 30 menit sebelum praktek</b></p>
        <p><em>*) kode antrian A dan C akan dipanggil pada jam 07:00 sd selesai </em></p>
        <p><em>*) kode antrian B dan D akan dipanggil pada jam 12:00 sd selesai </em></p>
        <br>
        <p><?php echo $waktu ?></p>
    </center>

</body>

</html>