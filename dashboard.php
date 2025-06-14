<?php
session_start(); // Oturum işlemlerini başlatır

// Kullanıcının giriş yapıp yapmadığını kontrol eder
if (!isset($_SESSION['user_id'])) {
    // Giriş yapılmamışsa login sayfasına yönlendirilir
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// Kullanıcının bilgilerini veritabanından çekiyoruz
$query = $pdo->prepare("SELECT username FROM users WHERE id = :id");
$query->execute(['id' => $_SESSION['user_id']]);
$user = $query->fetch();
?>

<?php include 'inc/header.php'; ?>
<div class="container mt-5">
    <div class="text-center">
        <h2>Merhaba, <?php echo htmlspecialchars($user['username']); ?> 👋</h2>
        <p class="lead">CRM Sağlık Turizmi Yönetim Paneline Hoş Geldiniz</p>
    </div>

    <div class="row mt-4">
        <!-- Hasta Yönetimi -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Hastaları Yönet</h5>
                    <p class="card-text">Hasta ekleme, düzenleme, silme işlemleri</p>
                    <a href="add_patient.php" class="btn btn-primary">Git</a>
                </div>
            </div>
        </div>

        <!-- Hizmet Planlama -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Hizmet Planla</h5>
                    <p class="card-text">Sağlık hizmeti planlama ve listeleme</p>
                    <a href="add_service.php" class="btn btn-primary">Git</a>
                </div>
            </div>
        </div>

        <!-- Finansal İşlemler -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Finansal Takip</h5>
                    <p class="card-text">Fatura, ödeme ve ücret bilgileri</p>
                    <a href="finance.php" class="btn btn-primary">Git</a>
                </div>
            </div>
        </div>

        <!-- Geri Bildirim -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Geri Bildirimler</h5>
                    <p class="card-text">Hastaların yorum ve puanlarını görüntüle</p>
                    <a href="feedback.php" class="btn btn-primary">Git</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-danger">Oturumu Kapat</a>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
