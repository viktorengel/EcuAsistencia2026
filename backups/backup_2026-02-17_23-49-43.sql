-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ecuasistencia2026_db
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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour_period` varchar(20) DEFAULT NULL,
  `status` enum('presente','ausente','tardanza','justificado') NOT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `shift_id` (`shift_id`),
  KEY `idx_date` (`date`),
  KEY `idx_student` (`student_id`),
  CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `attendances_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `attendances_ibfk_5` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  CONSTRAINT `attendances_ibfk_6` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,8,1,1,2,1,1,'2026-02-07','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(2,9,1,1,2,1,1,'2026-02-07','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(3,10,1,1,2,1,1,'2026-02-07','1ra hora','ausente','Sin justificación','2026-02-11 23:56:54','2026-02-11 23:56:54'),(4,11,1,1,2,1,1,'2026-02-07','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(5,8,1,1,2,1,1,'2026-02-08','1ra hora','tardanza','Llegó 10 min tarde','2026-02-11 23:56:54','2026-02-11 23:56:54'),(6,9,1,1,2,1,1,'2026-02-08','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(7,10,1,1,2,1,1,'2026-02-08','1ra hora','justificado','Cita médica','2026-02-11 23:56:54','2026-02-11 23:56:54'),(8,11,1,1,2,1,1,'2026-02-08','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(9,8,1,1,2,1,1,'2026-02-09','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(10,9,1,1,2,1,1,'2026-02-09','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(11,10,1,1,2,1,1,'2026-02-09','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(12,11,1,1,2,1,1,'2026-02-09','1ra hora','ausente','Enfermo','2026-02-11 23:56:54','2026-02-11 23:56:54'),(13,8,1,1,2,1,1,'2026-02-10','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(14,9,1,1,2,1,1,'2026-02-10','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(15,10,1,1,2,1,1,'2026-02-10','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(16,11,1,1,2,1,1,'2026-02-10','1ra hora','tardanza','Tráfico','2026-02-11 23:56:54','2026-02-11 23:56:54'),(17,8,1,1,2,1,1,'2026-02-11','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(18,9,1,1,2,1,1,'2026-02-11','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(19,10,1,1,2,1,1,'2026-02-11','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(20,11,1,1,2,1,1,'2026-02-11','1ra hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(21,8,1,2,3,1,1,'2026-02-09','2da hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(22,9,1,2,3,1,1,'2026-02-09','2da hora','presente','Participación activa','2026-02-11 23:56:54','2026-02-11 23:56:54'),(23,10,1,2,3,1,1,'2026-02-09','2da hora','ausente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(24,11,1,2,3,1,1,'2026-02-09','2da hora','presente','','2026-02-11 23:56:54','2026-02-11 23:56:54'),(25,21,5,2,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:19:50','2026-02-12 05:19:50'),(26,22,5,2,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:19:50','2026-02-12 05:19:50'),(27,21,5,1,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:20:27','2026-02-12 05:20:27'),(28,22,5,1,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:20:27','2026-02-12 05:20:27'),(29,21,5,1,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:21:05','2026-02-12 05:21:05'),(30,22,5,1,2,1,2,'2026-02-12','2da hora','ausente','','2026-02-12 05:21:05','2026-02-12 05:21:05'),(31,21,5,1,2,1,2,'2026-02-11','2da hora','ausente','','2026-02-12 05:22:21','2026-02-12 05:22:21'),(32,22,5,1,2,1,2,'2026-02-11','2da hora','ausente','','2026-02-12 05:22:21','2026-02-12 05:22:21'),(33,21,5,1,2,1,2,'2026-02-11','2da hora','tardanza','','2026-02-12 05:22:54','2026-02-12 05:22:54'),(34,22,5,1,2,1,2,'2026-02-11','2da hora','ausente','','2026-02-12 05:22:54','2026-02-12 05:22:54'),(35,21,5,3,1,1,2,'2026-02-15','1ra hora','ausente','','2026-02-15 02:16:55','2026-02-15 02:16:55'),(36,22,5,3,1,1,2,'2026-02-15','1ra hora','ausente','','2026-02-15 02:16:55','2026-02-15 02:16:55'),(37,21,5,3,1,1,2,'2026-02-14','1ra hora','justificado','','2026-02-15 02:29:20','2026-02-15 02:31:25'),(38,22,5,3,1,1,2,'2026-02-14','1ra hora','justificado','','2026-02-15 02:29:20','2026-02-15 02:31:25'),(39,21,5,3,1,1,2,'2026-02-14','1ra hora','presente','','2026-02-15 02:29:38','2026-02-15 02:29:38'),(40,22,5,3,1,1,2,'2026-02-14','1ra hora','presente','','2026-02-15 02:29:38','2026-02-15 02:29:38'),(41,20,4,5,1,1,2,'2026-02-14','1ra hora','presente','','2026-02-15 02:31:54','2026-02-15 02:32:33'),(42,18,4,5,1,1,2,'2026-02-14','1ra hora','presente','','2026-02-15 02:31:54','2026-02-15 02:32:33'),(43,19,4,5,1,1,2,'2026-02-14','1ra hora','presente','','2026-02-15 02:31:54','2026-02-15 02:32:33'),(44,12,2,8,28,1,1,'2026-02-16','1ra hora','justificado','','2026-02-16 15:13:20','2026-02-16 18:33:28'),(45,13,2,8,28,1,1,'2026-02-16','1ra hora','justificado','','2026-02-16 15:13:20','2026-02-16 20:23:12'),(46,14,2,8,28,1,1,'2026-02-16','1ra hora','ausente','','2026-02-16 15:13:20','2026-02-16 15:13:39'),(47,21,5,6,7,1,1,'2026-02-17','4ra hora','presente','','2026-02-18 03:36:14','2026-02-18 04:03:25'),(48,22,5,6,7,1,1,'2026-02-17','4ra hora','justificado','ABC','2026-02-18 03:36:14','2026-02-18 04:03:25');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_schedule`
--

DROP TABLE IF EXISTS `class_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `day_of_week` enum('lunes','martes','miercoles','jueves','viernes') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `period_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `class_schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `class_schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `class_schedule_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_schedule`
--

LOCK TABLES `class_schedule` WRITE;
/*!40000 ALTER TABLE `class_schedule` DISABLE KEYS */;
INSERT INTO `class_schedule` VALUES (2,5,3,5,1,'martes','00:00:00','00:00:00',7,'2026-02-15 03:05:28'),(5,5,6,7,1,'lunes','00:00:00','00:00:00',1,'2026-02-15 04:59:04'),(6,2,8,28,1,'lunes','00:00:00','00:00:00',1,'2026-02-16 15:13:02'),(7,5,6,7,1,'miercoles','00:00:00','00:00:00',1,'2026-02-18 03:06:58'),(8,5,6,7,1,'martes','00:00:00','00:00:00',4,'2026-02-18 03:17:05');
/*!40000 ALTER TABLE `class_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_students`
--

DROP TABLE IF EXISTS `course_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`,`school_year_id`),
  KEY `course_id` (`course_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `course_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `course_students_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `course_students_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_students`
--

LOCK TABLES `course_students` WRITE;
/*!40000 ALTER TABLE `course_students` DISABLE KEYS */;
INSERT INTO `course_students` VALUES (1,8,1,1,'2025-09-01','2026-02-11 23:56:54'),(2,9,1,1,'2025-09-01','2026-02-11 23:56:54'),(4,11,1,1,'2025-09-01','2026-02-11 23:56:54'),(5,12,2,1,'2025-09-01','2026-02-11 23:56:54'),(6,13,2,1,'2025-09-01','2026-02-11 23:56:54'),(7,14,2,1,'2025-09-01','2026-02-11 23:56:54'),(8,15,3,1,'2025-09-01','2026-02-11 23:56:54'),(9,16,3,1,'2025-09-01','2026-02-11 23:56:54'),(10,17,3,1,'2025-09-01','2026-02-11 23:56:54'),(11,18,4,1,'2025-09-01','2026-02-11 23:56:54'),(12,19,4,1,'2025-09-01','2026-02-11 23:56:54'),(13,20,4,1,'2025-09-01','2026-02-11 23:56:54'),(14,21,5,1,'2025-09-01','2026-02-11 23:56:54'),(15,22,5,1,'2025-09-01','2026-02-11 23:56:54');
/*!40000 ALTER TABLE `course_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `parallel` varchar(10) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,1,1,'8vo EGB \"A\" - Matutina','8vo EGB','A',1,'2026-02-11 23:56:54','2026-02-17 22:08:44'),(2,1,1,'1ro BGU \"A\" - Matutina','1ro BGU','A',1,'2026-02-11 23:56:54','2026-02-17 22:08:44'),(3,1,1,'1ro Técnico \"B\" - Matutina','1ro Técnico','B',1,'2026-02-11 23:56:54','2026-02-17 22:08:44'),(4,1,1,'9no EGB \"A\" - Vespertina','9no EGB','A',2,'2026-02-11 23:56:54','2026-02-17 22:08:44'),(5,1,1,'10mo EGB \"A\" - Vespertina','10mo EGB','A',2,'2026-02-11 23:56:54','2026-02-17 22:08:44'),(6,1,1,'1.º BT \"A\" - Informática (Soporte Técnico) - Matutina','1.º BT','A',1,'2026-02-18 02:53:24','2026-02-18 02:57:11');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institution_shifts`
--

DROP TABLE IF EXISTS `institution_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_institution_shift` (`institution_id`,`shift_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `institution_shifts_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `institution_shifts_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institution_shifts`
--

LOCK TABLES `institution_shifts` WRITE;
/*!40000 ALTER TABLE `institution_shifts` DISABLE KEYS */;
INSERT INTO `institution_shifts` VALUES (1,1,1,'2026-02-15 11:41:49'),(2,1,2,'2026-02-15 11:41:49');
/*!40000 ALTER TABLE `institution_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `director_name` varchar(200) DEFAULT NULL,
  `amie_code` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES (1,'Unidad Educativa Pomasqui','UEP001','Av. Manuel Cordova Galarza 123, Quito, Ecuador','Pichincha','Quito','02-2351072','contacto@uep.edu.ec','MSc. Nombre Apellido','17h01988','https://www.uep.edu.ec','uploads/institution/logo_1771364861.jpg',1,'2026-02-11 23:56:53','2026-02-17 21:47:41');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `justifications`
--

DROP TABLE IF EXISTS `justifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `reason` text NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `reviewed_by` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `attendance_id` (`attendance_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_status` (`status`),
  KEY `idx_student` (`student_id`),
  CONSTRAINT `justifications_ibfk_1` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `justifications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `justifications_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `justifications_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `justifications`
--

LOCK TABLES `justifications` WRITE;
/*!40000 ALTER TABLE `justifications` DISABLE KEYS */;
INSERT INTO `justifications` VALUES (1,44,12,12,'Médico','uploads/justifications/69935b0811b11.png','rechazado',1,'dfasf','2026-02-16 17:59:36','2026-02-16 18:33:32'),(2,44,12,12,'Médico','uploads/justifications/69935bf9605e9.png','aprobado',1,'ok','2026-02-16 18:03:37','2026-02-16 18:33:28'),(3,45,13,13,'Viaje','uploads/justifications/69937ca16e332.png','aprobado',1,'ok','2026-02-16 20:22:57','2026-02-16 20:23:12'),(4,46,14,14,'prueba','uploads/justifications/69954314eaeee.png','pendiente',NULL,NULL,'2026-02-18 04:41:56','2026-02-18 04:41:56'),(5,35,21,21,'vzxcvx','uploads/justifications/699543a6bad8f.png','pendiente',NULL,NULL,'2026-02-18 04:44:22','2026-02-18 04:44:22');
/*!40000 ALTER TABLE `justifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'Nueva justificación pendiente','Un estudiante envió una justificación que requiere revisión.','',NULL,1,'2026-02-18 04:41:56'),(2,28,'Nueva justificación pendiente','Un estudiante envió una justificación que requiere revisión.','',NULL,0,'2026-02-18 04:41:56'),(3,1,'Nueva justificación pendiente','Un estudiante envió una justificación que requiere revisión.','',NULL,1,'2026-02-18 04:44:22'),(4,28,'Nueva justificación pendiente','Un estudiante envió una justificación que requiere revisión.','',NULL,0,'2026-02-18 04:44:22');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_type` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `representatives`
--

DROP TABLE IF EXISTS `representatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representative_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rep_student` (`representative_id`,`student_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `representatives_ibfk_1` FOREIGN KEY (`representative_id`) REFERENCES `users` (`id`),
  CONSTRAINT `representatives_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `representatives`
--

LOCK TABLES `representatives` WRITE;
/*!40000 ALTER TABLE `representatives` DISABLE KEYS */;
INSERT INTO `representatives` VALUES (1,23,12,'Abuelo/a',0,'2026-02-11 23:56:54'),(2,23,13,'Madre',1,'2026-02-11 23:56:54'),(3,24,14,'Padre',1,'2026-02-11 23:56:54'),(4,25,15,'Madre',1,'2026-02-11 23:56:54'),(5,25,16,'Madre',1,'2026-02-11 23:56:54'),(6,26,17,'Padre',1,'2026-02-11 23:56:54'),(7,26,18,'Padre',1,'2026-02-11 23:56:54'),(8,27,19,'Madre',1,'2026-02-11 23:56:54'),(9,27,20,'Madre',1,'2026-02-11 23:56:54'),(10,27,21,'Madre',1,'2026-02-11 23:56:54');
/*!40000 ALTER TABLE `representatives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'docente','Profesor de la institución','2026-02-11 23:56:54','2026-02-11 23:56:54'),(2,'estudiante','Estudiante activo','2026-02-11 23:56:54','2026-02-11 23:56:54'),(3,'inspector','Inspector de disciplina','2026-02-11 23:56:54','2026-02-11 23:56:54'),(4,'autoridad','Autoridad institucional','2026-02-11 23:56:54','2026-02-11 23:56:54'),(5,'representante','Representante legal de estudiante','2026-02-11 23:56:54','2026-02-11 23:56:54');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_years`
--

DROP TABLE IF EXISTS `school_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `school_years_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_years`
--

LOCK TABLES `school_years` WRITE;
/*!40000 ALTER TABLE `school_years` DISABLE KEYS */;
INSERT INTO `school_years` VALUES (1,1,'2025-2026','2025-09-01','2026-07-30',1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(3,1,'2026-2027','2026-09-01','2027-07-30',0,'2026-02-16 15:43:15','2026-02-16 15:43:15');
/*!40000 ALTER TABLE `school_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,'matutina','2026-02-11 23:56:54'),(2,'vespertina','2026-02-11 23:56:54'),(3,'nocturna','2026-02-11 23:56:54');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,1,'Matemáticas','MAT','2026-02-11 23:56:54','2026-02-11 23:56:54'),(2,1,'Lengua y Literatura','LEN','2026-02-11 23:56:54','2026-02-11 23:56:54'),(3,1,'Ciencias Naturales','CCNN','2026-02-11 23:56:54','2026-02-16 21:21:06'),(4,1,'Estudios Sociales','EESS','2026-02-11 23:56:54','2026-02-16 21:21:24'),(5,1,'Inglés','ING','2026-02-11 23:56:54','2026-02-11 23:56:54'),(6,1,'Educación Física','EEFF','2026-02-11 23:56:54','2026-02-16 21:21:18'),(8,1,'Informática','INF','2026-02-11 23:56:54','2026-02-11 23:56:54');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_assignments`
--

DROP TABLE IF EXISTS `teacher_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `is_tutor` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `teacher_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_assignments`
--

LOCK TABLES `teacher_assignments` WRITE;
/*!40000 ALTER TABLE `teacher_assignments` DISABLE KEYS */;
INSERT INTO `teacher_assignments` VALUES (1,2,1,1,1,0,'2026-02-11 23:56:54'),(2,2,2,1,1,1,'2026-02-11 23:56:54'),(3,2,3,1,1,0,'2026-02-11 23:56:54'),(4,3,1,2,1,1,'2026-02-11 23:56:54'),(5,3,2,2,1,0,'2026-02-11 23:56:54'),(6,3,4,2,1,0,'2026-02-11 23:56:54'),(7,4,1,3,1,0,'2026-02-11 23:56:54'),(8,4,3,3,1,0,'2026-02-11 23:56:54'),(9,4,5,3,1,0,'2026-02-11 23:56:54'),(10,5,2,4,1,0,'2026-02-11 23:56:54'),(11,5,3,4,1,1,'2026-02-11 23:56:54'),(12,5,4,4,1,0,'2026-02-11 23:56:54'),(13,6,1,5,1,0,'2026-02-11 23:56:54'),(14,6,2,5,1,0,'2026-02-11 23:56:54'),(15,6,4,5,1,1,'2026-02-11 23:56:54'),(17,7,1,6,1,0,'2026-02-11 23:56:54'),(18,7,3,6,1,0,'2026-02-11 23:56:54'),(19,7,5,6,1,1,'2026-02-11 23:56:54'),(23,5,4,3,1,0,'2026-02-15 03:33:14'),(24,7,1,4,1,0,'2026-02-15 04:16:09'),(25,28,2,8,1,0,'2026-02-16 15:12:27');
/*!40000 ALTER TABLE `teacher_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (2,2,1,'2026-02-11 23:56:54'),(3,3,1,'2026-02-11 23:56:54'),(4,4,1,'2026-02-11 23:56:54'),(5,5,1,'2026-02-11 23:56:54'),(7,7,1,'2026-02-11 23:56:54'),(8,8,2,'2026-02-11 23:56:54'),(9,9,2,'2026-02-11 23:56:54'),(10,10,2,'2026-02-11 23:56:54'),(11,11,2,'2026-02-11 23:56:54'),(12,12,2,'2026-02-11 23:56:54'),(13,13,2,'2026-02-11 23:56:54'),(14,14,2,'2026-02-11 23:56:54'),(15,15,2,'2026-02-11 23:56:54'),(16,16,2,'2026-02-11 23:56:54'),(17,17,2,'2026-02-11 23:56:54'),(18,18,2,'2026-02-11 23:56:54'),(19,19,2,'2026-02-11 23:56:54'),(20,20,2,'2026-02-11 23:56:54'),(21,21,2,'2026-02-11 23:56:54'),(22,22,2,'2026-02-11 23:56:54'),(23,23,5,'2026-02-11 23:56:54'),(24,24,5,'2026-02-11 23:56:54'),(25,25,5,'2026-02-11 23:56:54'),(26,26,5,'2026-02-11 23:56:54'),(27,27,5,'2026-02-11 23:56:54'),(32,6,1,'2026-02-12 05:14:40'),(35,1,4,'2026-02-15 04:36:36'),(41,28,3,'2026-02-16 13:17:22'),(42,28,1,'2026-02-16 15:02:13');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `dni` (`dni`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin','admin@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Administrador','Sistema','1700000000','0999999999',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(2,1,'prof.garcia','garcia@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Roberto','García Mendoza','1751234567','0991234571',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(3,1,'prof.martinez','martinez@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ana','Martínez Silva','1751234568','0991234572',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(4,1,'prof.rodriguez','rodriguez@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Luis','Rodríguez Cano','1751234569','0991234573',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(5,1,'prof.fernandez','fernandez@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Carmen','Fernández Ortiz','1751234570','0991234574',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(6,1,'prof.gomez','gomez@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Miguel','Gómez Torres','1751234571','0991234575',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(7,1,'prof.diaz','diaz@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Patricia','Díaz Ramírez','1751234572','0991234576',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(8,1,'juan.perez','juan.perez@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Juan','Pérez García','1750123456','0991234567',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(9,1,'maria.lopez','maria.lopez@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','María','López Sánchez','1750234567','0991234568',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(10,1,'carlos.mora','carlos.mora@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Carlos','Mora Ruiz','1750345678','0991234569',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(11,1,'ana.torres','ana.torres@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ana','Torres Vega','1750456789','0991234570',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(12,1,'sofia.castro','sofia.castro@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Sofía','Castro Morales','1750567890','0991234577',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(13,1,'diego.herrera','diego.herrera@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Diego','Herrera Ríos','1750567891','0991234578',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(14,1,'valentina.lopez','valentina.lopez@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Valentina','López Suárez','1750567892','0991234579',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(15,1,'mateo.silva','mateo.silva@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Mateo','Silva Navarro','1750567893','0991234580',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(16,1,'isabella.rojas','isabella.rojas@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Isabella','Rojas Pérez','1750567894','0991234581',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(17,1,'sebastian.cruz','sebastian.cruz@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Sebastián','Cruz Méndez','1750567895','0991234582',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(18,1,'camila.ramos','camila.ramos@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Camila','Ramos Flores','1750567896','0991234583',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(19,1,'nicolas.vargas','nicolas.vargas@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nicolás','Vargas Castro','1750567897','0991234584',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(20,1,'martina.gil','martina.gil@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Martina','Gil Núñez','1750567898','0991234585',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(21,1,'lucas.medina','lucas.medina@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lucas','Medina Ortega','1750567899','0991234586',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(22,1,'emma.santos','emma.santos@estudiante.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Emma','Santos Molina','1750567900','0991234587',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(23,1,'rep.castro','lcastro@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Laura','Castro Vega','1752345678','0991234588',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(24,1,'rep.herrera','jherrera@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jorge','Herrera Palacios','1752345679','0991234589',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(25,1,'rep.lopez','mlopez@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','María','López Benítez','1752345680','0991234590',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(26,1,'rep.silva','psilva@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Pedro','Silva Moreno','1752345681','0991234591',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(27,1,'rep.rojas','crojas@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Carolina','Rojas Delgado','1752345682','0991234592',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54'),(28,1,'inspector','inspector@uep.edu.ec','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','José','Ramírez Campos','1753456789','0991234593',NULL,NULL,1,'2026-02-11 23:56:54','2026-02-11 23:56:54');
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

-- Dump completed on 2026-02-17 23:49:44
