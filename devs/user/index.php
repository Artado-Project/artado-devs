<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit();
}

// Toplam kullanıcı sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Toplam kullanıcı sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM announcements");
$total_announcements = $stmt->fetchColumn();


// Toplam proje sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM projects");
$total_projects = $stmt->fetchColumn();




// Kullanıcı oturum kontrolü
$user_id = $_SESSION['user_id'] ?? null;

// Varsayılan profil fotoğrafı
$default_profile_photo = 'https://artado.xyz/assest/img/artado-yeni.png';

// Kullanıcı bilgilerini çek
if ($user_id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_photo = $user['profile_photo'] ?: $default_profile_photo;
} else {
    $profile_photo = $default_profile_photo;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa - Artado Developers</title>
    <link rel="shortcut icon" href="https://raw.githubusercontent.com/Artado-Project/devs/refs/heads/main/ArtadoDevs/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="https://raw.githubusercontent.com/Artado-Project/devs/refs/heads/main/ArtadoDevs/images/favicon.ico" type="image/png">
    <link rel="stylesheet" crossorigin href="./assets/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" crossorigin href="./assets/compiled/css/iconly.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="index.php"><img src="https://raw.githubusercontent.com/Artado-Project/devs/refs/heads/main/ArtadoDevs/images/favicon.ico" alt="Logo"></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                        opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Konsol</li>

                        <li class="sidebar-item active">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Ana Sayfa</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="projects.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Projelerim</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Sosyal Medya</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="https://artadosearch.com" class="submenu-link">Artado Search</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="https://forum.artado.xyz" class="submenu-link">Forum</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="https://matrix.to/#/#artadoproject:matrix.org" class="submenu-link">Matrix</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="https://x.com/ArtadoL" class="submenu-link">Twitter</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Destekte Bulunun</span>
                            </a>
                            <ul class="submenu">

                                <li class="submenu-item">
                                    <a href="https://kreosus.com/artadosoft?hl=tr" target="_blank" class="submenu-link">Kreosus</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-title">Çalışma Panelim</li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-hexagon-fill"></i>
                                <span>Proje Oluştur</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="create-eklenti.php" class="submenu-link">Eklenti</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-logo.php" class="submenu-link">Logo</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-theme.php" class="submenu-link">Tema</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-uyg-mobil.php" class="submenu-link">Mobil Uygulama</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-uyg-pc.php" class="submenu-link">PC Uygulama</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-game-pc.php" class="submenu-link">PC Oyun</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="create-game-mobil.php" class="submenu-link">Mobil Oyun</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="todo-list.php" class='sidebar-link'>
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Yapılacaklar Listesi</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="announcements.php" class='sidebar-link'>
                                <i class="bi bi-journal-check"></i>
                                <span>Duyurular</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Ayarlar</li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-person-circle"></i>
                                <span>Hesap</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="account-profile.php" class="submenu-link">Profil</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="account-security.php" class="submenu-link">Güvenlik</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="auth-login.php?logout=true" class='sidebar-link'>
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Çıkış Yap</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Önizleme</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-9">
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon purple mb-2">
                                                    <i class="iconly-boldShow"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Toplam Proje Sayısı</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $total_projects; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="iconly-boldProfile"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Toplam Kullanıcı Sayısı</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $total_users; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon green mb-2">
                                                    <i class="iconly-boldAdd-User"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Duyurular</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $total_announcements; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon red mb-2">
                                                    <i class="iconly-boldBookmark"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Proje görüntülemem</h6>
                                                <h6 class="font-extrabold mb-0">None</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Son Eklenen Projeler</h4>
                                    </div>
                                    <div class="card-body">
                                        <section class="project-section">

                                            <?php
                                            // Projeleri alalım
                                            $stmt = $db->query("SELECT p.title, u.username FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5");
                                            $recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                            <ul>
                                                <?php foreach ($recent_projects as $project): ?>
                                                    <li><?php echo $project['title']; ?> <span>(Yükleyen:
                                                            @<?php echo $project['username']; ?>)</span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Proje önizlemeleri</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="d-flex align-items-center">
                                                    <svg class="bi text-danger" width="32" height="32" fill="blue"
                                                        style="width:10px">
                                                        <use
                                                            xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                                                    </svg>
                                                    <h5 class="mb-0 ms-3">Son 1 ay</h5>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <h5 class="mb-0 text-end">None</h5>
                                            </div>
                                            <div class="col-12">
                                                <div id="chart-indonesia"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Son duyurular</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-lg">
                                                <thead>
                                                    <tr>
                                                        <th>İsim</th>
                                                        <th>İçeriği</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="col-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md">
                                                                    <img src="./assets/compiled/jpg/5.jpg">
                                                                </div>
                                                                <p class="font-bold ms-3 mb-0">Artado</p>
                                                            </div>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class=" mb-0">Duyuru</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md">
                                                                    <img src="./assets/compiled/jpg/2.jpg">
                                                                </div>
                                                                <p class="font-bold ms-3 mb-0">Artado</p>
                                                            </div>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class=" mb-0">Duyuru</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md">
                                                                    <img src="./assets/compiled/jpg/8.jpg">
                                                                </div>
                                                                <p class="font-bold ms-3 mb-0">Artado</p>
                                                            </div>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class=" mb-0">Duyuru</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md">
                                                                    <img src="./assets/compiled/jpg/3.jpg">
                                                                </div>
                                                                <p class="font-bold ms-3 mb-0">Artado</p>
                                                            </div>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class=" mb-0">Duyuru</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body py-4 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl">
                                        <img src="<?php echo $user['profile_photo']; ?>" class="profile-photo">
                                    </div>
                                    <div class="ms-3 name">
                                        <h5 class="font-bold"><?php echo $user['username']; ?></h5>
                                        <h6 class="text-muted mb-0"><?php echo $user['email']; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Son Proje Yükleyenler</h4>
                            </div>
                            <div class="card-content pb-4">
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg">
                                        <img src="https://artado.xyz/assest/img/artado-yeni.png">
                                    </div>
                                    <div class="name ms-4">
                                        <?php foreach ($recent_projects as $project): ?>
                                            <li><span><?php echo $project['username']; ?></span></li>
                                        <?php endforeach; ?>
                                    </div>
                                </div>


                                <div class="px-4">
                                    <button class='btn btn-block btn-xl btn-outline-primary font-bold mt-3'>Projeleri
                                        İncele</button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Visitors Profile</h4>
                            </div>
                            <div class="card-body">
                                <script>
                                    var urlToGetAllOpenBugs = "https://api.github.com/repos/jquery/jquery/issues?state=open&labels=bug";

                                    $(document).ready(function () {
                                        $.getJSON(urlToGetAllOpenBugs, function (allIssues) {
                                            $("div").append("found " + allIssues.length + " issues</br>");
                                            $.each(allIssues, function (i, issue) {
                                                $("div")
                                                    .append("<b>" + issue.number + " - " + issue.title + "</b></br>")
                                                    .append("created at: " + issue.created_at + "</br>")
                                                    .append(issue.body + "</br></br></br>");
                                            });
                                        });
                                    });             
                                </script>
                            </div>
                        </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Artado Software</p>
                    </div>
                    <div class="float-end">
                        <p>Sxinar tarafından <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                            by <a href="https://sxi.is-a.dev">Sxinar</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>


    <script src="assets/compiled/js/app.js"></script>



    <!-- Need: Apexcharts -->
    <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="assets/static/js/pages/dashboard.js"></script>

</body>

</html>