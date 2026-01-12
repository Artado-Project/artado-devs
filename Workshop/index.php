<?php
require 'config.php'; // config.php aynı dizinde

// Arama terimini al
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // SQL sorgusunu hazırla (resim yolunu projects tablosundan al)
    $sql = "
        SELECT 
            p.id, p.title, p.description, p.upload_date, p.category, 
            p.image_path, -- Resim yolunu projects tablosundan al
            u.username 
        FROM projects p
        LEFT JOIN users u ON p.user_id = u.id
        -- project_images join'i kaldırıldı
    ";

    if (!empty($search_term)) {
        $sql .= " WHERE p.title LIKE :search OR p.description LIKE :search";
    }

    $sql .= " ORDER BY p.upload_date DESC";

    $stmt = $db->prepare($sql);

    // Arama parametresini bağla (varsa)
    if (!empty($search_term)) {
        $search_param = '%' . $search_term . '%';
        $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
    }

    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Veritabanı sorgu hatası: " . $e->getMessage()); 
}

// Kategori adını formatlamak için yardımcı fonksiyon
function formatCategory($category) {
    if (!$category) return 'Diğer';
    return htmlspecialchars(ucwords(str_replace('_', ' ', $category)));
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop - Projeler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../public/uploads/files/favicon.ico" type="image/x-icon">
    <style>
        body {
            background-color: #f3f4f6;
        }
        .project-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .project-card img {
             height: 12rem; /* Kart resimleri için sabit yükseklik */
             object-fit: cover; /* Resimleri kırparak sığdır */
        }
    </style>
</head>
<body class="font-sans">
    <div class="container mx-auto px-4 py-8">
        
        <header class="mb-8 text-center">
             <a href="./" class="inline-block">
                 <h1 class="text-4xl font-bold text-gray-800 hover:text-indigo-600 transition-colors">Workshop Projeleri</h1>
            </a>
            <p class="text-lg text-gray-600 mt-2">Topluluk tarafından geliştirilen en son projeleri keşfedin.</p>
        </header>

        <!-- Arama Formu -->
        <div class="mb-8 max-w-lg mx-auto">
            <form action="index.php" method="get" class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Proje ara (başlık veya açıklama)..." 
                    value="<?php echo htmlspecialchars($search_term); ?>" 
                    class="w-full px-4 py-3 pr-12 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <button type="submit" class="absolute top-0 right-0 mt-2 mr-2 px-4 py-1.5 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Proje Listesi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if (count($projects) > 0): ?>
                <?php foreach ($projects as $project): 
                    // Kart resmi için URL'yi oluştur
                    $card_image_url = null; // Başlangıçta null
                    if (!empty($project['image_path']) && strpos($project['image_path'], 'public/uploads/img/') === 0) {
                        // Veritabanı yolu doğru formatta ise başına / ekle
                        $card_image_url = '/' . $project['image_path']; 
                    }
                    
                    // --- HATA AYIKLAMA BAŞLANGICI ---
                    echo "<pre style='background: #eee; padding: 5px; margin: 5px; border: 1px solid #ccc;'>";
                    echo "Proje ID: " . htmlspecialchars($project['id']) . "\n";
                    echo "DB image_path: ";
                    var_dump($project['image_path']);
                    echo "Oluşturulan URL: ";
                    var_dump($card_image_url);
                    echo "</pre>";
                    // --- HATA AYIKLAMA SONU ---
                ?>
                    <div class="project-card flex flex-col bg-white rounded-lg shadow-md overflow-hidden">
                         <a href="project.php?id=<?php echo htmlspecialchars($project['id']); ?>">
                            <?php if ($card_image_url): // URL varsa resmi göster ?>
                               <img src="<?php echo htmlspecialchars($card_image_url); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="w-full"> 
                            <?php else: ?>
                                <!-- Varsayılan resim veya placeholder -->
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                    Resim Yok
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-5 flex flex-col flex-grow"> 
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded mb-2 self-start">
                                <?php echo formatCategory($project['category']); ?>
                            </span>
                            <h2 class="text-lg font-semibold text-gray-900 mb-2 flex-grow">
                                <a href="project.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="hover:text-indigo-700 transition-colors">
                                    <?php echo htmlspecialchars($project['title']); ?>
                                </a>
                            </h2>
                            <!-- Açıklama kaldırıldı, kartı sadeleştirmek için -->
                            <!-- <p class="text-gray-600 text-sm mb-4">
                                <?php echo htmlspecialchars(substr($project['description'], 0, 120)); ?>...
                            </p> -->
                            <div class="flex items-center justify-between text-sm text-gray-500 mt-3 pt-3 border-t border-gray-200">
                                <span class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <?php echo htmlspecialchars($project['username'] ?? 'Bilinmeyen'); ?>
                                </span>
                                <span class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <?php echo date('d.m.Y', strtotime($project['upload_date'])); ?>
                                </span>
                            </div>
                             <!-- Detay butonu yorum satırı yapıldı, kartın tamamı link -->
                             <!-- <a href="project.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="mt-4 inline-block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors font-medium">Detayları Gör</a> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!empty($search_term)): ?>
                 <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 text-center py-12">
                    <p class="text-xl text-gray-500">"<?php echo htmlspecialchars($search_term); ?>" için sonuç bulunamadı.</p>
                    <a href="index.php" class="mt-4 inline-block text-indigo-600 hover:underline">Tüm projeleri göster</a>
                </div>
            <?php else: ?>
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 text-center py-12">
                    <p class="text-xl text-gray-500">Henüz yayınlanmış bir proje bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <footer class="text-center text-gray-500 mt-12">
            &copy; <?php echo date('Y'); ?> Artado Workshop. Tüm hakları saklıdır.
        </footer>
    </div>
</body>
</html> 