-- phpMyAdmin SQL Dump
-- Generation Time: [Current Date/Time or original if preferred, this is a new file]
-- Server version: 5.1.62 (Example, can be updated)
-- PHP Version: 5.5.9 (Example, can be updated)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `result`
--

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_number` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `score` int(3) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`result_id`),
  KEY `exam_number` (`exam_number`),
  KEY `subject_id` (`subject_id`)
  -- Note: Actual FOREIGN KEY constraints might fail in MyISAM or older MySQL versions during dump import.
  -- For MyISAM, these are effectively comments. For InnoDB, they would be active.
  -- CONSTRAINT `fk_results_user` FOREIGN KEY (`exam_number`) REFERENCES `user` (`exam_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  -- CONSTRAINT `fk_results_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Dumping data for table `results`
--
-- Assuming subjects table has:
-- 1: English Language
-- 2: Mathematics
-- 3: Chemistry
-- 4: Commerce
-- 5: Physics
-- 6: Biology
--
-- Assuming user table has:
-- 'STUDENT001'
-- 'STUDENT002'
-- 'STUDENT003'
-- 'EXAM12345'

INSERT INTO `results` (`exam_number`, `subject_id`, `score`, `grade`, `remarks`) VALUES
('STUDENT001', 1, 75, 'A', 'Excellent'),
('STUDENT001', 2, 82, 'A+', 'Outstanding'),
('STUDENT001', 3, 60, 'B', 'Good Effort'),

('STUDENT002', 1, 55, 'C', 'Satisfactory'),
('STUDENT002', 2, 48, 'D', 'Needs Improvement'),
('STUDENT002', 4, 65, 'B', 'Well Done'),
('STUDENT002', 5, 70, 'A-', 'Very Good'),

('STUDENT003', 1, 88, 'A+', 'Exceptional'),
('STUDENT003', 2, 92, 'A+', 'Perfect Score in Math!'),
('STUDENT003', 3, 78, 'A', 'Strong Performance'),
('STUDENT003', 5, 85, 'A', 'Excellent in Physics'),
('STUDENT003', 6, 72, 'A-', 'Good work in Biology'),

-- EXAM12345 has no results yet for testing empty cases.
-- ('EXAM12345', 2, 30, 'F', 'Failed');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
