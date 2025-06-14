<?php
session_start(); // Oturum başlatılır

// Giriş yapılmamışsa kullanıcı login sayfasına yönlendirilir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

$success = '';
$error = '';

// Eğer form POST yöntemiyle gönderildiyse işlem başlatılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler alınır ve boşluklar temizlenir
    $full_name = trim($_POST['full_name']);
    $passport_no = trim($_POST['passport_no']);
    $country = trim($_POST['country']);
    $contact_info = trim($_POST['contact_info']);

    // Gerekli alanlar kontrol edilir
    if (!empty($full_name) && !empty($passport_no) && !empty($country)) {
        // Hazırlanmış SQL ile SQL enjeksiyonuna karşı koruma sağlanır
        $query = $pdo->prepare("INSERT INTO patients (full_name, passport_no, country, contact_info, created_by) 
                                VALUES (:full_name, :passport_no, :country, :contact_info, :created_by)");

        $query->execute([
            'full_name' => $full_name,
            'passport_no' => $passport_no,
            'country' => $country,
            'contact_info' => $contact_info,
            'created_by' => $_SESSION['user_id']
        ]);

        // Başarılı ekleme sonrası kullanıcıya bilgi verilir
        $success = "Hasta başarıyla kaydedildi.";
    } else {
        // Zorunlu alanlar boşsa hata mesajı gösterilir
        $error = "Lütfen gerekli tüm alanları doldurun.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Yeni Hasta Ekle</h3>

        <!-- Hata veya başarı mesajları -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Hasta Ekleme Formu -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="full_name" class="form-label">Tam Adı *</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

            <div class="mb-3">
                <label for="passport_no" class="form-label">Pasaport Numarası *</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no" required>
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">Ülke *</label>
                <input type="text" class="form-control" id="country" name="country" required>
            </div>

            <div class="mb-3">
                <label for="contact_info" class="form-label">İletişim Bilgileri</label>
                <textarea class="form-control" id="contact_info" name="contact_info" rows="3" placeholder="Telefon, e-posta vs."></textarea>
            </div>

            <button type="submit" class="btn btn-success">Kaydet</button>
            <a href="dashboard.php" class="btn btn-secondary">Panele Dön</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
