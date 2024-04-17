// Libraries
#include <Arduino.h>
#include <WiFi.h>
#include <WiFiMulti.h>
#include <HTTPClient.h>
#include <LiquidCrystal_I2C.h>
#include <ezButton.h>
#include <ESP32Servo.h>
#include <ArduinoJson.h>

// Setup pins, geluidssnelheid en rotory encoder draai richting
#define USE_SERIAL Serial
#define CLK_PIN 25
#define DT_PIN 26
#define SW_PIN 27
#define DIRECTION_CW 0
#define DIRECTION_CCW 1
#define SOUND_SPEED 0.034

WiFiMulti wifiMulti;

// Pins aan variabele voegen
const int trigPin = 5;
const int echoPin = 18;
const int servoPin1 = 32;
const int servoPin2 = 33;

// Variabele aanmaken
int led_state = 0;
int lcdColumns = 16;
int lcdRows = 2;
int direction = DIRECTION_CW;
int pos = 100;
int voltooideSessies = 0;
int aantalVoerSessies = 0;
int vooraadIndex = 0;

long start_time = 0;
long REQUEST_INTERVAL_TIME = 750;
unsigned long last_time_interupt;
unsigned long last_time_buttonPress;
unsigned long last_time_timerChecked;

double counter = 0.0;
double prev_counter;

bool isFeeding = false;

// Setup voor lcd scherm
LiquidCrystal_I2C lcd(0x27, lcdColumns, lcdRows);

ezButton button(SW_PIN);

Servo myservoZwart;
Servo myservoBlauw;


volatile bool interruptOccurred = false;

void IRAM_ATTR ISR_encoder() {
  interruptOccurred = true;
}

// Timer class uit les
class tmrMicros  { 
  private: 
    unsigned long nextChangeTime; 
    unsigned long timeOn_; 
    bool overFlow; 
  public: 
    void tmrSet(unsigned long timeOn) 
    { 
      timeOn_ = timeOn; 
      unsigned long currentTime = micros(); 
      nextChangeTime = currentTime + timeOn; 
      if (nextChangeTime > currentTime) 
        overFlow = false; 
      else overFlow = true; 
    } 
    bool tmrActive()  
    { 
      unsigned long currentTime = micros(); 
      bool val = false;          
      if (! overFlow) 
       { 
          if (currentTime < nextChangeTime) 
         { 
          val  = true; 
          } 
        } 
      else if ((currentTime + timeOn_) < (nextChangeTime + timeOn_)) 
        { 
         val  = true; 
        } 
       return val; 
     } 
};

tmrMicros tmrOpslag;
tmrMicros tmrCounter;


void setup() {
  Serial.begin(115200);

// Maak verbinding met wifi
  wifiMulti.addAP("GeenCrompouceMaarChocoladePoes", "mmmmmmlekker");
    while (wifiMulti.run() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("WiFi connected");
  start_time = millis();


  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(CLK_PIN, INPUT);
  pinMode(DT_PIN, INPUT);

// Initialize het lcd scherm en zet de backlight aan
  lcd.init();
  lcd.backlight();

  button.setDebounceTime(50);
  attachInterrupt(digitalPinToInterrupt(CLK_PIN), ISR_encoder, RISING);

  ESP32PWM::allocateTimer(0);
  ESP32PWM::allocateTimer(1);
  ESP32PWM::allocateTimer(2);
  ESP32PWM::allocateTimer(3);

// Setup voor de servos
  myservoZwart.setPeriodHertz(50);
  myservoZwart.attach(servoPin1, 500, 2400);
  myservoBlauw.setPeriodHertz(50);
  myservoBlauw.attach(servoPin2, 500, 2400);

  counter = getAmountOfFood();
}



void opslagMeten() {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  
  long duration = pulseIn(echoPin, HIGH);
  
// Om het verschil van het versturen van het geluid en het ontvangen van het geluid om te zetten naar afstand, gebruik geluidssnelheid
  float distanceCm = duration * SOUND_SPEED/2;
// Om dit om te zetten naar voorraadprocent, 100% - gemeten centimeter / diepte opslag bus in centimeter * 100
  float voorraadProcent = 100 - distanceCm / 20.8 * 100;

// Opslag percentage printen op het lcd scherm
  lcd.setCursor(0,0);
  lcd.print("Opslag: ");
  if (voorraadProcent >= 90){
    lcd.print("100%");
    vooraadIndex = 5;
  }
  else if (voorraadProcent >= 80 && voorraadProcent <= 89){
    lcd.print("90%");
    vooraadIndex = 5;
  }
  else if (voorraadProcent >= 70 && voorraadProcent <= 79){
    lcd.print("80%");
    vooraadIndex = 4;
  }
  else if (voorraadProcent >= 60 && voorraadProcent <= 69){
    lcd.print("70%");
    vooraadIndex = 4;
  }
  else if (voorraadProcent >= 50 && voorraadProcent <= 59){
    lcd.print("60%");
    vooraadIndex = 3;
  }
  else if (voorraadProcent >= 40 && voorraadProcent <= 49){
    lcd.print("50%");
    vooraadIndex = 3;
  }
  else if (voorraadProcent >= 30 && voorraadProcent <= 39){
    lcd.print("40%");
    vooraadIndex = 2;
  }
  else if (voorraadProcent >= 20 && voorraadProcent <= 29){
    lcd.print("30%");
    vooraadIndex = 2;
  }
  else if (voorraadProcent >= 10 && voorraadProcent <= 19){
    lcd.print("20%");
    vooraadIndex = 1;
  }
  else if (voorraadProcent >= 1 && voorraadProcent <= 9){
    lcd.print("10%");
    vooraadIndex = 1;
  }
  else{
    lcd.print("0%");
    vooraadIndex = 0;
  }
}

// Printen van het VOER NU systeem (met de rotory encoder)
void printCounter() {
  lcd.setCursor(0,1);
  lcd.print("Nu voeren: ");
  lcd.print(counter, 1); // Print met één decimaal
  lcd.print("gr");
}

// For loop voor het openen en sluiten van de servo motor. Dit gaat door servo.write(pos) en dan met de for loop pos 100 tm 140 en weer terug
void voerSessie() {
  for (int i = 0; i < aantalVoerSessies; i++) {
    for (pos = 100; pos <= 140; pos += 1) {
      myservoZwart.write(pos);
      delay(1);
    }
    delay(380);
    for (pos = 140; pos >= 100; pos -= 1) {
      myservoZwart.write(pos);
      delay(1);
    }
    delay(1000);
  }
}


// Controleer of de timer gecheckt is
void checkTimers(){
    if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/checkTimers");
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb de timer gechecked!!!");
      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }
    http.end();
  }
}

