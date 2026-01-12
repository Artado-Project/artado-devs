<?php

// Kullanıcı avatar URL'sini döndürür
function get_user_avatar($db_path, $is_in_user_dir = false) {
    $default = 'https://artado.xyz/assest/img/artado-yeni.png';
    
    if (!$db_path) return $default;
    
    // Eğer tam URL ise (http ile başlıyorsa)
    if (strpos($db_path, 'http') === 0) {
        return $db_path;
    }
    
    // Veritabanındaki yolun başındaki ./ veya ../ kısmını temizle (varsa)
    // Önce ../ temizle, sonra ./ temizle
    $clean_path = preg_replace('/^(\.\.\/|\.\/)/', '', $db_path);
    
    // Eğer user/ klasörü içindeysek ve public/ ile başlıyorsa başına ../ ekle
    if ($is_in_user_dir) {
        return '../' . $clean_path;
    }
    
    return $clean_path;
}

// Proje resmi yükleme fonksiyonu
// Başarılı olursa veritabanına kaydedilecek yolu (örn: public/uploads/img/resim.jpg),
// resim yüklenmediyse null, hata varsa string hata mesajı döndürür.
function uploadProjectImage() {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB (örnek limit)

        if (!in_array($file['type'], $allowed_types)) {
            return "Resim için sadece JPEG, PNG ve GIF formatları desteklenir.";
        }
        if ($file['size'] > $max_size) {
            return "Resim dosya boyutu en fazla 5MB olabilir.";
        }

        // Proje kök dizinini belirle (__DIR__ includes klasörünü gösterir, bir üste çıkmalıyız)
        $project_root = dirname(__DIR__); 
        $upload_dir = $project_root . '/public/uploads/img/'; 

        // Hedef dizin yoksa oluştur
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                error_log("Proje resmi yükleme dizini oluşturulamadı: " . $upload_dir);
                return "Proje resmi yükleme dizini oluşturulamadı.";
            }
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Benzersiz dosya adı oluşturmak daha iyi olur
        $file_name = uniqid('proj_img_') . '.' . $file_extension; 
        $target_path = $upload_dir . $file_name; // Tam sunucu yolu

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Veritabanına kaydedilecek yol (public/ ile başlayan)
            $db_image_path = 'public/uploads/img/' . $file_name; 
            return $db_image_path; // Başarılı, veritabanı yolunu döndür

        } else {
            error_log("Proje resmi taşınamadı (muhtemelen izinler): " . $target_path);
            return "Proje resmi yüklenirken bir hata oluştu.";
        }

    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        // Resim yüklenmemiş, bu bir hata değil.
        return null; 
    } elseif (isset($_FILES['image'])) {
        // Başka bir yükleme hatası
        return "Resim yükleme hatası (Kod: " . $_FILES['image']['error'] . ").";
    }

    // $_FILES['image'] hiç set edilmemişse.
    return null; 
}
