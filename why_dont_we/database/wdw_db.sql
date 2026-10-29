-- ============================================================================
-- wdw_db — full database for the Why Don't We site (structure + content).
-- Import in phpMyAdmin (Import tab) or: mysql -u root < database/wdw_db.sql
-- The admins table is exported without rows; create your own admin account.
-- ============================================================================


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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `wdw_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `wdw_db`;
DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `type` enum('album','ep','single') NOT NULL DEFAULT 'album',
  `release_year` year(4) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `spotify_url` varchar(255) DEFAULT NULL,
  `apple_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` VALUES (1,'8 Letters','album',2018,'assets/image/8letters.jpg','https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2','https://music.apple.com/us/artist/why-dont-we/1161814111','http://www.youtube.com/@whydontwe8155'),(2,'Let Me Down Easy (Lie)','single',2018,'assets/image/flower.jpg',NULL,NULL,NULL),(3,'How Do You Love Somebody','single',2017,'assets/image/HDYLS-new.jpg',NULL,NULL,NULL),(4,'Just Friends','single',2019,'assets/image/PRESAVE-COVER_1_0.jpg',NULL,NULL,NULL),(5,'The Good Times and the Bad Ones','album',2017,'assets/image/WDW COVER FINAL (1) (1).jpg',NULL,NULL,NULL),(6,'Love Back','single',2016,'assets/image/lovebackcover1_0.jpg',NULL,NULL,NULL),(7,'Fallin\'','single',2016,'assets/image/fallin.png',NULL,NULL,NULL),(8,'These Girls','single',2019,'assets/image/these-girl.png',NULL,NULL,NULL),(9,'Trust Fund Baby','single',2018,'assets/image/trust-fund-baby.png',NULL,NULL,NULL),(10,'Why Don\'t We Just','single',2020,'assets/image/why-dont-we-just.png',NULL,NULL,NULL),(11,'Unfinished Business','album',2026,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `concerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `concerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `venue_name` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `region` enum('Asia','America','Europe','Other') NOT NULL,
  `event_date` datetime NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','coming_soon','sold_out') NOT NULL DEFAULT 'coming_soon',
  PRIMARY KEY (`id`),
  KEY `idx_concert_date` (`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `concerts` WRITE;
