<?php
session_start(); // Oturumu başlat

// Giriş yapılmamışsa yönlendirme yapılır
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı dahil edilir

// Hasta ID'si GET parametresinden alınır
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Eğer id parametresi yoksa kullanıcı panele yönlendirilir
    header("Location: dashboard.php");
    exit();
}

$patient_id = intval($_GET['id']); // Güvenlik için tam sayı türüne çevrilir

// Hasta bilgilerini veritabanından çekiyoruz
$query = $pdo->prepare("SELECT * FROM patients WHERE id = :id AND created_by = :user_id");
$query->execute([
    'id' => $patient_id,
    'user_id' => $_SESSION['user_id']
]);

$patient = $query->fetch();

// Eğer hasta bulunamadıysa yönlendirme yapılır
if (!$patient) {
    header("Location: dashboard.php");
    exit();
}

$success = '';
$error = '';

// Form gönderildiyse güncelleme işlemi yapılır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $passport_no = trim($_POST['passport_no']);
    $country = trim($_POST['country']);
    $contact_info = trim($_POST['contact_info']);

    // Zorunlu alanlar boş mu kontrol edilir
    if (!empty($full_name) && !empty($passport_no) && !empty($country)) {
        // SQL enjeksiyonunu önlemek için prepare kullanılır
        $update = $pdo->prepare("UPDATE patients SET full_name = :full_name, passport_no = :passport_no, country = :country, contact_info = :contact_info WHERE id = :id AND created_by = :user_id");

        $update->execute([
            'full_name' => $full_name,
            'passport_no' => $passport_no,
            'country' => $country,
            'contact_info' => $contact_info,
            'id' => $patient_id,
            'user_id' => $_SESSION['user_id']
        ]);

        $success = "Hasta bilgileri güncellendi.";
        // Güncellenen verileri tekrar çekerek formda göster
        $patient['full_name'] = $full_name;
        $patient['passport_no'] = $passport_no;
        $patient['country'] = $country;
        $patient['contact_info'] = $contact_info;

    } else {
        $error = "Lütfen gerekli tüm alanları doldurun.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="col-md-8 offset-md-2">
        <h3 class="mb-4">Hasta Bilgilerini Düzenle</h3>

        <!-- Bilgilendirme mesajları -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Düzenleme Formu -->
        <form method="POST">
            <div class="mb-3">
                <label for="full_name" class="form-label">Tam Adı *</label>
                <input type="text" class="form-control" id="full_name" name="full_name"
                       value="<?php echo htmlspecialchars($patient['full_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="passport_no" class="form-label">Pasaport Numarası *</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no"
                       value="<?php echo htmlspecialchars($patient['passport_no']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">Ülke *</label>
                <input type="text" class="form-control" id="country" name="country"
                       value="<?php echo htmlspecialchars($patient['country']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="contact_info" class="form-label">İletişim Bilgileri</label>
                <textarea class="form-control" id="contact_info" name="contact_info" rows="3"><?php echo htmlspecialchars($patient['contact_info']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Güncelle</button>
            <a href="list_patients.php" class="btn btn-secondary">Geri Dön</a>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
