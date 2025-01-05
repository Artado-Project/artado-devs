<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artado Devs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animation for popup opening */
        @keyframes popup-enter {
            0% {
                transform: scale(0.9) translateX(-20px);
                opacity: 0;
            }
            100% {
                transform: scale(1) translateX(0);
                opacity: 1;
            }
        }

        .popup-enter {
            animation: popup-enter 0.3s ease-out forwards;
        }

        /* Optional: smooth transition for closing */
        .popup-exit {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100">

<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcı ID'si, oturumdan alınıyor
$user_id = $_SESSION['user_id'] ?? null; // Giriş yapmamışsa null

// Varsayılan profil fotoğrafı
$default_profile_photo = 'https://artado.xyz/assest/img/artado-yeni.png';

// Eğer kullanıcı giriş yaptıysa, kullanıcı bilgilerini veritabanından çek
if ($user_id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Kullanıcı profil fotoğrafı var mı kontrol et
    $profile_photo = $user['profile_photo'] ? $user['profile_photo'] : $default_profile_photo;
} else {
    // Giriş yapılmamışsa, varsayılan fotoğrafı kullan
    $profile_photo = $default_profile_photo;
}
?>

<header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
            <img src="https://web.archive.org/web/20240210174256im_/https://devs.artado.xyz/images/logo.png" alt="Logo" class="w-10 h-10">
            <span class="text-2xl font-bold tracking-wide">Artado Devs</span>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:flex space-x-8 text-lg font-medium">
            <a href="./" class="hover:text-purple-300 transition-colors">Ana Sayfa</a>
            <a href="https://forum.artado.xyz" class="hover:text-purple-300 transition-colors">Forum</a>
            <a href="https://artado.xyz" class="hover:text-purple-300 transition-colors">Hizmetler</a>
            <a href="mailto:arda@artadosearch.com" class="hover:text-purple-300 transition-colors">İletişim</a>
        </nav>

        <!-- Profile & Hamburger Menu -->
        <div class="flex items-center space-x-4">
            <div class="hidden md:flex items-center space-x-3 relative">
                <!-- Profil Fotoğrafı ve Kullanıcı Adı -->
                <img src="<?php echo $profile_photo; ?>" alt="Profil Fotoğrafı" class="w-12 h-12 rounded-full border-2 border-white shadow-md cursor-pointer" id="profile-photo">
                <span class="text-xl font-semibold cursor-pointer" id="username"><?php echo $user['username']; ?></span>

                <!-- Popup on the right side of the profile photo -->
                <div id="profile-popup" class="absolute hidden bg-white shadow-lg rounded-lg w-56 p-4 popup-enter" style="top: 0; left: 100%; margin-left: 10px;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Profil Ayarları</h3>
                        <button id="close-popup" class="text-xl text-gray-500 hover:text-gray-700">&times;</button>
                    </div>
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo $profile_photo; ?>" alt="Profil Fotoğrafı" class="w-12 h-12 rounded-full border-2 border-gray-300">
                        <span class="text-lg font-semibold text-gray-800"><?php echo $user['username']; ?></span>
                    </div>
                    <button id="logout-btn" class="mt-4 w-full py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">Çıkış Yap</button>
                </div>
            </div>

            <!-- Hamburger Button -->
            <button id="menu-toggle" class="md:hidden focus:outline-none">
                <span class="material-icons text-3xl">☰</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden bg-gradient-to-r from-purple-600 to-indigo-600">
        <ul class="flex flex-col items-center space-y-4 py-4">
            <li><a href="#" class="text-white hover:text-purple-300">Ana Sayfa</a></li>
            <li><a href="#" class="text-white hover:text-purple-300">Hakkında</a></li>
            <li><a href="#" class="text-white hover:text-purple-300">Hizmetler</a></li>
            <li><a href="#" class="text-white hover:text-purple-300">İletişim</a></li>
        </ul>
    </nav>
</header>

<script>
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Profile photo or username click to open popup
    const profilePhoto = document.getElementById('profile-photo');
    const username = document.getElementById('username');
    const profilePopup = document.getElementById('profile-popup');
    const closePopup = document.getElementById('close-popup');

    profilePhoto.addEventListener('click', () => {
        profilePopup.classList.remove('hidden');
        profilePopup.classList.add('popup-enter');
    });

    username.addEventListener('click', () => {
        profilePopup.classList.remove('hidden');
        profilePopup.classList.add('popup-enter');
    });

    closePopup.addEventListener('click', () => {
        profilePopup.classList.add('popup-exit');
        setTimeout(() => {
            profilePopup.classList.add('hidden');
            profilePopup.classList.remove('popup-exit');
        }, 300);
    });

    // Logout functionality
    const logoutBtn = document.getElementById('logout-btn');
    logoutBtn.addEventListener('click', () => {
        // PHP çıkış işlemi
        window.location.href = '../logout.php'; // Çıkış yapma işlemi için yönlendirme
    });

    // Close popup when clicking outside of the popup
    window.addEventListener('click', (event) => {
        if (!profilePopup.contains(event.target) && event.target !== profilePhoto && event.target !== username) {
            profilePopup.classList.add('popup-exit');
            setTimeout(() => {
                profilePopup.classList.add('hidden');
                profilePopup.classList.remove('popup-exit');
            }, 300);
        }
    });
</script>

</body>
</html>
