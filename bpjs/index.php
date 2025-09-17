<?php
if (preg_match("/\bindex.php\b/i", $_SERVER['REQUEST_URI'])) {
    exit;
} else {

    $link_back = "$default?act=$act";

    switch ($act2) {
        default:
            echo "<div class=\"row\" style=\"align-items: center;\">"; // supaya sejajar vertikal
            echo "  <div class=\"col-md-3\">";
            echo "      <nav aria-label=\"breadcrumb\">
                            <ol class=\"breadcrumb\" style=\"background-color: transparent;\">
                                <li class=\"breadcrumb-item\"><a href=\"$default\"><i class=\"fa fa-home\"></i></a></li>
                                <li class=\"breadcrumb-item active\" aria-current=\"page\">poliklinik</i></li>
                            </ol>
                        </nav>";
            echo "  </div>";
            echo "  <div class=\"col-md-6 text-center\">";
            echo "    <p>Silahkan pilih poliklinik yang ingin anda kunjungi !</p>";
            echo "  </div>";
            echo "</div>";


            $sqld = "select * from ms_poli where status_antrian='1' and status='1' and nama_poli like ? and hapus='0' order by id asc";
            $stmt = mysqli_prepare($db_result, $sqld);
            $search_term = "%$gname%";
            mysqli_stmt_bind_param($stmt, "s", $search_term);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $ndata = mysqli_num_rows($result);
            $box_poli = '';
            if ($ndata > 0) {
                while ($fdata = mysqli_fetch_assoc($result)) {
                    extract($fdata);
                    $nama_poli = isset($fdata['nama_poli']) ? $fdata['nama_poli'] : '';
                    $box_poli .= "
                    <div class=\"col-md-3\">
                        <a href=\"$link_back&act2=poli&gid=$id\">
                            <div class=\"info-box\">
                                <span class=\"info-box-icon bg-blue\"><i class=\"fa fa-stethoscope\"></i></span>

                                <div class=\"info-box-content\">
                                    <span class=\"info-box-number\">" . htmlspecialchars($nama_poli) . "</span>
                                </div>
                            </div>
                        </a>
                    </div>";
                }
            } else {
                $box_poli .= "<div class=\"col-md-12 text-center\" style=\"margin-top:50px\">
                <p>Poliklinik tidak ditemukan</p>
                </div>";
            }

            mysqli_stmt_close($stmt);

            echo "<div class=\"row\">
            <div class=\"col-md-4\"></div>
                <div class=\"col-md-8\">
                    <form role=\"form\" class=\"form-horizontal\" method=\"get\">
                        <input name=\"act\" type=\"hidden\" value=\"$act\" />

                        <div class=\"form-group\">
                            <div class=\"col-sm-4\">
                                <input type=\"text\" class=\"form-control\" name=\"gname\" style=\"background-color :transparent;\" placeholder=\"Cari Poliklinik\">
                            </div>
                            <div>
                                <button type=\"submit\" class=\"btn bg-blue\"><i class=\"fa fa-search\"></i> Cari</button>
                                <a href=\"$link_back\" class=\"btn bg-maroon\"><i class=\"fa fa-refresh\"></i> Refresh</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class=\"row\">
            $box_poli
            </div>";
            break;

        case "poli":
            $gid = filter_var($_GET['gid'], FILTER_VALIDATE_INT);
            if ($gid === false || $gid <= 0) {
                header("Location: $link_back");
                exit;
            }

            $sqld = "select * from ms_poli where id=? and status_antrian=\"1\" and status=\"1\" and hapus=\"0\"";
            $stmt = mysqli_prepare($db_result, $sqld);
            mysqli_stmt_bind_param($stmt, "i", $gid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $fdata = mysqli_fetch_assoc($result);
            $poli = $fdata['nama_poli'];
            mysqli_stmt_close($stmt);

            echo "<div class=\"row\" style=\"align-items: center;\">";
            echo "  <div class=\"col-md-3\">";
            echo "      <nav aria-label=\"breadcrumb\">
                                <ol class=\"breadcrumb\" style=\"background-color: transparent;\">
                                    <li class=\"breadcrumb-item\"><a href=\"$default\"><i class=\"fa fa-home\"></i></a></li>
                                    <li class=\"breadcrumb-item\"><a href=\"$link_back\">poliklinik</a></li>
                                    <li class=\"breadcrumb-item active\" aria-current=\"page\">dokter</i></li>
                                </ol>
                            </nav>";
            echo "  </div>";
            echo "  <div class=\"col-md-6 text-center\">";
            echo "    <p>Pilih dokter praktek di <b>$poli</b> !</p>";
            echo "  </div>";
            echo "</div>";

            $hariIndex = date('N', strtotime($ndate));

            $sqld2 = "select * from db_jadwal_dokter where id_poli=? and hari=? and hapus=\"0\" order by jam_awal asc";
            $stmt2 = mysqli_prepare($db_result, $sqld2);
            mysqli_stmt_bind_param($stmt2, "ii", $gid, $hariIndex);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            $ndata = mysqli_num_rows($result2);
            $box_dokter = "";
            $dokter_unik = []; // Array untuk melacak dokter yang sudah ditampilkan
            $data_praktek_per_dokter = []; // Array untuk menyimpan data praktek per dokter
            if ($ndata > 0) {
                while ($fdata2 = mysqli_fetch_assoc($result2)) {
                    $id = $fdata2['id'];
                    $id_pegawai = $fdata2['id_pegawai'];
                    $jam_awal = substr($fdata2['jam_awal'], 0, 5);
                    $jam_akhir = substr($fdata2['jam_akhir'], 0, 5);
                    $jam_praktek = "$jam_awal - $jam_akhir";
                    $kuota_bpjs = $fdata2['kuota_bpjs'];

                    $sqld3 = "select * from ps_pendaftaran_online where id_asuransi=\"1\" and tgl_daftar=? and id_instalasi=? and id_jadwal_dokter=? and hapus=\"0\"";
                    $stmt3 = mysqli_prepare($db_result, $sqld3);
                    mysqli_stmt_bind_param($stmt3, "sii", $ndate, $gid, $id);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);
                    $kuota_terpakai = mysqli_num_rows($result3);
                    mysqli_stmt_close($stmt3);

                    $persen_bpjs = ($kuota_bpjs > 0) ? ($kuota_terpakai / $kuota_bpjs) * 100 : 0;

                    $sqld4 = "select * from db_jadwal_dokter_ganti where id_jadwal_dokter=? and tgl_awal=? and hapus=\"0\"";
                    $stmt4 = mysqli_prepare($db_result, $sqld4);
                    mysqli_stmt_bind_param($stmt4, "is", $id, $ndate);
                    mysqli_stmt_execute($stmt4);
                    $result4 = mysqli_stmt_get_result($stmt4);
                    $fresult4 = mysqli_fetch_assoc($result4);
                    $status_ganti = isset($fresult4['jenis_status']) ? $fresult4['jenis_status'] : '';
                    mysqli_stmt_close($stmt4);

                    if (!isset($data_praktek_per_dokter[$id_pegawai])) {
                        $data_praktek_per_dokter[$id_pegawai] = [];
                    }

                    $data_praktek_per_dokter[$id_pegawai][] = [
                        'id_jadwal' => $id,
                        'jam_praktek' => $jam_praktek,
                        'kuota_bpjs' => $kuota_bpjs,
                        'kuota_terpakai'  => $kuota_terpakai,
                        'persen_bpjs' => $persen_bpjs,
                        'status_ganti' => $status_ganti
                    ];
                }
                // echo "<pre>";
                // print_r($data_praktek_per_dokter);
                // echo "</pre>";

                // Reset pointer hasil query untuk loop kedua
                mysqli_data_seek($result2, 0);

                while ($fdata2 = mysqli_fetch_assoc($result2)) {
                    extract($fdata2);
                    $id_pegawai = $fdata2['id_pegawai'];
                    // Jika dokter sudah ditampilkan, lewati
                    if (in_array($id_pegawai, $dokter_unik)) {
                        continue;
                    }
                    $dokter_unik[] = $id_pegawai;
                    $sdokter = SelPegawai();
                    $gelar_depan = $sdokter[$id_pegawai]['gelar_depan'];
                    $gelar_belakang = $sdokter[$id_pegawai]['gelar_belakang'];
                    $nama_pegawai = $sdokter[$id_pegawai]['nama_pegawai'];
                    $nama_dokter = NamaGabung($nama_pegawai, $gelar_depan, $gelar_belakang);


                    $collapseId = "menu_$id_pegawai";
                    $konten_box_body = '';

                    foreach ($data_praktek_per_dokter[$id_pegawai] as $data) {
                        $idJadwal = $data['id_jadwal'];
                        $modalId = "modal_" . $id_pegawai . "_" . $idJadwal;
                        if ($data['status_ganti'] == '0') {
                            $bg = "background-color: rgba(179, 0, 0, 0.2);";
                            $stts = "<span style=\"color: red\"><b>Dokter Off</b></span>";
                            $progres_bar = "";
                            $link = "style=\"text-decoration:none; color:inherit;\"";
                        } else if ($data['status_ganti'] == '1') {
                            $bg = "background-color: rgba(179, 146, 0, 0.2);";
                            $stts = "<span style=\"color: orange\"><b>Selesai</b></span>";
                            $progres_bar = "
                                <div class=\"progress\" style=\"height: 10px; border-radius: 10px\">
                                    <div class=\"progress-bar progress-bar-striped progress-bar-animated bg-orange\" role=\"progressbar\" style=\"width: " . $data['persen_bpjs'] . "%;\" aria-valuenow=\"" . $data['persen_bpjs'] . "\" aria-valuemin=\"0\" aria-valuemax=\"" . $data['kuota_bpjs'] . "\"></div>
                                </div>
                                <p>Kuota : " . $data['kuota_terpakai'] . "/" . $data['kuota_bpjs'] . " pasien</p>
                            ";
                            $link = "style=\"text-decoration:none; color:inherit;\"";
                        } else {
                            if ($data['kuota_terpakai'] == $data['kuota_bpjs'] - 1) {
                                $bg = "background-color: rgba(0, 179, 0, 0.2);";
                                $stts = "<span style=\"color: red\"><b>Kurang 1</b></span>";
                                $progres_bar = "
                                <div class=\"progress\" style=\"height: 10px; border-radius: 10px\">
                                    <div class=\"progress-bar progress-bar-striped progress-bar-animated bg-green\" role=\"progressbar\" style=\"width: " . $data['persen_bpjs'] . "%;\" aria-valuenow=\"" . $data['persen_bpjs'] . "\" aria-valuemin=\"0\" aria-valuemax=\"" . $data['kuota_bpjs'] . "\"></div>
                                </div>
                                <p>Kuota : " . $data['kuota_terpakai'] . "/" . $data['kuota_bpjs'] . " pasien</p>
                                ";
                                $link = "href=\"#\" style=\"text-decoration:none; color:inherit;\" data-toggle=\"modal\" data-target=\"#$modalId\"";
                            } else if ($data['kuota_terpakai'] == $data['kuota_bpjs']) {
                                $bg = "background-color: rgba(185, 185, 185, 0.38);";
                                $stts = "<span style=\"color: red\"><b>Kuota penuh</b></span>";
                                $progres_bar = "
                                <div class=\"progress\" style=\"height: 10px; border-radius: 10px\">
                                    <div class=\"progress-bar progress-bar-striped progress-bar-animated\" role=\"progressbar\" style=\"width: " . $data['persen_bpjs'] . "%; background-color:rgb(186, 186, 186) !important;\" aria-valuenow=\"" . $data['persen_bpjs'] . "\" aria-valuemin=\"0\" aria-valuemax=\"" . $data['kuota_bpjs'] . "\"></div>
                                </div>
                                <p>Kuota : " . $data['kuota_terpakai'] . "/" . $data['kuota_bpjs'] . " pasien</p>
                                ";
                                $link = "style=\"text-decoration:none; color:inherit;\"";
                            } else {
                                $bg = "background-color: rgba(0, 179, 0, 0.2);";
                                $stts = "<span style=\"color: green\"><b>Tersedia</b></span>";
                                $progres_bar = "
                                    <div class=\"progress\" style=\"height: 10px; border-radius: 10px\">
                                        <div class=\"progress-bar progress-bar-striped progress-bar-animated bg-green\" role=\"progressbar\" style=\"width: " . $data['persen_bpjs'] . "%;\" aria-valuenow=\"" . $data['persen_bpjs'] . "\" aria-valuemin=\"0\" aria-valuemax=\"" . $data['kuota_bpjs'] . "\"></div>
                                    </div>
                                    <p>Kuota : " . $data['kuota_terpakai'] . "/" . $data['kuota_bpjs'] . " pasien</p>
                                    ";
                                $link = "href=\"#\" style=\"text-decoration:none; color:inherit;\" data-toggle=\"modal\" data-target=\"#$modalId\"";
                            }
                        }

                        $konten_box_body .= "
                        <div class=\"row\">
                            <div class =\"col-md-12\">
                                <div class=\"box\">
                                    <a $link>
                                    <div class=\"box-body\" style=\"$bg\">
                                        <div class=\"col-sm-12\">
                                            <h4>" . $data['jam_praktek'] . "</h4>
                                            <p>Status : $stts</p>
                                            $progres_bar
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        ";

                        //modal pop-up
                        echo "
                    <div class=\"modal fade\" id=\"$modalId\" tabindex=\"-1\" role=\"dialog\">
                        <div class=\"modal-dialog\">
                            <div class=\"modal-content\" style=\"border-radius:10px\">
                                <div class=\"modal-header text-center\" style=\"background-color: #0097bc; border-radius:10px 10px 0 0\">
                                    <h4 class=\"modal-title\" style=\"color: white\">$nama_dokter</h4>
                                    <span style=\"color: white\">Jam Praktek " . $data['jam_praktek'] . "</span>
                                </div>
                                <div class=\"modal-body\">
                                    <div class=\"row text-center\">
                                        <h4>Apakah pasien memiliki nomor Rekam Medis (RM) RSU UMM ?</h4>
                                    </div>
                                    <div class=\"clearfix\"></div>
                                    <br />

                                    <div class=\"row\">
                                        <div class=\"col-md-6\">
                                            <a href=\"$link_back&act2=daftar&gid=$gid&gid2=$idJadwal&gket=tdk&act3=input\">
                                                <div class=\"box-btn bg-aqua\" style=\"height: 100px; display: flex; align-items: center; justify-content: center;\">
                                                    <div class=\"box-body\">
                                                        <span class=\"info-box-number\" style=\"font-size: 30px;\">TIDAK</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class=\"col-md-6\">
                                            <a href=\"$link_back&act2=daftar&gid=$gid&gid2=$idJadwal&gket=ya&act3=input\">
                                                <div class=\"box-btn bg-aqua\" style=\"height: 100px; display: flex; align-items: center; justify-content: center;\">
                                                    <div class=\"box-body\">
                                                        <span class=\"info-box-number\" style=\"font-size: 30px;\">YA</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"modal-footer\" style=\"text-align: center;\">
                                    <button type=\"button\" class=\"btn btn-info\" data-dismiss=\"modal\">Kembali</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                    }


                    $box_dokter .= "
                        <div class=\"col-md-4\">
                            <div class=\"box box-primary\">
                                <div class=\"box-header border-bottom1 text-decoration-none bg-blue\" style=\"height :50px; display: flex; align-items: center; justify-content: center;\">
                                    <h2 class=\"box-title\">$nama_dokter</h2>
                                </div>

                                    <div class=\"box-body\">
                                        $konten_box_body
                                    </div>
                            </div>
                        </div>
                        ";
                }

                echo "<div class=\"row\">
                        $box_dokter
                    </div>";
            } else {
                echo "  <div class=\"col-md-12 text-center\" style=\"margin-top:50px\">";
                echo "    <p>Tidak ada dokter di poliklinik ini, <a href=\"$link_back\">kembali</a> !</p>";
                echo "  </div>";
            }

            mysqli_stmt_close($stmt2);
            break;

        case "daftar":
            include "formdaftar/index.php";
            break;

        case "konfirmasi":
            include "konfirmasi/index.php";
            break;
    }
}
