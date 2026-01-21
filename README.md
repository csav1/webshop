# 🏀 Fake NBA Store

Ein moderner, fiktiver E-Commerce Webshop für NBA Merchandise, entwickelt als Schulprojekt.

## 📋 Über das Projekt
Dieses Projekt simuliert einen vollständigen Online-Shop für Basketball-Fans. Nutzer können Trikots, Sneakers, Basketbälle und mehr durchsuchen, einen Warenkorb verwalten und Bestellungen aufgeben. Das System verfügt zudem über einen geschützten Administrationsbereich zur Verwaltung von Produkten und Kategorien.

Das Projekt wurde **ohne große Frameworks** (wie Laravel) umgesetzt, um die Kernprinzipien der Webentwicklung (MVC-Architektur, Routing, PDO, Sicherheit) zu demonstrieren.

## ✨ Features
*   **Frontend:**
    *   Responsive Design (Mobile First) mit Tailwind CSS
    *   Dynamische UI-Elemente (Warenkorb-Updates, Dropdowns) mit Alpine.js
    *   Produktsuche & Filterung nach Kategorien
    *   Warenkorb (Session-basiert + Datenbank-Sync für eingeloggte User)
    *   User-Account (Bestellhistorie, Profil)
*   **Backend:**
    *   Custom Router mit SEO-freundlichen URLs
    *   MVC-Architektur (Model-View-Controller)
    *   Sichere Authentifizierung & Autorisierung
    *   CSRF-Protection & Input Sanitization
*   **Admin-Dashboard:**
    *   Produktverwaltung (Erstellen, Bearbeiten, Soft-Delete)
    *   Kategorieverwaltung
    *   Bestellübersicht

## 🛠️ Technologien
*   **Backend:** PHP 8.2+
*   **Datenbank:** MySQL / MariaDB
*   **Frontend:** HTML5, Tailwind CSS, Alpine.js, FontAwesome
*   **Server:** Apache (XAMPP/LAMP Stack)

## 🚀 Installation

### Voraussetzungen
*   PHP >= 8.0
*   MySQL/MariaDB Datenbank
*   Webserver (Apache empfohlen)

### Schritte
1.  **Repository klonen:**
    ```bash
    git clone https://github.com/csav1/webshop.git
    ```

2.  **Datenbank einrichten:**
    *   Erstelle eine neue Datenbank (z.B. `nba_webshop`).
    *   Importiere die Struktur und Testdaten:
        *   `database/schema.sql` (Tabellenstruktur)
        *   `database/seeds.sql` (Beispieldaten)
        *   Optional: `fix_descriptions.sql` & `fix_category_descriptions.sql` falls Encoding-Fehler auftreten.

3.  **Konfiguration:**
    *   Kopiere `config/database.example.php` (falls vorhanden) zu `config/database.php` oder erstelle sie neu:
    ```php
    <?php
    return [
        'host' => 'localhost',
        'dbname' => 'nba_webshop',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
    ```
    *   Passe `config/app.php` an deine URL an (z.B. `http://localhost/webshop/public`).

4.  **Verzeichnis-Rechte:**
    *   Stelle sicher, dass der Webserver Schreibrechte auf `public/uploads` (für Bilder) hat.

5.  **Starten:**
    *   Öffne den Browser und navigiere zu `http://localhost/webshop/public`.

## 👤 Login Daten (Demo)
*   **Admin:** `admin@nba-shop.de` / `admin123`
*   **User:** `max.mustermann@email.de` / `admin123`

---
*Entwickelt von [Dein Name] für [Projekt/Schule].*
