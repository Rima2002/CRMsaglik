<?php
session_start(); // Oturumu başlat

// Giriş yapılmamışsa kullanıcı login sayfasına yönlendirilir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// GET ile gelen hizmet ID alınır ve geçerliliği kontrol edilir
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: add_service.php");
    exit();
}

$service_id = intval($_GET['id']); // ID tam sayıya çevrilir

// Hizmet kaydı çekilir ve kullanıcıya ait olup olmadığı kontrol edilir
$stmt = $pdo->prepare("
    SELECT s.*, p.full_name 
    FROM services s
    JOIN patients p ON s.patient_id = p.id
    WHERE s.id = :id AND p.created_by = :user_id
");
$stmt->execute([
    'id' => $service_id,
    'user_id' => $_SESSION['user_id']
]);

$service = $stmt->fetch();

// Eğer kayıt bulunamazsa yönlendir
if (!$service) {
    header("Location: add_service.php");
    exit();
}

$success = '';
$error = '';

// POST isteği ile form gönderildiyse güncelleme başlatılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name']);
    $service_date = $_POST['service_date'];
    $notes = trim($_POST['notes']);

    // Hizmet adı ve tarih boş bırakılmamalı
    if (!empty($service_name) && !empty($service_date)) {
        $update = $pdo->prepare("
            UPDATE services 
            SET service_name = :name, service_date = :date, notes = :notes 
            WHERE id = :id
        ");
        $update->execute([
            'name' => $service_name,
            'date' => $service_date,
            'notes' => $notes,
            'id' => $service_id
        ]);

        $success = "Hizmet bilgileri başarıyla güncellendi.";
        // Formda güncel değerleri göstermek için değişkenler güncellenir
        $service['service_name'] = $service_name;
        $service['service_date'] = $service_date;
        $service['notes'] = $notes;
    } else {
        $error = "Lütfen tüm zorunlu alanları doldurun.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Hizmeti Düzenle</h3>

        <!-- Başarı veya hata mesajı -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Düzenleme formu -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Hasta</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['full_name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="service_name" class="form-label">Hizmet Adı *</label>
                <input type="text" name="service_name" id="service_name" class="form-control" required value="<?php echo htmlspecialchars($service['service_name']); ?>">
            </div>

            <div class="mb-3">
                <label for="service_date" class="form-label">Tarih *</label>
                <input type="date" name="service_date" id="service_date" class="form-control" required value="<?php echo $service['service_date']; ?>">
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notlar</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"><?php echo htmlspecialchars($service['notes']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Güncelle</button>
            <a href="list_services.php" class="btn btn-secondary">Geri Dön</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
