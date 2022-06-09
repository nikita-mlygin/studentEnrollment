-- MySQL dump 10.13  Distrib 8.0.28, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: students
-- ------------------------------------------------------
-- Server version	8.0.28

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
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group` (
  `group_id` int NOT NULL AUTO_INCREMENT,
  `ref_group_specialisty_id` int NOT NULL,
  `group_name` varchar(45) NOT NULL,
  `group_year` int NOT NULL,
  `group_classroom_teacher` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  KEY `fk_group_specialisty_id_idx` (`ref_group_specialisty_id`),
  CONSTRAINT `fk_group_specialisty_id` FOREIGN KEY (`ref_group_specialisty_id`) REFERENCES `specialisty` (`specialisty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specialisty`
--

DROP TABLE IF EXISTS `specialisty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `specialisty` (
  `specialisty_id` int NOT NULL AUTO_INCREMENT,
  `specialisty_name` varchar(45) DEFAULT NULL,
  `specialisty_description` text,
  `specialisty_direction` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`specialisty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `student_first_name` varchar(45) NOT NULL,
  `student_last_name` varchar(45) NOT NULL,
  `student_patronymic` varchar(45) NOT NULL,
  `student_certificate` int DEFAULT NULL,
  `student_application_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_student_group_id` int DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `fk_student_group_id_idx` (`ref_student_group_id`),
  CONSTRAINT `fk_student_group_id` FOREIGN KEY (`ref_student_group_id`) REFERENCES `group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student_scores`
--

DROP TABLE IF EXISTS `student_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_scores` (
  `ref_student_id` int NOT NULL,
  `discipline_name` varchar(45) NOT NULL,
  `score` int NOT NULL,
  PRIMARY KEY (`ref_student_id`,`discipline_name`),
  CONSTRAINT `fk_student_scores_student_id` FOREIGN KEY (`ref_student_id`) REFERENCES `student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student_specialisty`
--

DROP TABLE IF EXISTS `student_specialisty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_specialisty` (
  `ref_student_specialisty_student_id` int NOT NULL,
  `ref_student_specialisty_specialisty_id` int NOT NULL,
  `student_specialisty_priority` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`student_specialisty_priority`,`ref_student_specialisty_student_id`),
  KEY `fk_student_specialisty_student_id_idx` (`ref_student_specialisty_student_id`),
  KEY `fk_student_specialisty_specialisty_id_idx` (`ref_student_specialisty_specialisty_id`),
  CONSTRAINT `fk_student_specialisty_specialisty_id` FOREIGN KEY (`ref_student_specialisty_specialisty_id`) REFERENCES `specialisty` (`specialisty_id`),
  CONSTRAINT `fk_student_specialisty_student_id` FOREIGN KEY (`ref_student_specialisty_student_id`) REFERENCES `student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `user_login` varchar(45) NOT NULL,
  `user_password` varchar(45) NOT NULL,
  `user_role` varchar(45) NOT NULL DEFAULT 'user',
  `user_last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_token` varchar(45) DEFAULT NULL,
  `user_email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login_UNIQUE` (`user_login`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-30 22:57:54
