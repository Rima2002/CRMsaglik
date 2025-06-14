// Sayfa yüklendiğinde çalıştır
document.addEventListener("DOMContentLoaded", function () {

    // Sayfadaki tüm alert'leri otomatik olarak gizle (örneğin 4 saniye sonra)
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = 0;

                // Tamamen görünmez hale geldikten sonra DOM'dan kaldır
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000); // 4 saniye sonra başlasın
    }

    // Formlarda sayı girilmesi gereken yerlere negatif girişleri engelle
    const numberInputs = document.querySelectorAll("input[type='number']");
    numberInputs.forEach(input => {
        input.addEventListener("input", function () {
            if (this.value < 0) {
                this.value = 0;
            }
        });
    });

    // Geri bildirimde yıldız seçilmemişse form gönderimini engelle 
    const feedbackForm = document.querySelector("form.feedback-form");
    if (feedbackForm) {
        feedbackForm.addEventListener("submit", function (e) {
            const ratingSelect = feedbackForm.querySelector("select[name='rating']");
            if (ratingSelect && !ratingSelect.value) {
                alert("Lütfen puan seçiniz.");
                e.preventDefault(); // Formun gönderilmesini engelle
            }
        });
    }
    
});
