-- ============================================
-- NBA Webshop Beispieldaten (Seeds)
-- Version: 1.0
-- ============================================

USE nba_webshop;

-- ============================================
-- Admin-Benutzer erstellen
-- Passwort: admin123 (gehashed mit password_hash())
-- ============================================
INSERT INTO users (email, password_hash, name, role, is_active) VALUES
('admin@nba-shop.de', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Shop Admin', 'admin', TRUE),
('max.mustermann@email.de', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Max Mustermann', 'user', TRUE),
('anna.schmidt@email.de', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anna Schmidt', 'user', TRUE);

-- ============================================
-- Kategorien
-- ============================================
INSERT INTO categories (name, slug, description, is_active) VALUES
('Trikots', 'trikots', 'Offizielle NBA Spielertrikots aller Teams', TRUE),
('Caps & Mützen', 'caps-muetzen', 'NBA Team Caps, Snapbacks und Wintermützen', TRUE),
('Sneaker', 'sneaker', 'Basketball-Schuhe und Lifestyle Sneaker', TRUE),
('Basketbälle', 'basketbaelle', 'Offizielle Spielbälle und Trainingsbälle', TRUE),
('Hoodies & Jacken', 'hoodies-jacken', 'Warme NBA Team-Bekleidung', TRUE),
('Accessoires', 'accessoires', 'Taschen, Socken, Armbänder und mehr', TRUE);

-- ============================================
-- Produkte
-- ============================================

-- Trikots
INSERT INTO products (category_id, name, slug, description, short_description, price, sale_price, stock, sku, is_active, is_featured) VALUES
(1, 'LeBron James Lakers Trikot', 'lebron-james-lakers-trikot', 
'Offizielles Los Angeles Lakers Trikot von LeBron James. Nike Swingman Edition mit authentischen Team-Details. Material: 100% recyceltes Polyester für optimale Atmungsaktivität.', 
'Offizielles LA Lakers #23 Swingman Trikot', 119.99, NULL, 50, 'TRI-LAL-23', TRUE, TRUE),

(1, 'Stephen Curry Warriors Trikot', 'stephen-curry-warriors-trikot',
'Golden State Warriors Stephen Curry #30 Trikot. Nike Icon Edition mit gesticktem Spielernamen und -nummer. Perfekt für Fans des dreifachen Champions.',
'Warriors #30 Icon Edition Trikot', 119.99, 99.99, 35, 'TRI-GSW-30', TRUE, TRUE),

(1, 'Kevin Durant Suns Trikot', 'kevin-durant-suns-trikot',
'Phoenix Suns Kevin Durant #35 Statement Edition. Premium-Qualität mit Dri-FIT Technologie für maximalen Komfort.',
'Phoenix Suns #35 Statement Trikot', 129.99, NULL, 25, 'TRI-PHX-35', TRUE, FALSE),

(1, 'Giannis Antetokounmpo Bucks Trikot', 'giannis-bucks-trikot',
'Milwaukee Bucks Giannis Antetokounmpo #34 Icon Edition. Das Trikot des Greek Freak und NBA Champions.',
'Bucks #34 Icon Edition', 119.99, NULL, 40, 'TRI-MIL-34', TRUE, TRUE),

-- Caps & Mützen
(2, 'Lakers New Era 9FIFTY Snapback', 'lakers-new-era-snapback',
'Los Angeles Lakers New Era 9FIFTY Snapback Cap. Klassisches Design mit gesticktem Team-Logo. Verstellbare Passform.',
'LA Lakers Official Snapback', 34.99, NULL, 100, 'CAP-LAL-9FIFTY', TRUE, FALSE),

(2, 'Bulls Mitchell & Ness Wool Cap', 'bulls-mitchell-ness-cap',
'Chicago Bulls Mitchell & Ness Hardwood Classics Wollcap. Vintage-Design im 90er Jahre Stil.',
'Bulls Retro Wool Cap', 39.99, 29.99, 60, 'CAP-CHI-MN', TRUE, TRUE),

(2, 'NBA Logo Wintermütze', 'nba-logo-wintermuetze',
'Offizielle NBA Logo Wintermütze mit Bommel. Weich gefüttert, perfekt für kalte Tage.',
'NBA Official Beanie', 24.99, NULL, 80, 'CAP-NBA-BEANIE', TRUE, FALSE),

-- Sneaker
(3, 'Nike LeBron 21', 'nike-lebron-21',
'Die neueste LeBron Signature-Schuh von Nike. Zoom Air Dämpfung für explosive Bewegungen. Perfekt für das Spielfeld und die Straße.',
'LeBron Signature Basketball Shoe', 199.99, NULL, 30, 'SNK-LBJ-21', TRUE, TRUE),

(3, 'Jordan 1 Retro High Chicago', 'jordan-1-retro-chicago',
'Der Klassiker schlechthin. Air Jordan 1 in der ikonischen Chicago Farbgebung. Ein Must-Have für jeden Sneakerhead.',
'Air Jordan 1 Chicago Colorway', 179.99, NULL, 15, 'SNK-AJ1-CHI', TRUE, TRUE),

(3, 'Curry 11 Championship Gold', 'curry-11-championship-gold',
'Under Armour Curry 11 in Gold. Leicht, schnell und perfekt für Guards. Mit UA Flow Technologie.',
'Curry Brand Signature Shoe', 159.99, 139.99, 25, 'SNK-CUR-11', TRUE, FALSE),

-- Basketbälle
(4, 'Spalding NBA Official Game Ball', 'spalding-nba-official',
'Der offizielle Spielball der NBA. Echtes Leder, perfekter Grip. Der gleiche Ball wie die Profis spielen.',
'Official NBA Game Ball', 169.99, NULL, 20, 'BALL-NBA-OFF', TRUE, TRUE),

(4, 'Wilson NBA DRV Outdoor Ball', 'wilson-nba-drv-outdoor',
'Der perfekte Ball für Outdoor-Basketball. Langlebiges Performance Cover für Asphalt und Beton.',
'Wilson Outdoor Basketball', 34.99, 29.99, 100, 'BALL-WIL-DRV', TRUE, FALSE),

(4, 'Spalding Mini Basketball Lakers', 'spalding-mini-lakers',
'Mini-Basketball für Fans jeden Alters. Offizielles Lakers Team-Design. Perfekt für kleine Hände oder als Sammlerstück.',
'LA Lakers Mini Ball', 19.99, NULL, 150, 'BALL-MINI-LAL', TRUE, FALSE),

-- Hoodies & Jacken
(5, 'Lakers Essential Hoodie', 'lakers-essential-hoodie',
'Los Angeles Lakers Nike Essential Hoodie. Weiche Fleece-Qualität mit Team-Grafik. Ideal für Training oder Freizeit.',
'LA Lakers Team Hoodie', 79.99, NULL, 45, 'HOOD-LAL-ESS', TRUE, FALSE),

(5, 'Bulls Courtside Jacket', 'bulls-courtside-jacket',
'Chicago Bulls Courtside Jacke im Retro-Stil. Windabweisend mit Mesh-Futter. Ein Statement-Piece für Bulls Fans.',
'Bulls Retro Courtside Jacket', 129.99, 99.99, 20, 'JKT-CHI-COURT', TRUE, TRUE),

(5, 'NBA Logo Fleece Hoodie', 'nba-logo-fleece-hoodie',
'Klassischer NBA Logo Hoodie in Schwarz. Premium Fleece-Material, großes NBA Logo auf der Brust.',
'NBA Official Logo Hoodie', 69.99, NULL, 60, 'HOOD-NBA-LOGO', TRUE, FALSE),

-- Accessoires
(6, 'NBA Elite Sportsocken 3er Pack', 'nba-elite-socken-3pack',
'Performance Socken mit Polsterung an den richtigen Stellen. Feuchtigkeitsableitend. Set mit 3 Paar.',
'NBA Performance Socks 3-Pack', 19.99, NULL, 200, 'ACC-SOCKS-3PK', TRUE, FALSE),

(6, 'Lakers Sporttasche', 'lakers-sporttasche',
'Los Angeles Lakers Sporttasche mit großem Hauptfach und Seitentaschen. Perfekt für das Training.',
'LA Lakers Gym Bag', 49.99, NULL, 40, 'ACC-BAG-LAL', TRUE, FALSE),

(6, 'NBA Silikonarmband Set', 'nba-silikon-armband-set',
'Set mit 5 NBA Team Silikonarmbändern. Lakers, Bulls, Warriors, Celtics, Heat.',
'NBA Wristband Set 5-Pack', 12.99, 9.99, 300, 'ACC-BAND-5PK', TRUE, FALSE);

-- ============================================
-- Beispiel-Bewertungen
-- ============================================
INSERT INTO reviews (user_id, product_id, rating, title, content, verified_purchase, helpful_count, is_approved) VALUES
(2, 1, 5, 'Absolut perfekt!', 'Das Trikot sitzt wie angegossen und die Qualität ist erstklassig. Schneller Versand, sehr empfehlenswert!', TRUE, 12, TRUE),
(3, 1, 4, 'Gute Qualität, Größe beachten', 'Sehr zufrieden mit dem Kauf. Tipp: Eine Größe kleiner bestellen als normal.', TRUE, 8, TRUE),
(2, 2, 5, 'Curry Fan approved!', 'Als großer Warriors Fan bin ich begeistert. Die Stickerei ist sauber und das Material atmungsaktiv.', TRUE, 5, TRUE),
(3, 8, 5, 'Bester Basketball-Schuh ever', 'Die LeBrons sind einfach unglaublich. Perfekte Dämpfung und sieht dazu noch fantastisch aus.', TRUE, 15, TRUE),
(2, 9, 5, 'Klassiker!', 'Die Jordans sind ein Traum. Original-Qualität, schneller Versand. Absolut happy!', TRUE, 20, TRUE),
(3, 11, 4, 'Guter Ball für den Preis', 'Für Outdoor absolut ausreichend. Guter Grip, hält auch auf Asphalt lange.', TRUE, 3, TRUE);

-- ============================================
-- Beispiel-Bestellung
-- ============================================
INSERT INTO orders (user_id, order_number, subtotal, tax, shipping_cost, total, status, 
    shipping_name, shipping_street, shipping_city, shipping_zip, shipping_phone,
    payment_method, payment_status) VALUES
(2, 'ORD-2026-0001', 239.98, 45.60, 4.99, 290.57, 'delivered',
    'Max Mustermann', 'Musterstraße 123', 'München', '80331', '+49 123 456789',
    'paypal', 'paid');

INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_purchase, total) VALUES
(1, 1, 'LeBron James Lakers Trikot', 1, 119.99, 119.99),
(1, 2, 'Stephen Curry Warriors Trikot', 1, 119.99, 119.99);
