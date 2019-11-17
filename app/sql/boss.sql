-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 17, 2019 at 09:25 AM
-- Server version: 5.7.21
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boss`
--
CREATE DATABASE IF NOT EXISTS `boss` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `boss`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_round`
--

DROP TABLE IF EXISTS `admin_round`;
CREATE TABLE IF NOT EXISTS `admin_round` (
  `adminID` varchar(100) NOT NULL,
  `adminPW` varchar(100) NOT NULL,
  `adminTK` varchar(300) DEFAULT NULL,
  `roundID` int(1) NOT NULL,
  `roundStatus` varchar(50) NOT NULL,
  `r1Start` timestamp NULL DEFAULT NULL,
  `r1End` timestamp NULL DEFAULT NULL,
  `r2Start` timestamp NULL DEFAULT NULL,
  `r2End` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`adminID`,`adminPW`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bid`
--

DROP TABLE IF EXISTS `bid`;
CREATE TABLE IF NOT EXISTS `bid` (
  `userid` varchar(128) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  `code` varchar(100) NOT NULL,
  `section` varchar(3) NOT NULL,
  PRIMARY KEY (`userid`,`code`,`section`),
  KEY `BID_FK2` (`code`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bid_processor`
--

DROP TABLE IF EXISTS `bid_processor`;
CREATE TABLE IF NOT EXISTS `bid_processor` (
  `userid` varchar(128) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  `course` varchar(100) NOT NULL,
  `section` varchar(3) NOT NULL,
  `bidstatus` varchar(50) NOT NULL,
  `bidround` int(1) NOT NULL,
  PRIMARY KEY (`userid`,`amount`,`course`,`section`,`bidstatus`,`bidround`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `courseID` varchar(100) NOT NULL,
  `school` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `examDate` date NOT NULL,
  `examStart` time NOT NULL,
  `examEnd` time NOT NULL,
  PRIMARY KEY (`courseID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course_completed`
--

DROP TABLE IF EXISTS `course_completed`;
CREATE TABLE IF NOT EXISTS `course_completed` (
  `userid` varchar(128) NOT NULL,
  `code` varchar(100) NOT NULL,
  PRIMARY KEY (`userid`,`code`),
  KEY `COURSE_COMPLETED_FK2` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

DROP TABLE IF EXISTS `prerequisite`;
CREATE TABLE IF NOT EXISTS `prerequisite` (
  `course` varchar(100) NOT NULL,
  `prerequisite` varchar(100) NOT NULL,
  PRIMARY KEY (`course`,`prerequisite`),
  KEY `PREREQUISITE_FK2` (`prerequisite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `coursesID` varchar(100) NOT NULL,
  `sectionID` varchar(3) NOT NULL,
  `day` int(1) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `minbid` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`coursesID`,`sectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `userid` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `name` varchar(100) NOT NULL,
  `school` varchar(100) NOT NULL,
  `edollar` decimal(5,2) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_section`
--

DROP TABLE IF EXISTS `student_section`;
CREATE TABLE IF NOT EXISTS `student_section` (
  `userid` varchar(128) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  `course` varchar(100) NOT NULL,
  `section` varchar(3) NOT NULL,
  `bidstatus` varchar(50) DEFAULT NULL,
  `bidround` int(1) NOT NULL,
  PRIMARY KEY (`userid`,`amount`,`course`,`section`,`bidround`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bid`
--
ALTER TABLE `bid`
  ADD CONSTRAINT `BID_FK1` FOREIGN KEY (`userid`) REFERENCES `student` (`userid`),
  ADD CONSTRAINT `BID_FK2` FOREIGN KEY (`code`,`section`) REFERENCES `section` (`coursesID`, `sectionID`);

--
-- Constraints for table `course_completed`
--
ALTER TABLE `course_completed`
  ADD CONSTRAINT `COURSE_COMPLETED_FK1` FOREIGN KEY (`userid`) REFERENCES `student` (`userid`),
  ADD CONSTRAINT `COURSE_COMPLETED_FK2` FOREIGN KEY (`code`) REFERENCES `course` (`courseID`);

--
-- Constraints for table `prerequisite`
--
ALTER TABLE `prerequisite`
  ADD CONSTRAINT `PREREQUISITE_FK1` FOREIGN KEY (`course`) REFERENCES `course` (`courseID`),
  ADD CONSTRAINT `PREREQUISITE_FK2` FOREIGN KEY (`prerequisite`) REFERENCES `course` (`courseID`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `SECTION_FK1` FOREIGN KEY (`coursesID`) REFERENCES `course` (`courseID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
