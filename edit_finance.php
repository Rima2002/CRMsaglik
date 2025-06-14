<?php
session_start(); // Oturum başlatılır

// Kullanıcı giriş yapmamışsa yönlendirilir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// ID kontrolü: GET ile gelen fatura ID alınır
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: finance.php");
    exit();
}

$finance_id = intval($_GET['id']); // Güvenlik için tam sayıya çevrilir

// Fatura kaydı kullanıcıya ait mi kontrol edilir ve bilgiler çekilir
$stmt = $pdo->prepare("
    SELECT f.*, p.full_name 
    FROM finances f 
    JOIN patients p ON f.patient_id = p.id 
    WHERE f.id = :id AND p.created_by = :user_id
");
$stmt->execute([
    'id' => $finance_id,
    'user_id' => $_SESSION['user_id']
]);

$finance = $stmt->fetch();

// Eğer kayıt bulunamazsa geri gönder
if (!$finance) {
    header("Location: finance.php");
    exit();
}

$success = '';
$error = '';

// Form gönderildiyse güncelleme başlatılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    $paid = isset($_POST['paid']) ? 1 : 0;

    if ($amount > 0) {
        $update = $pdo->prepare("
            UPDATE finances 
            SET amount = :amount, description = :description, paid = :paid 
            WHERE id = :id
        ");
        $update->execute([
            'amount' => $amount,
            'description' => $description,
            'paid' => $paid,
            'id' => $finance_id
        ]);

        $success = "Finansal işlem başarıyla güncellendi.";
        // Güncellenen değerler formda da gösterilsin
        $finance['amount'] = $amount;
        $finance['description'] = $description;
        $finance['paid'] = $paid;
    } else {
        $error = "Tutar sıfırdan büyük olmalıdır.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Finansal İşlemi Güncelle</h3>

        <!-- Hata ve başarı mesajları -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Güncelleme formu -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Hasta</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($finance['full_name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Tutar *</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required value="<?php echo $finance['amount']; ?>">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <input type="text" name="description" id="description" class="form-control" value="<?php echo htmlspecialchars($finance['description']); ?>">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="paid" name="paid" <?php echo $finance['paid'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="paid">Ödeme yapıldı olarak işaretle</label>
            </div>

            <button type="submit" class="btn btn-primary">Güncelle</button>
            <a href="finance.php" class="btn btn-secondary">Finansa Dön</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
