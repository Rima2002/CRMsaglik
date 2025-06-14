<?php
session_start(); // Oturum işlemlerini başlatır

// Kullanıcı zaten giriş yaptıysa, doğrudan dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı dahil edilir

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler alınır ve boşluklar temizlenir
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Tüm alanların doldurulup doldurulmadığı kontrol edilir
    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        // Şifrelerin eşleşip eşleşmediği kontrol edilir
        if ($password === $confirm_password) {
            // Şifrenin minimum uzunluk kontrolü yapılabilir 
            if (strlen($password) >= 6) {
                // Aynı e-posta adresi daha önce kullanılmış mı diye kontrol edilir
                $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $check->execute(['email' => $email]);

                if ($check->rowCount() === 0) {
                    // Şifre hash'lenir (güvenlik için)
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Kullanıcı verileri veritabanına eklenir
                    $insert = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                    $insert->execute([
                        'username' => $username,
                        'email' => $email,
                        'password' => $hashedPassword
                    ]);

                    // Kayıt başarılıysa kullanıcıya bilgi verilir
                    $success = "Kayıt başarılı! Giriş yapabilirsiniz.";
                } else {
                    $error = "Bu e-posta adresi zaten kayıtlı.";
                }
            } else {
                $error = "Şifreniz en az 6 karakter olmalıdır.";
            }
        } else {
            $error = "Şifreler eşleşmiyor.";
        }
    } else {
        $error = "Lütfen tüm alanları eksiksiz doldurun.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Kayıt Ol</h2>

            <!-- Hata veya başarı mesajı gösterimi -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Kayıt formu -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Adınız">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="eposta@ornek.com">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="******">
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Şifre (Tekrar)</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="******">
                </div>

                <button type="submit" class="btn btn-success w-100">Kayıt Ol</button>
            </form>

            <p class="mt-3 text-center">
                Zaten hesabınız var mı? <a href="login.php">Giriş yapın</a>
            </p>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
