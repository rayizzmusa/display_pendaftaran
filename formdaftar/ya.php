<?php
if (preg_match("/\btidak.php\b/i", $_SERVER['REQUEST_URI'])) {
    exit;
} else {

    $link_back = "$default?act=$act";
    switch ($act3) {
        case "input":
        case "proses":
            echo "<div class=\"row\" style=\"align-items: center;\">";
            echo "  <div class=\"col-md-3\">";
            echo "      <nav aria-label=\"breadcrumb\">
                                <ol class=\"breadcrumb\" style=\"background-color: transparent;\">
                                    <li class=\"breadcrumb-item\"><a href=\"$default\"><i class=\"fa fa-home\"></i></a></li>
                                    <li class=\"breadcrumb-item\"><a href=\"$link_back\">poliklinik</a></li>
                                    <li class=\"breadcrumb-item\"><a href=\"$link_back&act2=poli&gid=$gid\">dokter</a></li>
                                    <li class=\"breadcrumb-item active\" aria-current=\"page\">formulir</i></li>
                                </ol>
                            </nav>";
            echo "  </div>";
            echo "  <div class=\"col-md-6 text-center\">";
            echo "    <p>Silahkan isi data diri pasien sesuai dengan Nomor KTP/NIK/RM !</p>";
            echo "  </div>";
            echo "</div>";
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (count($_POST) > 0) {
                    foreach ($_POST as $pkey => $pvalue) {
                        $post1 = mysqli_escape_string($db_result, $pvalue);
                        $post1 = preg_replace('/\s+/', ' ', $post1);
                        $post1 = trim($post1);

                        $arpost[$pkey] = "$post1";
                    }
                    extract($arpost);
                }

                $no_identitas = "";
                $nomor_rm = "";
                $error = "";
                $error .= (!ctype_digit($arpost['identitas'])) ? "&bull; Format isian hanya boleh angka 0-9<br/>" : "";
                if ($arpost['jenis_identitas'] == 'nik') {
                    $error .= (strlen($arpost['identitas']) != 16) ? "&bull; No.KTP/NIK harus 16 digit<br/>" : "";
                    $identitas_pasien = 1;
                    $no_identitas = $arpost['identitas'];
                } else if ($arpost['jenis_identitas'] == 'rm') {
                    $error .= (strlen($arpost['identitas']) != 7) ? "&bull; Nomor RM harus 7 digit<br/>" : "";
                    $identitas_pasien = 2;
                    $nomor_rm = $arpost['identitas'];
                }

                if (empty($error)) {

                    if ($arpost['jenis_identitas'] == 'nik') {
                        $sqld = "select * from ps_pendaftaran_online where no_identitas = ? and id_jadwal_dokter = ? and tgl_daftar = ? and hapus=\"0\"";
                        $stmt = mysqli_prepare($db_result, $sqld);
                        mysqli_stmt_bind_param($stmt, "sis", $no_identitas, $gid2, $ndate);
                    } else if ($arpost['jenis_identitas'] == 'rm') {
                        $sqld = "select * from ps_pendaftaran_online where nomor_rm = ? and id_jadwal_dokter = ? and tgl_daftar = ? and hapus=\"0\"";
                        $stmt = mysqli_prepare($db_result, $sqld);
                        mysqli_stmt_bind_param($stmt, "sis", $nomor_rm, $gid2, $ndate);
                    }
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $ndata = mysqli_num_rows($result);
                    mysqli_stmt_close($stmt);
                    if ($ndata == 0) {
                        mysqli_begin_transaction($db_result);
                        $sqld1 = "select coalesce(max(no_antri), 0) + 1 from ps_pendaftaran_online where id_jadwal_dokter =? and tgl_daftar=\"$ndate\" and hapus=\"0\" for update";
                        $stmt1 = mysqli_prepare($db_result, $sqld1);
                        mysqli_stmt_bind_param($stmt1, "i", $gid2);
                        mysqli_stmt_execute($stmt1);
                        mysqli_stmt_bind_result($stmt1, $next_no_antri);
                        mysqli_stmt_fetch($stmt1);
                        mysqli_stmt_close($stmt1);

                        if ($act == 'bpjs') {
                            $id_asuransi = 1;
                            $nomor_asuransi = $arpost['nomor_asuransi'];
                        } else {
                            $id_asuransi = 0;
                            $nomor_asuransi = '';
                        }


                        $vdata = "nama_pasien, id_asuransi, tgl_lahir, jenis_kelamin, identitas_pasien, no_identitas, nomor_rm, nomor_asuransi, identitas, tgl_daftar, id_instalasi, id_jadwal_dokter, no_antri, kode_booking, jenis_daftar, hapus, tgl_insert";

                        $sqld2 = "insert into ps_pendaftaran_online ($vdata) values (?, ?, ?, ?, ?, ?, ?, ?, \"0\", ?, ?, ?, ?, ?, \"2\", \"0\", ?)";

                        $stmt2 = mysqli_prepare($db_result, $sqld2);
                        mysqli_stmt_bind_param(
                            $stmt2,
                            "sisiissssiiiss", // sesuaikan jumlah/tipe data
                            $nama_pasien,
                            $id_asuransi,
                            $tgl_lahir,
                            $jenis_kelamin,
                            $identitas_pasien,
                            $no_identitas,
                            $nomor_rm,
                            $nomor_asuransi,
                            $ndate,
                            $gid,
                            $gid2,
                            $next_no_antri,
                            $kode_booking,
                            $ndatetime
                        );
                        $sukses = mysqli_stmt_execute($stmt2);
                        mysqli_stmt_close($stmt2);

                        if ($sukses) {
                            $id_daftar = mysqli_insert_id($db_result);
                            mysqli_commit($db_result);
                            echo "
                            <script>
                                Swal.fire({
                                    icon: 'success',
                                    title: '<span style=\"font-family: Source Sans Pro; font-size:25px;\"><b>Pengisian Data Berhasil</b></span>',
                                    html: '<span style=\"font-family: Source Sans Pro; font-size:18px;\">Silahkan cetak tiket pendaftaran</span>',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    didClose: () => {
                                        PopUp('http://localhost:8080/$default/cetak/index.php?gid=$id_daftar&gid2=$gid2');
                                        window.location.href = '$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket&act3=input';
                                    }
                                })
                            </script>
                            ";
                        } else {
                            mysqli_rollback($db_result);
                            echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: '<span style=\"font-family: Source Sans Pro; font-size:25px;\"><b>Pengisian Data Gagal</b></span>',
                                    html: '<span style=\"font-family: Source Sans Pro; font-size:18px;\">Silahkan isi ulang formulir</span>',
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    window.location.href = '$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket&act3=input';
                                });
                            </script>
                            ";
                        }
                    } else {
                        $nket = "<div class=\"alert alert-warning\">No.KTP/NIK/RM ini sudah melakukan pendaftaran</div>";
                        echo "<div class=\"row\"><div class=\"col-md-12\">$nket</div></div>
                        <meta http-equiv=\"refresh\" content=\"2;url=$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket&act3=input\">";
                    }
                } else {
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: '<span style=\"font-family: Source Sans Pro; font-size:25px;\"><b>Pengisian Data Gagal</b></span>',
                                html: '<span style=\"font-family: Source Sans Pro; font-size:18px;\">$error</span>',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                window.location.href = '$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket&act3=input';
                            });
                        </script>
                        ";
                }
            }

            include "formulir.php";
            break;
    }
}

