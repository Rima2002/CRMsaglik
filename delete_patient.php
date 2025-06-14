<?php
session_start(); // Oturumu başlat

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// Hasta ID'si GET parametresinden alınır
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Geçerli ID yoksa panele dönülür
    header("Location: dashboard.php");
    exit();
}

$patient_id = intval($_GET['id']); // ID güvenlik amacıyla tam sayıya çevrilir

// Silinmek istenen hasta gerçekten bu kullanıcıya mı ait kontrol edilir
$check = $pdo->prepare("SELECT id FROM patients WHERE id = :id AND created_by = :user_id");
$check->execute([
    'id' => $patient_id,
    'user_id' => $_SESSION['user_id']
]);

// Hasta bulunamazsa ya da başka kullanıcıya aitse işlem yapılmaz
if ($check->rowCount() === 0) {
    header("Location: dashboard.php");
    exit();
}

// Önce hastaya bağlı tüm geri bildirimleri sil
$pdo->prepare("DELETE FROM feedbacks WHERE patient_id = :id")->execute(['id' => $patient_id]);

// Sonra finansal kayıtları sil
$pdo->prepare("DELETE FROM finances WHERE patient_id = :id")->execute(['id' => $patient_id]);

// Son olarak hizmet kayıtlarını sil
$pdo->prepare("DELETE FROM services WHERE patient_id = :id")->execute(['id' => $patient_id]);

// Ardından hasta silinir
$delete = $pdo->prepare("DELETE FROM patients WHERE id = :id AND created_by = :user_id");
$delete->execute([
    'id' => $patient_id,
    'user_id' => $_SESSION['user_id']
]);

// Hasta veritabanından silinir
$delete = $pdo->prepare("DELETE FROM patients WHERE id = :id AND created_by = :user_id");
$delete->execute([
    'id' => $patient_id,
    'user_id' => $_SESSION['user_id']
]);

// Silme işlemi başarılıysa kullanıcı tekrar panele yönlendirilir
header("Location: dashboard.php?deleted=1");
exit();
