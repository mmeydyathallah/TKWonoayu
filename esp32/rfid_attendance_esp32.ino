#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Adafruit_PN532.h>

// ================== WIFI ==================
const char* WIFI_SSID = "NAMA_WIFI";
const char* WIFI_PASS = "PASSWORD_WIFI";

// ================== SERVER ==================
const char* API_URL = "http://103.215.229.171/api/rfid/attendance";
const char* API_TOKEN = "rfid_9ac7de3098aa4bd7b52735dc10ad69bb";

// ================== LCD ==================
LiquidCrystal_I2C lcd(0x27, 16, 2); // ganti ke 0x3F jika modul LCD berbeda

// ================== BUZZER ==================
const int BUZZER_PIN = 25;

// ================== PN532 I2C ==================
Adafruit_PN532 nfc(PN532_I2C);

String lastUid = "";
unsigned long lastTapMs = 0;
const unsigned long TAP_COOLDOWN_MS = 3000;

String jsonGetString(const String& json, const String& key) {
  String token = "\"" + key + "\":\"";
  int start = json.indexOf(token);
  if (start < 0) {
    return "";
  }

  start += token.length();
  int end = json.indexOf("\"", start);
  if (end < 0) {
    return "";
  }

  return json.substring(start, end);
}

void beep(int times, int onMs, int offMs) {
  for (int i = 0; i < times; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(onMs);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < times - 1) {
      delay(offMs);
    }
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
    if (uid[i] < 0x10) {
      out += "0";
    }
    out += String(uid[i], HEX);
  }
  out.toUpperCase();
  return out;
}

bool sendAttendance(const String& uidHex, int& httpCode, String& response) {
  if (WiFi.status() != WL_CONNECTED) {
    return false;
  }

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
    delay(500);
    retry++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    showLCD("WiFi Connected", WiFi.localIP().toString());
    beep(1, 120, 0);
  } else {
    showLCD("WiFi Failed", "Check SSID/PASS");
    beep(2, 120, 120);
  }
}

void setup() {
  Serial.begin(115200);

  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  Wire.begin(); // SDA=21, SCL=22

  lcd.init();
  lcd.backlight();
  showLCD("Init PN532...", "");

  nfc.begin();
  uint32_t versionData = nfc.getFirmwareVersion();

  if (!versionData) {
    showLCD("PN532 Not Found", "Check wiring");
    while (true) {
      beep(1, 80, 300);
      delay(700);
    }
  }

  nfc.SAMConfig();
  connectWiFi();
  showLCD("Tap Kartu...", "");
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  uint8_t uid[7];
  uint8_t uidLength = 0;

  bool success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 200);
  if (!success) {
    return;
  }

  String uidHex = uidToHex(uid, uidLength);
  unsigned long nowMs = millis();

  if (uidHex == lastUid && (nowMs - lastTapMs) < TAP_COOLDOWN_MS) {
    return;
  }

  lastUid = uidHex;
  lastTapMs = nowMs;

  showLCD("Kartu: " + uidHex.substring(0, 8), "Kirim absensi...");
  beep(1, 60, 0);

  int httpCode = 0;
  String response = "";
  bool sent = sendAttendance(uidHex, httpCode, response);

  Serial.println("UID: " + uidHex);
  Serial.println("HTTP: " + String(httpCode));
  Serial.println("RESP: " + response);

  if (!sent) {
    showLCD("Gagal koneksi", "Server/WiFi down");
    beep(3, 80, 80);
  } else if (httpCode == 200) {
    String studentName = jsonGetString(response, "student_name");
    String eventType = jsonGetString(response, "event_type");
    if (studentName.length() == 0) {
      studentName = "Nama tidak ada";
    }
    String eventLabel = "HADIR";
    if (eventType == "masuk") {
      eventLabel = "MASUK";
    } else if (eventType == "pulang") {
      eventLabel = "PULANG";
    } else if (eventType == "sudah_tercatat") {
      eventLabel = "SUDAH ADA";
    }
    showLCD(eventLabel + ": " + studentName.substring(0, 8), "UID " + uidHex.substring(0, 8));
    beep(2, 90, 80);
  } else if (httpCode == 404) {
    showLCD("Kartu belum", "terdaftar");
    beep(2, 250, 120);
  } else if (httpCode == 401) {
    showLCD("Token salah", "cek API token");
    beep(4, 70, 70);
  } else {
    showLCD("HTTP " + String(httpCode), "Cek server log");
    beep(3, 100, 90);
  }

  delay(1500);
  showLCD("Tap Kartu...", "");
}
