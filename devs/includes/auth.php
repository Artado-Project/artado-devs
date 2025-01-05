<?php

// Oturumu başlat
session_start();

// Veritabanı bağlantısı
require_once 'database.php';

// Giriş yapma fonksiyonu
function login($email, $password, $db) {
  $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    // Kullanıcı oturumu başlat
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username']; 
    $_SESSION['role'] = $user['role']; // Rol bilgisini oturuma ekle
    return true;
  } else {
    return false;
  }
}

// Çıkış yapma fonksiyonu
function logout() {
  session_destroy();
  header("Location: ../login.php");
  exit();
}


function getUserRole($email, $db) {
  $stmt = $db->prepare("SELECT role FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      return $user['role'];
  } else {
      return null; 
  }
}

?>