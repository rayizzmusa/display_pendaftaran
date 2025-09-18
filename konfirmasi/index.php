<?php
if (preg_match("/\bindex.php\b/i", $_SERVER['REQUEST_URI'])) {
    exit;
} else {
    switch ($act4) {
        default:
            echo "<div class=\"row\" style=\"align-items: center;\">";
            echo "  <div class=\"col-md-3\">";
            echo "      <nav aria-label=\"breadcrumb\">
                    <ol class=\"breadcrumb\" style=\"background-color: transparent;\">
                        <li class=\"breadcrumb-item\"><a href=\"$default\"><i class=\"fa fa-home\"></i></a></li>
                        <li class=\"breadcrumb-item\"><a href=\"$link_back\">poliklinik</a></li>
                        <li class=\"breadcrumb-item\"><a href=\"$link_back&act2=poli&gid=$gid\">dokter</a></li>
                        <li class=\"breadcrumb-item active\" aria-current=\"page\">formulir</li>
                        <li class=\"breadcrumb-item active\" aria-current=\"page\">konfirmasi</li>
                    </ol>
                </nav>";
            echo "  </div>";
            echo "  <div class=\"col-md-6 text-center\">";
            echo "    <p>Silahkan Konfirmasi data pendaftaran !</p>";
            echo "  </div>";
            echo "</div>";

            $sqld = "select * from ps_pendaftaran_online where id=? and id_jadwal_dokter=? and hapus=\"0\"";
            $stmt = mysqli_prepare($db_result, $sqld);
            mysqli_stmt_bind_param($stmt, "ii", $gid3, $gid2);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $ndata = mysqli_num_rows($result);
            if ($ndata == 1) {
                $fdata = mysqli_fetch_assoc($result);
                $id_poli = $fdata['id_instalasi'];
                $nama_poli = SelPoli($id_poli)[$id_poli]['nama_poli'];
                $kode_booking = $fdata['kode_booking'];
                $tgl_daftar = TglFormat4($fdata['tgl_daftar']);
                $hariIndex = date('N', strtotime($fdata['tgl_daftar']));
                $hari = NamaHari($hariIndex);
                $id_jadwal_dokter = $fdata['id_jadwal_dokter'];
                $sJadwalDokter = JadwalDokter();
                $id_pegawai = $sJadwalDokter[$id_jadwal_dokter]['id_pegawai'];
                $sdokter = SelPegawai();
                $gelar_depan = $sdokter[$id_pegawai]['gelar_depan'];
                $gelar_belakang = $sdokter[$id_pegawai]['gelar_belakang'];
                $nama_pegawai = $sdokter[$id_pegawai]['nama_pegawai'];
                $nama_dokter = NamaGabung($nama_pegawai, $gelar_depan, $gelar_belakang);
                $jam_awal = $sJadwalDokter[$id_jadwal_dokter]['jam_awal'];
                $jam_akhir = $sJadwalDokter[$id_jadwal_dokter]['jam_akhir'];
                $jam_praktek = substr($jam_awal, 0, 5) . " - " . substr($jam_akhir, 0, 5);
                $no_identitas = !empty($fdata['no_identitas']) ? $fdata['no_identitas'] : $fdata['nomor_rm'] . " (no.RM)";
                $nama_lengkap = $fdata['nama_pasien'];
                $tgl_lahir = TglFormat1($fdata['tgl_lahir']);
                if ($fdata['jenis_kelamin'] == '1') {
                    $jenis_kelamin = "Laki-laki";
                } else {
                    $jenis_kelamin = "Perempuan";
                }
                $no_antri = $fdata['no_antri'];

                if ($fdata['id_asuransi'] == '1') {
                    $bpjs = "
                    <tr>
                        <th width=\"300\">Nomor BPJS</th>
                        <td>: " . $fdata['nomor_asuransi'] . "</td>
                    </tr>
                    ";
                } else {
                    $bpjs = "";
                }



                echo "
            <div class=\"row\">
                <div class=\"col-md-12\">
                    <div class=\"box box-primary\">
                        <div class=\"box-header border-bottom1\">
                            <h3 class=\"box-title\"><b>Formulir konfirmasi pendaftaran $nama_poli</b></h3>
                        </div>

                        <div class=\"box-body\">
                            <div class=\"row\">
                                <div class=\"col-md-12\">
                                    <div class=\"alert alert-info\">
                                        Periksa kembali data pendaftaran dan identitas pasien, jika sudah sesuai silahkan tekan tombol <b>Konfirmasi</b>
                                    </div>
                                </div>
                            </div>
                            <form role=\"form\" class=\"form-horizontal\" method=\"post\" action=\"#\" enctype=\"multipart/form-data\">
                                <div class=\"row\">
                                    <div class=\"col-md-9\">
                                        <div class=\"table-reponsive\">
                                            <table class=\"table no-border\">
                                                <tr>
                                                    <th width=\"300\">Hari/Tanggal</th>
                                                    <td>: $hari/$tgl_daftar</td>
                                                </tr>
                                                <tr>
                                                    <th width=\"300\">Dokter</th>
                                                    <td>: $nama_dokter</td>
                                                </tr>
                                                <tr>
                                                    <th width=\"300\">Jam Praktek</th>
                                                    <td>: $jam_praktek</td>
                                                </tr>
                                                <tr>
                                                    <th width=\"300\">No. KTP/NIK/RM</th>
                                                    <td>: $no_identitas</td>
                                                </tr>
                                                <tr>
                                                    <th width=\"300\">Nama Lengkap</th>
                                                    <td>: $nama_lengkap</td>
                                                </tr>
                                                $bpjs
                                                <tr>
                                                    <th width=\"300\">Tanggal Lahir</th>
                                                    <td>: $tgl_lahir</td>
                                                </tr>
                                                <tr>
                                                    <th width=\"300\">Jenis Kelamin</th>
                                                    <td>: $jenis_kelamin</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class=\"form-group\">
                                            <div class=\"col-md-7\"></div>
                                            <div class=\"col-md-5\">
                                                <a href=\"#\" class=\"btn btn-warning\"><i class=\"fa fa-pencil\"></i> Edit</a>
                                                <button type=\"submit\" class=\"btn btn-info\">Konfirmasi</button>
                                                <a href=\"#\" class=\"btn btn-danger\"><i class=\"fa fa-trash\"></i> Batalkan</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"col-md-3\">
                                        <div class=\"form-group\">
                                            <label class=\"col-sm-12 text-center\">Nomor Antrian : <br/> <p style=\"font-size : 60px;\">$no_antri</p></label>
                                        </div>
                                        <div class=\"form-group\">
                                            <label class=\"col-sm-12 text-center\">Kode Booking :</label>
                                            <label class=\"col-sm-12 text-center\" style=\"font-size: 25px;\">$kode_booking</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            ";
            } else {
                echo "ganok";
            }
    }
}
