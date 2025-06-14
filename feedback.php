<?php
session_start(); // Oturumu başlat

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

$success = '';
$error = '';

// Kullanıcının hastaları çekilir (seçim için)
$patients = $pdo->prepare("SELECT id, full_name FROM patients WHERE created_by = :user_id ORDER BY full_name ASC");
$patients->execute(['user_id' => $_SESSION['user_id']]);
$patientList = $patients->fetchAll();

// Geri bildirim formu gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $message = trim($_POST['message']);
    $rating = intval($_POST['rating']);

    // Zorunlu alanlar kontrol edilir
    if ($patient_id && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO feedbacks (patient_id, message, rating) 
                               VALUES (:patient_id, :message, :rating)");
        $stmt->execute([
            'patient_id' => $patient_id,
            'message' => $message,
            'rating' => $rating
        ]);

        $success = "Geri bildirim kaydedildi.";
    } else {
        $error = "Lütfen hasta seçin ve 1 ile 5 arasında puan verin.";
    }
}

// Daha önce girilmiş geri bildirimler çekilir
$query = $pdo->prepare("
    SELECT f.*, p.full_name 
    FROM feedbacks f 
    JOIN patients p ON f.patient_id = p.id 
    WHERE p.created_by = :user_id 
    ORDER BY f.created_at DESC
");
$query->execute(['user_id' => $_SESSION['user_id']]);
$feedbacks = $query->fetchAll();
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-10 offset-md-1">
        <h3 class="mb-4">Geri Bildirimler</h3>

        <!-- Bilgilendirme mesajları -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Geri Bildirim Formu -->
        <form method="POST" class="border rounded p-4 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="patient_id" class="form-label">Hasta Seç *</label>
                    <select class="form-select" id="patient_id" name="patient_id" required>
                        <option value="">-- Seçiniz --</option>
                        <?php foreach ($patientList as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="rating" class="form-label">Puan (1-5) *</label>
                    <select class="form-select" id="rating" name="rating" required>
                        <option value="">-- Puan Verin --</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="message" class="form-label">Yorum</label>
                    <input type="text" class="form-control" id="message" name="message" placeholder="Yorum eklemek isterseniz...">
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success">Gönder</button>
                </div>
            </div>
        </form>

        <!-- Geri Bildirim Listesi -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Hasta</th>
                        <th>Puan</th>
                        <th>Yorum</th>
                        <th>Tarih</th>
                        <th>İşlem</th>

                    </tr>
                </thead>
                <tbody>
                    <?php if (count($feedbacks) > 0): ?>
                        <?php foreach ($feedbacks as $fb): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fb['full_name']); ?></td>
                                <td><?php echo str_repeat('★', $fb['rating']); ?></td>
                                <td><?php echo htmlspecialchars($fb['message']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($fb['created_at'])); ?></td>
                                <td><a href="edit_feedback.php?id=<?php echo $fb['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Henüz geri bildirim yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-4">Panele Dön</a>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