?>

<script>
    $(document).ready(function() {
        $('#btn-cek').on('click', function() {
            // Reset notifikasi sebelumnya dan form
            $('#notifikasi-pasien').html('');
            $('#nama-pasien').val('');
            $('#tgl-lahir').val('');
            $('input[name="jenis_kelamin"]').prop('checked', false);
            $('#nomor-asuransi').val(''); // Tambahkan reset untuk input BPJS

            let identitas = $('#identitas-pasien').val();
            let jenis_identitas = $('input[name="jenis_identitas"]:checked').val();

            if (!identitas || !jenis_identitas) {
                $('#notifikasi-pasien').html('<small style="color:red;">Silakan masukkan NIK/RM dan pilih jenis identitas.</small>');
                return;
            }

            $.ajax({
                url: "/a_disp_pendaftaran/formdaftar/cekNikRm.php",
                type: 'POST',
                data: {
                    identitas: identitas,
                    jenis_identitas: jenis_identitas
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let data = response.data;

                        // Isi formulir utama
                        $('#nama-pasien').val(data.nama_pasien);
                        $('#tgl-lahir').val(data.tgl_lahir);

                        if (data.jenis_kelamin == '1') {
                            $('#jk-laki').prop('checked', true);
                        } else if (data.jenis_kelamin == '2') {
                            $('#jk-perempuan').prop('checked', true);
                        }

                        // Cek dan isi nomor BPJS jika elemennya ada
                        if ($('#nomor-asuransi').length) {
                            $('#nomor-asuransi').val(data.no_bpjs); // Pastikan nama kolom di DB adalah 'nomor_asuransi'
                        }

                        let pesan = `No. ${jenis_identitas.toUpperCase()} ${identitas} ditemukan.`;
                        $('#notifikasi-pasien').html(`<small style="color:green;">${pesan}</small>`);

                    } else if (response.status === "error") {
                        $('#notifikasi-pasien').html('<small style="color:red;">' + response.message + '</small>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);

                    $('#notifikasi-pasien').html('<small style="color:red;">Terjadi kesalahan saat mencari data. Silakan coba lagi.</small>');
                }
            });
        });
    });
</script>