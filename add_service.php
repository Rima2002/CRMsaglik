<?php
session_start(); // Oturumu başlat

// Giriş yapılmamışsa kullanıcı login sayfasına yönlendirilir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

$success = '';
$error = '';

// Bu kullanıcıya ait hastaları çek (formda seçim yapılması için)
$patients = $pdo->prepare("SELECT id, full_name FROM patients WHERE created_by = :user_id ORDER BY full_name ASC");
$patients->execute(['user_id' => $_SESSION['user_id']]);
$patientList = $patients->fetchAll();

// Form gönderildiyse işlem başlatılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $service_name = trim($_POST['service_name']);
    $service_date = trim($_POST['service_date']);
    $notes = trim($_POST['notes']);

    // Gerekli alanlar boş bırakılmış mı kontrol edilir
    if ($patient_id && !empty($service_name) && !empty($service_date)) {
        // Hizmet planı veritabanına kaydedilir
        $stmt = $pdo->prepare("INSERT INTO services (patient_id, service_name, service_date, notes) 
                               VALUES (:patient_id, :service_name, :service_date, :notes)");
        $stmt->execute([
            'patient_id' => $patient_id,
            'service_name' => $service_name,
            'service_date' => $service_date,
            'notes' => $notes
        ]);

        $success = "Hizmet başarıyla planlandı.";
    } else {
        $error = "Lütfen zorunlu tüm alanları doldurun.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Yeni Hizmet Planla</h3>

        <!-- Başarı veya hata mesajı -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Hizmet Planlama Formu -->
        <form method="POST">
            <div class="mb-3">
                <label for="patient_id" class="form-label">Hasta Seç *</label>
                <select class="form-select" id="patient_id" name="patient_id" required>
                    <option value="">-- Hasta Seçin --</option>
                    <?php foreach ($patientList as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="service_name" class="form-label">Hizmet Adı *</label>
                <input type="text" class="form-control" id="service_name" name="service_name" required placeholder="Örn: Diş Tedavisi">
            </div>

            <div class="mb-3">
                <label for="service_date" class="form-label">Tarih *</label>
                <input type="date" class="form-control" id="service_date" name="service_date" required>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Açıklama (İsteğe Bağlı)</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tedavi detayları, doktor bilgisi, vb."></textarea>
            </div>

            <button type="submit" class="btn btn-success">Kaydet</button>
            <a href="dashboard.php" class="btn btn-secondary">Panele Dön</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
