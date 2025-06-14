<?php
session_start(); // Oturum başlatılır

// Giriş kontrolü yapılır, giriş yapılmamışsa yönlendirme yapılır
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı dahil edilir

$success = '';
$error = '';

// Kullanıcının hastaları çekilir (ödeme ilişkilendirmek için)
$patients = $pdo->prepare("SELECT id, full_name FROM patients WHERE created_by = :user_id ORDER BY full_name ASC");
$patients->execute(['user_id' => $_SESSION['user_id']]);
$patientList = $patients->fetchAll();

// Eğer form gönderildiyse finansal işlem eklenir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    $paid = isset($_POST['paid']) ? 1 : 0;

    // Gerekli alanların boş olup olmadığı kontrol edilir
    if ($patient_id && $amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO finances (patient_id, amount, description, paid) 
                               VALUES (:patient_id, :amount, :description, :paid)");
        $stmt->execute([
            'patient_id' => $patient_id,
            'amount' => $amount,
            'description' => $description,
            'paid' => $paid
        ]);

        $success = "Finansal işlem kaydedildi.";
    } else {
        $error = "Lütfen hasta seçin ve geçerli bir tutar girin.";
    }
}

// Tüm finansal işlemler listelenir (bu kullanıcıya ait hastalara göre)
$financeQuery = $pdo->prepare("
    SELECT f.*, p.full_name 
    FROM finances f 
    JOIN patients p ON f.patient_id = p.id 
    WHERE p.created_by = :user_id 
    ORDER BY f.created_at DESC
");
$financeQuery->execute(['user_id' => $_SESSION['user_id']]);
$finances = $financeQuery->fetchAll();
?>

<?php include 'inc/header.php'; ?>
<div class="container mt-5">
    <div class="col-md-10 offset-md-1">
        <h3 class="mb-4">Finansal İşlemler</h3>

        <!-- Hata ve başarı mesajları -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Finans Ekleme Formu -->
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

                <div class="col-md-2">
                    <label for="amount" class="form-label">Tutar *</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>

                <div class="col-md-4">
                    <label for="description" class="form-label">Açıklama</label>
                    <input type="text" class="form-control" id="description" name="description">
                </div>

                <div class="col-md-2">
                    <label class="form-label d-block">Ödendi mi?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="paid" name="paid">
                        <label class="form-check-label" for="paid">Evet</label>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success">Ekle</button>
                </div>
            </div>
        </form>

        <!-- Finansal İşlem Listesi -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Hasta</th>
                        <th>Tutar</th>
                        <th>Açıklama</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($finances) > 0): ?>
                        <?php foreach ($finances as $f): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($f['full_name']); ?></td>
                                <td><?php echo number_format($f['amount'], 2); ?> ₺</td>
                                <td><?php echo htmlspecialchars($f['description']); ?></td>
                                <td>
                                    <?php if ($f['paid']): ?>
                                        <span class="badge bg-success">Ödendi</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Bekliyor</span>
                                    <?php endif; ?>
                                    <br>
                                    <a href="edit_finance.php?id=<?php echo $f['id']; ?>">
                                        <span class="badge bg-primary mt-1">Düzenle</span>
                                    </a>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($f['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Kayıtlı finansal işlem yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-4">Panele Dön</a>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
