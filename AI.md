# 🤖 Yapay Zeka ile Geliştirme Günlüğü (AI.md)

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
