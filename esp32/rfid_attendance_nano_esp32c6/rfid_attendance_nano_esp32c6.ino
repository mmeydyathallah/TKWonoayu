/*
 * TK Wonoayu — Sistem Absensi Dual Verifikasi (RFID + Fingerprint)
 * Hardware: Arduino Nano ESP32-C6
 *   PN532 RFID  — I2C (SDA=6, SCL=7)
 *   FPM10A      — UART0 (U0RXD, U0TXD)  [Serial1 on ESP32-C6]
 *   LCD I2C     — 16x2 (address 0x27)
 *   Buzzer      — GPIO 0
 *   RGB LED     — GPIO 8 (onboard WS2812)
 *
 * Fitur:
 *   1. Absensi RFID (kartu PN532)  → POST /api/attendance (rfid_code)
 *   2. Absensi Fingerprint (FPM10A) → POST /api/attendance (fingerprint_id)
 *   3. Enroll Fingerprint via web   → poll /api/fingerprint/enrollment/check
 *   4. Delete Fingerprint via web   → poll /api/fingerprint/deletion/check
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <PN532_I2C.h>
#include <PN532.h>
#include <Adafruit_NeoPixel.h>
#include <Adafruit_Fingerprint.h>

// ================== CONFIG ==================
const char* WIFI_SSID = "sela";
const char* WIFI_PASS = "selasela";

const char* API_BASE   = "http://103.215.229.171/api";
const char* API_TOKEN  = "rfid_9ac7de3098aa4bd7b52735dc10ad69bb";

// Pin assignments
const int I2C_SDA_PIN = 6;
const int I2C_SCL_PIN = 7;
const int BUZZER_PIN  = 0;
const int RGB_PIN     = 8;
const bool BUZZER_ACTIVE_HIGH = true;

// FPM10A UART — use Serial1 on ESP32-C6
// ESP32-C6: Serial1 can map to any pins. U0RXD=RX2(Serial1 RX), U0TXD=TX(Serial1 TX)
// On Nano ESP32-C6: U0RXD = GPIO17, U0TXD = GPIO16  (USB CDC)
// But user says U0RXD/U0TXD — these are the default UART0 pins.
// We'll use Serial1 with configurable pins to avoid conflict with USB-CDC Serial.
const int FP_RX_PIN = 17;  // U0RXD — connect to FPM10A TX
const int FP_TX_PIN = 16;  // U0TXD — connect to FPM10A RX

// Polling interval for enrollment/deletion
const unsigned long POLL_INTERVAL_MS = 5000;

// ================== OBJECTS ==================
Adafruit_NeoPixel rgb(1, RGB_PIN, NEO_GRB + NEO_KHZ800);
LiquidCrystal_I2C lcd(0x27, 16, 2);
PN532_I2C pn532i2c(Wire);
PN532 nfc(pn532i2c);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial1);

// ================== STATE ==================
enum DeviceState { STATE_IDLE, STATE_ENROLL, STATE_DELETE };
DeviceState currentState = STATE_IDLE;

String lastUid = "";
unsigned long lastTapMs = 0;
const unsigned long TAP_COOLDOWN_MS = 3000;

unsigned long lastPollMs = 0;

// Enroll state
int enrollEnrollmentId = -1;
String enrollStudentName = "";

// Delete state
int deleteDeletionId = -1;
int deleteFingerprintId = -1;

// ================== RGB ==================
void setRGB(uint8_t r, uint8_t g, uint8_t b, uint8_t brightness = 50) {
  rgb.setBrightness(brightness);
  rgb.setPixelColor(0, rgb.Color(r, g, b));
  rgb.show();
}
void rgbOff() { rgb.setPixelColor(0, 0); rgb.show(); }

// ================== BUZZER ==================
void buzzerOn()  { digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? HIGH : LOW); }
void buzzerOff() { digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? LOW : HIGH); }

void beep(int times, int onMs, int offMs) {
  for (int i = 0; i < times; i++) {
    buzzerOn(); delay(onMs); buzzerOff();
    if (i < times - 1) delay(offMs);
  }
}

// ================== LCD ==================
void showLCD(const String& l1, const String& l2 = "") {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(l1.substring(0, 16));
  lcd.setCursor(0, 1); lcd.print(l2.substring(0, 16));
}

// ================== HELPERS ==================
String jsonGetString(const String& json, const String& key) {
  String token = "\"" + key + "\":\"";
  int start = json.indexOf(token);
  if (start < 0) return "";
  start += token.length();
  int end = json.indexOf("\"", start);
  if (end < 0) return "";
  return json.substring(start, end);
}

int jsonGetInt(const String& json, const String& key) {
  String token = "\"" + key + "\":";
  int start = json.indexOf(token);
  if (start < 0) return -1;
  start += token.length();
  // skip whitespace
  while (start < (int)json.length() && (json[start] == ' ' || json[start] == '\n')) start++;
  int end = start;
  while (end < (int)json.length() && json[end] >= '0' && json[end] <= '9') end++;
  if (end == start) return -1;
  return json.substring(start, end).toInt();
}

String jsonGetRaw(const String& json, const String& key) {
  String token = "\"" + key + "\":";
  int start = json.indexOf(token);
  if (start < 0) return "";
  start += token.length();
  // skip whitespace
  while (start < (int)json.length() && (json[start] == ' ' || json[start] == '\n')) start++;
  if (json[start] == '"') {
    // string value
    start++;
    int end = json.indexOf("\"", start);
    if (end < 0) return "";
    return json.substring(start, end);
  } else {
    // numeric or null
    int end = start;
    while (end < (int)json.length() && json[end] != ',' && json[end] != '}' && json[end] != ']') end++;
    return json.substring(start, end);
  }
}

String uidToHex(uint8_t* uid, uint8_t uidLength) {
  String out = "";
  for (uint8_t i = 0; i < uidLength; i++) {
    if (uid[i] < 0x10) out += "0";
    out += String(uid[i], HEX);
  }
  out.toUpperCase();
  return out;
}

String uidToUsbDecimal(uint8_t* uid, uint8_t uidLength) {
  if (uidLength != 4) return uidToHex(uid, uidLength);
  uint32_t value = ((uint32_t)uid[3] << 24) | ((uint32_t)uid[2] << 16) | ((uint32_t)uid[1] << 8) | (uint32_t)uid[0];
  char out[11];
  snprintf(out, sizeof(out), "%010lu", (unsigned long)value);
  return String(out);
}

// Base64 encoding for fingerprint template data
const char* b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
String base64Encode(const uint8_t* data, size_t len) {
  String out = "";
  for (size_t i = 0; i < len; i += 3) {
    uint32_t n = ((uint32_t)data[i]) << 16;
    int rem = len - i;
    if (rem > 1) n |= ((uint32_t)data[i+1]) << 8;
    if (rem > 2) n |= (uint32_t)data[i+2];
    out += b64chars[(n >> 18) & 63];
    out += b64chars[(n >> 12) & 63];
    out += (rem > 1) ? b64chars[(n >> 6) & 63] : '=';
    out += (rem > 2) ? b64chars[n & 63] : '=';
  }
  return out;
}

// ================== WIFI ==================
void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  showLCD("Connecting WiFi", "Please wait...");
  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry < 30) {
    setRGB(255, 180, 0); delay(250); rgbOff(); delay(250);
    retry++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    setRGB(0, 255, 0);
    showLCD("WiFi Connected", WiFi.localIP().toString());
    beep(1, 120, 0);
    delay(1000);
  } else {
    setRGB(255, 0, 0);
    showLCD("WiFi Failed", "Check SSID/PASS");
    beep(2, 120, 120);
    delay(1500);
  }
}

// ================== HTTP ==================
String postForm(const String& path, const String& body) {
  if (WiFi.status() != WL_CONNECTED) return "";
  HTTPClient http;
  http.begin(String(API_BASE) + path);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(10000);
  int code = http.POST(body);
  String response = "";
  if (code > 0) response = http.getString();
  http.end();
  return response;
}

String getJson(const String& path) {
  if (WiFi.status() != WL_CONNECTED) return "";
  HTTPClient http;
  http.begin(String(API_BASE) + path);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(10000);
  int code = http.GET();
  String response = "";
  if (code > 0) response = http.getString();
  http.end();
  return response;
}

// ================== ATTENDANCE ==================
bool sendAttendanceRfid(const String& rfidCode, int& httpCode, String& response) {
  if (WiFi.status() != WL_CONNECTED) return false;
  HTTPClient http;
  http.begin(String(API_BASE) + "/attendance");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(10000);
  String body = "token=" + String(API_TOKEN) + "&rfid_code=" + rfidCode;
  httpCode = http.POST(body);
  response = http.getString();
  http.end();
  return httpCode > 0;
}

bool sendAttendanceFingerprint(int fpId, int& httpCode, String& response) {
  if (WiFi.status() != WL_CONNECTED) return false;
  HTTPClient http;
  http.begin(String(API_BASE) + "/attendance");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(10000);
  String body = "token=" + String(API_TOKEN) + "&fingerprint_id=" + String(fpId);
  httpCode = http.POST(body);
  response = http.getString();
  http.end();
  return httpCode > 0;
}

void handleAttendanceResponse(int httpCode, const String& response) {
  Serial.println("HTTP: " + String(httpCode));
  Serial.println("RESP: " + response);

  if (httpCode <= 0) {
    setRGB(255, 0, 0, 80);
    showLCD("Gagal koneksi", "WiFi/server");
    beep(3, 80, 80);
    return;
  }

  if (httpCode == 200) {
    String studentName = jsonGetString(response, "student_name");
    String eventType   = jsonGetString(response, "event_type");
    if (studentName.length() == 0) studentName = "Siswa";

    if (eventType == "masuk") {
      setRGB(0, 255, 0, 60);
      showLCD("MASUK", studentName);
      beep(2, 90, 80);
    } else if (eventType == "pulang") {
      setRGB(0, 255, 200, 60);
      showLCD("PULANG", studentName);
      beep(2, 90, 80);
    } else if (eventType == "sudah_masuk" || eventType == "sudah_pulang") {
      setRGB(255, 165, 0, 60);
      showLCD("SUDAH ADA", studentName);
      beep(2, 250, 120);
    } else if (eventType == "di_luar_jadwal") {
      setRGB(255, 165, 0, 60);
      showLCD("DI LUAR JADWAL", studentName);
      beep(3, 100, 90);
    } else {
      setRGB(0, 255, 0, 60);
      showLCD("HADIR", studentName);
      beep(2, 90, 80);
    }
  } else if (httpCode == 404) {
    setRGB(255, 0, 0, 80);
    showLCD("Tidak terdaftar", "Cek biodata");
    beep(2, 250, 120);
  } else if (httpCode == 401) {
    setRGB(255, 0, 100, 80);
    showLCD("Token salah", "cek API token");
    beep(4, 70, 70);
  } else {
    setRGB(255, 50, 0, 80);
    showLCD("HTTP " + String(httpCode), "Cek server");
    beep(3, 100, 90);
  }
}

// ================== RFID SCAN ==================
void checkRfid() {
  uint8_t uid[7];
  uint8_t uidLength = 0;
  bool success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 200);
  if (!success) return;

  String rfidCode = uidToUsbDecimal(uid, uidLength);
  unsigned long nowMs = millis();
  if (rfidCode == lastUid && (nowMs - lastTapMs) < TAP_COOLDOWN_MS) return;

  lastUid = rfidCode;
  lastTapMs = nowMs;

  setRGB(0, 0, 255, 60);
  showLCD("RFID: " + rfidCode.substring(0, 10), "Kirim data...");
  beep(1, 60, 0);

  int httpCode = 0;
  String response = "";
  sendAttendanceRfid(rfidCode, httpCode, response);
  handleAttendanceResponse(httpCode, response);

  delay(1800);
  setRGB(0, 100, 255, 40);
  if (currentState == STATE_IDLE) showLCD("Tap Kartu/Jari", "");
}

// ================== FINGERPRINT SCAN (Attendance) ==================
void checkFingerprint() {
  if (finger.getImage() != FINGERPRINT_OK) return;

  if (finger.image2Tz() != FINGERPRINT_OK) {
    beep(1, 50, 0);
    return;
  }

  if (finger.fingerSearch() != FINGERPRINT_OK) {
    // No match found
    setRGB(255, 0, 0, 80);
    showLCD("Fingerprint", "tidak dikenali");
    beep(2, 120, 120);
    delay(1500);
    setRGB(0, 100, 255, 40);
    if (currentState == STATE_IDLE) showLCD("Tap Kartu/Jari", "");
    return;
  }

  int fpId = finger.fingerID;
  setRGB(0, 0, 255, 60);
  showLCD("FP ID:" + String(fpId), "Kirim data...");
  beep(1, 60, 0);

  int httpCode = 0;
  String response = "";
  sendAttendanceFingerprint(fpId, httpCode, response);
  handleAttendanceResponse(httpCode, response);

  delay(1800);
  setRGB(0, 100, 255, 40);
  if (currentState == STATE_IDLE) showLCD("Tap Kartu/Jari", "");
}

// ================== ENROLLMENT POLLING ==================
void pollEnrollment() {
  String resp = getJson("/fingerprint/enrollment/check?token=" + String(API_TOKEN));
  if (resp.length() == 0) return;

  String rawNull = jsonGetRaw(resp, "data");
  // Check if data is null
  if (rawNull == "null" || rawNull == "" || rawNull == "}") return;

  enrollEnrollmentId = jsonGetInt(resp, "enrollment_id");
  enrollStudentName = jsonGetString(resp, "student_name");

  if (enrollEnrollmentId <= 0) return;

  Serial.println("Enrollment pending: ID=" + String(enrollEnrollmentId) + " student=" + enrollStudentName);
  currentState = STATE_ENROLL;
  setRGB(255, 180, 0, 60);
  showLCD("Enroll:", enrollStudentName.substring(0, 16));
  beep(2, 80, 80);
  delay(1000);

  doEnrollment();
}

void doEnrollment() {
  // Find a free slot on FPM10A
  int slotId = -1;
  for (int id = 1; id <= 162; id++) {
    if (finger.loadModel(id) != FINGERPRINT_OK) {
      slotId = id;
      break;
    }
  }

  if (slotId < 0) {
    failEnrollment("Tidak ada slot kosong di FPM10A (maks 162).");
    return;
  }

  Serial.println("Using slot: " + String(slotId));

  // Step 1: first scan
  showLCD("Tempel jari 1x", enrollStudentName.substring(0, 16));
  int r = -1;
  unsigned long timeout = millis() + 30000; // 30s timeout
  while (millis() < timeout) {
    r = finger.getImage();
    if (r == FINGERPRINT_OK) break;
    if (r == FINGERPRINT_NOFINGER) continue;
    failEnrollment("Error gambar 1: " + String(r));
    return;
  }
  if (r != FINGERPRINT_OK) {
    failEnrollment("Timeout scan 1");
    return;
  }
  if (finger.image2Tz(1) != FINGERPRINT_OK) {
    failEnrollment("Error convert 1");
    return;
  }
  beep(1, 100, 0);
  showLCD("Angkat jari...", "");
  delay(2000);

  // Step 2: second scan
  showLCD("Tempel jari 2x", "Sama ya...");
  timeout = millis() + 30000;
  while (millis() < timeout) {
    r = finger.getImage();
    if (r == FINGERPRINT_OK) break;
    if (r == FINGERPRINT_NOFINGER) continue;
    failEnrollment("Error gambar 2: " + String(r));
    return;
  }
  if (r != FINGERPRINT_OK) {
    failEnrollment("Timeout scan 2");
    return;
  }
  if (finger.image2Tz(2) != FINGERPRINT_OK) {
    failEnrollment("Error convert 2");
    return;
  }
  beep(1, 100, 0);

  // Create model
  if (finger.createModel() != FINGERPRINT_OK) {
    failEnrollment("Create model gagal (jari tidak cocok)");
    return;
  }

  // Store model
  if (finger.storeModel(slotId) != FINGERPRINT_OK) {
    failEnrollment("Store model gagal");
    return;
  }

  // Download template data
  if (finger.loadModel(slotId) != FINGERPRINT_OK) {
    failEnrollment("Load model gagal setelah store");
    return;
  }

  uint8_t templateData[512];
  int templateLen = finger.getModel(templateData, sizeof(templateData));
  if (templateLen <= 0) {
    failEnrollment("Download template gagal");
    return;
  }

  Serial.println("Template len: " + String(templateLen));

  // Base64 encode and send to server
  String b64 = base64Encode(templateData, templateLen);
  String body = "token=" + String(API_TOKEN) +
                "&enrollment_id=" + String(enrollEnrollmentId) +
                "&fingerprint_id=" + String(slotId) +
                "&fingerprint_data=" + b64;

  String resp = postForm("/fingerprint/enrollment/complete", body);
  Serial.println("Complete resp: " + resp);

  String success = jsonGetRaw(resp, "success");
  if (success == "true") {
    setRGB(0, 255, 0, 60);
    showLCD("Enroll sukses!", "ID: " + String(slotId));
    beep(2, 100, 80);
    delay(2000);
  } else {
    setRGB(255, 0, 0, 80);
    showLCD("Upload gagal", jsonGetString(resp, "message"));
    beep(3, 100, 90);
    delay(2000);
  }

  enrollEnrollmentId = -1;
  enrollStudentName = "";
  currentState = STATE_IDLE;
  setRGB(0, 100, 255, 40);
  showLCD("Tap Kartu/Jari", "");
}

void failEnrollment(const String& errorMsg) {
  Serial.println("Enroll failed: " + errorMsg);
  String body = "token=" + String(API_TOKEN) +
                "&enrollment_id=" + String(enrollEnrollmentId) +
                "&error_message=" + errorMsg;
  postForm("/fingerprint/enrollment/fail", body);

  setRGB(255, 0, 0, 80);
  showLCD("Enroll gagal", errorMsg.substring(0, 16));
  beep(3, 100, 90);
  delay(2000);

  enrollEnrollmentId = -1;
  enrollStudentName = "";
  currentState = STATE_IDLE;
  setRGB(0, 100, 255, 40);
  showLCD("Tap Kartu/Jari", "");
}

// ================== DELETION POLLING ==================
void pollDeletion() {
  String resp = getJson("/fingerprint/deletion/check?token=" + String(API_TOKEN));
  if (resp.length() == 0) return;

  String rawNull = jsonGetRaw(resp, "data");
  if (rawNull == "null" || rawNull == "" || rawNull == "}") return;

  deleteDeletionId = jsonGetInt(resp, "deletion_id");
  deleteFingerprintId = jsonGetInt(resp, "fingerprint_id");

  if (deleteDeletionId <= 0 || deleteFingerprintId <= 0) return;

  Serial.println("Delete pending: id=" + String(deleteDeletionId) + " fp_id=" + String(deleteFingerprintId));
  currentState = STATE_DELETE;
  setRGB(255, 180, 0, 60);
  showLCD("Hapus FP:", "ID:" + String(deleteFingerprintId));
  beep(1, 120, 0);
  delay(1000);

  doDeletion();
}

void doDeletion() {
  int r = finger.deleteModel(deleteFingerprintId);
  Serial.println("Delete model result: " + String(r));

  String body = "token=" + String(API_TOKEN) + "&deletion_id=" + String(deleteDeletionId);
  String resp = postForm("/fingerprint/deletion/done", body);
  Serial.println("Delete done resp: " + resp);

  if (r == FINGERPRINT_OK) {
    setRGB(0, 255, 0, 60);
    showLCD("Fingerprint", "dihapus");
    beep(2, 100, 80);
  } else {
    setRGB(255, 165, 0, 60);
    showLCD("Hapus gagal", "code:" + String(r));
    beep(2, 120, 120);
  }
  delay(2000);

  deleteDeletionId = -1;
  deleteFingerprintId = -1;
  currentState = STATE_IDLE;
  setRGB(0, 100, 255, 40);
  showLCD("Tap Kartu/Jari", "");
}

// ================== SETUP ==================
void setup() {
  Serial.begin(115200);
  delay(300);

  digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? LOW : HIGH);
  pinMode(BUZZER_PIN, OUTPUT);
  buzzerOff();

  rgb.begin();
  rgb.show();
  setRGB(255, 255, 255, 30);

  Wire.begin(I2C_SDA_PIN, I2C_SCL_PIN);
  Wire.setClock(100000);

  lcd.init();
  lcd.backlight();
  showLCD("Nano ESP32-C6", "Init devices...");

  // Init PN532
  nfc.begin();
  uint32_t versionData = nfc.getFirmwareVersion();
  if (!versionData) {
    showLCD("PN532 Not Found", "Check wiring");
    while (true) { setRGB(255, 0, 0); beep(1, 80, 300); rgbOff(); delay(700); }
  }
  nfc.SAMConfig();

  // Init FPM10A on Serial1
  Serial1.begin(57600, SERIAL_8N1, FP_RX_PIN, FP_TX_PIN);
  delay(100);
  if (finger.verifyPassword()) {
    Serial.println("FPM10A found!");
    showLCD("FPM10A OK", "");
  } else {
    Serial.println("FPM10A not found");
    showLCD("FPM10A Missing", "Check wiring");
    beep(2, 120, 120);
    delay(2000);
  }

  connectWiFi();
  setRGB(0, 100, 255, 40);
  showLCD("Tap Kartu/Jari", "");
}

// ================== LOOP ==================
void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
    if (currentState == STATE_IDLE) {
      setRGB(0, 100, 255, 40);
      showLCD("Tap Kartu/Jari", "");
    }
  }

  switch (currentState) {
    case STATE_IDLE:
      // Check RFID
      checkRfid();
      // Check Fingerprint
      checkFingerprint();
      // Poll server for enrollment/deletion
      if (millis() - lastPollMs > POLL_INTERVAL_MS) {
        lastPollMs = millis();
        pollEnrollment();
        if (currentState == STATE_IDLE) pollDeletion();
      }
      break;

    case STATE_ENROLL:
      // Enrollment is handled synchronously in doEnrollment()
      // State will be set back to IDLE when done
      break;

    case STATE_DELETE:
      // Deletion is handled synchronously in doDeletion()
      break;
  }
}
