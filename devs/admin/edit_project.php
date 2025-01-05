<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $project_id = $_GET['id'];
    // Proje bilgilerini veritabanından çek
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :project_id AND user_id = :user_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $error = "Proje bulunamadı veya bu projeyi düzenleme yetkiniz yok.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $version = $_POST['version'];
            $features = implode(',', $_POST['features']); // Özellikleri virgülle ayrılmış bir dize olarak birleştir

            try {
                // Projeyi veritabanında güncelle
                $stmt = $db->prepare("UPDATE projects SET title = :title, description = :description, version = :version, features = :features WHERE id = :project_id");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':version', $version);
                $stmt->bindParam(':features', $features);
                $stmt->bindParam(':project_id', $project_id);
                $stmt->execute();

                // Proje resmi yükleme
                $image_upload_result = uploadProjectImage($project_id, $db);
                if ($image_upload_result !== true && $image_upload_result !== false) { // False ise resim seçilmemiş demektir, hata vermemeli
                    $error = $image_upload_result;
                } else {
                    // Güncelleme başarılı, proje sayfasına yönlendir
                    header("Location: projects");
                    exit();
                }
            } catch (PDOException $e) {
                $error = "Proje güncellenirken bir hata oluştu.";
            }
        }
    }
} else {
    $error = "Geçersiz proje ID.";
}

?>

<?php require_once 'header.php'; ?>
<DOCTYPE>
<html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="icon" href="../uploads/logo.png" type="image/x-icon">


<div class="container mx-auto mt-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
        <h2 class="text-2xl font-bold mb-4">Projeyi Düzenle</h2>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Hata!</strong>
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($project)): ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-bold mb-2">Proje Başlığı:</label>
                    <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-bold mb-2">Açıklama:</label>
                    <textarea id="description" name="description"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $project['description']; ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="version" class="block text-gray-700 font-bold mb-2">Versiyon:</label>
                    <input type="text" id="version" name="version" value="<?php echo $project['version']; ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="features" class="block text-gray-700 font-bold mb-2">Özellikler (virgülle ayırın):</label>
                    <input type="text" id="features" name="features[]" value="<?php echo str_replace(',', ' ', $project['features']); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-bold mb-2">Yeni Proje Resmi:</label>
                    <input type="file" id="image" name="image"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Güncelle
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<html>