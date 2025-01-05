<?php

// Proje resmi yükleme fonksiyonu
function uploadProjectImage($project_id, $db) {
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../public/uploads/files/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Dosya türü kontrolü
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
      return "Sadece JPG, JPEG, PNG & GIF dosyaları desteklenir.";
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      // Resmi veritabanına kaydet
      $stmt = $db->prepare("INSERT INTO project_images (project_id, image_path) VALUES (:project_id, :image_path)");
      $stmt->bindParam(':project_id', $project_id);
      $stmt->bindParam(':image_path', $target_file);
      $stmt->execute();
      return true;
    } else {
      return "Dosya yüklenirken bir hata oluştu.";
    }
  }
  return false;
}

?>