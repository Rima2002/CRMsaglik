<?php
session_start(); // Oturum işlemlerini başlatır

// Eğer kullanıcı zaten giriş yaptıysa doğrudan kontrol paneline gönder
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısını içeri aktar

// Hata mesajını tutmak için değişken
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen verileri al ve boşluklardan temizle
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // E-posta ve şifre alanlarının boş olmadığını kontrol et
    if (!empty($email) && !empty($password)) {
        // Kullanıcıyı e-posta ile veritabanında ara
        $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $user = $query->fetch();

        // Kullanıcı bulunduysa ve şifre doğruysa oturumu başlat
        if ($user && password_verify($password, $user['password'])) {
            // Oturum bilgisi olarak kullanıcı ID'si saklanır
            $_SESSION['user_id'] = $user['id'];

            // Dashboard'a yönlendirme yapılır
            header("Location: dashboard.php");
            exit();
        } else {
            // Şifre yanlışsa ya da kullanıcı yoksa hata mesajı göster
            $error = "Geçersiz e-posta veya şifre.";
        }
    } else {
        // Boş alanlar varsa uyarı ver
        $error = "Lütfen tüm alanları doldurunuz.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Giriş Yap</h2>

            <!-- Eğer hata varsa burada gösterilir -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Giriş formu -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="ornek@eposta.com">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="******">
                </div>

                <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
            </form>

            <p class="mt-3 text-center">
                Henüz hesabınız yok mu? <a href="register.php">Kayıt olun</a>
            </p>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
