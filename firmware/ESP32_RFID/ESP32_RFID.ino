/*
 * ==============================================================================
 * NEXANEST IOT SMART GATE & RFID CLIENT — ESP32 FIRMWARE
 * ==============================================================================
 * Perangkat  : DOIT ESP32 DevKit V1 (30 / 36 Pin)
 * Modul RFID : MFRC522 (13.56 MHz RFID / NFC / E-KTP)
 * Kompatibel : Laravel NexaNest (RW 018) Endpoint /api/rfid/scan
 *
 * FITUR UTAMA:
 * 1. Membaca UID E-KTP / Kartu RFID / Tag 13.56 MHz (Format Hex Kapital).
 * 2. Fallback Hotspot (AP 'HotSpot_ESP32' + Captive Portal Web GUI di 192.168.4.1).
 * 3. Penyimpanan Permanen NVS (Preferences) untuk SSID, Password, Server URL, & Token.
 * 4. Pengiriman JSON over HTTPS (setInsecure) & HTTP dengan Header X-API-TOKEN.
 * 5. Indikator LED cerdas (Standby, Hotspot, Sukses Akses, Ditolak).
 * 6. Tombol Fisik BOOT (GPIO 0) ditekan 3 detik untuk Factory Reset / Masuk Hotspot.
 * 7. Anti-spam Cooldown (3 detik) agar tidak terjadi dobel scan.
 * ==============================================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <WebServer.h>
#include <DNSServer.h>
#include <Preferences.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

// ==============================================================================
// 1. PINOUT WIRING FISIK
// ==============================================================================
#define RST_PIN        22    // RC522 RST -> GPIO 22 (D22)
#define SS_PIN         5     // RC522 SDA/SS -> GPIO 5 (D5)
// SCK  = GPIO 18 (D18) — Standar VSPI ESP32
// MISO = GPIO 19 (D19) — Standar VSPI ESP32
// MOSI = GPIO 23 (D23) — Standar VSPI ESP32

#define LED_PIN        2     // LED Biru Internal ESP32 (GPIO 2)
#define RESET_BTN_PIN  0     // Tombol Fisik BOOT ESP32 (GPIO 0)

// ==============================================================================
// 2. KONFIGURASI DEFAULT & VARIABEL GLOBAL
// ==============================================================================
const char* AP_SSID = "HotSpot_ESP32";
const char* AP_PASS = "12345678";

// Nilai default jika belum dikonfigurasi lewat Hotspot
String wifiSsid      = "";
String wifiPassword  = "";
String serverUrl     = "https://mynexanest.mehape.com/api/rfid/scan";
String apiToken      = "";
String deviceId      = "ESP32-GATE-01";

// Status Sistem
bool isHotspotMode   = false;
unsigned long lastScanTime = 0;
const unsigned long SCAN_COOLDOWN_MS = 3000; // Cooldown 3 detik anti dobel scan

// Objek Modul
MFRC522 mfrc522(SS_PIN, RST_PIN);
WebServer server(80);
DNSServer dnsServer;
Preferences preferences;

// ==============================================================================
// 3. FUNGSI INDIKATOR LED (GPIO 2)
// ==============================================================================
void ledOn() {
  digitalWrite(LED_PIN, HIGH);
}

void ledOff() {
  digitalWrite(LED_PIN, LOW);
}

// Berkedip cepat 3x (Akses Ditolak / Gagal Koneksi / Error)
void ledBlinkReject() {
  for (int i = 0; i < 3; i++) {
    ledOn();
    delay(100);
    ledOff();
    delay(100);
  }
}

// Menyala 2 detik (Akses Diterima & Kartu Terdaftar)
void ledSuccessGranted() {
  ledOn();
  delay(2000);
  ledOff();
}

// ==============================================================================
// 4. PREFERENCES (NVS) — LOAD, SAVE & RESET
// ==============================================================================
void loadSavedSettings() {
  preferences.begin("nexanest_cfg", true); // Read-only mode
  wifiSsid      = preferences.getString("ssid", "");
  wifiPassword  = preferences.getString("pass", "");
  serverUrl     = preferences.getString("url", "https://mynexanest.mehape.com/api/rfid/scan");
  apiToken      = preferences.getString("token", "");
  deviceId      = preferences.getString("devid", "ESP32-GATE-01");
  preferences.end();

  Serial.println("\n[CFG] Memuat Konfigurasi Tersimpan:");
  Serial.print(" - WiFi SSID  : "); Serial.println(wifiSsid.length() > 0 ? wifiSsid : "(Kosong)");
  Serial.print(" - Server URL : "); Serial.println(serverUrl);
  Serial.print(" - Device ID  : "); Serial.println(deviceId);
  Serial.print(" - Token Set  : "); Serial.println(apiToken.length() > 0 ? "Ya (Terisi)" : "Belum Diisi");
}

void saveSettings(String newSsid, String newPass, String newUrl, String newTok, String newDev) {
  preferences.begin("nexanest_cfg", false); // Read-write mode
  preferences.putString("ssid", newSsid);
  preferences.putString("pass", newPass);
  preferences.putString("url", newUrl);
  preferences.putString("token", newTok);
  preferences.putString("devid", newDev);
  preferences.end();
  Serial.println("[CFG] Konfigurasi baru berhasil disimpan ke NVS!");
}

void factoryReset() {
  Serial.println("\n[WARN] Melakukan Reset Konfigurasi Pabrik...");
  preferences.begin("nexanest_cfg", false);
  preferences.clear();
  preferences.end();

  // Kedip cepat 5x sebagai tanda reset berhasil
  for (int i = 0; i < 5; i++) {
    ledOn();
    delay(80);
    ledOff();
    delay(80);
  }
  Serial.println("[INFO] Restarting ke Mode Hotspot...");
  delay(1000);
  ESP.restart();
}

// ==============================================================================
// 5. CAPTIVE PORTAL & WEB GUI CONFIGURATION (192.168.4.1)
// ==============================================================================
String buildHtmlPage(String scannedWifiOptions) {
  String html = "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>";
  html += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  html += "<title>NexaNest — Konfigurasi ESP32 Smart Gate</title>";
  html += "<style>";
  html += "* { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }";
  html += "body { background: #0F172A; color: #F8FAFC; margin: 0; padding: 16px; display: flex; justify-content: center; }";
  html += ".card { background: #1E293B; border: 1px solid #334155; border-radius: 16px; padding: 24px; max-width: 440px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }";
  html += ".header { text-align: center; margin-bottom: 20px; }";
  html += ".icon-box { width: 56px; height: 56px; background: linear-gradient(135deg, #1E4633 0%, #285943 100%); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 10px; }";
  html += "h2 { margin: 0 0 6px 0; font-size: 20px; color: #38BDF8; }";
  html += "p { margin: 0; font-size: 13px; color: #94A3B8; }";
  html += ".form-group { margin-bottom: 16px; }";
  html += "label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #CBD5E1; text-transform: uppercase; letter-spacing: 0.5px; }";
  html += "input, select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #475569; background: #0F172A; color: #FFFFFF; font-size: 14px; }";
  html += "input:focus, select:focus { outline: none; border-color: #38BDF8; box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2); }";
  html += ".btn-submit { width: 100%; padding: 14px; background: #10B981; color: #FFFFFF; font-size: 15px; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; margin-top: 10px; transition: background 0.2s; }";
  html += ".btn-submit:hover { background: #059669; }";
  html += ".badge { display: inline-block; background: rgba(56, 189, 248, 0.15); color: #38BDF8; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-bottom: 14px; }";
  html += ".footer { text-align: center; margin-top: 20px; font-size: 11px; color: #64748B; }";
  html += "</style></head><body>";
  
  html += "<div class='card'>";
  html += "<div class='header'>";
  html += "<div class='icon-box'>&#128274;</div>";
  html += "<h2>NexaNest Smart Gate</h2>";
  html += "<p>Portal Pengaturan WiFi & Endpoint ESP32 RC522</p>";
  html += "</div>";

  html += "<div style='text-align: center;'><span class='badge'>HotSpot: " + String(AP_SSID) + " (192.168.4.1)</span></div>";

  html += "<form method='POST' action='/save'>";
  
  // Pilihan SSID dari hasil scan atau ketik manual
  html += "<div class='form-group'>";
  html += "<label>Jaringan WiFi Terdeteksi</label>";
  html += "<select id='ssidSelect' onchange='document.getElementById(\"ssidInput\").value = this.value;'>";
  html += "<option value=''>-- Pilih SSID Sekitar atau Ketik Manual --</option>";
  html += scannedWifiOptions;
  html += "</select>";
  html += "</div>";

  html += "<div class='form-group'>";
  html += "<label>SSID WiFi Target</label>";
  html += "<input type='text' id='ssidInput' name='ssid' value='" + wifiSsid + "' placeholder='Nama WiFi...' required>";
  html += "</div>";

  html += "<div class='form-group'>";
  html += "<label>Password WiFi</label>";
  html += "<input type='password' name='pass' value='" + wifiPassword + "' placeholder='Kata Sandi WiFi...'>";
  html += "</div>";

  html += "<div class='form-group'>";
  html += "<label>Endpoint Server URL (POST)</label>";
  html += "<input type='text' name='url' value='" + serverUrl + "' placeholder='https://.../api/rfid/scan' required>";
  html += "</div>";

  html += "<div class='form-group'>";
  html += "<label>API Secret Token</label>";
  html += "<input type='text' name='token' value='" + apiToken + "' placeholder='Token dari Dashboard Pengaturan IoT...' required>";
  html += "</div>";

  html += "<div class='form-group'>";
  html += "<label>Nama / ID Perangkat</label>";
  html += "<input type='text' name='devid' value='" + deviceId + "' placeholder='ESP32-GATE-01' required>";
  html += "</div>";

  html += "<button type='submit' class='btn-submit'>Simpan &amp; Hubungkan Ulang</button>";
  html += "</form>";

  html += "<div class='footer'>RW 018 Kelurahan Mekarjaya &bull; NexaNest IoT 2026</div>";
  html += "</div></body></html>";
  return html;
}

void handleRoot() {
  Serial.println("[HTTP] Membuka halaman konfigurasi web...");
  
  // Scan WiFi sekitar untuk dropdown
  int n = WiFi.scanNetworks();
  String options = "";
  if (n > 0) {
    for (int i = 0; i < n; ++i) {
      options += "<option value='" + WiFi.SSID(i) + "'>" + WiFi.SSID(i) + " (" + String(WiFi.RSSI(i)) + " dBm)</option>";
    }
  }

  server.send(200, "text/html", buildHtmlPage(options));
}

void handleSave() {
  String newSsid = server.arg("ssid");
  String newPass = server.arg("pass");
  String newUrl  = server.arg("url");
  String newTok  = server.arg("token");
  String newDev  = server.arg("devid");

  newSsid.trim();
  newPass.trim();
  newUrl.trim();
  newTok.trim();
  newDev.trim();

  saveSettings(newSsid, newPass, newUrl, newTok, newDev);

  String resp = "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>";
  resp += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  resp += "<title>Tersimpan!</title>";
  resp += "<style>body{background:#0F172A;color:#FFFFFF;text-align:center;padding:50px 20px;font-family:sans-serif;}";
  resp += ".box{background:#1E293B;padding:30px;border-radius:12px;max-width:400px;margin:auto;}";
  resp += "h2{color:#10B981;} p{color:#94A3B8;font-size:14px;}</style></head><body>";
  resp += "<div class='box'><h2>&#10004; Konfigurasi Berhasil Disimpan!</h2>";
  resp += "<p>ESP32 akan restart otomatis dalam 3 detik dan mencoba tersambung ke jaringan <b>" + newSsid + "</b>.</p>";
  resp += "<p>Silakan hubungkan kembali ponsel/laptop Anda ke jaringan normal.</p></div></body></html>";

  server.send(200, "text/html", resp);
  delay(2000);
  ESP.restart();
}

void startHotspotMode() {
  isHotspotMode = true;
  WiFi.disconnect(true);
  WiFi.mode(WIFI_AP);
  WiFi.softAP(AP_SSID, AP_PASS);

  IPAddress apIP = WiFi.softAPIP();
  Serial.println("\n==========================================");
  Serial.println("  MODE HOTSPOT AKTIF (FALLBACK CAPTIVE)");
  Serial.println("==========================================");
  Serial.print("  SSID     : "); Serial.println(AP_SSID);
  Serial.print("  Password : "); Serial.println(AP_PASS);
  Serial.print("  IP Web   : http://"); Serial.println(apIP);
  Serial.println("  Buka browser dan buka http://192.168.4.1");
  Serial.println("==========================================\n");

  // DNS Server untuk menangani captive portal
  dnsServer.start(53, "*", apIP);

  server.on("/", HTTP_GET, handleRoot);
  server.on("/save", HTTP_POST, handleSave);
  server.onNotFound([]() {
    server.sendHeader("Location", "http://192.168.4.1/", true);
    server.send(302, "text/plain", "");
  });

  server.begin();
  Serial.println("[HTTP] Web Server Konfigurasi Siap.");
}

// ==============================================================================
// 6. KONEKSI KE WIFI DENGAN TIMEOUT 15 DETIK
// ==============================================================================
bool connectToWiFi() {
  if (wifiSsid.length() == 0) {
    Serial.println("[WIFI] Belum ada konfigurasi SSID tersimpan.");
    return false;
  }

  Serial.println("\n[WIFI] Menghubungkan ke: " + wifiSsid);
  WiFi.mode(WIFI_STA);
  WiFi.begin(wifiSsid.c_str(), wifiPassword.c_str());

  unsigned long startAttempt = millis();
  const unsigned long TIMEOUT = 15000; // 15 detik

  while (WiFi.status() != WL_CONNECTED && millis() - startAttempt < TIMEOUT) {
    delay(400);
    ledOn();
    delay(100);
    ledOff();
    Serial.print(".");
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[WIFI] Terhubung dengan sukses!");
    Serial.print(" - IP Address : "); Serial.println(WiFi.localIP());
    Serial.print(" - RSSI Sinya : "); Serial.print(WiFi.RSSI()); Serial.println(" dBm");
    ledOff();
    return true;
  } else {
    Serial.println("\n[WIFI] Gagal terhubung (Timeout 15s tercapai).");
    return false;
  }
}

// ==============================================================================
// 7. PENGIRIMAN DATA UID KE LARAVEL (HTTPS / HTTP)
// ==============================================================================
void sendScanToServer(String uidHex) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[ERR] Tidak ada koneksi WiFi! Mencoba menyambung kembali...");
    ledBlinkReject();
    return;
  }

  Serial.println("\n--------------------------------------------------");
  Serial.print("[RFID] UID Terdeteksi   : "); Serial.println(uidHex);
  Serial.print("[HTTP] Mengirim ke Server : "); Serial.println(serverUrl);

  HTTPClient http;
  bool isHttps = serverUrl.startsWith("https://");

  if (isHttps) {
    WiFiClientSecure secureClient;
    secureClient.setInsecure(); // Bypass validasi sertifikat SSL agar fleksibel
    http.begin(secureClient, serverUrl);
  } else {
    WiFiClient plainClient;
    http.begin(plainClient, serverUrl);
  }

  // Header Permintaan
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  if (apiToken.length() > 0) {
    http.addHeader("X-API-TOKEN", apiToken);
  }

  // Body JSON
  // Format: {"uid":"A1B2C3D4","token":"...","device_id":"ESP32-GATE-01","context":"smart_gate"}
  String requestPayload = "{\"uid\":\"" + uidHex + "\",\"token\":\"" + apiToken + "\",\"device_id\":\"" + deviceId + "\",\"context\":\"smart_gate\"}";
  Serial.print("[HTTP] Payload JSON    : "); Serial.println(requestPayload);

  int httpCode = http.POST(requestPayload);
  String responsePayload = http.getString();

  Serial.print("[HTTP] Response Code    : "); Serial.println(httpCode);
  Serial.print("[HTTP] Response Body    : "); Serial.println(responsePayload);

  // Parsing Respon JSON dari Laravel NexaNest
  if (httpCode == 200) {
    #if ARDUINOJSON_VERSION_MAJOR >= 7
      JsonDocument doc;
    #else
      StaticJsonDocument<512> doc;
    #endif

    DeserializationError error = deserializeJson(doc, responsePayload);
    if (!error) {
      bool isRegistered = doc["registered"] | false;
      const char* wargaNama = doc["warga"]["nama"] | "Belum Terdaftar";
      const char* serverMsg = doc["message"] | "Berhasil";

      if (isRegistered) {
        Serial.println("\n==================================================");
        Serial.println("  >>> AKSES DITERIMA: PALANG GERBANG TERBUKA! <<<");
        Serial.print("  Selamat Datang : "); Serial.println(wargaNama);
        Serial.print("  Status         : "); Serial.println(serverMsg);
        Serial.println("==================================================");
        ledSuccessGranted(); // LED menyala mantap 2 detik
      } else {
        Serial.println("\n[WARN] Kartu terbaca, tetapi BELUM TERDAFTAR pada database warga.");
        ledBlinkReject(); // Kedip cepat 3x
      }
    } else {
      Serial.println("[JSON] Respon valid HTTP 200, namun gagal parse JSON.");
      ledSuccessGranted();
    }
  } 
  else if (httpCode == 401) {
    Serial.println("\n[DENIED] 401 UNAUTHORIZED: API Token ditolak oleh server!");
    Serial.println("Periksa API Secret Token pada menu Pengaturan API & IoT.");
    ledBlinkReject();
  } 
  else if (httpCode == 503) {
    Serial.println("\n[DENIED] 503 SERVICE UNAVAILABLE: Penerimaan API IoT sedang NONAKTIF.");
    ledBlinkReject();
  } 
  else {
    Serial.printf("\n[ERR] Gagal terhubung ke endpoint server (HTTP Code: %d)\n", httpCode);
    ledBlinkReject();
  }

  http.end();
  Serial.println("--------------------------------------------------\n");
}

// ==============================================================================
// 8. SETUP UTAMA
// ==============================================================================
void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("\n========================================================");
  Serial.println("     NEXANEST SMART GATE & RFID CLIENT v2.0 (ESP32)");
  Serial.println("========================================================");

  // Inisialisasi Pin GPIO
  pinMode(LED_PIN, OUTPUT);
  pinMode(RESET_BTN_PIN, INPUT_PULLUP);
  ledOff();

  // Inisialisasi SPI & RC522
  SPI.begin();
  mfrc522.PCD_Init();
  delay(50);
  mfrc522.PCD_DumpVersionToSerial();
  Serial.println("[RC522] Modul RFID MFRC522 siap digunakan (13.56 MHz).");

  // Muat data dari NVS
  loadSavedSettings();

  // Coba hubungkan ke WiFi
  if (!connectToWiFi()) {
    Serial.println("[INFO] Mengaktifkan Mode Hotspot Fallback...");
    startHotspotMode();
  } else {
    Serial.println("[INFO] Sistem Standby. Dekatkan kartu ke sensor RFID...");
  }
}

// ==============================================================================
// 9. LOOP UTAMA
// ==============================================================================
void loop() {
  // A. Cek Tombol BOOT (GPIO 0) ditekan selama 3 detik untuk Factory Reset
  if (digitalRead(RESET_BTN_PIN) == LOW) {
    unsigned long pressStart = millis();
    Serial.println("[BTN] Tombol BOOT ditekan, tahan 3 detik untuk reset...");
    while (digitalRead(RESET_BTN_PIN) == LOW) {
      if (millis() - pressStart > 3000) {
        factoryReset();
        return;
      }
      delay(50);
    }
  }

  // B. Jika dalam Mode Hotspot: layani DNS dan Web Request, kedipkan LED pelan
  if (isHotspotMode) {
    dnsServer.processNextRequest();
    server.handleClient();

    // Kedip LED pelan (indikator mode hotspot)
    static unsigned long lastBlink = 0;
    if (millis() - lastBlink > 600) {
      lastBlink = millis();
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
    }
    return;
  }

  // C. Menjaga Koneksi WiFi jika tiba-tiba terputus
  if (WiFi.status() != WL_CONNECTED) {
    static unsigned long lastReconnect = 0;
    if (millis() - lastReconnect > 10000) {
      lastReconnect = millis();
      Serial.println("[WIFI] Terputus! Mencoba menyambung kembali...");
      WiFi.reconnect();
    }
  }

  // D. Deteksi Kartu Baru pada Pembaca RFID RC522
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }

  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  // E. Cek Cooldown Anti Dobel Scan (3 Detik)
  if (millis() - lastScanTime < SCAN_COOLDOWN_MS) {
    Serial.println("[RC522] Tap diabaikan: Cooldown 3 detik aktif.");
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
    return;
  }

  // F. Konversi UID Kartu ke String Hex Huruf Kapital (contoh: A1B2C3D4)
  String uidString = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) {
      uidString += "0";
    }
    uidString += String(mfrc522.uid.uidByte[i], HEX);
  }
  uidString.toUpperCase();

  lastScanTime = millis();

  // Hentikan komunikasi dengan kartu saat ini
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  // G. Kirim UID ke Server NexaNest
  sendScanToServer(uidString);
}
