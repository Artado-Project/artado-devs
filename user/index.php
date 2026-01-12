<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL); // Tüm hataları raporlar
ini_set('display_errors', 1); // Hata mesajlarını görüntüler
?>

<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ziyaret kaydı ekle (Dashboard sayfası için)
$visitor_ip = $_SERVER['REMOTE_ADDR'] ?: 'unknown';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$log_stmt = $db->prepare("INSERT INTO visit_logs (page_type, target_id, visitor_ip, user_agent) VALUES ('dashboard', :user_id, :ip, :ua)");
$log_stmt->execute([':user_id' => $_SESSION['user_id'], ':ip' => $visitor_ip, ':ua' => $user_agent]);

// Toplam kullanıcı sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Toplam duyuru sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM announcements");
$total_announcements = $stmt->fetchColumn();

// Toplam proje sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM projects");
$total_projects = $stmt->fetchColumn();

// Kullanıcı oturum kontrolü
$user_id = $_SESSION['user_id'] ?? null;

// Kullanıcı bilgilerini çek
if ($user_id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    require_once '../includes/functions.php';
    $profile_photo = get_user_avatar($user['profile_photo'] ?? null, true);
} else {
    require_once '../includes/functions.php';
    $profile_photo = get_user_avatar(null, true);
}

// İstatistik verilerini hazırla (Son 12 ayın proje sayıları)
$stats_stmt = $db->query("
    SELECT 
        DATE_FORMAT(dates.date, '%b') as month,
        dates.date as sort_date,
        IFNULL(COUNT(p.id), 0) as count
    FROM (
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 11 MONTH) + INTERVAL 1 DAY as date UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 10 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 9 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 8 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 7 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 6 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 5 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 4 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 3 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 2 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH) + INTERVAL 1 DAY UNION ALL
        SELECT LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY
    ) as dates
    LEFT JOIN projects p ON DATE_FORMAT(p.created_at, '%Y-%m') = DATE_FORMAT(dates.date, '%Y-%m')
    GROUP BY dates.date, month
    ORDER BY sort_date
");
$stats_data = $stats_stmt->fetchAll(PDO::FETCH_ASSOC);
$chart_months = json_encode(array_column($stats_data, 'month'));
$chart_counts = json_encode(array_column($stats_data, 'count'));

// Son duyuruları al
$ann_stmt = $db->query("
    SELECT a.*, u.username, u.profile_photo 
    FROM announcements a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC LIMIT 4
");
$latest_announcements = $ann_stmt->fetchAll(PDO::FETCH_ASSOC);

// Visitors Profile (Ziyaretçi istatistikleri - Örnek veri veya loglardan çekme)
$visit_stats_stmt = $db->query("SELECT page_type, COUNT(*) as count FROM visit_logs GROUP BY page_type");
$visit_stats = $visit_stats_stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: ['home' => 0, 'project' => 0];
$visit_series = json_encode(array_values($visit_stats));
$visit_labels = json_encode(array_keys($visit_stats));
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
                                    <a href="create-tema.php" class="submenu-link">Tema</a>
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
                                                    <?php foreach ($latest_announcements as $ann): ?>
                                                    <tr>
                                                        <td class="col-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md">
                                                                    <img src="<?php echo get_user_avatar($ann['profile_photo'] ?? null, true); ?>">
                                                                </div>
                                                                <p class="font-bold ms-3 mb-0"><?php echo htmlspecialchars($ann['username'] ?: 'Artado'); ?></p>
                                                            </div>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class=" mb-0"><strong><?php echo htmlspecialchars($ann['title']); ?>:</strong> <?php echo mb_strimwidth(strip_tags($ann['description']), 0, 100, "..."); ?></p>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php if (empty($latest_announcements)): ?>
                                                    <tr><td colspan="2" class="text-center">Henüz duyuru bulunmuyor.</td></tr>
                                                    <?php endif; ?>
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
                                        <img src="<?php echo $profile_photo; ?>" class="profile-photo">
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
                                    <div class="name ms-4 w-100">
                                        <?php 
                                        $uploader_stmt = $db->query("
                                            SELECT u.username, u.profile_photo, MAX(p.created_at) as last_upload
                                            FROM projects p 
                                            JOIN users u ON p.user_id = u.id 
                                            GROUP BY u.id, u.username, u.profile_photo
                                            ORDER BY last_upload DESC 
                                            LIMIT 5
                                        ");
                                        $uploaders = $uploader_stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($uploaders as $uploader): ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="<?php echo get_user_avatar($uploader['profile_photo'], true); ?>">
                                                </div>
                                                <span class="font-bold">@<?php echo htmlspecialchars($uploader['username']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>


                                <div class="px-4">
                                    <button class='btn btn-block btn-xl btn-outline-primary font-bold mt-3'><a href="https://artadosearch.com/Workshop">Projeleri
                                        İncele<a></button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Visitors Profile</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-visitors-profile"></div>
                                <script>
                                    // Grafik verilerini PHP'den JS'e aktar
                                    window.dashboardStats = {
                                        months: <?php echo $chart_months; ?>,
                                        counts: <?php echo $chart_counts; ?>,
                                        visitSeries: <?php echo $visit_series; ?>,
                                        visitLabels: <?php echo $visit_labels; ?>
                                    };
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