# 🤖 AI.md – Yapay Zeka ile Geliştirme Desteği

Bu proje geliştirilirken ChatGPT gibi yapay zeka tabanlı sohbet araçlarından kod yazımı, algoritma önerileri ve kullanıcı arayüzü tasarımı gibi konularda destek alınmıştır. Aşağıda örnek sohbet kayıtları yer almaktadır.

---

## 🟡 1. Veritabanı Yapısı Tasarımı

👤 Ben:  
CRM Sağlık Turizmi için kullanıcı, hasta, hizmet, ödeme ve geri bildirim tablolarını içeren bir MySQL veritabanı yapısı önerir misin?

🤖 ChatGPT:  
Elbette. Aşağıda örnek tablo yapıları verilmiştir:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100),
  country VARCHAR(50),
  passport_number VARCHAR(50),
  contact_info VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- diğer tablolar devam eder...

