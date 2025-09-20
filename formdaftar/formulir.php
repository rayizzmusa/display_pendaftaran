<?php
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

if ($gket == "ya") {
    $readonly = "readonly style=\"background-color: #f5f5f5;\"";
    $datepicker = "";
    $radioStyle = "style=\"pointer-events:none;\"";
} else if ($gket == "tdk") {
    $readonly = "";
    $datepicker = "datepicker";
    $radioStyle = "";
}


if ($act == "bpjs") {
    $form = "<div class=\"form-group\">
                    <label class=\"col-sm-3\">Nomor BPJS <span style=\"color:red\">*</span></label>
                    <div class=\"col-sm-9\">
                        <input type=\"text\" name=\"nomor_asuransi\" id=\"nomor-asuransi\" class=\"form-control\" $readonly required/>
                    </div>
                </div>";
} else if ($act == "nonbpjs") {
    $form = "";
}

$ndt1 = date("YmdHis");
$kode_booking = "RSUMM_$ndt1";

echo "<div class=\"row\">
            <div class=\"col-md-12\">
                <div class=\"box box-primary\">
                    <div class=\"box-header border-bottom1\">
                        <h3 class=\"box-title\"><b>Formulir pendaftaran $nama_poli</b></h3>
                    </div>

                    <div class=\"box-body\">
                        <form role=\"form\" class=\"form-horizontal\" method=\"post\" action=\"$link_back&act2=$act2&gid=$gid&gid2=$gid2&gket=$gket&act3=proses\" enctype=\"multipart/form-data\">
                            <div class=\"row\">
                                <div class=\"col-md-6\">
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
                                    <div class=\"form-group\" >
                                            <label class=\"col-sm-10 text-center\">Periksa dan Isi identitas berdasarkan</label>
                                            <div class=\"col-sm-10 text-center\">
                                                    <input type=\"radio\" name=\"jenis_identitas\" id=\"radio-nik\" value=\"nik\" required/> No.Ktp/NIK
                                                    <input type=\"radio\" name=\"jenis_identitas\" id=\"radio-rm\" value=\"rm\" style=\"margin-left: 85px;\"> No.RM
                                            </div>
                                    </div>
                                    <div class=\"form-group\">
                                        <label class=\"col-sm-3\">No. KTP/NIK/RM <span style=\"color:red\">*</span></label>
                                        <div class=\"col-sm-9\" >
                                            <div class=\"input-group\">
                                                <input type=\"text\" name=\"identitas\" id=\"identitas-pasien\" class=\"form-control\" required/>
                                                <input type=\"hidden\" name=\"kode_booking\" value=\"$kode_booking\"/>
                                                <span class=\"input-group-btn\">
                                                    <button type=\"button\" style=\"margin-left: 0px\" class=\"btn bg-maroon\" id=\"btn-cek\"><i class=\"fa fa-search\"></i></button>
                                                </span>
                                            </div>
                                            <small>Periksa apakah no.ktp/nik/rm sudah terdaftar sebagai pasien RSU UMM</small>
                                            <div id=\"notifikasi-pasien\"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class=\"col-md-6\">
                                    <div class=\"form-group\">
                                        <label class=\"col-sm-3\">Nama Lengkap <span style=\"color:red\">*</span></label>
                                        <div class=\"col-sm-9\">
                                            <input type=\"text\" name=\"nama_pasien\" id=\"nama-pasien\" class=\"form-control\" $readonly required/>
                                        </div>
                                    </div>
                                    $form
                                    <div class=\"form-group\">
                                        <label class=\"col-sm-3\">Tanggal Lahir <span style=\"color:red\">*</span></label>
                                        <div class=\"col-sm-9\">
                                            <input type=\"text\" name=\"tgl_lahir\" id=\"tgl-lahir\" class=\"form-control $datepicker\" autocomplete =\"off\" placeholder=\"yyyy-mm-dd\" $readonly required/>
                                        </div>
                                    </div>
                                    <div class=\"form-group\">
                                        <label class=\"col-sm-3\">Jenis Kelamin <span style=\"color:red\">*</span></label>
                                        <div class=\"col-sm-6\">
                                            <div class=\"col-sm-6\">
                                                <input type=\"radio\" name=\"jenis_kelamin\" id=\"jk-laki\" value=\"1\" $radioStyle required> Laki-laki
                                            </div>
                                            <div class=\"col-sm-6\">
                                                <input type=\"radio\" name=\"jenis_kelamin\" id=\"jk-perempuan\" value=\"2\" $radioStyle > Perempuan
                                            </div>
                                        </div>
                                    </div>

                                    <div class=\"row\">
                                        <div class=\"col-md-12\">
                                            <div class=\"clearfix\"></div>
                                            <br/>
                                            <div class=\"alert alert-info\">
                                                *** Pastikan data yang diisi sesuai dengan identitas pasien sebelum mencetak tiket
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                    <div class=\"form-group\">
                                        <div class=\"col-md-12 text-center\">
                                            <a href=\"$link_back&act2=poli&gid=$gid\" class=\"btn bg-navy\"><i class=\"fa fa-caret-left\"></i> Kembali</a>
                                            <button type=\"submit\" class=\"btn btn-success\"><i class=\"fa fa-print\"></i> Daftar</button>
                                        </div>
                                    </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        ";
