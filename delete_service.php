<?php
session_start(); // Oturum başlatılır

// Giriş yapılmamışsa login sayfasına yönlendirilir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// GET ile gelen hizmet ID kontrol edilir
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list_services.php");
    exit();
}

$service_id = intval($_GET['id']); // Güvenlik için sayıya çevrilir

// Hizmetin kullanıcıya ait olup olmadığı kontrol edilir
$stmt = $pdo->prepare("
    SELECT s.id 
    FROM services s
    JOIN patients p ON s.patient_id = p.id
    WHERE s.id = :id AND p.created_by = :user_id
");
$stmt->execute([
    'id' => $service_id,
    'user_id' => $_SESSION['user_id']
]);

$service = $stmt->fetch();

// Eğer hizmet bulunamazsa yönlendirme yapılır
if (!$service) {
    header("Location: list_services.php");
    exit();
}

// Silme işlemi gerçekleştirilir
$delete = $pdo->prepare("DELETE FROM services WHERE id = :id");
$delete->execute(['id' => $service_id]);

// Silindikten sonra yönlendirme
header("Location: list_services.php?deleted=1");
exit();
?>
