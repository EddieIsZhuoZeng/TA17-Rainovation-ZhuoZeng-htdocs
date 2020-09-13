-- MySQL dump 10.13  Distrib 8.0.21, for macos10.15 (x86_64)
--
-- Host: 192.168.33.132    Database: ie
-- ------------------------------------------------------
-- Server version	8.0.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` VALUES (4,'2020_09_12_063135_create_trees_table',1);
INSERT INTO `migrations` VALUES (7,'2020_09_12_063158_create_small_plants_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `small_plants`
--

DROP TABLE IF EXISTS `small_plants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `small_plants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latin_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `common_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moisture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medicinal` int DEFAULT NULL,
  `habit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `small_plants`
--

LOCK TABLES `small_plants` WRITE;
/*!40000 ALTER TABLE `small_plants` DISABLE KEYS */;
INSERT INTO `small_plants` VALUES (1,NULL,NULL,'Allium ampeloprasum','Wild Leek, Broadleaf wild leek','M',3,'Bulb',0.10);
INSERT INTO `small_plants` VALUES (2,NULL,NULL,'Allium canadense mobilense','Canadian Garlic','We',2,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (3,NULL,NULL,'Allium canadense mobilense','Canadian Garlic','M',2,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (4,NULL,NULL,'Allium cernuum','Nodding Onion, New Mexican nodding onion','M',2,'Bulb',0.30);
INSERT INTO `small_plants` VALUES (5,NULL,NULL,'Allium fistulosum','Welsh Onion','M',2,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (6,NULL,NULL,'Allium neapolitanum','Daffodil Garlic, White garlic','M',2,'Bulb',0.10);
INSERT INTO `small_plants` VALUES (7,NULL,NULL,'Allium paradoxum','Few-Flowered Leek','M',2,'Bulb',0.10);
INSERT INTO `small_plants` VALUES (8,NULL,NULL,'Allium sativum','Garlic, Cultivated garlic','M',5,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (9,NULL,NULL,'Allium sativum ophioscorodon','Serpent Garlic','M',5,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (10,NULL,NULL,'Allium schoenoprasum','Chives, Wild chives, Flowering Onion','M',2,'Bulb',0.30);
INSERT INTO `small_plants` VALUES (11,NULL,NULL,'Allium schoenoprasum sibiricum','Giant Chives','M',2,'Bulb',0.30);
INSERT INTO `small_plants` VALUES (12,NULL,NULL,'Allium tuberosum','Garlic Chives, Chinese chives, Oriental Chives,','M',2,'Bulb',0.30);
INSERT INTO `small_plants` VALUES (13,NULL,NULL,'Allium ursinum','Wild Garlic','M',3,'Bulb',0.30);
INSERT INTO `small_plants` VALUES (14,NULL,NULL,'Amelanchier alnifolia','Saskatoon, Saskatoon serviceberry, Serviceberry','M',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (15,NULL,NULL,'Amelanchier confusa',NULL,'M',0,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (16,NULL,NULL,'Amelanchier lamarckii','Apple Serviceberry','M',0,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (17,NULL,NULL,'Amelanchier x grandiflora','Apple Serviceberry','M',0,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (18,NULL,NULL,'Atriplex halimus','Sea Orach, Saltbush','M',1,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (19,NULL,NULL,'Camassia quamash','Quamash, Small camas, Utah small camas, Walpole\'s small camas','M',1,'Bulb',0.20);
INSERT INTO `small_plants` VALUES (20,NULL,NULL,'Caragana arborescens','Siberian Pea Tree, Siberian peashrub','M',1,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (21,NULL,NULL,'Cephalotaxus harringtonia','Japanese Plum Yew','M',0,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (22,NULL,NULL,'Cephalotaxus harringtonia drupacea','Japanese Plum Yew','M',0,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (23,NULL,NULL,'Cephalotaxus harringtonia koreana','Korean Plum Yew','M',0,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (24,NULL,NULL,'Cephalotaxus harringtonia nana','Japanese Plum Yew','M',0,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (25,NULL,NULL,'Chenopodium quinoa','Quinoa, Goosefoot, Pigweed, Inca Wheat','M',0,'Annual',0.30);
INSERT INTO `small_plants` VALUES (26,NULL,NULL,'Corylus maxima','Filbert, Giant filbert','M',0,'Shrub',5.00);
INSERT INTO `small_plants` VALUES (27,NULL,NULL,'Crataegus festiva',NULL,'M',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (28,NULL,NULL,'Crataegus festiva',NULL,'We',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (29,NULL,NULL,'Cucurbita maxima','Winter Squash','M',3,'Annual Climber',5.00);
INSERT INTO `small_plants` VALUES (30,NULL,NULL,'Cucurbita moschata','Squash, Crookneck squash','M',3,'Annual Climber',4.00);
INSERT INTO `small_plants` VALUES (31,NULL,NULL,'Elaeagnus cordifolia',NULL,'M',2,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (32,NULL,NULL,'Elaeagnus macrophylla',NULL,'M',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (33,NULL,NULL,'Elaeagnus multiflora','Goumi, Cherry silverberry','M',2,'Shrub',2.00);
INSERT INTO `small_plants` VALUES (34,NULL,NULL,'Elaeagnus multiflora ovata','Goumi','M',2,'Shrub',2.00);
INSERT INTO `small_plants` VALUES (35,NULL,NULL,'Elaeagnus pungens','Elaeagnus, Thorny olive, Thorny Elaeagnus, Oleaster, Silverberry, Silverthorn, Pungent Elaeagnus','M',2,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (36,NULL,NULL,'Elaeagnus x ebbingei','Elaeagnus, Ebbing\'s Silverberry','M',2,'Shrub',5.00);
INSERT INTO `small_plants` VALUES (37,NULL,NULL,'Gaultheria shallon','Shallon, Salal','M',2,'Shrub',1.00);
INSERT INTO `small_plants` VALUES (38,NULL,NULL,'Helianthus annuus','Sunflower, Common sunflower','M',2,'Annual',0.30);
INSERT INTO `small_plants` VALUES (39,NULL,NULL,'Hippophae rhamnoides','Sea Buckthorn, Seaberry','We',5,'Shrub',2.50);
INSERT INTO `small_plants` VALUES (40,NULL,NULL,'Hippophae rhamnoides','Sea Buckthorn, Seaberry','M',5,'Shrub',2.50);
INSERT INTO `small_plants` VALUES (41,NULL,NULL,'Hippophae rhamnoides turkestanica','Sea Buckthorn','M',5,'Shrub',2.50);
INSERT INTO `small_plants` VALUES (42,NULL,NULL,'Hippophae rhamnoides turkestanica','Sea Buckthorn','We',5,'Shrub',2.50);
INSERT INTO `small_plants` VALUES (43,NULL,NULL,'Lupinus mutabilis','Pearl Lupin, Tarwi','M',0,'Annual',0.30);
INSERT INTO `small_plants` VALUES (44,NULL,NULL,'Manihot esculenta','Cassava, Tapioca Plant, Yuca','M',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (45,NULL,NULL,'Oryza sativa','Rice, Common Rice','M',2,'Annual',0.30);
INSERT INTO `small_plants` VALUES (46,NULL,NULL,'Oryza sativa','Rice, Common Rice','We',2,'Annual',0.30);
INSERT INTO `small_plants` VALUES (47,NULL,NULL,'Oryza sativa','Rice, Common Rice','Wa',2,'Annual',0.30);
INSERT INTO `small_plants` VALUES (48,NULL,NULL,'Passiflora ligularis','Sweet Grenadilla, Passion Flower','M',0,'Climber',0.50);
INSERT INTO `small_plants` VALUES (49,NULL,NULL,'Ribes uva-crispa','Gooseberry, European gooseberry','M',1,'Shrub',1.00);
INSERT INTO `small_plants` VALUES (50,NULL,NULL,'Rosa rugosa','Ramanas Rose, Rugosa rose','M',2,'Shrub',2.00);
INSERT INTO `small_plants` VALUES (51,NULL,NULL,'Rubus fruticosus','Blackberry, Shrubby blackberry','M',3,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (52,NULL,NULL,'Rubus idaeus','Raspberry, American red raspberry, Grayleaf red raspberry','M',3,'Shrub',1.50);
INSERT INTO `small_plants` VALUES (53,NULL,NULL,'Rubus loganobaccus','Loganberry','M',0,'Shrub',2.50);
INSERT INTO `small_plants` VALUES (54,NULL,NULL,'Rubus nepalensis','Nepalese Raspberry','M',0,'Shrub',1.00);
INSERT INTO `small_plants` VALUES (55,NULL,NULL,'Rubus phoenicolasius','Japanese Wineberry, Wine raspberry','M',0,'Shrub',1.00);
INSERT INTO `small_plants` VALUES (56,NULL,NULL,'Salacca zalacca','Salak Palm, Snake Palm','M',0,'Shrub',4.00);
INSERT INTO `small_plants` VALUES (57,NULL,NULL,'Sauropus androgynus','Sweet Leaf, Sweetleaf Bush, Katuk','M',2,'Shrub',3.00);
INSERT INTO `small_plants` VALUES (58,NULL,NULL,'Semiarundinaria fastuosa','Narihiradake, Narihira bamboo','M',0,'Bamboo',3.00);
INSERT INTO `small_plants` VALUES (59,NULL,NULL,'Solanum lycopersicum','Tomato, Garden Tomato','M',3,'Annual',1.50);
INSERT INTO `small_plants` VALUES (60,NULL,NULL,'Ugni molinae','U�i, Chilean guava','M',0,'Shrub',1.00);
/*!40000 ALTER TABLE `small_plants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trees`
--

DROP TABLE IF EXISTS `trees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latin_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `common_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moisture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wind` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edibility` int NOT NULL,
  `medicinal` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trees`
--

LOCK TABLES `trees` WRITE;
/*!40000 ALTER TABLE `trees` DISABLE KEYS */;
INSERT INTO `trees` VALUES (1,NULL,NULL,'Aesculus spp','Horse chestnut','M','W',4,4);
INSERT INTO `trees` VALUES (2,NULL,NULL,'Anacardium occidentale','Cashew, Caju','M','W',5,3);
INSERT INTO `trees` VALUES (3,NULL,NULL,'Annona salzmannii','Beach Sugar Apple','M','W',4,0);
INSERT INTO `trees` VALUES (4,NULL,NULL,'Annona squamosa','Sugar Apple, Sweetsop, Custard Apple','M','W',5,2);
INSERT INTO `trees` VALUES (5,NULL,NULL,'Araucaria araucana','Monkey Puzzle Tree','M','M',5,1);
INSERT INTO `trees` VALUES (6,NULL,NULL,'Arbutus unedo','Strawberry Tree','M','M',4,2);
INSERT INTO `trees` VALUES (7,NULL,NULL,'Artocarpus altilis','Breadfruit','M','W',5,4);
INSERT INTO `trees` VALUES (8,NULL,NULL,'Artocarpus camansi','Breadnut, Kamansi','M','W',4,1);
INSERT INTO `trees` VALUES (9,NULL,NULL,'Artocarpus mariannensis','Seeded breadfruit, Marianas bread','M','W',4,3);
INSERT INTO `trees` VALUES (10,NULL,NULL,'Averrhoa carambola','Carambola, Star Fruit','M','W',4,2);
INSERT INTO `trees` VALUES (11,NULL,NULL,'Bactris gasipaes','Peach Palm, Pupunha','M','W',5,2);
INSERT INTO `trees` VALUES (12,NULL,NULL,'Byrsonima crassifolia','Golden Spoon, Nance, Nancy Tree','M','W',4,2);
INSERT INTO `trees` VALUES (13,NULL,NULL,'Canarium ovatum','Pili Nut','M','W',4,3);
INSERT INTO `trees` VALUES (14,NULL,NULL,'Castanea sativa','Sweet Chestnut, European chestnut','M','M',5,2);
INSERT INTO `trees` VALUES (15,NULL,NULL,'Chrysobalanus icaco','Coco Plum, Paradise Plum','M','W',4,2);
INSERT INTO `trees` VALUES (16,NULL,NULL,'Chrysophyllum cainito','Star Apple, Caimito','M','W',4,2);
INSERT INTO `trees` VALUES (17,NULL,NULL,'Cornus capitata','Bentham\'s Cornel','M','M',4,1);
INSERT INTO `trees` VALUES (18,NULL,NULL,'Cornus elliptica','Dogwood','M','M',4,1);
INSERT INTO `trees` VALUES (19,NULL,NULL,'Corylus avellana','Common Hazel, Common filbert, European Filbert, Harry Lauder\'s Walking Stick, Corkscrew Hazel, Hazel','M','W',5,2);
INSERT INTO `trees` VALUES (20,NULL,NULL,'Corylus avellana pontica','Corylus avellana pontica','M','W',4,0);
INSERT INTO `trees` VALUES (21,NULL,NULL,'Crataegus acclivis','Crataegus acclivis','M','W',4,2);
INSERT INTO `trees` VALUES (22,NULL,NULL,'Crataegus acclivis','Crataegus acclivis','We','W',4,2);
INSERT INTO `trees` VALUES (23,NULL,NULL,'Crataegus arnoldiana','Arnold Hawthorn','M','W',5,2);
INSERT INTO `trees` VALUES (24,NULL,NULL,'Crataegus arnoldiana','Arnold Hawthorn','We','W',5,2);
INSERT INTO `trees` VALUES (25,NULL,NULL,'Crataegus azarolus','Azarole','M','W',4,2);
INSERT INTO `trees` VALUES (26,NULL,NULL,'Crataegus azarolus','Azarole','We','W',4,2);
INSERT INTO `trees` VALUES (27,NULL,NULL,'Crataegus champlainensis','Quebec hawthorn','M','W',4,2);
INSERT INTO `trees` VALUES (28,NULL,NULL,'Crataegus champlainensis','Quebec hawthorn','We','W',4,2);
INSERT INTO `trees` VALUES (29,NULL,NULL,'Crataegus douglasii','Black Hawthorn','M','W',4,2);
INSERT INTO `trees` VALUES (30,NULL,NULL,'Crataegus douglasii','Black Hawthorn','We','W',4,2);
INSERT INTO `trees` VALUES (31,NULL,NULL,'Crataegus ellwangeriana','Scarlet Hawthorn','M','W',5,2);
INSERT INTO `trees` VALUES (32,NULL,NULL,'Crataegus ellwangeriana','Scarlet Hawthorn','We','W',5,2);
INSERT INTO `trees` VALUES (33,NULL,NULL,'Crataegus holmesiana','Holmes\' hawthorn','M','W',4,2);
INSERT INTO `trees` VALUES (34,NULL,NULL,'Crataegus holmesiana','Holmes\' hawthorn','We','W',4,2);
INSERT INTO `trees` VALUES (35,NULL,NULL,'Crataegus illinoiensis','Crataegus illinoiensis','M','W',4,2);
INSERT INTO `trees` VALUES (36,NULL,NULL,'Crataegus illinoiensis','Crataegus illinoiensis','We','W',4,2);
INSERT INTO `trees` VALUES (37,NULL,NULL,'Crataegus laciniata','Crataegus laciniata','M','W',4,2);
INSERT INTO `trees` VALUES (38,NULL,NULL,'Crataegus laciniata','Crataegus laciniata','We','W',4,2);
INSERT INTO `trees` VALUES (39,NULL,NULL,'Crataegus missouriensis','Crataegus missouriensis','M','W',5,2);
INSERT INTO `trees` VALUES (40,NULL,NULL,'Crataegus missouriensis','Crataegus missouriensis','We','W',5,2);
INSERT INTO `trees` VALUES (41,NULL,NULL,'Crataegus mollis','Red Haw, Downy hawthorn','M','W',4,2);
INSERT INTO `trees` VALUES (42,NULL,NULL,'Crataegus mollis','Red Haw, Downy hawthorn','We','W',4,2);
INSERT INTO `trees` VALUES (43,NULL,NULL,'Crataegus pennsylvanica','Crataegus pennsylvanica','M','W',5,2);
INSERT INTO `trees` VALUES (44,NULL,NULL,'Crataegus pennsylvanica','Crataegus pennsylvanica','We','W',5,2);
INSERT INTO `trees` VALUES (45,NULL,NULL,'Crataegus pinnatifida major','Chinese Haw','M','W',4,3);
INSERT INTO `trees` VALUES (46,NULL,NULL,'Crataegus pinnatifida major','Chinese Haw','We','W',4,3);
INSERT INTO `trees` VALUES (47,NULL,NULL,'Crataegus pontica','Crataegus pontica','M','W',4,2);
INSERT INTO `trees` VALUES (48,NULL,NULL,'Crataegus pontica','Crataegus pontica','We','W',4,2);
INSERT INTO `trees` VALUES (49,NULL,NULL,'Crataegus schraderana','Blue hawthorn','M','W',5,2);
INSERT INTO `trees` VALUES (50,NULL,NULL,'Crataegus schraderana','Blue hawthorn','We','W',5,2);
INSERT INTO `trees` VALUES (51,NULL,NULL,'Crataegus submollis','Quebec Hawthorn','M','W',4,2);
INSERT INTO `trees` VALUES (52,NULL,NULL,'Crataegus submollis','Quebec Hawthorn','We','W',4,2);
INSERT INTO `trees` VALUES (53,NULL,NULL,'Crataegus tanacetifolia','Tansy-Leaved Thorn','M','W',5,2);
INSERT INTO `trees` VALUES (54,NULL,NULL,'Crataegus tanacetifolia','Tansy-Leaved Thorn','We','W',5,2);
INSERT INTO `trees` VALUES (55,NULL,NULL,'Dictyosperma album','Hurricane Palm, Princess Palm, Red Palm','M','M',4,2);
INSERT INTO `trees` VALUES (56,NULL,NULL,'Eriobotrya japonica','Loquat, Japanese Loquat','M','M',4,3);
INSERT INTO `trees` VALUES (57,NULL,NULL,'Fagus sylvatica','Beech, European beech, Common Beech','M','W',4,2);
INSERT INTO `trees` VALUES (58,NULL,NULL,'Hippophae salicifolia','Willow-Leaved Sea Buckthorn','M','W',5,3);
INSERT INTO `trees` VALUES (59,NULL,NULL,'Hippophae salicifolia','Willow-Leaved Sea Buckthorn','We','W',5,3);
INSERT INTO `trees` VALUES (60,NULL,NULL,'Hippophae sinensis','Chinese Sea Buckthorn','M','W',5,3);
INSERT INTO `trees` VALUES (61,NULL,NULL,'Hippophae sinensis','Chinese Sea Buckthorn','We','W',5,3);
INSERT INTO `trees` VALUES (62,NULL,NULL,'Hippophae tibetana','Tibetan Sea Buckthorn','M','W',4,3);
INSERT INTO `trees` VALUES (63,NULL,NULL,'Hippophae tibetana','Tibetan Sea Buckthorn','We','W',4,3);
INSERT INTO `trees` VALUES (64,NULL,NULL,'Mammea americana','Mammee Apple, Mammey','M','W',4,2);
INSERT INTO `trees` VALUES (65,NULL,NULL,'Mangifera indica','Mango, Bowen Mango','M','W',5,3);
INSERT INTO `trees` VALUES (66,NULL,NULL,'Manilkara zapota','Sapodilla, Nispero','M','W',5,2);
INSERT INTO `trees` VALUES (67,NULL,NULL,'Melicoccus bijugatus','Mamoncillo, Spanish Lime, Guayo','M','W',4,2);
INSERT INTO `trees` VALUES (68,NULL,NULL,'Mespilus germanica','Medlar','M','W',4,1);
INSERT INTO `trees` VALUES (69,NULL,NULL,'Metroxylon sagu','Sago Palm','M','M',4,0);
INSERT INTO `trees` VALUES (70,NULL,NULL,'Metroxylon sagu','Sago Palm','We','M',4,0);
INSERT INTO `trees` VALUES (71,NULL,NULL,'Moringa oleifera','Horseradish Tree, Moringa,','M','W',4,4);
INSERT INTO `trees` VALUES (72,NULL,NULL,'Moringa oleifera','Horseradish Tree, Moringa,','We','W',4,4);
INSERT INTO `trees` VALUES (73,NULL,NULL,'Morus alba','White Mulberry, Common Mulberry,','M','W',4,3);
INSERT INTO `trees` VALUES (74,NULL,NULL,'Persea americana','Avocado, Alligator Pear','M','W',5,3);
INSERT INTO `trees` VALUES (75,NULL,NULL,'Pinus albicaulis','White-Bark Pine','M','W',4,2);
INSERT INTO `trees` VALUES (76,NULL,NULL,'Pinus cembra','Swiss Stone Pine, Swiss Pine, Arolla Pine','M','W',4,2);
INSERT INTO `trees` VALUES (77,NULL,NULL,'Pinus pinea','Italian Stone Pine, Umbrella Pine, Stone Pine','M','W',4,2);
INSERT INTO `trees` VALUES (78,NULL,NULL,'Pithecellobium dulce','Manila Tamarind, Madras Thorn','M','W',4,2);
INSERT INTO `trees` VALUES (79,NULL,NULL,'Pouteria campechiana','Canistel, Eggfruit','M','M',4,2);
INSERT INTO `trees` VALUES (80,NULL,NULL,'Pouteria campechiana','Canistel, Eggfruit','We','M',4,2);
INSERT INTO `trees` VALUES (81,NULL,NULL,'Pouteria sapota','Sapote, Mamey Sapote','M','M',4,2);
INSERT INTO `trees` VALUES (82,NULL,NULL,'Prunus cerasifera','Cherry Plum, Myrobalan Plum, Newport Cherry Plum, Pissard Plum','M','W',4,1);
INSERT INTO `trees` VALUES (83,NULL,NULL,'Prunus insititia','Damson','M','W',5,1);
INSERT INTO `trees` VALUES (84,NULL,NULL,'Quercus bicolor','Swamp White Oak','M','W',4,2);
INSERT INTO `trees` VALUES (85,NULL,NULL,'Quercus bicolor','Swamp White Oak','We','W',4,2);
INSERT INTO `trees` VALUES (86,NULL,NULL,'Quercus frainetto','Hungarian Oak, Italian Oak, Forest Green Oak','M','W',4,2);
INSERT INTO `trees` VALUES (87,NULL,NULL,'Quercus ilex','Holly Oak, Evergreen Oak','M','M',5,2);
INSERT INTO `trees` VALUES (88,NULL,NULL,'Quercus ilex ballota','Holm Oak','M','M',5,2);
INSERT INTO `trees` VALUES (89,NULL,NULL,'Quercus ithaburensis macrolepis','Valonia Oak','M','W',4,2);
INSERT INTO `trees` VALUES (90,NULL,NULL,'Quercus prinus','Rock Chestnut Oak','M','W',4,2);
INSERT INTO `trees` VALUES (91,NULL,NULL,'Quercus robur','Pedunculate Oak, English oak','M','W',4,3);
INSERT INTO `trees` VALUES (92,NULL,NULL,'Quercus robur','Pedunculate Oak, English oak','We','W',4,3);
INSERT INTO `trees` VALUES (93,NULL,NULL,'Sorbus domestica','Service Tree','M','W',5,0);
INSERT INTO `trees` VALUES (94,NULL,NULL,'Sorbus latifolia','French Hales','M','W',4,0);
INSERT INTO `trees` VALUES (95,NULL,NULL,'Sorbus mougeotii','Sorbus mougeotii','M','W',4,0);
INSERT INTO `trees` VALUES (96,NULL,NULL,'Sorbus torminalis','Wild Service Tree, Checkertree','M','W',4,0);
INSERT INTO `trees` VALUES (97,NULL,NULL,'Syzygium aromaticum','Clove, Zanzibar Redhead','M','M',4,4);
INSERT INTO `trees` VALUES (98,NULL,NULL,'Tamarindus indica','Tamarind','M','M',4,3);
INSERT INTO `trees` VALUES (99,NULL,NULL,'Terminalia catappa','Indian Almond, Tropical Almond Tree','M','M',4,2);
INSERT INTO `trees` VALUES (100,NULL,NULL,'Tilia cordata','Small Leaved Lime, Littleleaf linden','M','W',5,3);
INSERT INTO `trees` VALUES (101,NULL,NULL,'Tilia x europaea','Linden, Common Lime','M','W',5,3);
/*!40000 ALTER TABLE `trees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-12 19:18:15
