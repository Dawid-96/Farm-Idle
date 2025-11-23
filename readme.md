# 🌱 Farm-Idle

Ein kleines Browser-Idle-Game, das ich während meiner Umschulung gebaut habe.  
Komplett in **Vanilla JavaScript, HTML, CSS** und **PHP/MySQL** – ohne Frameworks!

Das Projekt ist mein zweites größeres Übungsprojekt nach dem [AusgabeNotizBlock](https://github.com/Dawid-96/AusgabeNotizBlock).  
Ich wollte mehr über DOM-Manipulation, State-Management und Backend-Integration lernen.

---

## Screenshots

### Login & Registrierung
<img width="563" height="455" alt="grafik" src="https://github.com/user-attachments/assets/6116779f-d61c-45aa-9813-64993debfc22" />


### Farm-Tab (Säen & Ernten)
<img width="1337" height="548" alt="grafik" src="https://github.com/user-attachments/assets/626bdb9e-bb71-4bdb-a514-543473e7959b" />


### Verarbeitung mit Timer
<img width="1547" height="676" alt="grafik" src="https://github.com/user-attachments/assets/152d8e62-dec8-4585-8b18-bda47a47f68b" />


### Markt
<img width="1501" height="794" alt="grafik" src="https://github.com/user-attachments/assets/00aae9d7-4ccd-4b34-a119-997289e61168" />


### Inventar
<img width="1495" height="511" alt="grafik" src="https://github.com/user-attachments/assets/091f2048-7ba3-4c0e-aa6e-b12d5b0c5ba0" />

---

## Was kann das Spiel?

- **Login-System** mit Session-Management (PHP)
- **Farm**: Felder kaufen, Pflanzen säen und ernten (Weizen, Mais, Soja)
- **Verarbeitung**: Rohstoffe zu Produkten verarbeiten (Mehl, Popcorn, Öl)
- **Timer & Fortschritt**: Echtzeit-Wachstum und Job-Fortschritt
- **Inventar**: Alle Items mit Icons anzeigen
- **Markt**: Items verkaufen und Gold verdienen
- **Auto-Save**: Spielstand wird automatisch gespeichert (MySQL)

---

## Projekt lokal starten

### Was du brauchst
- Docker oder einen Webserver mit **PHP 7.4+** und **MySQL 5.7+**

### Installation

1. **Repo klonen**
   ```bash
   git clone https://github.com/Dawid-96/Farm-Idle.git
   cd Farm-Idle
   ```

2. **Datenbank anlegen**
   
   In MySQL eine Datenbank `2351_Farmidle` erstellen und folgende Tabellen anlegen:

   ```sql
   CREATE TABLE users (
     userId INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) UNIQUE NOT NULL,
     password VARCHAR(255) NOT NULL
   );

   CREATE TABLE saves (
     saveId INT AUTO_INCREMENT PRIMARY KEY,
     userId INT NOT NULL,
     gameStateJson TEXT NOT NULL,
     timestamp DATETIME NOT NULL,
     FOREIGN KEY (userId) REFERENCES users(userId)
   );
   ```

3. **Verbindung anpassen**
   
   In [php/connection.php](php/connection.php) deine Zugangsdaten eintragen:
   ```php
   $host = "mysql";        // oder "localhost"
   $user = "root";
   $password = "1234";     // dein Passwort
   $dbname = "2351_Farmidle";
   ```

4. **Starten**
   
   Browser öffnen und `http://localhost/Farm-Idle/login.html` aufrufen.  
   Neuen Account registrieren und losspielen!

---

## Projektstruktur

```
Farm-Idle/
│
├── index.php              # Hauptspiel (nur mit Login erreichbar)
├── login.html             # Login/Register-Seite
│
├── js/
│   ├── auth.js            # Login/Register-Logik
│   ├── base.js            # Tab-Navigation
│   ├── config.js          # Items, Crops, Recipes
│   ├── storage.js         # Laden & Speichern (API-Calls)
│   ├── game.js            # Haupt-Spiellogik (State-Management)
│   ├── farm.js            # Farm-Tab (Felder, Pflanzen, Ernte)
│   ├── process.js         # Verarbeitungs-Tab (Jobs, Timer)
│   ├── markt.js           # Markt-Tab (Verkauf)
│   └── inventory.js       # Inventar-Anzeige
│
├── php/
│   ├── connection.php     # MySQL-Verbindung
│   ├── helpers.php        # Flash-Messages, Session-Check
│   ├── default_state.php  # Anfangszustand für neue Spieler
│   ├── register.php       # User registrieren
│   ├── login.php          # User einloggen
│   ├── logout.php         # Ausloggen
│   ├── save.php           # Spielstand speichern
│   └── load_save.php      # Spielstand laden
│
├── style/
│   └── style.css          # CSS mit Variablen
│
└── img/
    ├── Crops/             # Pflanzen-Icons (Weizen, Mais, Soja)
    ├── Process/           # Produkt-Icons (Mehl, Popcorn, Öl)
    └── svg/               # UI-Icons (Tabs, Buttons)
```

---

## Spielmechanik

### So funktioniert's

1. **Farm**: Saat auswählen → Feld bepflanzen → Warten → Ernten
2. **Verarbeitung**: Rohstoffe auswählen → Job starten (max. 3 gleichzeitig) → Warten → Automatisch abgeschlossen
3. **Markt**: Items auswählen → Menge eingeben → Verkaufen → Gold bekommen
4. **Inventar**: Alle Items im Überblick

---

## Technische Details

### Frontend
- **State-Management**: Ein globales `window.state`-Objekt für alles
- **Module**: Jede `.js`-Datei exportiert Funktionen via `window.X = { ... }`
- **Templates**: HTML `<template>`-Tags für wiederverwendbare Karten
- **Auto-Update**: `setInterval` für Timer (Farm-Wachstum, Verarbeitungs-Jobs)

### Backend
- **API-Endpoints**:
  - `POST /php/register.php` – Neuen User anlegen
  - `POST /php/login.php` – Session starten
  - `GET /php/logout.php` – Ausloggen
  - `GET /php/load_save.php` – Letzten Spielstand laden
  - `POST /php/save.php` – Spielstand speichern

- **Session**: PHP-Session mit User-ID und Username
- **Sicherheit**: Password-Hashing mit `password_hash()`, Session-Regeneration
