-- Result Checker Database Schema: results
-- Engine: InnoDB
-- Charset: utf8mb4

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_number` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `score` int(3) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `uq_student_subject` (`exam_number`, `subject_id`),
  KEY `exam_number` (`exam_number`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `fk_results_user` FOREIGN KEY (`exam_number`) REFERENCES `user` (`exam_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_results_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

-- Sample Results Data
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
('STUDENT003', 6, 72, 'A-', 'Good work in Biology');
