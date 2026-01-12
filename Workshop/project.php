<?php
require 'config.php'; // Veritabanı bağlantısı (aynı dizinde)

// URL'den proje ID'sini al
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($project_id === 0) {
    // ID yoksa veya geçersizse ana sayfaya yönlendirilebilir veya hata gösterilebilir
    header('Location: index.php'); 
    // die("Geçersiz proje ID.");
    exit;
}

// Kullanıcı bilgilerini almak için fonksiyon (index.php'den kopyalandı)
function getUserById($userId, $db) {
    try {
        $stmt = $db->prepare("SELECT username FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['username'] : 'Bilinmeyen Kullanıcı';
    } catch (PDOException $e) {
        return 'Bilinmeyen Kullanıcı';
    }
}

try {
    // Proje detaylarını ve kullanıcı adını çek (JOIN ile)
    $stmt = $db->prepare("
        SELECT 
            p.*, 
            pi.image_path, 
            u.username AS uploader_username
        FROM projects p 
        LEFT JOIN project_images pi ON p.id = pi.project_id 
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = :id
    ");
    $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        // Proje bulunamazsa ana sayfaya yönlendirilebilir veya 404 hatası gösterilebilir
        header('Location: index.php?error=notfound');
        // die("Proje bulunamadı.");
        exit;
    }

} catch (PDOException $e) {
    die("Veritabanı sorgu hatası: " . $e->getMessage()); // Hata detayını loglamak daha iyi
}

// Kategori adını formatlamak için yardımcı fonksiyon
function formatCategory($category) {
    if (!$category) return 'Diğer';
    return htmlspecialchars(ucwords(str_replace('_', ' ', $category)));
}

// Dosya ve resim yollarını oluştur (ana dizine göre)
// Varsayım: Veritabanında yollar `public/uploads/img/abc.jpg` veya `../public/uploads/img/abc.jpg` gibi saklanıyor.
// `ltrim` ile başta olabilecek `../` kaldırılıp tekrar ekleniyor.
$file_download_path = isset($project['file_path']) ? '../' . ltrim($project['file_path'], '../') : null;
$image_display_path = isset($project['image_path']) ? '../' . ltrim($project['image_path'], '../') : null;

// Dosya ve resmin varlığını kontrol et
$file_exists = $file_download_path && file_exists($file_download_path);
$image_exists = $image_display_path && file_exists($image_display_path);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Workshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../public/uploads/files/favicon.ico" type="image/x-icon">
    <style>
        body {
            background-color: #f9fafb; /* Biraz daha açık gri */
        }
        .prose img { /* Açıklama içindeki resimler için (varsa) */
            margin-top: 1em;
            margin-bottom: 1em;
        }
    </style>
</head>
<body class="font-sans">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <!-- Breadcrumb Navigasyon -->
        <nav class="mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex space-x-2">
                <li class="flex items-center">
                    <a href="index.php" class="text-gray-500 hover:text-indigo-600 transition-colors">Workshop</a>
                </li>
                <li class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($project['title']); ?></span>
                </li>
            </ol>
        </nav>

        <!-- Ana İçerik Alanı -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="md:flex">
                <!-- Sol Taraf: Resim ve İndirme Butonu -->
                <div class="md:w-2/5 p-6 bg-gray-50 flex flex-col items-center justify-center">
                    <?php if ($image_exists): ?>
                         <img src="<?php echo htmlspecialchars($image_display_path); ?>" 
                              alt="<?php echo htmlspecialchars($project['title']); ?> Resmi" 
                              class="w-full h-auto rounded-lg shadow-md mb-4 object-cover">
                         <a href="<?php echo htmlspecialchars($image_display_path); ?>" target="_blank" class="text-xs text-gray-500 hover:text-indigo-600 underline mb-4 break-all">
                              <?php echo htmlspecialchars($image_display_path); ?>
                         </a>
                    <?php else: ?>
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg shadow-md mb-6 text-gray-500">
                            Resim Yok
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($file_exists): ?>
                        <a href="<?php echo htmlspecialchars($file_download_path); ?>" 
                           class="w-full max-w-xs inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors" 
                           download>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Projeyi İndir
                        </a>
                    <?php else: ?>
                        <span class="w-full max-w-xs inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Dosya Yok
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Sağ Taraf: Proje Detayları -->
                <div class="md:w-3/5 p-8">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full mb-3">
                        <?php echo formatCategory($project['category']); ?>
                    </span>
                    <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($project['title']); ?></h1>
                    
                    <div class="text-sm text-gray-600 mb-6">
                        <span class="mr-4 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <?php echo htmlspecialchars($project['uploader_username'] ?? 'Bilinmeyen'); ?>
                        </span>
                        <span class="inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?php echo date('d F Y', strtotime($project['upload_date'])); ?>
                        </span>
                    </div>
                    
                    <div class="prose prose-sm sm:prose lg:prose-lg xl:prose-xl max-w-none text-gray-700 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Açıklama</h3>
                        <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 border-t border-gray-200 pt-4">
                        <div>
                            <strong class="block text-gray-500">Versiyon</strong> 
                            <?php echo htmlspecialchars($project['version'] ?? '-'); ?>
                        </div>
                        <div>
                            <strong class="block text-gray-500">Özellikler</strong> 
                            <?php echo htmlspecialchars($project['features'] ?? '-'); ?>
                        </div>
                        <!-- Gerekirse başka detaylar eklenebilir -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Geri Dön Butonu -->
         <div class="mt-8 text-center">
            <a href="index.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                 </svg>
                Tüm Projelere Geri Dön
            </a>
        </div>
        
        <!-- Footer (İsteğe bağlı eklenebilir) -->
        <footer class="text-center text-gray-500 mt-12 text-sm">
            &copy; <?php echo date('Y'); ?> Artado Workshop.
        </footer>
    </div>

</body>
</html> 