// Controleer of er gevoerd moet worden
void checkGiveFood(){
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/getFeedNow");
    int httpCode = http.GET();

    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb feed now gechecked");
        String payload = http.getString();

        DynamicJsonDocument doc(512);
        DeserializationError error = deserializeJson(doc, payload);
        
        if (!error) {
          int feedNow = doc["message"];
          if (feedNow == 1){
            counter = getAmountOfFood();
            aantalVoerSessies = counter / 2.5;
            voerSessie();
            setFoodNow();
          }
        }

      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }

    http.end();

  }
}

// Reset de feednow naar 0
void setFoodNow(){
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/setFeedNow?feed_now=0");
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb feed now op 0 gezet");
      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }
    http.end();
  }
}

// Update de voorraad percentage
void updateStorageLevel(){
    if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/setStorageLevel?fill_level=" + String(vooraadIndex));
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb net de storage level geupdated");
      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }
    http.end();
  }
}

// Zet de feed counter met 2.5gram omhoog
void increaseFood() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/increaseFood");
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb het aantal verhoogd!!!");
      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }
    http.end();
  }
}

// Zet de feed counter met 2.5gram omlaag
void decreaseFood() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/decreaseFood");
    int httpCode = http.GET();

    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb het aantal verlaagd!!!");
      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }

    http.end();
  }
}

// Haalt het aantal gram voedsel op dat op de display staat of in de web app
double getAmountOfFood() {
  double amountOfFood = -1;

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin("http://pi.local/amountOfFood");
    int httpCode = http.GET();

    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("ik heb het aantal opgehaald!!!");
        String payload = http.getString();

        DynamicJsonDocument doc(512);
        DeserializationError error = deserializeJson(doc, payload);

        if (!error) {
          amountOfFood = doc["doubleValue"];
        }

      }
      else{
        Serial.println("Mislukt x_x");
      }
    }
    else{
      Serial.println("Mislukt x_x");
    }

    http.end();

  }

  return amountOfFood;
}


// Interrupt handeling
void handleInterrupt() {
  if ((millis() - last_time_interupt) < 50){
    return;
  }

  if (digitalRead(DT_PIN) == HIGH) {
    Serial.println("ik ga het aantal verhogen...");
    increaseFood();
    direction = DIRECTION_CCW;
  } else {
    Serial.println("ik ga het aantal verlagen...");
    decreaseFood();
    direction = DIRECTION_CW;
  }

  Serial.println("ik ga het aantal ophalen...");
  counter = getAmountOfFood();
  last_time_interupt = millis();
}


void loop() {
  if (interruptOccurred) {
    interruptOccurred = false;
    handleInterrupt();
  }

// Controleert de timers, update de storage en controleert hoeveel gram voedsel er gegeven moet worden
  if ((millis() - last_time_timerChecked) > 10000){
    checkTimers();
    updateStorageLevel();
    checkGiveFood();
    last_time_timerChecked = millis();
  }

  button.loop();

// Als er op de knop gedrukt wordt om te voeren zet hij als eerst de variabele isFeeding op true zodat je weet dat er wordt gevoedt.
// Dan wordt het lcd scherm verandert. Ook wordt het aantalVoerSessies berekent door de counter (dat is het aantal gram dat is gekozen) gedeelt door 2.5
// Dit omdat elke voer sessie (portie) 2.5 gram is.
// Als laatste gaat isFeeding weer op false omdat de feed cyclus voorbij is
  if ((millis() - last_time_buttonPress) > 1500 && button.isPressed() && isFeeding == false){
    isFeeding = true;

    Serial.println("The button is pressed");
    lcd.setCursor(0,1);
    lcd.print("Aan het voeren...");

    aantalVoerSessies = counter / 2.5;
    voerSessie();

    last_time_buttonPress = millis();
    isFeeding = false;
  }
  
// Zorgt ervoor dat de opslag elke 2 seconde gemeten wordt
  if (! tmrOpslag.tmrActive()) { 
    opslagMeten(); 
    tmrOpslag.tmrSet(2000000); 
  } 
  
// Zorgt ervoor dat de counter elke 0.5 seconden geupdate kan worden met de draaiknop
  if (! tmrCounter.tmrActive()) { 
    printCounter(); 
    tmrCounter.tmrSet(500000); 
  } 

}
