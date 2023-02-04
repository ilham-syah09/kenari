// Inisialisasi pin Ultrasonik
#define pakanPinTrigger D1
#define pakanPinEcho D2

#define airPinTrigger D6
#define airPinEcho D7

// deklarasi tinggi wadah
int tinggiWadahPakan = 10;
int tinggiWadahAir = 10;

#define selenoid D5

#define relay_on LOW
#define relay_off HIGH

#include <Servo.h>
Servo servoPakan;

void setup() {
  Serial.begin(115200);   //Komunikasi baud rate

  //  inisialisasi pin ultrasonik
  pinMode(pakanPinTrigger, OUTPUT);
  pinMode(pakanPinEcho, INPUT);

  pinMode(airPinTrigger, OUTPUT);
  pinMode(airPinEcho, INPUT);

  pinMode(selenoid, OUTPUT);
  digitalWrite(selenoid, relay_off);

  servoPakan.attach(D0);
  servoPakan.write(0);

  delay(1000);
  Serial.println();
}

void loop() {
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

  tinggiPakan = tinggiWadahPakan - jarak;

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

  tinggiAir = tinggiWadahAir - jarak2;

  if (tinggiAir < 0)
  {
    tinggiAir = 0;
  }
  
  Serial.print("Tinggi Pakan : ");
  Serial.println(tinggiPakan);
  Serial.print("Tinggi Air : ");
  Serial.println(tinggiAir);

  if (tinggiPakan <= 5) {
    Serial.println("Sedang mengisi pakan");

    servoPakan.write(90);
    delay(300);
    servoPakan.write(0);
    delay(300);
    servoPakan.write(90);
    delay(300);
    servoPakan.write(0);
    delay(300);
    servoPakan.write(90);
    delay(300);
    servoPakan.write(0);
  }

  if (tinggiAir <= 5) {
    Serial.println("Sedang mengisi air");
    pinMode(selenoid, relay_on);
  } else {
    pinMode(selenoid, relay_off);
  }

  delay(500);
}
