<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sağlık Turizmi CRM Sistemi</title>

    <!-- Mobil uyumluluk için viewport ayarı -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS CDN bağlantısı -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Özel stil dosyamız -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Sayfa genelinde kullanılacak üst navigasyon menüsü -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">🩺 Sağlık CRM</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Oturum açıksa gösterilecek bağlantılar -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_patient.php">Hasta Ekle</a></li>
                    <li class="nav-item"><a class="nav-link" href="list_patients.php">Hasta Listesi</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_service.php">Hizmet Planla</a></li>
                    <li class="nav-item"><a class="nav-link" href="list_services.php">Hizmet Listesi</a></li>
                    <li class="nav-item"><a class="nav-link" href="finance.php">Finans</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Geri Bildirim</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Çıkış Yap</a></li>
                <?php else: ?>
                    <!-- Giriş yapılmamışsa gösterilecek bağlantılar -->
                    <li class="nav-item"><a class="nav-link" href="login.php">Giriş</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Kayıt</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
