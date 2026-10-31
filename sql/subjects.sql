-- Result Checker Database Schema: subjects
-- Engine: InnoDB
-- Charset: utf8mb4

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_name` (`subject_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

-- Default Subjects
INSERT INTO `subjects` (`subject_name`) VALUES
('English Language'),
('Mathematics'),
('Chemistry'),
('Commerce'),
('Physics'),
('Biology');
