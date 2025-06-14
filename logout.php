<?php
session_start(); // Aktif oturumu başlatır veya mevcut oturumu devam ettirir

// Oturumdaki tüm verileri temizler
$_SESSION = [];

// Oturumu tamamen sona erdirir (sunucudaki session dosyası silinir)
session_destroy();

// Güvenlik için çerez varsa (örneğin session ID çerezi), onu da sıfırlamak iyi bir pratiktir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Kullanıcıyı giriş sayfasına yönlendir
header("Location: login.php");
exit();
