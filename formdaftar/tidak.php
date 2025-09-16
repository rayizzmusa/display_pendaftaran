<?php
if (preg_match("/\btidak.php\b/i", $_SERVER['REQUEST_URI'])) {
    exit;
} else {

    $link_back = "$default?act=$act";
    switch ($act3) {
        default:
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
            echo "    <p>Silahkan isi data diri pasien sesuai dengan Nomor KTP/NIK !</p>";
            echo "  </div>";
            echo "</div>";

            $sqld = "select * from db_jadwal_dokter where id=? and hapus=\"0\"";
            $stmt = mysqli_prepare($db_result, $sqld);
            mysqli_stmt_bind_param($stmt, 'i', $gid2);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $fdata = mysqli_fetch_assoc($result);
            $id_poli = $fdata['id_poli'];
            $id_pegawai = $fdata['id_pegawai'];
            $jam_praktek = substr($fdata['jam_awal'], 0, 5) . " - " . substr($fdata['jam_akhir'], 0, 5);
            $nama_poli = SelPoli($id_poli)[$id_poli]['nama_poli'];
            $sdokter = SelPegawai();
            $gelar_depan = $sdokter[$id_pegawai]['gelar_depan'];
            $gelar_belakang = $sdokter[$id_pegawai]['gelar_belakang'];
            $nama_pegawai = $sdokter[$id_pegawai]['nama_pegawai'];
            $nama_dokter = NamaGabung($nama_pegawai, $gelar_depan, $gelar_belakang);
            mysqli_stmt_close($stmt);

            $ftgl1 = TglFormat4($ndate);
            $hariIndex = date('N', strtotime($ndate));
            $hari = NamaHari($hariIndex);


            if ($act == "bpjs") {
                $form = "<div class=\"form-group\">
                            <label class=\"col-sm-3\">Nomor BPJS <span style=\"color:red\">*</span></label>
                            <div class=\"col-sm-4\">
                                <input type=\"text\" name=\"nomor_asuransi\" id=\"nomor-asuransi\" class=\"form-control\" required/>
                            </div>
                        </div>";
            } else if ($act == "nonbpjs") {
                $form = "";
            }

            echo "<div class=\"row\">
                    <div class=\"col-md-12\">
                        <div class=\"box box-primary\">
                            <div class=\"box-header border-bottom1\">
                                <h3 class=\"box-title\"><b>Formulir pendaftaran $nama_poli</b></h3>
                            </div>

                            <div class=\"box-body\">
                                <form role=\"form\" class=\"form-horizontal\" method=\"post\" action=\"$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket\" enctype=\"multipart/form-data\">
                                    <div class=\"row\">
                                        <div class=\"col-md-12\">
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Hari/Tanggal</label>
                                                <div class=\"col-sm-6\">
                                                    : $hari/$ftgl1
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Dokter</label>
                                                <div class=\"col-sm-6\">
                                                    : $nama_dokter
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Jam Praktek</label>
                                                <div class=\"col-sm-6\">
                                                    : $jam_praktek
                                                </div>
                                            </div>
                                            <div class=\"form-group\" style=\"margin-bottom: 0;\">
                                                <div class=\"col-sm-12\">
                                                    <label class=\"col-sm-9 pull-right\">Periksa identitas berdasarkan</label>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <div class=\"col-sm-6\">
                                                    <div class=\"col-sm-6 pull-right\">
                                                        <input type=\"radio\" name=\"jenis_identitas\" id=\"radio-nik\" value=\"nik\" required/> No.Ktp/NIK
                                                        <input type=\"radio\" name=\"jenis_identitas\" id=\"radio-rm\" value=\"rm\" style=\"margin-left: 85px;\"> No.RM
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">No. KTP/NIK/RM <span style=\"color:red\">*</span></label>
                                                <div class=\"col-sm-4\">
                                                    <input type=\"text\" name=\"identitas\" id=\"identitas-pasien\" class=\"form-control\" required/>
                                                    <small>Periksa apakah no.ktp/nik/rm sudah terdaftar sebagai pasien RSU UMM</small>
                                                    <div id=\"notifikasi-pasien\"></div>
                                                </div>
                                                <div class=\"col-sm-4\">
                                                <button type=\"button\" class=\"btn bg-maroon\" id=\"btn-cek\"><i class=\"fa fa-search\"></i></button>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Nama Lengkap <span style=\"color:red\">*</span></label>
                                                <div class=\"col-sm-4\">
                                                    <input type=\"text\" name=\"nama_pasien\" id=\"nama-pasien\" class=\"form-control\" required/>
                                                </div>
                                            </div>
                                            $form
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Tanggal Lahir <span style=\"color:red\">*</span></label>
                                                <div class=\"col-sm-4\">
                                                    <input type=\"text\" name=\"tgl_lahir\" id=\"tgl-lahir\" class=\"form-control datepicker\" autocomplete =\"off\" placeholder=\"yyyy-mm-dd\" required/>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label class=\"col-sm-3\">Jenis Kelamin <span style=\"color:red\">*</span></label>
                                                <div class=\"col-sm-4\">
                                                    <div class=\"col-sm-4\">
                                                        <input type=\"radio\" name=\"jenis_kelamin\" id=\"jk-laki\" value=\"1\" required> Laki-laki
                                                    </div>
                                                    <div class=\"col-sm-4\">
                                                        <input type=\"radio\" name=\"jenis_kelamin\" id=\"jk-perempuan\" value=\"2\"> Perempuan
                                                    </div>
                                                </div>
                                            </div>

                                            <div class=\"form-group\">
                                                <div class=\"col-md-12 text-center\">
                                                    <a href=\"$link_back&act2=poli&gid=$gid\" class=\"btn bg-navy\"><i class=\"fa fa-caret-left\"></i> Kembali</a>
                                                    <button type=\"submit\" class=\"btn btn-info\"><i class=\"fa fa-save\"></i> Daftar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            break;

        case "proses":
            if (count($_POST) > 0) {
                foreach ($_POST as $pkey => $pvalue) {
                    $post1 = mysqli_escape_string($db_result, $pvalue);
                    $post1 = preg_replace('/\s+/', ' ', $post1);
                    $post1 = trim($post1);

                    $arpost[$pkey] = "$post1";
                }
                extract($arpost);
            }

            $error = "";
            $error .= (!ctype_digit($arpost['identitas'])) ? "&bull; Format isian hanya boleh angka 0-9<br/>" : "";
            if ($arpost['jenis_identitas'] == 'nik') {
                $error .= (strlen($arpost['identitas']) != 16) ? "&bull; No.KTP/NIK harus 16 digit<br/>" : "";
                $identitas_pasien = 1;
            } else if ($arpost['jenis_identitas'] == 'rm') {
                $error .= (strlen($arpost['identitas']) != 7) ? "&bull; Nomor RM harus 7 digit<br/>" : "";
                $identitas_pasien = 2;
            }

            if (empty($error)) {
                mysqli_begin_transaction($db_result);
                $sqld = "select coalesce(max(no_antri), 0) + 1 from ps_pendaftaran_online where id_jadwal_dokter =? and tgl_daftar=\"$ndate\" and hapus=\"0\" for update";
                $stmt = mysqli_prepare($db_result, $sqld);
                mysqli_stmt_bind_param($stmt, "i", $id_jadwal_dokter);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $next_no_antri);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                if ($act == 'bpjs') {
                    $id_asuransi = 1;
                } else {
                    $id_asuransi = 0;
                }

                $vdata = "nama_pasien, id_asuransi, tgl_lahir, jenis_kelamin, identitas_pasien, no_identitas, nomor_asuransi, identitas, tgl_daftar, id_instalasi, id_jadwal_dokter, no_antri, jenis_daftar, hapus, tgl_insert";
                $vvalues = "?, \"$id_asuransi\", ?, ?, \"$identitas_pasien\", ?, ?, \"1\", \"$ndate\", \"$gid\", \"$gid2\", \"$next_no_antri\", \"2\", \"0\", \"$ndatetime\"";
            }
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

                    }
                    if (response.status === "error") {
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