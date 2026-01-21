-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nba_webshop
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_cart_user` (`user_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (15,2,8,1,'2026-01-12 19:16:34','2026-01-12 19:16:34'),(16,2,1,1,'2026-01-12 19:16:34','2026-01-12 19:16:34'),(17,7,19,1,'2026-01-12 19:23:23','2026-01-12 19:23:23');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Trikots','trikots','Offizielle NBA Spielertrikots aller Teams',NULL,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(2,'Caps & Mützen','caps-muetzen','NBA Team Caps, Snapbacks und Winterm??tzen',NULL,1,'2026-01-12 12:55:45','2026-01-12 16:07:22'),(3,'Sneaker','sneaker','Basketball-Schuhe und Lifestyle Sneaker',NULL,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(4,'Basketbälle','basketbaelle','Offizielle Spielb??lle und Trainingsb??lle',NULL,1,'2026-01-12 12:55:45','2026-01-12 16:07:22'),(5,'Hoodies & Jacken','hoodies-jacken','Warme NBA Team-Bekleidung',NULL,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(6,'Accessoires','accessoires','Taschen, Socken, Armb??nder und mehr',NULL,1,'2026-01-12 12:55:45','2026-01-12 12:55:45');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_order_items_order` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'LeBron James Lakers Trikot',NULL,1,119.99,119.99),(2,1,2,'Stephen Curry Warriors Trikot',NULL,1,119.99,119.99),(3,2,1,'LeBron James Lakers Trikot','images/lebron-lakers.png',1,119.99,119.99),(4,2,2,'Stephen Curry Warriors Trikot','images/curry-warriors.png',2,119.99,239.98),(5,3,3,'Kevin Durant Suns Trikot','images/durant-suns.png',1,129.99,129.99);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT 0.00,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_name` varchar(100) NOT NULL,
  `shipping_street` varchar(255) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_zip` varchar(20) NOT NULL,
  `shipping_country` varchar(100) DEFAULT 'Deutschland',
  `shipping_phone` varchar(50) DEFAULT NULL,
  `billing_same_as_shipping` tinyint(1) DEFAULT 1,
  `billing_name` varchar(100) DEFAULT NULL,
  `billing_street` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(20) DEFAULT NULL,
  `billing_country` varchar(100) DEFAULT NULL,
  `payment_method` enum('paypal','creditcard','invoice','sofort') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_number` (`order_number`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'ORD-2026-0001',239.98,45.60,4.99,290.57,'delivered','Max Mustermann','Musterstra??e 123','M??nchen','80331','Deutschland','+49 123 456789',1,NULL,NULL,NULL,NULL,NULL,'paypal','paid',NULL,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(2,2,'ORD-2026-0002',359.97,68.39,0.00,428.36,'pending','Max Mustermann','Musterstraße 1','Berlin','10115','Deutschland',NULL,1,NULL,NULL,NULL,NULL,NULL,'paypal','pending',NULL,'2026-01-12 15:07:21','2026-01-12 17:07:21'),(3,2,'ORD-2026-0003',129.99,24.70,0.00,154.69,'delivered','Max Mustermann','Musterstraße 1','Berlin','10115','Deutschland',NULL,1,NULL,NULL,NULL,NULL,NULL,'creditcard','paid',NULL,'2026-01-07 17:07:21','2026-01-12 17:07:21');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `stock` int(11) DEFAULT 0,
  `sku` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_slug` (`slug`),
  KEY `idx_products_featured` (`is_featured`),
  KEY `idx_products_active` (`is_active`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'LeBron James Lakers Trikot','lebron-james-lakers-trikot','Offizielles Los Angeles Lakers Trikot von LeBron James. Nike Swingman Edition mit authentischen Team-Details. Material: 100% recyceltes Polyester f??r optimale Atmungsaktivit??t.','Offizielles LA Lakers #23 Swingman Trikot',119.99,NULL,'images/lebron-lakers.png',NULL,49,'TRI-LAL-23',1,1,'2026-01-12 12:55:45','2026-01-12 17:07:21'),(2,1,'Stephen Curry Warriors Trikot','stephen-curry-warriors-trikot','Golden State Warriors Stephen Curry #30 Trikot. Nike Icon Edition mit gesticktem Spielernamen und -nummer. Perfekt f??r Fans des dreifachen Champions.','Warriors #30 Icon Edition Trikot',119.99,99.99,'images/curry-warriors.png',NULL,33,'TRI-GSW-30',1,1,'2026-01-12 12:55:45','2026-01-12 17:07:21'),(3,1,'Kevin Durant Suns Trikot','kevin-durant-suns-trikot','Phoenix Suns Kevin Durant #35 Statement Edition. Premium-Qualit??t mit Dri-FIT Technologie f??r maximalen Komfort.','Phoenix Suns #35 Statement Trikot',129.99,NULL,'images/durant-suns.png',NULL,24,'TRI-PHX-35',1,0,'2026-01-12 12:55:45','2026-01-12 17:07:21'),(4,1,'Giannis Antetokounmpo Bucks Trikot','giannis-bucks-trikot','Milwaukee Bucks Giannis Antetokounmpo #34 Icon Edition. Das Trikot des Greek Freak und NBA Champions.','Bucks #34 Icon Edition',119.99,NULL,'images/giannis-bucks.png',NULL,40,'TRI-MIL-34',1,1,'2026-01-12 12:55:45','2026-01-12 15:54:22'),(5,2,'Lakers New Era 9FIFTY Snapback','lakers-new-era-snapback','Los Angeles Lakers New Era 9FIFTY Snapback Cap. Klassisches Design mit gesticktem Team-Logo. Verstellbare Passform.','LA Lakers Official Snapback',34.99,NULL,'images/lakers-cap.png',NULL,100,'CAP-LAL-9FIFTY',1,0,'2026-01-12 12:55:45','2026-01-12 15:54:22'),(6,2,'Bulls Mitchell & Ness Wool Cap','bulls-mitchell-ness-cap','Chicago Bulls Mitchell & Ness Hardwood Classics Wollcap. Vintage-Design im 90er Jahre Stil.','Bulls Retro Wool Cap',39.99,29.99,'images/bulls-mitchell-ness-cap.png',NULL,60,'CAP-CHI-MN',1,1,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(7,2,'NBA Logo Wintermütze','nba-logo-wintermuetze','Offizielle NBA Logo Winterm??tze mit Bommel. Weich gef??ttert, perfekt f??r kalte Tage.','NBA Official Beanie',24.99,NULL,'images/nba-logo-wintermuetze.png',NULL,80,'CAP-NBA-BEANIE',1,0,'2026-01-12 12:55:45','2026-01-12 16:07:22'),(8,3,'Nike LeBron 21','nike-lebron-21','Die neueste LeBron Signature-Schuh von Nike. Zoom Air D??mpfung f??r explosive Bewegungen. Perfekt f??r das Spielfeld und die Stra??e.','LeBron Signature Basketball Shoe',199.99,NULL,'images/nike-lebron-21.png',NULL,30,'SNK-LBJ-21',1,1,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(9,3,'Jordan 1 Retro High Chicago','jordan-1-retro-chicago','Der Klassiker schlechthin. Air Jordan 1 in der ikonischen Chicago Farbgebung. Ein Must-Have f??r jeden Sneakerhead.','Air Jordan 1 Chicago Colorway',179.99,NULL,'images/jordan-1-retro-chicago.png',NULL,15,'SNK-AJ1-CHI',1,1,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(10,3,'Curry 11 Championship Gold','curry-11-championship-gold','Under Armour Curry 11 in Gold. Leicht, schnell und perfekt f??r Guards. Mit UA Flow Technologie.','Curry Brand Signature Shoe',159.99,139.99,'images/curry-11-championship-gold.png',NULL,25,'SNK-CUR-11',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(11,4,'Spalding NBA Official Game Ball','spalding-nba-official','Der offizielle Spielball der NBA. Echtes Leder, perfekter Grip. Der gleiche Ball wie die Profis spielen.','Official NBA Game Ball',169.99,NULL,'images/spalding-nba-official.png',NULL,20,'BALL-NBA-OFF',1,1,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(12,4,'Wilson NBA DRV Outdoor Ball','wilson-nba-drv-outdoor','Der perfekte Ball f??r Outdoor-Basketball. Langlebiges Performance Cover f??r Asphalt und Beton.','Wilson Outdoor Basketball',34.99,29.99,'images/wilson-nba-drv-outdoor.png',NULL,100,'BALL-WIL-DRV',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(13,4,'Spalding Mini Basketball Lakers','spalding-mini-lakers','Mini-Basketball f??r Fans jeden Alters. Offizielles Lakers Team-Design. Perfekt f??r kleine H??nde oder als Sammlerst??ck.','LA Lakers Mini Ball',19.99,NULL,'images/spalding-mini-lakers.png',NULL,150,'BALL-MINI-LAL',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(14,5,'Lakers Essential Hoodie','lakers-essential-hoodie','Los Angeles Lakers Nike Essential Hoodie. Weiche Fleece-Qualit??t mit Team-Grafik. Ideal f??r Training oder Freizeit.','LA Lakers Team Hoodie',79.99,NULL,'images/lakers-essential-hoodie.png',NULL,45,'HOOD-LAL-ESS',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(15,5,'Bulls Courtside Jacket','bulls-courtside-jacket','Chicago Bulls Courtside Jacke im Retro-Stil. Windabweisend mit Mesh-Futter. Ein Statement-Piece f??r Bulls Fans.','Bulls Retro Courtside Jacket',129.99,99.99,'images/bulls-courtside-jacket.png',NULL,20,'JKT-CHI-COURT',1,1,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(16,5,'NBA Logo Fleece Hoodie','nba-logo-fleece-hoodie','Klassischer NBA Logo Hoodie in Schwarz. Premium Fleece-Material, gro??es NBA Logo auf der Brust.','NBA Official Logo Hoodie',69.99,NULL,'images/nba-logo-fleece-hoodie.png',NULL,60,'HOOD-NBA-LOGO',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(17,6,'NBA Elite Sportsocken 3er Pack','nba-elite-socken-3pack','Performance Socken mit Polsterung an den richtigen Stellen. Feuchtigkeitsableitend. Set mit 3 Paar.','NBA Performance Socks 3-Pack',19.99,NULL,'images/nba-elite-socken-3pack.png',NULL,200,'ACC-SOCKS-3PK',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(18,6,'Lakers Sporttasche','lakers-sporttasche','Los Angeles Lakers Sporttasche mit gro??em Hauptfach und Seitentaschen. Perfekt f??r das Training.','LA Lakers Gym Bag',49.99,NULL,'images/lakers-sporttasche.png',NULL,40,'ACC-BAG-LAL',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14'),(19,6,'NBA Silikonarmband Set','nba-silikon-armband-set','Set mit 5 NBA Team Silikonarmb??ndern. Lakers, Bulls, Warriors, Celtics, Heat.','NBA Wristband Set 5-Pack',12.99,9.99,'images/nba-silikon-armband-set.png',NULL,300,'ACC-BAND-5PK',1,0,'2026-01-12 12:55:45','2026-01-12 16:03:14');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_helpful`
--

DROP TABLE IF EXISTS `review_helpful`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_helpful` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_helpful` (`review_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `review_helpful_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `review_helpful_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_helpful`
--

LOCK TABLES `review_helpful` WRITE;
/*!40000 ALTER TABLE `review_helpful` DISABLE KEYS */;
/*!40000 ALTER TABLE `review_helpful` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `verified_purchase` tinyint(1) DEFAULT 0,
  `helpful_count` int(11) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product_review` (`user_id`,`product_id`),
  KEY `idx_reviews_product` (`product_id`),
  KEY `idx_reviews_rating` (`rating`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,2,1,5,'Absolut perfekt!','Das Trikot sitzt wie angegossen und die Qualit??t ist erstklassig. Schneller Versand, sehr empfehlenswert!',1,12,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(2,3,1,4,'Gute Qualit??t, Gr????e beachten','Sehr zufrieden mit dem Kauf. Tipp: Eine Gr????e kleiner bestellen als normal.',1,8,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(3,2,2,5,'Curry Fan approved!','Als gro??er Warriors Fan bin ich begeistert. Die Stickerei ist sauber und das Material atmungsaktiv.',1,5,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(4,3,8,5,'Bester Basketball-Schuh ever','Die LeBrons sind einfach unglaublich. Perfekte D??mpfung und sieht dazu noch fantastisch aus.',1,15,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(5,2,9,5,'Klassiker!','Die Jordans sind ein Traum. Original-Qualit??t, schneller Versand. Absolut happy!',1,20,1,'2026-01-12 12:55:45','2026-01-12 12:55:45'),(6,3,11,4,'Guter Ball f??r den Preis','F??r Outdoor absolut ausreichend. Guter Grip, h??lt auch auf Asphalt lange.',1,3,1,'2026-01-12 12:55:45','2026-01-12 12:55:45');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user` (`user_id`),
  KEY `idx_sessions_activity` (`last_activity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@nba-shop.de','$2y$10$LAFw1Q/hJ.uYdZYCdY7u5.rEtgUXiikW3MtzeTObfduls27iECoiq','Shop Admin','admin',NULL,NULL,1,'2026-01-12 12:55:45','2026-01-12 16:19:40'),(2,'max.mustermann@email.de','$2y$10$wJGYifrDRcrAu6M8X0X5V.5jmjX0lRpDEYFUcr7llzxTvKktkQNQu','Max Mustermann','user',NULL,NULL,1,'2026-01-12 12:55:45','2026-01-12 16:44:29'),(3,'anna.schmidt@email.de','$2y$10$wJGYifrDRcrAu6M8X0X5V.5jmjX0lRpDEYFUcr7llzxTvKktkQNQu','Anna Schmidt','user',NULL,NULL,1,'2026-01-12 12:55:45','2026-01-12 16:44:29'),(4,'test@test.de','/euEbAgHyp/dytYweltJUMO','Test User','user',NULL,NULL,1,'2026-01-12 13:32:43','2026-01-12 19:19:54'),(5,'test_automator@example.com','$2y$10$nEbAxo4rqNccIb6kbiYsu.waKiZk3UtP4WyNtKDMEMDpQdaUiDZ/O','Test Automator','user',NULL,NULL,1,'2026-01-12 15:47:48','2026-01-12 15:47:48'),(6,'test_reg_1768241471@example.com','$2y$12$8vAcxV0rpqGjuljb9g8dQejb8UyhYWdICaopMh/mf7IvEG3F.EBGe','Registration Test User','user',NULL,NULL,1,'2026-01-12 18:11:12','2026-01-12 18:11:12'),(7,'bernd.koini@gmx.at','$2y$12$ZpV8JxAYW.CsedxE6z46ee9J0X1noJ823.2kZ4ZVobDsQo8YRXhra','Bernd','user',NULL,NULL,1,'2026-01-12 19:21:44','2026-01-12 19:21:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-12 20:33:23