/*!40000 ALTER TABLE `concerts` DISABLE KEYS */;
INSERT INTO `concerts` VALUES (1,'Jakarta Convention Center (JCC)','Jakarta Pusat','Indonesia','Asia','2026-08-12 19:00:00','assets/image/why-dont-we-just.png','available'),(2,'Sasana Budaya Ganesha (Sabuga)','Bandung','Indonesia','Asia','2027-02-13 19:00:00','assets/image/why-dont-we-just.png','available'),(3,'Singapore Indoor Stadium','Singapore','Singapore','Asia','2027-03-06 19:30:00','assets/image/why-dont-we-just.png','coming_soon'),(4,'Axiata Arena','Kuala Lumpur','Malaysia','Asia','2027-02-20 20:00:00','assets/image/why-dont-we-just.png','sold_out'),(5,'Istora Senayan','Jakarta','Indonesia','Asia','2025-03-15 19:00:00','assets/image/why-dont-we-just.png','sold_out');
/*!40000 ALTER TABLE `concerts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `limelight_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limelight_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `favorite_member_id` int(10) unsigned DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `favorite_member_id` (`favorite_member_id`),
  CONSTRAINT `limelight_members_ibfk_1` FOREIGN KEY (`favorite_member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `limelight_members` WRITE;
/*!40000 ALTER TABLE `limelight_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `limelight_members` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `member_trivia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_trivia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `content` varchar(255) NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `member_trivia_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `member_trivia` WRITE;
/*!40000 ALTER TABLE `member_trivia` DISABLE KEYS */;
INSERT INTO `member_trivia` VALUES (1,1,'Can play more than 20 musical instruments.',1),(2,1,'Huge fan of The Beatles and Ed Sheeran.',2),(3,1,'Has a pet dog named Kobe.',3),(4,3,'Has a daughter named Lavender May Avery.',1),(5,3,'Initially more interested in basketball before focusing on music.',2),(6,3,'Can play the piano and guitar.',3),(7,4,'Huge fan of Harry Potter.',1),(8,4,'His father is a musician and music teacher.',2),(9,4,'Has a habit of drinking coffee every morning.',3),(10,5,'Joined the school choir before becoming famous.',1),(11,5,'Cites Justin Bieber and Shawn Mendes as his inspirations.',2),(12,5,'Released his debut solo single \"Timelapse\" in 2016.',3),(16,2,'Built his first fanbase on YouNow and YouTube.',1),(17,2,'Plays guitar and piano.',2),(18,2,'The only member from the East Coast of the US.',3);
/*!40000 ALTER TABLE `member_trivia` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `roles` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `zodiac` varchar(20) DEFAULT NULL,
  `hometown` varchar(100) DEFAULT NULL,
  `extra_label` varchar(50) DEFAULT NULL,
  `extra_value` varchar(150) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `hero_photo` varchar(255) DEFAULT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'daniel','Daniel Seavey','Vocals · Guitar · Producer','1999-04-02','Aries','Portland, Oregon','Instruments','Guitar, Cello, Piano','Daniel James Seavey is the main singer, songwriter, and producer of the group. He first gained public attention as a contestant on American Idol season 14 at the age of 15.','assets/image/daniel.jpg','assets/image/seavey-daniel-image.jpg',1),(2,'corbyn','Corbyn Besson','Vocals · Guitar','1998-11-25','Sagittarius','Fairfax, Virginia','Instruments','Guitar, Piano','Corbyn Matthew Besson grew up in Fairfax, Virginia. Before Why Don\'t We he built a following by livestreaming and posting song covers online, and in 2016 he joined the band as one of its five vocalists.','assets/image/corbyn.jpg','assets/image/besson-corbyn-image.jpg',2),(3,'jack','Jack Avery','Vocals · Guitar','1999-07-01','Cancer','Burbank, California','Skills','Skateboarding','Jack Robert Avery started his career by uploading song covers on YouTube and participating in the IMPACT music tour before joining Why Don\'t We.','assets/image/jack.jpg','assets/image/avery-jack-image.jpg',3),(4,'jonah','Jonah Marais','Vocals · Keys','1998-06-16','Gemini','Stillwater, Minnesota','Sports','Baseball','Jonah Marais Roth Frantzich is the oldest member of the group. Like Corbyn, he also gained early popularity through the YouNow app and previously released a solo EP titled \"When the Daylight\'s Gone\".','assets/image/jonah.jpg','assets/image/marais-jonah-image.jpg',4),(5,'zach','Zach Herron','Vocals','2001-05-27','Gemini','Dallas, Texas','Position','Youngest Member (Maknae)','Zachary Dean Herron is the youngest member of Why Don\'t We. He went viral on social media after a video of him singing Shawn Mendes\'s \"Stitches\" at school spread widely.','assets/image/zach.jpg','assets/image/herron-zach-image.jpg',5);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `content` mediumtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `read_time_min` tinyint(3) unsigned DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `idx_news_published` (`published_at`),
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,1,'album','New Album Drops This Summer — Full Details','Unfinished Business, a 14-track collection, arrives on all major streaming platforms on August 22, 2026.','<p>\r\nWhy Don\'t We is officially ready to share what they have been working on behind\r\nclosed studio doors for the past eighteen months. The band confirmed today that\r\ntheir highly anticipated new album will drop this summer, and the details are\r\nevery bit as exciting as fans had hoped.\r\n</p>\r\n<h2>The Album</h2>\r\n<p>\r\nTitled <em>Unfinished Business</em>, the record is a 14-track collection that\r\nthe group describes as their most personal work yet. Written and produced largely\r\nby the five members themselves, it moves between the polished pop-rock sound that\r\nput them on the map and a rawer, more acoustic-driven direction they have been\r\nquietly developing since their break.\r\n</p>\r\n<blockquote>\r\n\"We went into this one with zero expectations and just wrote what felt true.\r\nThat freedom changed everything.\" — Jonah Marais\r\n</blockquote>\r\n<h2>Release Date &amp; Pre-Save</h2>\r\n<p>\r\n<em>Unfinished Business</em> arrives on all major streaming platforms on\r\n<strong>August 22, 2026</strong>. A physical deluxe edition — featuring a 48-page\r\nbooklet, two bonus tracks, and a poster — is available for pre-order now through\r\nthe official store.\r\n</p>\r\n<h2>Lead Singles</h2>\r\n<p>\r\nThe album\'s lead single, <em>Come Around</em>, is already climbing the charts after\r\nits surprise release last month. A second single is scheduled to drop two weeks\r\nbefore the album, so keep notifications on.\r\n</p>\r\n<p>\r\nPre-save <em>Unfinished Business</em> on Spotify and Apple Music now, and stay\r\ntuned to the band\'s socials for daily countdowns and exclusive track previews.\r\n</p>','assets/image/featured-news.png',3,1,'2026-05-15 09:00:00'),(2,1,'tour-dates','Tour Dates Revealed','The 2026 North American headline tour — 32 cities, three months, and the biggest production the band has ever brought to the road.','<p>\r\nThe wait is over. Why Don\'t We has officially announced their 2026 North American\r\nheadline tour — 32 cities, three months, and what promises to be the biggest\r\nproduction the band has ever brought to the road.\r\n</p>\r\n<h2>Full Tour Routing</h2>\r\n<p>\r\nThe <strong>Unfinished Business Tour</strong> kicks off on <strong>September 5</strong>\r\nin Seattle, WA and wraps on <strong>November 28</strong> in Miami, FL. Major stops\r\ninclude New York, Los Angeles, Chicago, Dallas, and Toronto.\r\n</p>\r\n<blockquote>\r\n\"We have been dying to get back out there. This tour is going to feel like one long\r\nreunion with everyone who has stuck with us.\" — Jack Avery\r\n</blockquote>\r\n<h2>Tickets</h2>\r\n<p>\r\nFan club pre-sale opens <strong>May 20 at 10 AM local time</strong>. General on-sale\r\nfollows on <strong>May 22</strong> via all major ticketing platforms. VIP packages —\r\nincluding soundcheck access, a meet-and-greet, and exclusive merchandise —\r\nare available in limited quantities.\r\n</p>\r\n<h2>Support Acts</h2>\r\n<p>\r\nSupport acts will be announced on a city-by-city basis over the coming weeks.\r\nFollow the band\'s Instagram and Twitter for the latest updates as each date\r\nis confirmed.\r\n</p>\r\n<p>\r\nFull dates and venue listings are available on the <a href=\"tour.html\">Tour page</a>. Don\'t sleep on tickets —\r\nseveral markets sold out within hours during the last run.\r\n</p>','assets/image/hdylsthumb-5_0.jpg',3,0,'2026-05-15 10:00:00'),(3,2,'festival','Summer Festival Appearance','Why Don\'t We will take the mainstage at Sunwave Festival 2026 this July.','<p>\r\nWhy Don\'t We will take the mainstage at <strong>Sunwave Festival 2026</strong> this\r\nJuly, marking their first major festival headline slot since the band\'s return.\r\nThe annual three-day event in Austin, Texas draws over 80,000 fans and has become\r\none of the premier pop music gatherings in the country.\r\n</p>\r\n<h2>The Set</h2>\r\n<p>\r\nThe band promises a career-spanning set blending fan favourites with fresh material\r\nfrom the upcoming album <em>Unfinished Business</em>. Expect a full production —\r\ncustom lighting rigs, live strings, and a few surprises the guys are keeping close\r\nto their chests.\r\n</p>\r\n<blockquote>\r\n\"Playing a festival is a different energy entirely. Fifty thousand people singing\r\nback your lyrics all at once — there is nothing like it.\" — Corbyn Besson\r\n</blockquote>\r\n<h2>Festival Details</h2>\r\n<p>\r\nSunwave Festival runs <strong>July 18–20, 2026</strong> at Zilker Park, Austin, TX.\r\nWhy Don\'t We headlines the main stage on <strong>Saturday, July 19</strong> at 9 PM CT.\r\nThe full lineup features over 60 artists across four stages.\r\n</p>\r\n<h2>Tickets &amp; Travel</h2>\r\n<p>\r\nSingle-day and three-day passes are available now at the Sunwave Festival website.\r\nCamping packages and shuttle passes can also be added at checkout. The event\r\nrecommends booking accommodation early, as Austin hotels fill up quickly during\r\nfestival weekend.\r\n</p>','assets/image/jfthumbnail.jpg',4,0,'2026-05-18 09:00:00'),(4,3,'studio','Exclusive Studio Session','Rare access to the Los Angeles studio where the band is finishing their new record.','<p>\r\nWe were given rare access to the Los Angeles studio where Why Don\'t We has been\r\nputting the finishing touches on their new record. What we found was five friends\r\nwho have grown into genuine artists — confident, collaborative, and completely\r\nin sync.\r\n</p>\r\n<h2>Inside the Room</h2>\r\n<p>\r\nThe session started at noon and ran past midnight. Daniel Seavey sat behind a\r\ngrand piano in the corner, working through chord progressions with a notebook\r\nopen beside him. Across the room Zach Herron and Jonah Marais traded verses on\r\nacoustic guitars, stopping occasionally to replay lines into a voice memo app.\r\nThe vibe was relaxed — almost domestic — broken only by bursts of laughter when\r\na harmony landed perfectly on the first try.\r\n</p>\r\n<blockquote>\r\n\"We are writing for ourselves this time. Not for a demographic or a release\r\nwindow — just for the five of us in this room.\" — Daniel Seavey\r\n</blockquote>\r\n<h2>The Sound</h2>\r\n<p>\r\nProducer Evan Blair, who helmed the session, described the album\'s sonic palette\r\nas \"open-air pop\" — big choruses with space left inside them. Live drums, real\r\nbass, and analogue synths sit beneath polished vocals that the band tracked mostly\r\ntogether, live in the room, to capture a sense of energy that overdubs rarely\r\nreplicate.\r\n</p>\r\n<h2>What\'s Next</h2>\r\n<p>\r\nMixing wraps this month, with mastering scheduled for early July ahead of the\r\nAugust release. The band will share a studio diary series on their YouTube channel\r\nleading up to the drop — subscribe now so you don\'t miss an episode.\r\n</p>','assets/image/flower.jpg',5,0,'2026-05-22 09:00:00'),(5,4,'video','New Music Video Released','The official video for Come Around passed 4 million views in its first 48 hours.','<p>\r\nThe official music video for Why Don\'t We\'s latest single <em>Come Around</em>\r\nis now live on YouTube, and it has already surpassed 4 million views in its first\r\n48 hours — making it the fastest-viewed video in the band\'s catalogue.\r\n</p>\r\n<h2>The Concept</h2>\r\n<p>\r\nDirected by Malia James, the video unfolds across a series of split-screen\r\nvignettes that mirror the song\'s theme of parallel lives barely missing each\r\nother. Shot over two days in downtown Los Angeles and the Malibu coast, it trades\r\nthe high-gloss visuals of earlier releases for a warmer, more intimate look —\r\nall golden-hour light and handheld camera work.\r\n</p>\r\n<blockquote>\r\n\"We wanted the video to feel like a memory you can\'t quite hold onto.\r\nMalia captured exactly that.\" — Zach Herron\r\n</blockquote>\r\n<h2>Behind the Scenes</h2>\r\n<p>\r\nA behind-the-scenes documentary of the shoot is available exclusively on the\r\nband\'s Patreon. Members get an uncut 22-minute look at how the split-screen\r\nsequences were choreographed and a director\'s commentary track recorded the\r\nmorning after the wrap.\r\n</p>\r\n<h2>Watch Now</h2>\r\n<p>\r\nThe <em>Come Around</em> music video is streaming now on YouTube. Hit the like\r\nbutton and share it with a friend who needs to hear this song — every stream\r\nhelps the album chart when it drops in August.\r\n</p>','assets/image/Polaroid5_0.png',4,0,'2026-05-25 09:00:00'),(6,5,'merch','Limited Edition Merch','A limited-edition collection is out now, exclusively through the official store.','<p>\r\nTo celebrate the album announcement, Why Don\'t We has dropped a limited-edition\r\nmerch collection exclusively through the official store — and quantities are\r\nextremely limited. If you want a piece, move fast.\r\n</p>\r\n<h2>What\'s in the Drop</h2>\r\n<p>\r\nThe collection includes a heavyweight pullover hoodie in midnight navy, a\r\nwashed-cotton crewneck in faded sage, a short-sleeve graphic tee featuring\r\noriginal artwork by LA-based illustrator Cara Rios, and a enamel pin set.\r\nEach item is screen-printed by hand in small batches in Los Angeles.\r\n</p>\r\n<blockquote>\r\n\"We wanted merch that feels like something you actually want to wear, not\r\njust a souvenir. Cara\'s artwork made that possible.\" — Corbyn Besson\r\n</blockquote>\r\n<h2>Album Bundle</h2>\r\n<p>\r\nBundle any merch item with the deluxe physical album pre-order and save 15%.\r\nBundles also include a signed lyric card — hand-signed by all five members —\r\nwhile supplies last.\r\n</p>\r\n<h2>How to Order</h2>\r\n<p>\r\nThe collection is live now at the official store. Items ship worldwide.\r\nOrders placed before June 10 will arrive before the album release date.\r\nHead to the <a href=\"merch.html\">Merch page</a>\r\nto grab yours before they sell out.\r\n</p>','assets/image/hoodie.png',2,0,'2026-05-28 09:00:00'),(7,6,'international-tour','International Tour Begins','The tour heads to Europe, Asia, and Latin America for the first time since 2019.','<p>\r\nFollowing the sold-out North American run, Why Don\'t We has confirmed an\r\ninternational leg that will take them to Europe, Asia, and Latin America\r\nfor the first time since 2019. The global extension of the\r\n<strong>Unfinished Business Tour</strong> runs from December 2026 through\r\nMarch 2027.\r\n</p>\r\n<h2>European Dates</h2>\r\n<p>\r\nThe European leg opens on <strong>December 5 in London</strong> at the O2 Arena,\r\nfollowed by dates in Paris, Amsterdam, Berlin, Madrid, and Stockholm. The band\r\nwill also make their first-ever appearance in Dublin and Warsaw before wrapping\r\nEurope on December 30 in Milan.\r\n</p>\r\n<blockquote>\r\n\"International fans have been waiting a long time and so have we.\r\nWe cannot wait to finally be in the same room as you.\" — Jack Avery\r\n</blockquote>\r\n<h2>Asia-Pacific</h2>\r\n<p>\r\nJanuary brings the band to the Asia-Pacific region, with confirmed shows in\r\nTokyo, Seoul, Manila, Jakarta, Singapore, and Sydney. The Southeast Asia leg\r\nalone spans 8 cities across 3 weeks — the most comprehensive run the band\r\nhas ever done in the region.\r\n</p>\r\n<h2>Latin America</h2>\r\n<p>\r\nThe tour closes out in Latin America through February and March, hitting\r\nSão Paulo, Buenos Aires, Bogotá, Mexico City, and Santiago. Fan-club\r\npre-sale for all international dates opens <strong>June 10</strong>.\r\nGeneral on-sale follows on <strong>June 14</strong>.\r\n</p>\r\n<p>\r\nFull international dates are listed on the <a href=\"tour.html\">Tour page</a>. This one is going to be\r\nspecial — do not miss your city.\r\n</p>','assets/image/something-different.png',4,0,'2026-06-01 09:00:00');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `news_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `news_categories` WRITE;
/*!40000 ALTER TABLE `news_categories` DISABLE KEYS */;
INSERT INTO `news_categories` VALUES (1,'Announcement'),(2,'Event'),(3,'Interview'),(5,'Merch'),(4,'Music'),(6,'Tour');
/*!40000 ALTER TABLE `news_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `collection` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Bracelet','Covid-19 Collection',65.00,'assets/image/gelang.png',1,1),(2,'Limited Edition Hoodie','New Era Collection',65.00,'assets/image/hoodie.png',1,1),(3,'Black T-Shirt','New Era Collection',65.00,'assets/image/—Pngtree—black t-shirt_5609353.png',0,1),(4,'Dark Green T-Shirt','New Era Collection',65.00,'assets/image/—Pngtree—plain dark green t shirt_14667981.png',0,1),(5,'Seafoam Green T-Shirt','New Era Collection',65.00,'assets/image/vecteezy_seafoam-green-short-sleeve-crew-neck-tshirt-mockup-for_59901009.png',0,1),(6,'Cowboy Hat','New Era Collection',65.00,'assets/image/vecteezy_ai-generated-cowboy-hat-png_34616681.png',0,1),(7,'Socks','New Era Collection',65.00,'assets/image/kaoskaki.png',0,1);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `songs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `duration_sec` smallint(5) unsigned DEFAULT NULL,
  `audio_file` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `lyrics` text DEFAULT NULL,
  `spotify_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `album_id` (`album_id`),
  CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `songs` WRITE;
/*!40000 ALTER TABLE `songs` DISABLE KEYS */;
INSERT INTO `songs` VALUES (1,1,'8 Letters',214,'assets/audio/wdw.mp3','assets/image/8letters.jpg','You know my worst, see me hurt, but you don\'t judge\r\nThat right there is the scariest feeling\r\nOpening and closing up again\r\nI\'ve been hurt, so I don\'t trust\r\nNow here we are, staring at the ceiling\r\n\r\nI\'ve said those words before, but it was a lie\r\nAnd you deserve to hear them a thousand times\r\n\r\nIf all it is is eight letters\r\nWhy is it so hard to say?\r\nIf all it is is eight letters\r\nWhy am I in my own way?\r\nWhy do I pull you close?\r\nAnd then ask you for space\r\nIf all it is is eight letters\r\nWhy is it so hard to say?\r\n\r\nIsn\'t it amazing how almost every line on our hands align?\r\nWhen your hand\'s in mine\r\nIt\'s like I\'m whole again, isn\'t that a sign?\r\nI should speak my mind\r\n\r\nI\'ve said those words before, but it was a lie\r\nAnd you deserve to hear them a thousand times\r\n\r\nIf all it is is eight letters\r\nWhy is it so hard to say?\r\nIf all it is is eight letters\r\nWhy am I in my own way?\r\nWhy do I pull you close?\r\nAnd then ask you for space\r\nIf all it is is eight letters\r\nWhy is it so hard to say? (Woah, oh yeah)\r\n\r\nWhen I close my eyes\r\nIt\'s you there in my mind\r\nWhen I close my eyes\r\n\r\nIf all it is is eight letters\r\nWhy is it so hard to say?\r\nIf all it is is eight letters\r\nWhy am I in my own way?\r\nWhy do I pull you close?\r\nAnd then ask you for space\r\nIf all it is is eight letters\r\nWhy is it so hard to say?\r\nIf all it is is eight letters\r\n\r\nWhen I close my eyes\r\nIt\'s you there in my mind (it\'s only you)\r\nWhen I close my eyes\r\n\r\nIf all it is is eight letters\r\n\r\nWhen I close my eyes\r\nIt\'s you there in my mind (you)\r\nWhen I close my eyes\r\n\r\nIf all it is is eight letters','https://open.spotify.com/track/4zRZAmBQP8vhNPf9i9opXt',1),(2,1,'Big Plans',171,NULL,NULL,NULL,NULL,1),(3,1,'Chills',198,NULL,NULL,NULL,NULL,1),(4,7,'Fallin\'',166,'assets/audio/Why Don\'t We - Fallin\' (Adrenaline) [Official Music Video].mp3','assets/image/fallin.png','Caught in the moment, not even thinkin\' twice\r\nEverything\'s frozen, nothing but you and I\r\nCan\'t stop my heart from beating\r\nWhy do I love this feeling?\r\nMake me a promise, tell me you\'ll stay with me\r\nIf I\'m bein\' honest, I don\'t know where this leads\r\nBut that\'s the only question\r\nBaby, don\'t keep me guessin\'\r\n\r\nOoh\r\nYou are my muse\r\nI feel so reckless\r\nOh, you\'re makin\' me, makin\' me, makin\' me give in\r\n\r\nOh, baby, I can feel the rush of adrenalin\r\nI\'m not scared to jump if you want to\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\'\r\nI can feel the rush of adrenaline\r\nI\'m not scared to jump \'cause I want you\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\'\r\n\r\nWhen I\'m around ya, I feel it in my veins\r\nThere\'s something about ya that\'s makin\' me go insane\r\nWe have a storm to weather\r\nMy little sweet surrender\r\n\r\nOoh (Woah)\r\nYou are my muse (You are my muse)\r\nI feel so reckless\r\nOh, you\'re makin\' me, makin\' me, makin\' me give in\r\n\r\nOh, baby, I can feel the rush of adrenaline\r\nI\'m not scared to jump if you want to\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\'\r\nI can feel the rush of adrenaline\r\nI\'m not scared to jump \'cause I want you\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\'\r\n\r\n(Fallin\', fallin\', fallin\', fallin\') Fall, fallin\'\r\n(Fallin\', fallin\', fallin\', fallin\') Fallin\'\r\n(Fallin\', fallin\', fallin\', fallin\') Fallin\'\r\nMaybe, we\'ll just keep fallin\'\r\n\r\nOoh\r\nYou are my muse\r\nI feel so reckless\r\nOh, you\'re makin\' me, makin\' me, makin\' me, makin\' me\r\nMakin\' me, makin\' me, makin\' me give in\r\n\r\nOh, baby, I can feel the rush of adrenaline\r\nI\'m not scared to jump if you want to\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\' (Just keep fallin\')\r\nI can feel the rush of adrenaline (Adrenaline, ooh)\r\nI\'m not scared to jump \'cause I want you (I want you)\r\nLet\'s just fall in love for the hell of it\r\nMaybe we\'ll just keep fallin\' (We\'ll just keep fallin\')\r\n\r\n(Fallin\', fallin\', fallin\', fallin\') Yeah\r\n(Fallin\', fallin\', fallin\', fallin\') We\'ll just keep fallin\'\r\n(Fallin\', fallin\', fallin\', fallin\') Ooh, ooh\r\nMaybe we\'ll just keep fallin\'',NULL,1),(5,NULL,'What Am I',NULL,'assets/audio/Why Don\'t We - What Am I [Official Video].mp3','assets/image/album2.jpg','I met her on a Monday evening\r\nShe was standing outside of the park and ride\r\nAnd I said, \"Hey ma, where you been?\"\r\n\"Been looking for a woman like you for all my life\"\r\nAnd she said, \"Hola, talk to me, before I have to leave\"\r\nAnd one day we could be\r\nMaybe more than two lost souls just passing by\r\n\r\nKnew I was falling when I looked inside your eyes\r\nShe said, \"I know you are, but what am I?\"\r\n\r\nTell me, have you seen a sunset\r\nTurn into a sunrise?\r\nKiss right through the night?\r\n\'Cause we should try that sometime\r\nHold you \'til the mornin\'\r\nAnd if I said I\'m fallin\', would you just reply\r\n\"I know you are, but what am I?\"\r\n\r\n(Ah-ooh, ah-ooh)\r\n(Ah-ooh, ah-ooh)\r\n\r\nOpen the door to my apartment, when we get in\r\nKissin\' on my neck and throw my jacket to the side\r\nAnd I know your heart is beating quick\r\nAnd if you put your head on my chest, hear the same in mine, yeah\r\nAnd we\'ll be making love\r\nUntil the sun comes up\r\nBut one day maybe I\r\nI could put a ring on your finger before you change your mind, yeah\r\n\r\nKnew I was falling when I looked inside your eyes\r\nShe said, \"I know you are, but what am I?\"\r\n\r\nTell me, have you seen a sunset\r\nTurn into a sunrise?\r\nKiss right through the night?\r\n\'Cause we should try that sometime\r\nHold you \'til the mornin\'\r\nAnd if I said I\'m fallin\', would you just reply\r\n\"I know you are, but what am I?\"\r\n\r\n(Ooh-ooh, ooh-ooh)\r\nWhat am I?\r\n(Ah-ooh, ah-ooh)\r\n\r\nTell me, have you seen a sunset\r\nTurn into a sunrise?\r\nKiss right through the night?\r\n\'Cause we should try that sometime\r\nHold you \'til the mornin\'\r\nAnd if I said I\'m fallin\', would you just reply\r\n\"I know you are, but what am I?\"\r\n\r\nTell me, have you seen a sunset (Yeah)\r\nTurn into a sunrise?\r\nKiss right through the night? (The night)\r\n\'Cause we should try that sometime (Try that sometime)\r\nHold you \'til the mornin\' (Hold you \'til the morning)\r\nAnd if I said I\'m fallin\', would you just reply\r\n\"I know you are, but what am I?\"\r\n\r\nWhat am I?\r\nWhat am I?\r\nOh, what am I?\r\nWhat am I?\r\nWhat am I?\r\n\r\nWhat am I?',NULL,1),(6,9,'Trust Fund Baby',NULL,'assets/audio/Trust Fund Baby - Why Don\'t We [Official Music Video].mp3','assets/image/trust-fund-baby.png','I don\'t want a girl who gets a car for her sweet 16\r\nOr spends a stack of dollar bills on a limousine\r\nI want a girl who takes the bus and who wears baggy jeans\r\nRockin\' Nike Airs, what the hell are Louboutins?\r\nDon\'t want no fake tan, short skirt, daddy\'s money don\'t work\r\nShop until you drop on the town\r\nI want a smart girl, stronger than her father\r\nSomeone who will laugh and tryna fit in the crowd (ah-ow, ow)\r\n\r\nAnd all we used to dream about\r\nIs getting rich and getting out\r\nMove to the nicer part of town\r\nWhere we\'d have numbers on our house\r\nIt took a while to figure out\r\nWhat type of girl that I\'m about\r\nWho brings the real man out of me, yah\r\n\r\nI don\'t really want no trust fund baby\r\nI like my women independent\r\nAnd I say to people, \"That\'s my lady\"\r\nAnd we don\'t need nothing e-e-e-else\r\nI don\'t want no trust fund baby\r\nSave your money, don\'t spend it\r\nAnd I say to people, \"That\'s my lady\"\r\nAnd we don\'t need nothing e-e-e-else (I don\'t want no-)\r\n\r\nDon\'t want a girl who take selfies, want her makeup free\r\nDon\'t want no mean girl lady, a pink prom Queen\r\nI want a girl who climbs trees, always dirt on her jeans\r\nHer daddy told her how to fix cars, maybe she could fix me\r\nI want a throwback kid who loves Missy Elliot\r\nWho for my birthday, makes a mixtape and puts it on cassette\r\nWant a girl with common sense, who\'s dripping in competence\r\nDon\'t wanna die to get rich, but she loves 50 Cent (ah-ow, ow)\r\n\r\nAnd all we used to dream about\r\nIs getting rich and getting out\r\nMove to the nicer part of town\r\nWhere we\'d have numbers on our house\r\nIt took a while to figure out\r\nWhat type of girl that I\'m about\r\nWho brings the real man out of me, yah\r\n\r\nI don\'t really want no trust fund baby\r\nI like my women independent\r\nAnd I say to people, \"That\'s my lady\"\r\nAnd we don\'t need nothing e-e-e-else\r\nI don\'t want no trust fund baby\r\nSave your money, don\'t spend it\r\nAnd I say to people, \"That\'s my lady\"\r\nAnd we don\'t need nothing e-e-e-else\r\n\r\nI don\'t want no- (no, I don\'t want me)\r\nI don\'t want no trust fund baby (no, I don\'t want me)\r\nI just want me a proper lady (no, I don\'t want me)\r\nI don\'t want no trust fund baby (no, I don\'t want me)\r\n\r\nI don\'t really want no trust fund baby\r\nI like my women independent (hey!)\r\nAnd I say to people, \"That\'s my lady\" (that\'s my lady, na-na-na-na-na)\r\nAnd we don\'t need nothing e-e-e-else\r\n\r\nI don\'t want no- (no, I don\'t want me)\r\nI don\'t want no trust fund baby (no, I don\'t want me)\r\nI just want me a proper lady (no, I don\'t want me)\r\nI don\'t want no trust fund baby (no, I don\'t want me)\r\n\r\nNo, I don\'t want me',NULL,1),(7,11,'Come Around',NULL,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `songs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `source` enum('home','tour','news') NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `concert_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `quota` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_concert_category` (`concert_id`,`name`),
  CONSTRAINT `ticket_categories_ibfk_1` FOREIGN KEY (`concert_id`) REFERENCES `concerts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ticket_categories` WRITE;
/*!40000 ALTER TABLE `ticket_categories` DISABLE KEYS */;
INSERT INTO `ticket_categories` VALUES (1,1,'Regular','Standing area access, official merch discount voucher.',50.00,2000),(2,1,'VIP','Front-row standing area, exclusive merch pack, meet & greet pass.',100.00,200),(3,2,'Regular','Standing area access, official merch discount voucher.',50.00,1500),(4,2,'VIP','Front-row standing area, exclusive merch pack, meet & greet pass.',100.00,150);
/*!40000 ALTER TABLE `ticket_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ticket_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL,
  `ticket_category_id` int(10) unsigned NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `quantity` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('paid','cancelled') NOT NULL DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `ticket_category_id` (`ticket_category_id`),
  KEY `idx_ticket_email` (`email`),
  CONSTRAINT `ticket_orders_ibfk_1` FOREIGN KEY (`ticket_category_id`) REFERENCES `ticket_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ticket_orders` WRITE;
/*!40000 ALTER TABLE `ticket_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_orders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `youtube_id` varchar(20) NOT NULL,
  `start_second` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
INSERT INTO `videos` VALUES (1,'Video 1','C3DlM19x4RQ',108),(2,'Video 2','ZxgbQz4n8-s',108),(3,'Video 3','fOmhbMXY52o',108),(4,'Video 4','vgx-R_4s12U',108),(5,'Video 5','Y15MOr9saxE',108),(6,'Video 6','vJF9kJxQpBY',108),(7,'Video 7','5qUfjqksDic',108),(8,'Video 8','Oy8H4zUxWjg',108);
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


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
DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

