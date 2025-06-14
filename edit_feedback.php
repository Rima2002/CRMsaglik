<?php
session_start(); // Oturumu başlat

// Giriş yapılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// Geri bildirim ID'si GET ile alınır
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: feedback.php");
    exit();
}

$feedback_id = intval($_GET['id']); // Güvenlik için tam sayıya çevrilir

// Kullanıcıya ait olan geri bildirim kaydı çekilir
$stmt = $pdo->prepare("
    SELECT f.*, p.full_name 
    FROM feedbacks f
    JOIN patients p ON f.patient_id = p.id
    WHERE f.id = :id AND p.created_by = :user_id
");
$stmt->execute([
    'id' => $feedback_id,
    'user_id' => $_SESSION['user_id']
]);

$feedback = $stmt->fetch();

// Kayıt bulunamadıysa yönlendir
if (!$feedback) {
    header("Location: feedback.php");
    exit();
}

$success = '';
$error = '';

// Form POST ile gönderildiyse güncelleme başlatılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $message = trim($_POST['message']);

    // Puan geçerli aralıkta mı kontrol edilir
    if ($rating >= 1 && $rating <= 5) {
        $update = $pdo->prepare("
            UPDATE feedbacks 
            SET rating = :rating, message = :message 
            WHERE id = :id
        ");
        $update->execute([
            'rating' => $rating,
            'message' => $message,
            'id' => $feedback_id
        ]);

        $success = "Geri bildirim başarıyla güncellendi.";
        $feedback['rating'] = $rating;
        $feedback['message'] = $message;
    } else {
        $error = "Lütfen 1 ile 5 arasında bir puan giriniz.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Geri Bildirimi Düzenle</h3>

        <!-- Hata veya başarı mesajı -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Düzenleme Formu -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Hasta</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($feedback['full_name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="rating" class="form-label">Puan (1–5)</label>
                <select name="rating" id="rating" class="form-select" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($feedback['rating'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?> ★
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Yorum</label>
                <textarea name="message" id="message" rows="3" class="form-control"><?php echo htmlspecialchars($feedback['message']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Güncelle</button>
            <a href="feedback.php" class="btn btn-secondary">Geri Bildirim Listesi</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
