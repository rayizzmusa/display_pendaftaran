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

            include "formulir.php";
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
            } else if ($arpost['jenis_identitas'] == 'rm') {
                $error .= (strlen($arpost['identitas']) != 7) ? "&bull; Nomor RM harus 7 digit<br/>" : "";
            }

            if (empty($error)) {
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