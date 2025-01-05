<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının admin olup olmadığını kontrol et
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login");
    exit();
}

// Tüm projeleri veritabanından çek
$stmt = $db->query("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projeleri Yönet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="icon" href="../uploads/logo.png" type="image/x-icon">
</head>
<body class="bg-gray-100">

<?php require_once 'header.php'; ?>


  <main class="py-8">
    <div class="container mx-auto px-4">
      <section class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center mb-6">Projeleri Yönet</h2>
        <table class="min-w-full table-auto text-sm">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 text-left">ID</th>
              <th class="px-4 py-2 text-left">Başlık</th>
              <th class="px-4 py-2 text-left">Yükleyen</th>
              <th class="px-4 py-2 text-left">Tarih</th>
              <th class="px-4 py-2 text-left">İşlemler</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <?php foreach ($projects as $project): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2"><?php echo $project['id']; ?></td>
                <td class="px-4 py-2">
                  <a href="../project.php?id=<?php echo $project['id']; ?>" class="text-blue-500 hover:underline"><?php echo $project['title']; ?></a>
                </td>
                <td class="px-4 py-2"><?php echo $project['username']; ?></td>
                <td class="px-4 py-2"><?php echo $project['created_at']; ?></td>
                <td class="px-4 py-2">
                  <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="text-yellow-500 hover:text-yellow-700 mr-2">Düzenle</a>
                  <a href="delete_project.php?id=<?php echo $project['id']; ?>"  class="text-red-500 hover:text-red-700" onclick="confirmDeletion(event, this.href)">Sil</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </div>
  </main>


  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  function confirmDeletion(event, url) {
    event.preventDefault(); // Linke tıklamayı durdur
    const userConfirmed = confirm("Bu projeyi silmek istediğinizden emin misiniz?");
    if (userConfirmed) {
      // Eğer kullanıcı onay verdiyse yönlendir
      window.location.href = url;
    }
  }
</script>

</body>
</html>
