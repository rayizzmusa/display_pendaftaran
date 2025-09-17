<?php
include "../../setting/kon.php";
include "../../setting/function.php";
include "../../setting/variable.php";

$identitas = isset($_POST['identitas']) ? trim($_POST['identitas']) : '';
$jenis_identitas = isset($_POST['jenis_identitas']) ? trim($_POST['jenis_identitas']) : '';


// Validasi panjang input
if (empty($identitas) || strlen($identitas) > 16) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Input tidak valid.'
    ]);
    exit;
}

$sqld = '';
if ($jenis_identitas == 'nik') {
    $sqld = "select nama_pasien, tgl_lahir, jenis_kelamin, no_bpjs from db_pasien where no_identitas=? and hapus=\"0\" limit 1";
} else if ($jenis_identitas == 'rm') {
    $sqld = "select nama_pasien, tgl_lahir, jenis_kelamin, no_bpjs from db_pasien where kode_rm=? and hapus=\"0\" limit 1";
}

$stmt = mysqli_prepare($db_result, $sqld);
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error.'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $identitas);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$response = [];
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $response['status'] = 'success';
    $response['data'] = $row;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Data pasien tidak ditemukan.';
}

echo json_encode($response);
mysqli_stmt_close($stmt);
