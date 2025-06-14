<?php
session_start();

// Eğer kullanıcı oturum açmışsa doğrudan kontrol paneline yönlendirilir
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="mb-4">CRM Sağlık Turizmi Yönetim Sistemi</h1>
            <p class="lead">
                Bu sistem, yurt dışından gelen hastaların sağlık süreçlerini etkili ve merkezi bir şekilde yönetmek için geliştirilmiştir.
            </p>
            <hr>
            <p>
                Sisteme giriş yaparak hasta bilgilerini yönetebilir, hizmet planlaması yapabilir, finansal işlemleri takip edebilir ve geri bildirimleri görüntüleyebilirsiniz.
            </p>
            <a href="login.php" class="btn btn-primary btn-lg me-2">Giriş Yap</a>
            <a href="register.php" class="btn btn-outline-primary btn-lg">Kayıt Ol</a>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
