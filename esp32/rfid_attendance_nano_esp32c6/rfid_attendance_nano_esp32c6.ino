#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <PN532_I2C.h>
#include <PN532.h>
#include <Adafruit_NeoPixel.h>

// ================== WIFI ==================
const char* WIFI_SSID = "meydyathallah";
const char* WIFI_PASS = "23052004";

// ================== SERVER ==================
const char* API_URL = "http://103.215.229.171/api/rfid/attendance";
const char* API_TOKEN = "rfid_9ac7de3098aa4bd7b52735dc10ad69bb";

// ================== PIN ==================
const int I2C_SDA_PIN = 6;
const int I2C_SCL_PIN = 7;
const int BUZZER_PIN  = 0;   // Jika LCD kedip/reset, pindahkan buzzer ke GPIO lain yang aman
const int RGB_PIN     = 8;   // Onboard WS2812 RGB LED di Nano ESP32-C6
const bool BUZZER_ACTIVE_HIGH = true; // Ubah ke false jika buzzer aktif saat pin LOW

// ================== RGB LED ==================
Adafruit_NeoPixel rgb(1, RGB_PIN, NEO_GRB + NEO_KHZ800);

void setRGB(uint8_t r, uint8_t g, uint8_t b, uint8_t brightness = 50) {
  rgb.setBrightness(brightness);
  rgb.setPixelColor(0, rgb.Color(r, g, b));
  rgb.show();
}

void rgbOff() {
  rgb.setPixelColor(0, 0);
  rgb.show();
}

// ================== BUZZER ==================
void buzzerOn() {
  digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? HIGH : LOW);
}

void buzzerOff() {
  digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? LOW : HIGH);
}

// ================== LCD ==================
LiquidCrystal_I2C lcd(0x27, 16, 2);

// ================== PN532 ==================
PN532_I2C pn532i2c(Wire);
PN532 nfc(pn532i2c);

String lastUid = "";
unsigned long lastTapMs = 0;
const unsigned long TAP_COOLDOWN_MS = 3000;

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

void beep(int times, int onMs, int offMs) {
  for (int i = 0; i < times; i++) {
    buzzerOn();
    delay(onMs);
    buzzerOff();
    if (i < times - 1) delay(offMs);
  }
}

void showLCD(const String& line1, const String& line2 = "") {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(line1.substring(0, 16));
  lcd.setCursor(0, 1);
  lcd.print(line2.substring(0, 16));
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

  uint32_t value = ((uint32_t)uid[3] << 24)
                 | ((uint32_t)uid[2] << 16)
                 | ((uint32_t)uid[1] << 8)
                 | (uint32_t)uid[0];

  char out[11];
  snprintf(out, sizeof(out), "%010lu", (unsigned long)value);
  return String(out);
}

bool sendAttendance(const String& uidHex, int& httpCode, String& response) {
  if (WiFi.status() != WL_CONNECTED) return false;
  HTTPClient http;
  http.begin(API_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String body = "token=" + String(API_TOKEN) + "&rfid_code=" + uidHex;
  httpCode = http.POST(body);
  response = http.getString();
  http.end();
  return httpCode > 0;
}

void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  showLCD("Connecting WiFi", "Please wait...");

  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry < 30) {
    // Kuning berkedip saat connecting
    setRGB(255, 180, 0);
    delay(250);
    rgbOff();
    delay(250);
    retry++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    setRGB(0, 255, 0); // Hijau = connected
    showLCD("WiFi Connected", WiFi.localIP().toString());
    beep(1, 120, 0);
    delay(1000);
  } else {
    setRGB(255, 0, 0); // Merah = gagal
    showLCD("WiFi Failed", "Check SSID/PASS");
    beep(2, 120, 120);
    delay(1500);
  }
}

