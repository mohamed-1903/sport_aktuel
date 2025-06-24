-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Jun 2025 um 16:25
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `sportx`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `created_at`) VALUES
(1, 7, '2025-06-22 00:00:11'),
(2, 6, '2025-06-22 11:36:38'),
(3, 8, '2025-06-22 17:50:53');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `discount` int(11) DEFAULT 0,
  `gift` tinyint(1) DEFAULT 0,
  `custom_name` varchar(50) DEFAULT NULL,
  `custom_number` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `size`, `quantity`, `discount`, `gift`, `custom_name`, `custom_number`) VALUES
(101, 3, 1, '42', 5, 0, 0, NULL, NULL),
(102, 3, 6, 'M', 1, 0, 0, NULL, NULL),
(113, 2, 1, 'M', 1, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('neu','in_bearbeitung','abgeschlossen','abgelehnt','storniert') DEFAULT 'neu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `admin_comment`) VALUES
(3, 6, 'neu', '2025-06-22 17:48:57', NULL, '[{\"cart_item_id\":86,\"product_id\":2,\"size\":\"M\",\"quantity\":1,\"discount\":0,\"gift\":0,\"name\":\"Bayern Trikot 2024\\/25\",\"price\":\"89.99\",\"image_main\":\"img\\/bayern.jpg\"}]'),
(4, 8, 'storniert', '2025-06-22 17:51:02', '2025-06-22 18:03:21', '[{\"cart_item_id\":87,\"product_id\":1,\"size\":\"43\",\"quantity\":1,\"discount\":0,\"gift\":0,\"name\":\"Nike Air Zoom Mercurial Vapor XVI Elite\",\"price\":\"249.99\",\"image_main\":\"img\\/Fu\\u00dfball.jpeg\"}]'),
(5, 8, 'storniert', '2025-06-22 17:56:16', '2025-06-22 18:03:32', '[{\"cart_item_id\":88,\"product_id\":1,\"size\":\"44\",\"quantity\":2,\"discount\":0,\"gift\":0,\"name\":\"Nike Air Zoom Mercurial Vapor XVI Elite\",\"price\":\"249.99\",\"image_main\":\"img\\/Fu\\u00dfball.jpeg\"},{\"cart_item_id\":89,\"product_id\":1,\"size\":\"39\",\"quantity\":1,\"discount\":0,\"gift\":0,\"name\":\"Nike Air Zoom Mercurial Vapor XVI Elite\",\"price\":\"249.99\",\"image_main\":\"img\\/Fu\\u00dfball.jpeg\"}]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `poll_options`
--

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) DEFAULT NULL,
  `option_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_text` varchar(50) DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `marke` varchar(100) DEFAULT NULL,
  `farbe` varchar(50) DEFAULT NULL,
  `geschlecht` enum('Herren','Damen','Unisex') DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `sizes` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `price_text`, `image_main`, `marke`, `farbe`, `geschlecht`, `category`, `subcategory`, `sizes`, `images`) VALUES
