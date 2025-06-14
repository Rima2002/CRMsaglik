<?php
session_start(); // Oturumu başlat

// Kullanıcı giriş yapmamışsa login ekranına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'inc/db.php'; // Veritabanı bağlantısı

// Kullanıcının hastalarına ait hizmet kayıtları çekilir
$stmt = $pdo->prepare("
    SELECT s.*, p.full_name 
    FROM services s 
    JOIN patients p ON s.patient_id = p.id 
    WHERE p.created_by = :user_id 
    ORDER BY s.service_date DESC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$services = $stmt->fetchAll();
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">Planlanmış Hizmetler</h3>

    <?php if (count($services) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Hasta Adı</th>
                        <th>Hizmet</th>
                        <th>Tarih</th>
                        <th>Notlar</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($s['service_name']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($s['service_date'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($s['notes'])); ?></td>
                            <td>
                                <!-- Hizmeti düzenleme bağlantısı -->
                                <a href="edit_service.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>

                                <!-- Hizmeti silme bağlantısı -->
                                <a href="delete_service.php?id=<?php echo $s['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu hizmet kaydını silmek istediğinizden emin misiniz?')">
                                   Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Henüz hizmet planlanmamış.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Panele Dön</a>
</div>

<?php include 'inc/footer.php'; ?>