void setup() {
  Serial.begin(115200);
  delay(300);

  digitalWrite(BUZZER_PIN, BUZZER_ACTIVE_HIGH ? LOW : HIGH);
  pinMode(BUZZER_PIN, OUTPUT);
  buzzerOff();

  // Init RGB
  rgb.begin();
  rgb.show();
  setRGB(255, 255, 255, 30); // Putih saat boot

  Wire.begin(I2C_SDA_PIN, I2C_SCL_PIN);
  Wire.setClock(100000);

  lcd.init();
  lcd.backlight();
  showLCD("Nano ESP32-C6", "Init PN532...");

  nfc.begin();
  uint32_t versionData = nfc.getFirmwareVersion();

  if (!versionData) {
    showLCD("PN532 Not Found", "Check wiring");
    while (true) {
      setRGB(255, 0, 0);
      beep(1, 80, 300);
      rgbOff();
      delay(700);
    }
  }

  nfc.SAMConfig();
  connectWiFi();

  setRGB(0, 100, 255, 40); // Biru muda = standby
  showLCD("Tap Kartu...", "");
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
    setRGB(0, 100, 255, 40);
    showLCD("Tap Kartu...", "");
  }

  uint8_t uid[7];
  uint8_t uidLength = 0;

  bool success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 200);
  if (!success) return;

  String uidHex = uidToHex(uid, uidLength);
  String rfidCode = uidToUsbDecimal(uid, uidLength);
  unsigned long nowMs = millis();

  if (rfidCode == lastUid && (nowMs - lastTapMs) < TAP_COOLDOWN_MS) return;

  lastUid = rfidCode;
  lastTapMs = nowMs;

  setRGB(0, 0, 255, 60); // Biru = kartu terdeteksi
  showLCD("RFID: " + rfidCode.substring(0, 10), "Kirim data...");
  beep(1, 60, 0);

  int httpCode = 0;
  String response = "";
  bool sent = sendAttendance(rfidCode, httpCode, response);

  Serial.println("UID HEX: " + uidHex);
  Serial.println("RFID USB: " + rfidCode);
  Serial.println("HTTP: " + String(httpCode));
  Serial.println("RESP: " + response);

  if (!sent) {
    setRGB(255, 0, 0, 80); // Merah = gagal koneksi
    showLCD("Gagal koneksi", "WiFi/server");
    beep(3, 80, 80);
  } else if (httpCode == 200) {
    String studentName = jsonGetString(response, "student_name");
    String eventType   = jsonGetString(response, "event_type");

    if (studentName.length() == 0) studentName = "Siswa";

    String eventLabel = "HADIR";
    if (eventType == "masuk") {
      eventLabel = "MASUK";
      setRGB(0, 255, 0, 60);    // Hijau = masuk
    } else if (eventType == "pulang") {
      eventLabel = "PULANG";
      setRGB(0, 255, 200, 60);  // Cyan = pulang
    } else if (eventType == "sudah_tercatat") {
      eventLabel = "SUDAH ADA";
      setRGB(255, 165, 0, 60);  // Oranye = sudah tercatat
    } else {
      setRGB(0, 255, 0, 60);    // Hijau default = hadir
    }

    showLCD(eventLabel, studentName);
    beep(2, 90, 80);
  } else if (httpCode == 404) {
    setRGB(255, 0, 0, 80); // Merah = kartu tidak dikenal
    showLCD("Kartu belum", "terdaftar");
    beep(2, 250, 120);
  } else if (httpCode == 401) {
    setRGB(255, 0, 100, 80); // Merah-pink = auth error
    showLCD("Token salah", "cek API token");
    beep(4, 70, 70);
  } else {
    setRGB(255, 50, 0, 80); // Oranye-merah = error lain
    showLCD("HTTP " + String(httpCode), "Cek server");
    beep(3, 100, 90);
  }

  delay(1800);
  setRGB(0, 100, 255, 40); // Kembali ke biru muda = standby
  showLCD("Tap Kartu...", "");
}
