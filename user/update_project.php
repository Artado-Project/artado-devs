<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $features = $_POST['features'];
    
    try {
        // Projenin bu kullanıcıya ait olduğunu kontrol et
        $stmt = $db->prepare("SELECT user_id FROM projects WHERE id = :project_id");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($project && $project['user_id'] == $_SESSION['user_id']) {
            // Proje bilgilerini güncelle
            $stmt = $db->prepare("
                UPDATE projects 
                SET title = :title, 
                    category = :category, 
                    description = :description, 
                    features = :features,
                    updated_at = NOW()
                WHERE id = :project_id
            ");
            
            $stmt->bindParam(':project_id', $project_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':features', $features);
            $stmt->execute();
            
            // Eğer yeni bir resim yüklendiyse
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];
                $image_name = uniqid() . '_' . basename($image['name']);
                $target_dir = '../public/uploads/img/';
                $image_path_server = $target_dir . $image_name;
                $image_path_db = 'public/uploads/img/' . $image_name;
                
                // Uploads klasörü yoksa oluştur
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                // Resmi yükle
                if (move_uploaded_file($image['tmp_name'], $image_path_server)) {
                    // Eski resmi sil
                    $stmt = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :project_id");
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->execute();
                    $old_image = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($old_image && file_exists($old_image['image_path'])) {
                        unlink($old_image['image_path']);
                    }
                    
                    // Yeni resim yolunu veritabanına kaydet
                    $stmt = $db->prepare("
                        INSERT INTO project_images (project_id, image_path) 
                        VALUES (:project_id, :image_path)
                        ON DUPLICATE KEY UPDATE image_path = :image_path
                    ");
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->bindParam(':image_path', $image_path_db);
                    $stmt->execute();
                }
            }

            // Eğer yeni bir proje dosyası yüklendiyse
            if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['project_file'];
                
                // Dosya boyutunu kontrol et (100MB)
                if ($file['size'] > 100 * 1024 * 1024) {
                    throw new Exception("Dosya boyutu 100MB'dan büyük olamaz.");
                }
                
                // Dosya uzantısını kontrol et
                $allowed_extensions = ['zip', 'rar', '7z'];
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception("Sadece ZIP, RAR ve 7Z dosyaları yüklenebilir.");
                }
                
                $file_name = uniqid() . '_' . basename($file['name']);
                $target_dir = '../public/uploads/files/';
                $file_path_server = $target_dir . $file_name;
                $file_path_db = 'public/uploads/files/' . $file_name;
                
                // Uploads klasörü yoksa oluştur
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                // Dosyayı yükle
                if (move_uploaded_file($file['tmp_name'], $file_path_server)) {
                    // Eski dosyayı sil
                    $stmt = $db->prepare("SELECT file_path FROM projects WHERE id = :project_id");
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->execute();
                    $old_file = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($old_file && !empty($old_file['project_file']) && file_exists($old_file['project_file'])) {
                        unlink($old_file['project_file']);
                    }
                    
                    // Yeni dosya yolunu veritabanına kaydet
                    $stmt = $db->prepare("UPDATE projects SET file_path = :file_path WHERE id = :project_id");
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->bindParam(':file_path', $file_path_db);
                    $stmt->execute();
                }
            }
            
            $_SESSION['success_message'] = "Proje başarıyla güncellendi.";
        } else {
            $_SESSION['error_message'] = "Bu projeyi düzenleme yetkiniz yok.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Proje güncellenirken bir hata oluştu: " . $e->getMessage();
    }
    
    header("Location: projects.php");
    exit();
}
?> 