<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının admin olup olmadığını kontrol et
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login");
    exit();
}

// Toplam kullanıcı sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Toplam proje sayısını al
$stmt = $db->query("SELECT COUNT(*) FROM projects");
$total_projects = $stmt->fetchColumn();

// Son eklenen projeleri al (örneğin son 5 proje)
$stmt = $db->query("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5");
$recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<html>
<head>
  <title>Admin Paneli</title>
  <link rel="icon" href="../uploads/logo.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="icon" href="../uploads/logo.png" type="image/x-icon">
  <link rel="stylesheet" href="homepage/css/artado.css" media="screen">
    <link rel="stylesheet" href="homepage/css/index.css" media="screen">
    <script class="u-script" type="text/javascript" src="homepage/js/jquery-1.9.1.min.js" defer=""></script>
    <script class="u-script" type="text/javascript" src="homepage/js/artado.js" defer=""></script>
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    
</head>
<body>

<?php require_once 'header.php'; ?>


  <main class="container mx-auto px-4 py-8">
    <section class="admin-dashboard">
      <h2 class="text-xl font-bold mb-4">Genel Bakış</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-bold">Toplam Kullanıcı</h3>
          <p class="text-2xl font-bold text-blue-500"><?php echo $total_users; ?></p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-bold">Toplam Proje</h3>
          <p class="text-2xl font-bold text-green-500"><?php echo $total_projects; ?></p>
        </div>
      </div>

      <div class="recent-projects">
        <h3 class="text-xl font-bold mb-4">Son Eklenen Projeler</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Başlık</th>
              <th scope="col">Yükleyen</th>
              <th scope="col">Tarih</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_projects as $project): ?>
              <tr>
                <td><a href="../project.php?id=<?php echo $project['id']; ?>" class="text-blue-500 hover:underline"><?php echo $project['title']; ?></a></td>
                <td><?php echo $project['username']; ?></td>
                <td><?php echo $project['created_at']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-actions mt-8">
        <h3 class="text-xl font-bold mb-4">İşlemler</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <a href="users" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Kullanıcıları Yönet
          </a>
          <a href="projects" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Projeleri Yönet
          </a>
          <a href="duyuru" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Duyurular
          </a>
          <a href="https://matrix.to/#/#artadoproject:matrix.org" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Matrix
          </a>
          <a href="https://forum.artado.xyz" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded">
            Forum
          </a>
          <a href="statistics" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
            İstatistikler
          </a>
        </div>
      </div>
    </section>
  </main>

  <footer class="u-clearfix u-footer u-grey-80" id="sec-bce8"><div class="u-clearfix u-sheet u-valign-middle u-sheet-1">
        <div class="u-align-left u-social-icons u-spacing-10 u-social-icons-1">
          <a class="u-social-url" title="Github" target="_blank" href="https://github.com/Artado-Project"><span class="u-file-icon u-icon u-social-facebook u-social-icon u-icon-1"><img src="homepage/images/919847.png" alt=""></span>
          </a>
          <a class="u-social-url" title="twitter" target="_blank" href=""><span class="u-file-icon u-icon u-social-icon u-social-twitter u-icon-2"><img src="homepage/images/4675088.png" alt=""></span>
          </a>
          <a class="u-social-url" title="instagram" target="_blank" href="https://x.com/ArtadoL"><span class="u-file-icon u-icon u-social-icon u-social-instagram u-icon-3"><img src="homepage/images/11823292.png" alt=""></span>
          </a>
        </div>
      </div>
    </footer>

</body>
</html>