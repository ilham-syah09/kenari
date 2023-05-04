#define airPinTrigger 4
#define airPinEcho 5

// deklarasi tinggi wadah
int tinggiWadahAir = 10;

#define selenoid 6

#define relay_on HIGH
#define relay_off LOW

void setup() {
  Serial.begin(115200);   //Komunikasi baud rate


  pinMode(airPinTrigger, OUTPUT);
  pinMode(airPinEcho, INPUT);

  pinMode(selenoid, OUTPUT);
  digitalWrite(selenoid, relay_off);

  delay(1000);
  Serial.println();
}

void loop() {
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
  

  if (tinggiAir <= 5) {
    Serial.println("Sedang mengisi air");
    pinMode(selenoid, relay_on);
  } else {
    pinMode(selenoid, relay_off);
  }

  delay(500);
}
