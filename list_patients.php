<?php
session_start();

// Giriş yapılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php';

// Bu kullanıcıya ait hastaları çek
$stmt = $pdo->prepare("SELECT * FROM patients WHERE created_by = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$patients = $stmt->fetchAll();
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">Kayıtlı Hastalar</h3>

    <?php if (count($patients) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Adı Soyadı</th>
                        <th>Pasaport No</th>
                        <th>Ülke</th>
                        <th>İletişim</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['passport_no']); ?></td>
                            <td><?php echo htmlspecialchars($p['country']); ?></td>
                            <td><?php echo htmlspecialchars($p['contact_info']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($p['created_at'])); ?></td>
                            <td>
                                <a href="edit_patient.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                <a href="delete_patient.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu hastayı silmek istediğinizden emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Henüz kayıtlı hasta yok.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Panele Dön</a>
</div>

<?php include 'inc/footer.php'; ?>