(1, 'Nike Air Zoom Mercurial Vapor XVI Elite', 'Der Nike Air Zoom Mercurial Vapor XVI Elite FG aus dem exklusiven Mad Ambition Pack bietet herausragenden Komfort und Performance auf dem Spielfeld. Dieser Fußballschuh wurde speziell für schnelle Richtungswechsel und maximale Beschleunigung entwickelt. Der Nike Mercurial Vapor XVI Elite ist der perfekte Schuh für ambitionierte Fußballer, die auf der Suche nach höchster Geschwindigkeit, Kontrolle und Komfort sind.', 249.99, '249.99€ inkl. Mwst.', 'img/Fußball.jpeg', 'Nike', 'Blau', 'Unisex', 'Fußballschuhe', 'Stollen', '[38,39,40,41,42,43,44,45,46]', '[\"img\\/Fu\\u00dfball.jpeg\",\"img\\/Fu\\u00dfball2.jpeg\",\"img\\/Fu\\u00dfball3.jpeg\",\"img\\/Fu\\u00dfball4.jpeg\",\"img\\/Fu\\u00dfball5.jpeg\",\"img\\/Fu\\u00dfball6.jpeg\",\"img\\/Fu\\u00dfball7.jpeg\",\"img\\/Fu\\u00dfball8.jpeg\"]'),
(2, 'Bayern Trikot 2024/25', 'Diese Saison im kreativen Streifendesign mit legendärer FC Bayern Rauten-DNA! Hier siehst Du das adidas FC Bayern München Home Trikot 2024/2025 in Herrengrößen. Dieses offiziell lizenzierte Fan-Trikot ist eine detailgetreue Replik des offiziellen Trikots, wie es die Bundesligaspieler des FC Bayern tragen. Ein Streifentrikot-Klassiker, kreativ neu interpretiert für den Rekordmeister.', 89.99, '89.99 € inkl. Mwst.', 'img/bayern.jpg', 'Adidas', 'Rot', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/bayern.jpg\",\"img\\/bayern2.jpeg\",\"img\\/bayern3.jpeg\",\"img\\/bayern4.jpeg\"]'),
(3, 'Puma Handschuhe Schwarz', 'Leichte und flexible Puma Sporthandschuhe in elegantem Schwarz - ideal für Trainingseinheiten bei kühlerem Wetter. Bieten perfekten Grip, hohen Tragekomfort und eine gute Passform für sportliche Aktivitäten.', 29.99, '29.99 € inkl. Mwst.', 'img/handschuheSchwarz.jpg', 'Puma', 'Schwarz', 'Herren', 'Sportbekleidung', 'Handschuhe', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/handschuheSchwarz.jpg\"]'),
(4, 'Trainingsanzug Nike Blau', 'Der Nike Trainingsanzug in edlem Blau vereint sportliches Design mit hohem Tragekomfort. Hergestellt aus atmungsaktivem Funktionsmaterial bietet er optimale Bewegungsfreiheit beim Training oder in der Freizeit. Das moderne, körpernahe Design sorgt für einen stylischen Look - ideal für ambitionierte Sportler und modebewusste Aktive.', 139.99, '139.99 € inkl. Mwst.', 'img/trainingsanzugBlau.jpg', 'Nike', 'Blau', 'Herren', 'Sportbekleidung', 'Trainingsanzüge', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/trainingsanzugBlau.jpg\",\"img\\/trainingsanzugBlau2.jpg\"]'),
(5, 'Trainingsanzug Nike Rot', 'Der Nike Trainingsanzug in kräftigem Rot setzt ein sportliches Statement. Das hochwertige, feuchtigkeitsableitende Material garantiert ein angenehmes Tragegefühl, egal ob beim Workout oder im Alltag. Der ergonomische Schnitt und die elastischen Bündchen sorgen für eine perfekte Passform und maximale Bewegungsfreiheit.', 119.99, '119.99 € inkl. Mwst.', 'img/trainingsanzugRot.jpg', 'Nike', 'Rot', 'Herren', 'Sportbekleidung', 'Trainingsanzüge', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/trainingsanzugRot.jpg\",\"img\\/trainingsanzugRot2.jpg\"]'),
(6, 'Dortmund Trikot 2024/25', 'Das offizielle Borussia Dortmund Heimtrikot der Saison 2024/25 - mit ikonischem Design und modernster Technologie für höchsten Tragekomfort. Ein Muss für alle echten BVB-Fans.', 89.99, '89.99 € inkl. Mwst.', 'img/dortmund.jpg', 'Puma', 'Gelb', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/dortmund.jpg\",\"img\\/dortmund2.jpg\"]'),
(7, 'weiße Sportsocken Nike', 'Weiß wie dein Game am Sonntagmorgen: clean, klassisch und einfach zuverlässig. Diese Nike Sportsocken verbinden sportlichen Style mit Komfort für den ganzen Tag. Ob Workout, Streetwear oder Sofa - sie machen überall eine gute Figur. Und ja, sie fühlen sich so gut an, wie sie aussehen.', 19.99, '19.99 € inkl. Mwst.', 'img/sockenWeiß.jpg', 'Nike', 'Weiß', 'Herren', 'Sportbekleidung', 'Socken', '[\"39-40\",\"41-42\",\"43-44\",\"45-46\"]', '[\"img\\/sockenWei\\u00df.jpg\"]'),
(8, 'schwarze Sportsocken Nike', 'Schlicht. Stark. Schwarz. Diese Nike Sportsocken sitzen wie angegossen und liefern dir Halt, wo du ihn brauchst. Die weiche Polsterung schont deine Schritte, während das schweißableitende Material deine Füße auch bei langen Matches frisch hält. Für alle, die lieber performen statt reden.', 19.99, '19.99 € inkl. Mwst.', 'img/sockenSchwarz.jpg', 'Nike', 'Schwarz', 'Herren', 'Sportbekleidung', 'Socken', '[\"39-40\",\"41-42\",\"43-44\",\"45-46\"]', '[\"img\\/sockenSchwarz.jpg\"]'),
(9, 'Real Madrid Trikot 2024/25', 'Das neue Real Madrid Heimtrikot 2024/25 von Adidas. Klassisch weiß mit goldenen Akzenten, wie es die Stars im Bernabéu tragen.', 99.99, '99.99 € inkl. Mwst.', 'img/realmadrid.jpg', 'Adidas', 'Weiß', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/realmadrid.jpg\"]'),
(10, 'Manchester City Trikot 2024/25', 'Das offizielle Manchester City Home Trikot 2024/25 von Puma. Himmelblau, modern und mit allen Details des englischen Meisters.', 99.99, '99.99 € inkl. Mwst.', 'img/mancity.jpg', 'Puma', 'Blau', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/mancity.jpg\"]'),
(11, 'Paris Saint-Germain Trikot 2024/25', 'Das neue PSG Heimtrikot 2024/25 von Nike. Dunkelblau mit roten und weißen Details – Pariser Eleganz für Fans.', 99.99, '99.99 € inkl. Mwst.', 'img/psg.jpg', 'Nike', 'Blau', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/psg.jpg\"]'),
(12, 'Juventus Turin Trikot 2024/25', 'Das Juventus Turin Heimtrikot 2024/25 von Adidas. Klassisch schwarz-weiß gestreift, wie es die Stars in Turin tragen.', 99.99, '99.99 € inkl. Mwst.', 'img/juventus.jpg', 'Adidas', 'Schwarz-Weiß', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/juventus.jpg\"]'),
(13, 'Inter Mailand Trikot 2024/25', 'Das neue Inter Mailand Heimtrikot 2024/25 von Nike. Blau-schwarze Streifen, modernes Design für echte Nerazzurri-Fans.', 99.99, '99.99 € inkl. Mwst.', 'img/inter.jpg', 'Nike', 'Blau-Schwarz', 'Herren', 'Sportbekleidung', 'Trikots', '[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/inter.jpg\"]'),
(14, 'Adidas Trainingsjacke Schwarz', 'Leichte Adidas Trainingsjacke in Schwarz, atmungsaktiv und perfekt für das Aufwärmen oder den Weg zum Training.', 69.99, '69.99 € inkl. Mwst.', 'img/trainingsjackeSchwarz.jpg', 'Adidas', 'Schwarz', 'Herren', 'Sportbekleidung', 'Trainingsjacken', '[\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/trainingsjackeSchwarz.jpg\"]'),
(15, 'Nike Trainingshose Grau', 'Komfortable Nike Trainingshose in Grau mit elastischem Bund und Reißverschlusstaschen.', 49.99, '49.99 € inkl. Mwst.', 'img/trainingshoseGrau.jpg', 'Nike', 'Grau', 'Herren', 'Sportbekleidung', 'Trainingshosen', '[\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/trainingshoseGrau.jpg\"]'),
(16, 'Adidas T-Shirt Weiß', 'Klassisches Adidas T-Shirt in Weiß, ideal für Training und Freizeit.', 24.99, '24.99 € inkl. Mwst.', 'img/tshirtWeiß.jpg', 'Adidas', 'Weiß', 'Herren', 'Sportbekleidung', 'T-Shirts', '[\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/tshirtWei\\u00df.jpg\"]'),
(17, 'Puma Poloshirt Navy', 'Elegantes Puma Poloshirt in Navy für einen sportlich-schicken Look.', 34.99, '34.99 € inkl. Mwst.', 'img/poloshirtNavy.jpg', 'Puma', 'Navy', 'Herren', 'Sportbekleidung', 'Poloshirts', '[\"S\",\"M\",\"L\",\"XL\",\"2XL\"]', '[\"img\\/poloshirtNavy.jpg\"]'),
(18, 'Adidas Predator Edge.3 AG', 'Adidas Predator Fußballschuhe für Kunstrasenplätze. Perfekter Grip und Ballkontrolle.', 129.99, '129.99 € inkl. Mwst.', 'img/predatorAG.jpg', 'Adidas', 'Schwarz-Rot', 'Unisex', 'Fußballschuhe', 'Kunstrasen', '[39,40,41,42,43,44,45]', '[\"img\\/predatorAG.jpg\"]'),
(19, 'Nike Tiempo Legend 10 IC', 'Nike Tiempo Legend 10 Hallenschuhe für präzises Spiel auf Indoor-Böden.', 99.99, '99.99 € inkl. Mwst.', 'img/tiempoIC.jpg', 'Nike', 'Weiß-Blau', 'Unisex', 'Fußballschuhe', 'Hallenschuhe', '[38,39,40,41,42,43,44,45]', '[\"img\\/tiempoIC.jpg\"]'),
(20, 'Puma Future Z 1.4 FG/AG', 'Puma Future Z 1.4 Fußballschuhe für Stollen- und Kunstrasenplätze. Maximale Beweglichkeit und Komfort.', 199.99, '199.99 € inkl. Mwst.', 'img/futureZ.jpg', 'Puma', 'Gelb-Schwarz', 'Unisex', 'Fußballschuhe', 'Stollen', '[40,41,42,43,44,45]', '[\"img\\/futureZ.jpg\"]'),
(21, 'Nike Schienbeinschoner Mercurial Lite', 'Leichte und robuste Nike Schienbeinschoner für optimalen Schutz beim Spiel.', 24.99, '24.99 € inkl. Mwst.', 'img/schienbeinschoner.jpg', 'Nike', 'Weiß-Schwarz', 'Unisex', 'Zubehör', 'Schienbeinschoner', '[\"S\",\"M\",\"L\"]', '[\"img\\/schienbeinschoner.jpg\"]'),
(22, 'Adidas Fußball Al Rihla', 'Offizieller Adidas Trainingsball Al Rihla, FIFA Quality Pro zertifiziert.', 39.99, '39.99 € inkl. Mwst.', 'img/fussballAlRihla.jpg', 'Adidas', 'Weiß-Bunt', 'Unisex', 'Zubehör', 'Fußbälle', '[\"5\"]', '[\"img\\/fussballAlRihla.jpg\"]'),
(23, 'Puma Sporttasche TeamGOAL', 'Geräumige Puma Sporttasche TeamGOAL mit separatem Schuhfach.', 29.99, '29.99 € inkl. Mwst.', 'img/sporttaschePuma.jpg', 'Puma', 'Schwarz', 'Unisex', 'Zubehör', 'Sporttaschen', '[\"One Size\"]', '[\"img\\/sporttaschePuma.jpg\"]'),
(24, 'Nike Trainingsjacke Sale', 'Nike Trainingsjacke in Schwarz im Sale – sportlich, bequem und reduziert.', 49.99, '49.99 € inkl. Mwst.', 'img/trainingsjackeSale.jpg', 'Nike', 'Schwarz', 'Herren', 'Sale %', 'Sportbekleidung', '[\"S\",\"M\",\"L\",\"XL\"]', '[\"img\\/trainingsjackeSale.jpg\"]'),
(25, 'Adidas Predator Sale', 'Adidas Predator Fußballschuhe im Sale – Top-Performance zum Sonderpreis.', 99.99, '99.99 € inkl. Mwst.', 'img/predatorSale.jpg', 'Adidas', 'Schwarz-Rot', 'Unisex', 'Sale %', 'Fußballschuhe', '[40,41,42,43,44]', '[\"img\\/predatorSale.jpg\"]'),
(26, 'Puma Sporttasche Sale', 'Puma Sporttasche im Sale – praktisch und günstig.', 19.99, '19.99 € inkl. Mwst.', 'img/sporttascheSale.jpg', 'Puma', 'Blau', 'Unisex', 'Sale %', 'Zubehör', '[\"One Size\"]', '[\"img\\/sporttascheSale.jpg\"]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `product_stock`
--

CREATE TABLE `product_stock` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `stars` int(11) DEFAULT NULL CHECK (`stars` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','banned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `status`, `created_at`, `is_admin`) VALUES
(4, 'Mohamed', '$2y$10$FjEmKkdNyJCyAEUK6TX69OvkNlgDDyNrBCc/lRGHGJ.JFD.RcXn/S', 'raoufmohamed0605@gmail.com', 'user', 'active', '2025-06-19 05:25:34', 1),
(5, 'Tesst', '$2y$10$FNGd4iTxwJL6ASquZOYQHeumtXMudWSNzaWAePQLOQIEi.CnXbihm', 'test@gmail.com', 'user', 'active', '2025-06-19 17:17:21', 0),
(6, 'Hussein Alsumat', '$2y$10$jI90/XYaeA2dxHwz3e.SV.X1/UqcD78kRVvjvfbKI3FwrJE1GA8Jy', 'hua1999@thi.de', 'user', 'active', '2025-06-19 18:23:13', 1),
(7, 'TestAcc', '$2y$10$PhfbvSdofqZzX7667Oz8yOwfUxsUPe5DVhHmCcqo5bk8xESLjrHQe', 'Test.Acc@gmail.com', 'user', 'active', '2025-06-21 17:39:31', 0),
(8, 'Test2', '$2y$10$UgWj3cohujAwXYi1TYH1L.ehXo3aF1KQVsDAQDjqWQgGLKboKRFcS', 'Test2@gmail.com', 'user', 'active', '2025-06-22 17:50:22', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `watchlists`
--

CREATE TABLE `watchlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `watchlists`
--

INSERT INTO `watchlists` (`id`, `user_id`, `created_at`) VALUES
(2, 6, '2025-06-22 17:02:14'),
(3, 8, '2025-06-22 17:55:56');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `watchlist_items`
--

CREATE TABLE `watchlist_items` (
  `id` int(11) NOT NULL,
  `watchlist_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `watchlist_items`
--

INSERT INTO `watchlist_items` (`id`, `watchlist_id`, `product_id`, `created_at`) VALUES
(74, 3, 1, '2025-06-22 21:16:36'),
(245, 2, 1, '2025-06-23 14:19:09');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_ibfk_1` (`user_id`);

--
-- Indizes für die Tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indizes für die Tabelle `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indizes für die Tabelle `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `watchlists`
--
ALTER TABLE `watchlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `watchlist_items`
--
ALTER TABLE `watchlist_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `watchlist_id` (`watchlist_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `watchlists`
--
ALTER TABLE `watchlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `watchlist_items`
--
ALTER TABLE `watchlist_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints der Tabelle `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `product_stock`
--
ALTER TABLE `product_stock`
  ADD CONSTRAINT `product_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `watchlists`
--
ALTER TABLE `watchlists`
  ADD CONSTRAINT `watchlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `watchlist_items`
--
ALTER TABLE `watchlist_items`
  ADD CONSTRAINT `watchlist_items_ibfk_1` FOREIGN KEY (`watchlist_id`) REFERENCES `watchlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watchlist_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
