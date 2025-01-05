<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının admin olup olmadığını kontrol et
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login");
    exit();
}

// Tüm kullanıcıları veritabanından çek
$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
  <title>Kullanıcıları Yönet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="icon" href="../uploads/logo.png" type="image/x-icon">

</head>
<body>

<?php require_once 'header.php'; ?>


<main class="container mx-auto p-4">
  <section class="manage-users">
    <h2 class="text-2xl font-bold mb-4 text-blue-500 font-sans">Kullanıcıları Yönet</h2>
    <table class="table table-striped overflow-x-auto">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Kullanıcı Adı</th>
          <th scope="col">E-posta</th>
          <th scope="col">Rol</th>
          <th scope="col">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo $user['id']; ?></td>
          <td><?php echo $user['username']; ?></td>
          <td><?php echo $user['email']; ?></td>
          <td><?php echo $user['role']; ?></td>
          <td>
            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Düzenle</a>
            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="confirmDeletion(event, this.href)">Sil</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>



</body>
</html>

<script>
  function confirmDeletion(event, url) {
    event.preventDefault(); // Linke tıklamayı durdur
    const userConfirmed = confirm("Bu kullanıcıyı silmek istediğinizden emin misiniz?");
    if (userConfirmed) {
      // Eğer kullanıcı onay verdiyse yönlendir
      window.location.href = url;
    }
  }
</script>