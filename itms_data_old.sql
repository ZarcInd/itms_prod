-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 27, 2025 at 03:00 PM
-- Server version: 10.11.10-MariaDB-ubu2204
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itms_staging_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `itms_data`
--

CREATE TABLE `itms_data` (
  `packet_header` varchar(5) DEFAULT NULL,
  `mode` varchar(1) DEFAULT NULL,
  `device_type` varchar(3) DEFAULT NULL,
  `packet_type` varchar(2) DEFAULT NULL,
  `firmware_version` varchar(20) DEFAULT NULL,
  `device_id` bigint(15) DEFAULT NULL,
  `ignition` varchar(5) DEFAULT NULL,
  `driver_id` int(1) DEFAULT NULL,
  `time` varchar(8) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `gps` varchar(1) DEFAULT NULL,
  `lat` decimal(10,6) DEFAULT NULL,
  `lat_dir` varchar(1) DEFAULT NULL,
  `lon` decimal(10,6) DEFAULT NULL,
  `lon_dir` varchar(1) DEFAULT NULL,
  `speed_knots` int(1) DEFAULT NULL,
  `network` int(2) DEFAULT NULL,
  `route_no` int(2) DEFAULT NULL,
  `speed_kmh` decimal(5,2) DEFAULT NULL,
  `odo_meter` decimal(10,2) DEFAULT NULL,
  `Led_health_1` int(1) DEFAULT NULL,
  `Led_health_2` int(1) DEFAULT NULL,
  `Led_health_3` int(1) DEFAULT NULL,
  `Led_health_4` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE IF NOT EXISTS itms_can_data (
  packet_header VARCHAR(5) DEFAULT NULL,
  mode VARCHAR(1) DEFAULT NULL,
  device_type VARCHAR(3) DEFAULT NULL,
  packet_type VARCHAR(2) DEFAULT NULL,
  firmware_version VARCHAR(20) DEFAULT NULL,
  device_id BIGINT(15) DEFAULT NULL,
  time VARCHAR(8) DEFAULT NULL,
  date VARCHAR(10) DEFAULT NULL,
  speed_kmh DECIMAL(5,2) DEFAULT NULL,
  oil_pressure INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
-- &PEIS,N,CAN,LP,MTC_IPC_V1.22,865546042801833,12:47:16,08/03/2025,,,,,#

CREATE TABLE `itms_device_stats` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `device_id` bigint NOT NULL,
  `data_date` date NOT NULL,
  `first_pkt` datetime NOT NULL, 
  `first_so_pkt` datetime DEFAULT NULL,
  `last_pkt` datetime NOT NULL,
  `last_so_pkt` datetime DEFAULT NULL,
  `online_time` int NOT NULL,
  `pkt_count` int DEFAULT NULL,
  `so_pkt_cnt` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SEARCH1` (`device_id`),
  KEY `SEARCH2` (`data_date`),
  KEY `SEARCH3` (`device_id`,`data_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `itms_xml` (
  `id` int NOT NULL AUTO_INCREMENT,
  `version` varchar(45) NOT NULL,
  `status` varchar(1) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `custom_fields` text,
  `group_id` int DEFAULT NULL,
  `sub_group_id` int DEFAULT NULL,
  `imei` bigint DEFAULT NULL,
  `active` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_XML_IMEI_idx` (`imei`),
  KEY `FK_XML_GROUP_1_idx` (`group_id`),
  KEY `FK_XML_GROUP_2_idx` (`sub_group_id`),
  CONSTRAINT `FK_XML_GROUP_1` FOREIGN KEY (`group_id`) REFERENCES `itms_groups` (`id`),
  CONSTRAINT `FK_XML_GROUP_2` FOREIGN KEY (`sub_group_id`) REFERENCES `itms_groups` (`id`),
  CONSTRAINT `FK_XML_IMEI` FOREIGN KEY (`imei`) REFERENCES `itms_imei` (`imei`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Dumping data for table `itms_data`
--

INSERT INTO `itms_data` (`packet_header`, `mode`, `device_type`, `packet_type`, `firmware_version`, `device_id`, `ignition`, `driver_id`, `time`, `date`, `gps`, `lat`, `lat_dir`, `lon`, `lon_dir`, `speed_knots`, `network`, `route_no`, `speed_kmh`, `odo_meter`, `Led_health_1`, `Led_health_2`, `Led_health_3`, `Led_health_4`) VALUES
('&PEIS', 'N', 'VTS', 'LP', 'ITS_AL_v0.37', 865546042801833, 'IGNON', 0, '17:38:35', '27/01/25', 'A', 1300.658599, 'N', 8012.895503, 'E', 6, -1, 19, 0, 0, 0, 0, 0, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Add partition key column to itms_can_data
ALTER TABLE itms_can_data ADD COLUMN partition_key int;

-- Change primary key of itms_can_data
ALTER TABLE itms_can_data DROP PRIMARY KEY, ADD PRIMARY KEY (id, partition_key, device_id);

-- Add Partitions to itms_can_data
ALTER TABLE itms_can_data  
PARTITION BY RANGE (partition_key)  
SUBPARTITION BY HASH (device_id) SUBPARTITIONS 4 (  
    PARTITION p_old VALUES LESS THAN (20250325),  
    PARTITION p20250326 VALUES LESS THAN (20250326),  
    PARTITION p20250327 VALUES LESS THAN (20250327),  
    PARTITION p20250328 VALUES LESS THAN (20250328),  
    PARTITION p20250329 VALUES LESS THAN (20250329),  
    PARTITION p20250330 VALUES LESS THAN (20250330),  
    PARTITION p20250331 VALUES LESS THAN (20250331),  
    PARTITION p20250401 VALUES LESS THAN (20250401),  
    PARTITION p20250402 VALUES LESS THAN (20250402),  
    PARTITION p20250403 VALUES LESS THAN (20250403),  

    -- Future Partition (Temporary)  
    PARTITION p_future VALUES LESS THAN MAXVALUE  
);

-- Add partition key column to itms_data
ALTER TABLE itms_data ADD COLUMN partition_key int;

-- Change primary key of itms_data
ALTER TABLE itms_data DROP PRIMARY KEY, ADD PRIMARY KEY (id, partition_key, device_id);

-- Add Partitions to itms_data
ALTER TABLE itms_data  
PARTITION BY RANGE (partition_key)  
SUBPARTITION BY HASH (device_id) SUBPARTITIONS 4 (  
    PARTITION p_old VALUES LESS THAN (20250325),  
    PARTITION p20250326 VALUES LESS THAN (20250326),  
    PARTITION p20250327 VALUES LESS THAN (20250327),  
    PARTITION p20250328 VALUES LESS THAN (20250328),  
    PARTITION p20250329 VALUES LESS THAN (20250329),  
    PARTITION p20250330 VALUES LESS THAN (20250330),  
    PARTITION p20250331 VALUES LESS THAN (20250331),  
    PARTITION p20250401 VALUES LESS THAN (20250401),  
    PARTITION p20250402 VALUES LESS THAN (20250402),  
    PARTITION p20250403 VALUES LESS THAN (20250403),  

    -- Future Partition (Temporary)  
    PARTITION p_future VALUES LESS THAN MAXVALUE  
);


-- Partition Creation Event (itms_can_data)
DELIMITER //

CREATE EVENT daily_partition_add_itms_can_data
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DECLARE new_partition_name VARCHAR(50);
    DECLARE tomorrow INT;
    SET tomorrow = YEAR(CURDATE() + INTERVAL 1 DAY) * 10000 +
                   MONTH(CURDATE() + INTERVAL 1 DAY) * 100 +
                   DAY(CURDATE() + INTERVAL 1 DAY);
    SET new_partition_name = CONCAT('p', tomorrow);

    SET @sql_create = CONCAT('ALTER TABLE itms_can_data ADD PARTITION (PARTITION ', new_partition_name, 
                             ' VALUES LESS THAN (', tomorrow, '))');
    PREPARE stmt FROM @sql_create;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END //

DELIMITER ;


-- Partition Creation Event (itms_data)
DELIMITER //

CREATE EVENT daily_partition_add_itms_data
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DECLARE new_partition_name VARCHAR(50);
    DECLARE tomorrow INT;
    SET tomorrow = YEAR(CURDATE() + INTERVAL 1 DAY) * 10000 +
                   MONTH(CURDATE() + INTERVAL 1 DAY) * 100 +
                   DAY(CURDATE() + INTERVAL 1 DAY);
    SET new_partition_name = CONCAT('p', tomorrow);

    SET @sql_create = CONCAT('ALTER TABLE itms_data ADD PARTITION (PARTITION ', new_partition_name, 
                             ' VALUES LESS THAN (', tomorrow, '))');
    PREPARE stmt FROM @sql_create;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END //

DELIMITER ;


-- Cleanup Triggers (itms_can_data)
DELIMITER //

CREATE EVENT daily_partition_cleanup_itms_can_data
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DECLARE drop_partition_name VARCHAR(50);
    DECLARE last_date INT;

    SET last_date = YEAR(CURDATE() - INTERVAL 30 DAY) * 10000 +
                    MONTH(CURDATE() - INTERVAL 30 DAY) * 100 +
                    DAY(CURDATE() - INTERVAL 30 DAY);
    SET drop_partition_name = CONCAT('p', last_year);

    SET @sql_drop = CONCAT('ALTER TABLE itms_can_data DROP PARTITION ', drop_partition_name);
    PREPARE stmt FROM @sql_drop;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END //

DELIMITER ;


-- Cleanup Triggers (itms_data)
DELIMITER //

CREATE EVENT daily_partition_cleanup_itms_can_data
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DECLARE drop_partition_name VARCHAR(50);
    DECLARE last_date INT;

    SET last_date = YEAR(CURDATE() - INTERVAL 30 DAY) * 10000 +
                    MONTH(CURDATE() - INTERVAL 30 DAY) * 100 +
                    DAY(CURDATE() - INTERVAL 30 DAY);
    SET drop_partition_name = CONCAT('p', last_year);

    SET @sql_drop = CONCAT('ALTER TABLE itms_data DROP PARTITION ', drop_partition_name);
    PREPARE stmt FROM @sql_drop;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END //

DELIMITER ;
