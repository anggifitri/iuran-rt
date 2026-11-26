# 📡 Panduan Firmware ESP32 RFID & Smart Gate — NexaNest (RW 018)

Firmware ini digunakan untuk menghubungkan mikrokontroler **ESP32 DevKit** dengan modul pembaca **RFID-RC522 (13.56 MHz)** secara terpusat ke sistem **NexaNest** melalui endpoint REST API JSON (`/api/rfid/scan`).

Dapat membaca **E-KTP**, kartu RFID Mifare 1K, gantungan kunci RFID, serta kartu elektronik berfrekuensi 13.56 MHz lainnya.

---

## 1. Skema Pengkabelan (Wiring Pinout)

> [!CAUTION]
> **PENTING: VCC Modul RC522 WAJIB dihubungkan ke pin 3.3V (3V3) ESP32!**
> Jangan menghubungkan VCC RC522 ke pin 5V / VIN karena dapat merusak chip sensor secara permanen.

| Pin Modul RC522 | Pin ESP32 DevKit | Keterangan |
|---|---|---|
| **SDA / SS** | **GPIO 5 (D5)** | SPI Chip Select (Wajib D5) |
| **SCK** | **GPIO 18 (D18)** | SPI Clock bawaan ESP32 (VSPI) |
| **MOSI** | **GPIO 23 (D23)** | SPI Master-Out-Slave-In |
| **MISO** | **GPIO 19 (D19)** | SPI Master-In-Slave-Out |
| **IRQ** | *Tidak Dihubungkan (NC)* | Dibiarkan kosong |
| **GND** | **GND** | Ground bersama |
| **RST** | **GPIO 22 (D22)** | Reset Pin RC522 (Wajib D22) |
| **3.3V (VCC)** | **3V3** | Sumber Daya 3.3 Volt ESP32 |

### Komponen Bawaan Board ESP32:
* **LED Indikator Status:** **GPIO 2** (LED Biru internal ESP32).
* **Tombol Factory Reset:** **GPIO 0** (Tombol `BOOT` bawaan pada papan ESP32).

---

## 2. Persiapan Arduino IDE & Library

1. **Board Package ESP32:**
   * Di Arduino IDE, buka **File** → **Preferences** → masukkan URL package board berikut ke *Additional Boards Manager URLs*:
     ```text
     https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
     ```
   * Buka **Tools** → **Board** → **Boards Manager** → cari `esp32` oleh *Espressif Systems* → klik **Install** (Direkomendasikan versi `2.0.x` atau `3.0.x`).
   * Pilih tipe board: **DOIT ESP32 DEVKIT V1** (atau *ESP32 Dev Module*).

2. **Library yang Wajib Diinstal:**
   Buka **Sketch** → **Include Library** → **Manage Libraries...** lalu cari dan instal:
   * **`MFRC522`** (oleh *GithubCommunity / Miguel Balboa*) — Versi `1.4.10` atau terbaru.
   * **`ArduinoJson`** (oleh *Benoit Blanchon*) — Versi `6.x` atau `7.x` (keduanya didukung otomatis).
   * *Catatan:* Library WiFi, WebServer, Preferences, dan DNSServer merupakan library bawaan core ESP32 sehingga tidak perlu diinstal terpisah.

---

## 3. Cara Flashing / Upload Firmware

1. Sambungkan ESP32 ke laptop menggunakan kabel data micro-USB / USB-C yang berkualitas baik.
2. Di Arduino IDE, pilih port COM yang sesuai (misal: `COM3`, `COM5`).
3. Set kecepatan baud monitor ke: **115200 baud**.
4. Klik tombol **Upload** (tanda panah ke kanan).
5. Jika proses upload terhenti pada tulisan `Connecting........_____.....`, **tahan tombol BOOT pada ESP32 selama 2 detik** sampai proses *Writing* mulai berjalan, lalu lepaskan.

---

## 4. Cara Konfigurasi via Hotspot (`HotSpot_ESP32`)

Jika ESP32 baru dinyalakan atau tidak menemukan WiFi yang tersimpan:

1. **Hubungkan HP / Laptop ke Hotspot:**
   * Cari jaringan WiFi baru bernama: `HotSpot_ESP32`
   * Masukkan kata sandi: `12345678`
2. **Buka Halaman Konfigurasi:**
   * Biasanya browser HP akan langsung memunculkan pop-up portal login (Captive Portal).
   * Jika tidak terbuka otomatis, buka browser (Chrome / Safari) lalu ketik alamat:
     ```text
     http://192.168.4.1
     ```
3. **Isi Formulir Konfigurasi:**
   * **Jaringan WiFi:** Pilih SSID WiFi yang tersedia atau ketik manual beserta kata sandinya.
   * **Endpoint Server URL:** 
     * Untuk hosting VPS produksi: `https://mynexanest.mehape.com/api/rfid/scan`
     * Untuk testing lokal: `http://192.168.1.xxx:8000/api/rfid/scan` (ganti dengan IP laptop Anda di jaringan yang sama).
   * **API Secret Token:** Masukkan token rahasia yang tercantum di web NexaNest menu **Pengaturan API & IoT** (dapat di-copy langsung dari tombol Copy).
   * **Nama / ID Perangkat:** `ESP32-GATE-01` (atau sesuaikan dengan titik gerbang).
4. **Klik tombol "Simpan & Hubungkan Ulang":**
   * Data akan tersimpan secara permanen di memori NVS (Non-Volatile Storage) ESP32.
   * ESP32 akan reboot otomatis dan langsung terhubung ke WiFi serta standby menunggu tap kartu.

---

## 5. Arti Indikator LED Biru (GPIO 2)

* **Kedip Lambat (0.6 Detik):** Sedang berada di mode `HotSpot_ESP32` (menunggu konfigurasi atau WiFi terputus).
* **Menyala Terang 2 Detik:** Tap kartu berhasil, token valid, dan **akses diterima / kartu terdaftar** (palang gerbang terbuka).
* **Kedip Cepat 3 Kali:** Akses ditolak (kartu belum terdaftar di database warga, token salah, atau API dinonaktifkan).
* **Mati:** Posisi Standby normal, siap membaca kartu berikutnya.

---

## 6. Tombol Factory Reset (Masuk Ulang Mode Hotspot)

Jika Anda ingin mengganti WiFi atau memindahkan perangkat ke lokasi lain:
1. Saat ESP32 menyala, **tekan dan tahan tombol BOOT (GPIO 0) selama 3 detik**.
2. LED biru akan berkedip cepat 5 kali sebagai tanda bahwa seluruh konfigurasi NVS telah dihapus.
3. ESP32 otomatis me-restart dan kembali menyalakan Access Point `HotSpot_ESP32`.
