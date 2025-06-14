# 🤖 Yapay Zeka ile Geliştirme Günlüğü

Bu dosyada, proje geliştirme sürecinde danıştığım yapay zeka araçları ile olan örnek konuşmalar ve projeme özgü bazı önemli bileşenler yer almaktadır.

---

## 🧠 Sohbet 1: Otomatik Uyarı Kapanma Özelliği (JavaScript)

**Ben:**  
Sayfada beliren uyarı kutularını otomatik olarak 4 saniye sonra yok etmek istiyorum. Bunu modern ve animasyonlu şekilde nasıl yaparım?

**ChatGPT:**  
Bunun için `setTimeout` kullanabilirsin. Opaklık değerini azaltarak bir "fade out" efekti verebilir, ardından `remove()` ile DOM'dan kaldırabilirsin. İşte örnek:

```js
setTimeout(() => {
  alerts.forEach(alert => {
    alert.style.transition = "opacity 0.5s ease";
    alert.style.opacity = 0;
    setTimeout(() => alert.remove(), 500);
  });
}, 4000);
```

## 🧠 Sohbet 2: Negatif Sayı Girişi Engelleme

**Ben:**  
Formlarda kullanıcıların negatif sayı girmesini istemiyorum. JavaScript ile bunu nasıl sınırlarım?

**ChatGPT:**  
`input[type="number"]` olan alanlarda `input` event’i dinleyerek negatif girişleri sıfıra çekebilirsin.

```input.addEventListener("input", function () {
  if (this.value < 0) {
    this.value = 0;
  }
});
```

## 🧠 Sohbet 3: Sohbet 3: Yıldızlı Geri Bildirim Validasyonu

**Ben:**  
Geri bildirim formumda kullanıcılar puan seçmeden formu gönderebiliyor. Bunu engellemek istiyorum.

**ChatGPT:**  
`submit` event’ine müdahale ederek, rating seçilmemişse `preventDefault()` ile gönderimi engelleyebilirsin.

```form.addEventListener("submit", function (e) {
  if (!ratingSelect.value) {
    alert("Lütfen puan seçiniz.");
    e.preventDefault();
  }
});
```

## 🧠 Sohbet 4: Finansal Durum Takibi için Boolean Alan Kullanımı

**Ben:**  
Her hastanın ödemesinin yapılıp yapılmadığını sistemde göstermek istiyorum. En sade ve doğru yöntem nedir?

**ChatGPT:**  
Veritabanında `paid` adında `BOOLEAN` (ya da `TINYINT`) bir sütun oluşturman yeterli. `0` ödenmedi, `1` ödendi anlamına gelir. Arayüzde bu değeri ikon ya da renkli etiketle gösterebilirsin. 

```
paid BOOLEAN DEFAULT FALSE
```

## 🧠 Sohbet 5: Responsive ve Estetik Arayüz (Mobile First)

**Ben:**  
Uygulamam sade ama modern gözüksün istiyorum. Özellikle mobilde dağılmamalı. Bootstrap kullanmadan bunu nasıl yaparım?

**ChatGPT:**  
Özel CSS yazabilirsin. Örneğin:

```
@media (max-width: 768px) {
  .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }

  .card {
    margin-bottom: 1rem;
  }
}
```

## 🧠 Sohbet 5: Kullanıcı Bazlı Hasta Kaydı (created_by ilişkisi)

**Ben:**  
Her hasta kaydının kim tarafından eklendiğini takip etmek istiyorum. Bu bilgiye hem yönetim hem güvenlik için ihtiyacım var.

**ChatGPT:**  
`patients` tablosuna `created_by` adında bir `users(id)` dış anahtar referansı eklersen bu bilgi tutulabilir.

```
created_by INT,
FOREIGN KEY (created_by) REFERENCES users(id)
```

## 🧠 Sohbet 6: Form Doğrulama: Hem JS Hem PHP Tarafında

**Ben:**  
Formları sadece frontend değil, backend'de de güvenli doğrulamak istiyorum. En iyi yöntem nedir?

**ChatGPT:**  
JS ile kullanıcı deneyimini iyileştirebilirsin ama asıl doğrulama mutlaka PHP tarafında yapılmalı. Örneğin:

```
if (!isset($_POST['full_name']) || empty(trim($_POST['full_name']))) {
  // hata mesajı
}
```
Ayrıca `htmlspecialchars` ve `mysqli_real_escape_string` gibi yöntemlerle veri temizliği de şart.


