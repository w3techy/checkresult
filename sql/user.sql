-- Result Checker Database Schema: user
-- Engine: InnoDB
-- Charset: utf8mb4

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `exam_number` varchar(50) NOT NULL,
  `pin` varchar(255) NOT NULL COMMENT 'Bcrypt or Argon2 hashed pin',
  PRIMARY KEY (`exam_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Student Accounts
-- Passwords corresponding to the hashes below:
-- STUDENT001 -> PIN001
-- STUDENT002 -> PIN002
-- STUDENT003 -> PIN003
-- EXAM12345  -> SECUREPIN

INSERT INTO `user` (`exam_number`, `pin`) VALUES
('STUDENT001', '$2y$10$7uvEFoH0RASLbrcpUwvXludUVuX10tVygJz70h64fO.nr31Gj3Wjy'),
('STUDENT002', '$2y$10$NyIqpP7MiRJBmw6gxUWTVOjeN1eE/LGanMBNFv1HfbNEO/S8xqg9K'),
('STUDENT003', '$2y$10$L1n.FpvmUVjQsR8x8xuDvOhUUFOo2Q5CkcP2bS03IoGLs.XOoCTLe'),
('EXAM12345',  '$2y$10$pzR3kUezP5MlSsK8.vf.6O0vOS06wghTaDuAVwI6D7Hj7HzzzPL2W');
