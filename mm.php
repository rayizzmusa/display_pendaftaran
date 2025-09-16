<?php
ini_set('display_errors', 1);

include "../setting/kon.php";
include "../setting/function.php";
include "../setting/variable.php";

$link_back = "a_disp_pendaftaran";


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rumah Sakit Umum Universitas Muhammadiyah Malang</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="robots" content="noindex, nofollow">

    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="../bootstrap/css/font-awesome.min.css">

    <style>
        body {
            background-color: #ecf0f5;
        }

        .main-header .navbar {
            display: flex;
            /* Mengaktifkan Flexbox */
            align-items: center;
            /* Pusatkan secara vertikal */
            justify-content: space-between;
            /* Dorong elemen ke ujung */
            padding-right: 20px;
            /* Spasi di kanan untuk jam */
            background-color: #3c8dbc;
            /* Warna navbar AdminLTE */
            color: white;
            min-height: 50px;
            /* Tinggi navbar default AdminLTE */
        }

        .main-header .navbar .title-web {
            font-size: 22px;
            /* Ukuran font jam */
            font-weight: bold;
            white-space: nowrap;
            /* Mencegah jam melompat baris */
            color: white;
        }

        .main-header .navbar #jam {
            font-size: 20px;
            /* Ukuran font jam */
            font-weight: bold;
            white-space: nowrap;
            /* Mencegah jam melompat baris */
            color: white;
        }

        .logo {
            background-color: #367fa9 !important;
            /* Warna logo AdminLTE */
        }

        .content-wrapper {
            background-color: #ecf0f5;
            padding: 0px;
        }



        .small-box {
            border-radius: 10px;
            box-shadow: none !important;
            display: block;
            margin: 30px;
            position: relative;
            height: 400px;
        }

        .small-box>.inner {
            padding-top: 100px;
            padding-left: 50px;
        }


        .small-box h3 {
            font-size: 60px;
            font-weight: bold;
            margin: 0 0 10px;
            padding: 0;
            white-space: nowrap;
        }

        .small-box .icon {
            color: rgba(0, 0, 0, 0.15);
            font-size: 200px;
            padding-right: 20px;
            position: absolute;
            right: 10px;
            top: -10px;
            transition: all 0.3s linear 0s;
            z-index: 0;
        }

        .small-box:hover .icon {
            font-size: 210px;
        }

        .small-box>.small-box-footer {
            background: rgba(0, 0, 0, 0.1) none repeat scroll 0 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
            height: 35px;
            border-radius: 0 0 10px 10px;
            display: block;
            padding: 3px 0;
            position: relative;
            top: 160px;
            text-align: center;
            text-decoration: none;
            z-index: 10;
        }

        .small-box>.small-box-footer:hover {
            background: rgba(0, 0, 0, 0.15) none repeat scroll 0 0;
            box-shadow: none !important;
            color: #fff;
        }
    </style>

    <script src="../plugins/jQuery/jQuery-3.3.1.js"></script>
</head>

<body class="hold-transition sidebar-mini skin-blue">
    <div class="wrapper">

        <header class="main-header">
            <span class="logo">
                <span class="logo-mini"><img src="<?php echo "$phost"; ?>/images/hospital.png" width="45" height="45" alt="logo"></span>
                <span class="logo-lg"><img src="<?php echo "$phost"; ?>/images/hospital.png" width="45" height="45" alt="logo"></span>
            </span>

            <nav class="navbar navbar-static-top" role="navigation">
                <span class="title-web visible-lg">Rumah Sakit Umum Universitas Muhammadiyah Malang</span>
                <span class="title-web hidden-lg">RSU UMM</span>
                <span id="jam"></span>
            </nav>
        </header>

        <div class="content-wrapper" style='margin-left:0px !important;'>
            <div id="main-menu">
                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small-box bg-aqua" onclick="loadBPJS()" style="cursor:pointer;">
                                <div class="inner">
                                    <h3>BPJS</h3>
                                    <div class="clearfix"></div><br />
                                </div>
                                <div class="icon">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <div class="small-box-footer">
                                    untuk pasien BPJS
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small-box bg-aqua" onclick="loadNonBPJS()" style="cursor:pointer;">
                                <div class="inner">
                                    <h3>Non BPJS</h3>
                                    <div class="clearfix"></div><br />
                                </div>
                                <div class="icon">
                                    <i class="fa fa-wheelchair-alt"></i>
                                </div>
                                <div class="small-box-footer">
                                    untuk pasien Umum dan Asuransi
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Container untuk content yang akan dimuat -->
            <div id="content-container" style="display: none;">
                <button onclick="backToMainMenu()" class="btn btn-default" style="margin: 10px;">
                    <i class="fa fa-arrow-left"></i> Kembali ke Menu Utama
                </button>
                <iframe id="content-frame" style="width: 100%; height: calc(100vh - 100px); border: none;"></iframe>
            </div>
        </div>


    </div>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../dist/js/app.min.js"></script>

    <script>
        function loadBPJS() {
            document.getElementById('main-menu').style.display = 'none';
            document.getElementById('content-container').style.display = 'block';
            document.getElementById('content-frame').src = 'a_disp_pendaftaran/bpjs/index.php';
        }

        function loadNonBPJS() {
            document.getElementById('main-menu').style.display = 'none';
            document.getElementById('content-container').style.display = 'block';
            document.getElementById('content-frame').src = 'a_disp_pendaftaran/nonbpjs/index.php';
        }

        function backToMainMenu() {
            document.getElementById('content-container').style.display = 'none';
            document.getElementById('content-frame').src = '';
            document.getElementById('main-menu').style.display = 'block';
        }

        function tampilkanjam() {
            var month = new Array();
            month[0] = "Januari";
            month[1] = "Februari";
            month[2] = "Maret";
            month[3] = "April";
            month[4] = "Mei";
            month[5] = "Juni";
            month[6] = "Juli";
            month[7] = "Agustus";
            month[8] = "September";
            month[9] = "Oktober";
            month[10] = "November";
            month[11] = "Desember";

            var waktu = new Date();
            var tahun = waktu.getFullYear();

            var bulan = waktu.getMonth();
            bulan = month[bulan];

            var day = waktu.getDate();
            day = ((day < 10) ? "0" : "") + day;

            var jam = waktu.getHours();
            jam = ((jam < 10) ? "0" : "") + jam;

            var menit = waktu.getMinutes();
            menit = ((menit < 10) ? "0" : "") + menit;

            var detik = waktu.getSeconds();
            detik = ((detik < 10) ? "0" : "") + detik;

            var jamsekarang = day + " " + bulan + " " + tahun + " " + jam + ":" + menit + ":" + detik + " WIB";

            document.getElementById("jam").innerHTML = jamsekarang;
        }

        $(function() {
            tampilkanjam(); // Panggil sekali saat dimuat
            setInterval(function() {
                tampilkanjam()
            }, 1000); // Perbarui setiap 1 detik
        });
    </script>

</body>

</html>
<?php
mysqli_close($db_result);
?>