-- Adminer 4.7.6 MySQL dump

INSERT INTO `category` (`name`, `created_at`) VALUES
('U7',	'2022-10-11 00:00:00'),
('U9',	'2022-10-11 00:00:00'),
('U11',	'2022-10-11 00:00:00'),
('U15',	'2022-10-11 00:00:00'),
('U17',	'2022-10-11 00:00:00'),
('U20',	'2022-10-11 00:00:00'),
('Loisir',	'2022-10-11 00:00:00'),
('Régional',	'2022-10-11 00:00:00'),
('Pré National',	'2022-10-11 00:00:00'),
('N3',	'2022-10-11 00:00:00'),
('N2',	'2022-10-11 00:00:00'),
('N1',	'2022-10-11 00:00:00'),
('Elite',	'2022-10-11 00:00:00'),
('Féminine N2',	'2022-10-11 00:00:00'),
('Féminine N1',	'2022-10-11 00:00:00');

INSERT INTO `type` (`name`, `created_at`) VALUES
('match amical',	'2022-10-11 00:00:00'),
('match de poule',	'2022-10-11 00:00:00'),
('quart de finale',	'2022-10-11 00:00:00'),
('demi finale',	'2022-10-11 00:00:00'),
('finale',	'2022-10-11 00:00:00');

INSERT INTO `user` (`firstname`, `lastname`, `email`, `password`, `created_at`, `roles`) VALUES
('admin',	'admin',	'findmearef@gmail.com',	'$2y$13$LZvVrqaT/gEPKNfRgVm6lOoG37h1rcrOieZrRFcBT3Litp0VeeSj.', '2022-10-11 00:00:00', '[\"ROLE_ADMIN\"]');

-- 2022-10-11 11:52:51