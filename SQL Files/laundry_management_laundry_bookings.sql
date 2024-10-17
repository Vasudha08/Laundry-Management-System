-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: laundry_management
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `laundry_bookings`
--

DROP TABLE IF EXISTS `laundry_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laundry_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roll_number` varchar(50) NOT NULL,
  `slot_date` date NOT NULL,
  `slot_time` time NOT NULL,
  `status` enum('Pending','In Progress','Ready for Collection','Collected','Cancelled') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled` tinyint(1) DEFAULT '0',
  `expected_collection_time` datetime DEFAULT NULL,
  `collection_time` time DEFAULT NULL,
  `type` enum('Normal','Urgent') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roll_number` (`roll_number`),
  CONSTRAINT `laundry_bookings_ibfk_1` FOREIGN KEY (`roll_number`) REFERENCES `students` (`roll_number`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laundry_bookings`
--

LOCK TABLES `laundry_bookings` WRITE;
/*!40000 ALTER TABLE `laundry_bookings` DISABLE KEYS */;
INSERT INTO `laundry_bookings` VALUES (2,'21Z231','2024-10-14','11:00:00','In Progress','2024-10-12 04:30:00',0,'2024-10-14 14:00:00',NULL,NULL),(3,'21Z264','2024-10-13','12:00:00','Ready for Collection','2024-10-12 05:30:00',0,NULL,NULL,NULL),(18,'21Z267','2024-10-16','11:30:00','In Progress','2024-10-16 07:52:16',1,'2024-10-16 12:30:00','10:30:00','Normal'),(19,'21Z267','2024-10-17','10:00:00','Pending','2024-10-16 07:53:09',1,'2024-10-17 11:00:00','02:00:00','Normal'),(20,'21Z267','2024-10-16','10:30:00','Ready for Collection','2024-10-16 07:56:54',0,'2024-10-16 11:30:00','11:30:00','Normal'),(21,'21Z267','2024-10-17','11:00:00','Pending','2024-10-17 08:43:31',0,'2024-10-17 12:00:00',NULL,'Urgent');
/*!40000 ALTER TABLE `laundry_bookings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-17 14:51:59
