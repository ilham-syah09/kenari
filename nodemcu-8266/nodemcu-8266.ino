#include <Arduino.h>

// Wifi
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>

#define USE_SERIAL Serial
ESP8266WiFiMulti WiFiMulti;
WiFiClient client;
HTTPClient http;

// Sensor DHT
#include <DHT.h>

#define DHTPIN D0
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

// deklarasi pin Ultrasonik
#define pakanPinTrigger D1
#define pakanPinEcho D2

#define airPinTrigger D6
#define airPinEcho D7

// deklarasi tinggi tandon
int tinggiTandonPakan = 20;
int tinggiTandonAir = 20;

// Pin Kipas
#define kipas D5

#define relay_on LOW
#define relay_off HIGH

// URL WEB IOT
String urlSimpan = "http://192.168.40.7/kenari/data/save?suhu=";

String respon;

void setup() {
  Serial.begin(115200);   //Komunikasi baud rate

  USE_SERIAL.begin(115200);
  USE_SERIAL.setDebugOutput(false);

  for(uint8_t t = 4; t > 0; t--) {
      USE_SERIAL.printf("[SETUP] Tunggu %d...\n", t);
      USE_SERIAL.flush();
      delay(1000);
  }

  WiFi.mode(WIFI_STA);
  WiFiMulti.addAP("Redmi", "123456789"); // Sesuaikan SSID dan password ini

  Serial.println();
  
  for (int u = 1; u <= 5; u++)
  {
    if ((WiFiMulti.run() == WL_CONNECTED))
    {
      USE_SERIAL.println("Internet Connected");
      USE_SERIAL.flush();
      delay(1000);
    }
    else
    {
      Serial.println("No Internet Connected");
      delay(1000);
    }
  }

  dht.begin(); // dht mulai bekerja/ on
  
  //  deklarasi pin ultrasonik
  pinMode(pakanPinTrigger, OUTPUT);
  pinMode(pakanPinEcho, INPUT);

  pinMode(airPinTrigger, OUTPUT);
  pinMode(airPinEcho, INPUT);

  // deklarasi pin kipas
  pinMode(kipas, OUTPUT);
  digitalWrite(kipas, relay_off);
  
  delay(1000);
  Serial.println();
}

void loop() {
  // coding suhu
  int suhu = dht.readTemperature();
  int kelembapan = dht.readHumidity();

  //  Inisialisasi variabel untuk pembacaan tinggi pakan
  long duration, jarak, tinggiPakan;
  digitalWrite(pakanPinTrigger, LOW);
  delayMicroseconds(2);
  digitalWrite(pakanPinTrigger, HIGH);
  delayMicroseconds(10);
  digitalWrite(pakanPinTrigger, LOW);
  duration = pulseIn(pakanPinEcho, HIGH);

  //  Rumus pembacaan jarak tinggi
  jarak = (duration / 2) / 29.1;

  tinggiPakan = tinggiTandonPakan - jarak;

  if (tinggiPakan < 0)
  {
    tinggiPakan = 0;
  }

  //  Inisialisasi variabel untuk pembacaan tinggi air
  long duration2, jarak2, tinggiAir;
  digitalWrite(airPinTrigger, LOW);
  delayMicroseconds(2);
  digitalWrite(airPinTrigger, HIGH);
  delayMicroseconds(10);
  digitalWrite(airPinTrigger, LOW);
  duration2 = pulseIn(airPinEcho, HIGH);

  //  Rumus pembacaan jarak tinggi
  jarak2 = (duration2 / 2) / 29.1;

  tinggiAir = tinggiTandonAir - jarak2;

  if (tinggiAir < 0)
  {
    tinggiAir = 0;
  }
  
  if (isnan(kelembapan) || isnan(suhu)) {
    Serial.println("Failed to read from DHT sensor!");
  } else {
    Serial.println();
    if (suhu < 100 || kelembapan < 100) {
      Serial.print("Suhu : ");
      Serial.println(suhu);
      Serial.print("Kelembapan Udara : ");
      Serial.println(kelembapan);

      Serial.println();

      Serial.print("Tinggi Pakan : ");
      Serial.println(tinggiPakan);
      Serial.print("Tinggi Air : ");
      Serial.println(tinggiAir);

      Serial.println();

      if (suhu > 30) {
        Serial.println("Kipas ON");
        digitalWrite(kipas, relay_on);
      } else {
        Serial.println("Kipas OFF");
        digitalWrite(kipas, relay_off);
      }

      Serial.println();
      
      // kirim data sensor ke website
      if ((WiFiMulti.run() == WL_CONNECTED))
      {
        USE_SERIAL.print("[HTTP] Memulai...\n");
        
        http.begin(client, urlSimpan + (String) suhu + "&kelembapan=" + (String) kelembapan + "&jml_pakan=" + (String) tinggiPakan + "&jml_air=" + (String) tinggiAir );
        
        USE_SERIAL.print("[HTTP] Menyimpan data sensor ke database ...\n");
        int httpCode = http.GET();
    
        if(httpCode > 0)
        {
          USE_SERIAL.printf("[HTTP] kode response GET : %d\n", httpCode);
    
          if (httpCode == HTTP_CODE_OK)
          {
            respon = http.getString();
            USE_SERIAL.println("Respon : " + respon);
            delay(200);
          }
        }
        else
        {
          USE_SERIAL.printf("[HTTP] GET data gagal, error: %s\n", http.errorToString(httpCode).c_str());
        }
        http.end();
      }
    } else {
      Serial.println("Failed to read from DHT sensor!");
    }
    
    Serial.println();
  }

  delay(1000);
}