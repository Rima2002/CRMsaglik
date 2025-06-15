<?php
// Veritabanı bağlantı bilgileri
$host = 'localhost';            
$dbname = 'crm_saglik';         
$username = 'root';            
$password = '';                

try {
    // PDO nesnesi ile veritabanına bağlanılıyor
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // PDO'nun hata ayıklama modunu açıyoruz (hatalar Exception olarak fırlatılır)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Güvenlik için emulated prepares özelliğini kapatmak daha güvenlidir
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Bağlantı hatası durumunda kullanıcıya hata mesajı gösterilmez, geliştirici için log tutulabilir
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}
