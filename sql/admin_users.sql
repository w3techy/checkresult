-- Result Checker Database Schema: admin_users
-- Engine: InnoDB
-- Charset: utf8mb4

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Bcrypt or Argon2 hashed password',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

-- Default Admin Account
-- Username: admin
-- Password: admin123 (Change immediately upon installation)
INSERT INTO `admin_users` (`username`, `password`) VALUES
('admin', '$2y$10$Ou.1bUzgP//5p9f4FL7qs.Nwn0D/gZRBfig7vHw9bnO3Gc4AaD/Ce');
