-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `arena`;
CREATE TABLE `arena` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` int(11) NOT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `arena` (`id`, `name`, `address`, `zip_code`, `longitude`, `latitude`, `created_at`, `updated_at`) VALUES
(25,	'gymnase du polygone',	'10 Rue Maryse Bastié, VALENCE',	26000,	4.9044346,	44.9371644,	'2022-10-13 15:30:12',	NULL),
(26,	'Roller Hockey Bourges',	'impasse Gustave Pailloux BOURGES',	18000,	2.4104512,	47.110863,	'2022-10-13 15:30:40',	NULL),
(27,	'Gymnase Roger Frison-Roche',	'rue Louis Néel VILLARD-BONNOT',	38190,	5.8983092,	45.2500426,	'2022-10-13 15:31:14',	NULL),
(28,	'Varces Roller Hockey Club',	'Rue Charles de Gaulle VARCES',	38760,	5.6845032,	45.0892147,	'2022-10-13 15:31:48',	NULL),
(29,	'Seynod rhs',	'54 avanue des Neigeos  ANNECY',	74600,	6.11667,	45.9,	'2022-10-13 15:32:27',	NULL),
(30,	'Stade de la duchère',	'270 Av. Andrei Sakharov, Lyon',	69009,	4.7995002,	45.7870668,	'2022-10-13 15:33:03',	NULL),
(31,	'Complexe sportif Marcy l\'étoile',	'avenue Jean Colomb  MARCY L ETOILE',	69280,	4.7102837,	45.7831002,	'2022-10-13 15:33:44',	'2022-10-13 15:43:10'),
(32,	'Gymnase Pierre COULANGE',	'2 boulevard du Docteur SCWHEITZER  AIX EN PROVENCE',	13090,	5.44973,	43.5283,	'2022-10-13 15:34:10',	NULL),
(33,	'Espace Pierre Cot',	'Quai des Allobroges CHAMBERY',	73000,	5.9158726,	45.5726446,	'2022-10-13 15:34:34',	NULL),
(34,	'Complexe Sportif Albert Batteux',	'rue François Joseph Gossec MONTPELLIER',	34070,	3.8611192,	43.5751005,	'2022-10-13 15:35:11',	NULL);

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `category` (`id`, `name`, `created_at`, `updated_at`) VALUES
(7,	'U7',	'2022-10-13 10:42:11',	NULL),
(8,	'U9',	'2022-10-13 10:42:11',	NULL),
(9,	'U11',	'2022-10-13 10:42:11',	NULL),
(10,	'U13',	'2022-10-13 10:42:11',	NULL),
(11,	'U15',	'2022-10-13 10:42:11',	NULL),
(12,	'U17',	'2022-10-13 10:42:11',	NULL),
(13,	'U20',	'2022-10-13 15:22:53',	NULL),
(14,	'Loisir',	'2022-10-13 15:23:16',	NULL),
(15,	'Régional',	'2022-10-13 15:23:27',	NULL),
(16,	'Pré National',	'2022-10-13 15:23:39',	NULL),
(17,	'N3',	'2022-10-13 15:23:46',	NULL),
(18,	'N2',	'2022-10-13 15:23:56',	NULL),
(19,	'N1',	'2022-10-13 15:24:04',	NULL),
(20,	'Elite',	'2022-10-13 15:24:13',	NULL),
(21,	'Féminine N2',	'2022-10-13 15:24:26',	NULL),
(22,	'Féminine N1',	'2022-10-13 15:24:36',	NULL);

DROP TABLE IF EXISTS `club`;
CREATE TABLE `club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` int(11) NOT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `club` (`id`, `name`, `address`, `zip_code`, `longitude`, `latitude`, `created_at`, `updated_at`) VALUES
(21,	'Les Aiglons',	'10 Rue Maryse Bastié, VALENCE',	26000,	4.9044346,	44.9371644,	'2022-10-13 15:37:02',	NULL),
(22,	'Les Alchimistes',	'impasse Gustave Pailloux BOURGES',	18000,	2.4104512,	47.110863,	'2022-10-13 15:37:42',	NULL),
(23,	'Les Dauphins',	'rue Louis Néel VILLARD-BONNOT',	38190,	5.8983092,	45.2500426,	'2022-10-13 15:38:37',	NULL),
(24,	'Les Frelons',	'Rue Charles de Gaulle VARCES',	38760,	5.6845032,	45.0892147,	'2022-10-13 15:39:03',	NULL),
(25,	'Seynod Roller Hockey',	'54 avanue des Neigeos  ANNECY',	74600,	6.11667,	45.9,	'2022-10-13 15:39:31',	NULL),
(26,	'Lyon Roller Metropole',	'270 Av. Andrei Sakharov Lyon',	69009,	4.7995002,	45.7870668,	'2022-10-13 15:40:33',	NULL),
(27,	'Les Abeilles',	'avenue Jean Colomb MARCY L ETOILE',	69280,	4.7102837,	45.7831002,	'2022-10-13 15:41:08',	NULL),
(28,	'Salyens',	'2 boulevard du Docteur SCWHEITZER  AIX EN PROVENCE',	13090,	5.44973,	43.5283,	'2022-10-13 15:41:36',	NULL),
(29,	'Diabolik\'s',	'Quai des Allobroges CHAMBERY',	73000,	5.9158726,	45.5726446,	'2022-10-13 15:42:06',	NULL),
(30,	'Les Mantas',	'rue François Joseph Gossec MONTPELLIER',	34070,	3.8611192,	43.5751005,	'2022-10-13 15:42:34',	NULL);

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20221012122619',	'2022-10-12 14:26:33',	180);

DROP TABLE IF EXISTS `game`;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arena_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_232B318C663565CF` (`arena_id`),
  KEY `IDX_232B318CC54C8C93` (`type_id`),
  CONSTRAINT `FK_232B318C663565CF` FOREIGN KEY (`arena_id`) REFERENCES `arena` (`id`),
  CONSTRAINT `FK_232B318CC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `game` (`id`, `arena_id`, `type_id`, `date`, `created_at`, `updated_at`) VALUES
(42,	25,	14,	'2022-10-16 10:00:00',	'2022-10-13 15:46:16',	NULL),
(43,	25,	14,	'2022-10-16 11:30:00',	'2022-10-13 15:47:39',	NULL),
(44,	28,	15,	'2022-10-23 15:00:00',	'2022-10-13 15:49:16',	NULL),
(45,	34,	17,	'2022-10-22 20:30:00',	'2022-10-13 15:53:29',	NULL),
(46,	30,	15,	'2022-11-27 12:12:00',	'2022-10-13 16:08:43',	NULL),
(47,	31,	14,	'2022-11-05 20:20:00',	'2022-10-13 16:12:38',	NULL),
(48,	29,	14,	'2023-01-15 14:15:00',	'2022-10-13 16:15:46',	NULL),
(49,	30,	13,	'2023-01-15 16:30:00',	'2022-10-13 16:23:11',	NULL),
(50,	31,	15,	'2022-10-09 16:25:00',	'2022-10-13 16:25:31',	NULL),
(51,	33,	13,	'2023-03-12 10:30:00',	'2022-10-13 16:30:49',	NULL),
(52,	25,	17,	'2022-12-18 15:30:00',	'2022-10-13 16:32:14',	NULL);

DROP TABLE IF EXISTS `game_team`;
CREATE TABLE `game_team` (
  `game_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`team_id`),
  KEY `IDX_2FF5CA33E48FD905` (`game_id`),
  KEY `IDX_2FF5CA33296CD8AE` (`team_id`),
  CONSTRAINT `FK_2FF5CA33296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2FF5CA33E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `game_team` (`game_id`, `team_id`) VALUES
(42,	31),
(42,	32),
(43,	33),
(43,	36),
(44,	37),
(44,	38),
(45,	39),
(45,	40),
(46,	41),
(46,	43),
(47,	45),
(47,	47),
(48,	32),
(48,	33),
(49,	34),
(49,	35),
(50,	49),
(50,	50),
(51,	51),
(51,	52),
(52,	43),
(52,	52);

DROP TABLE IF EXISTS `game_user`;
CREATE TABLE `game_user` (
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `IDX_6686BA65E48FD905` (`game_id`),
  KEY `IDX_6686BA65A76ED395` (`user_id`),
  CONSTRAINT `FK_6686BA65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6686BA65E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `game_user` (`game_id`, `user_id`) VALUES
(43,	43),
(44,	41),
(44,	47),
(46,	37),
(48,	46),
(48,	47),
(50,	40),
(50,	48);

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_C4E0A61F61190A32` (`club_id`),
  KEY `IDX_C4E0A61F12469DE2` (`category_id`),
  CONSTRAINT `FK_C4E0A61F12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_C4E0A61F61190A32` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `team` (`id`, `club_id`, `category_id`, `name`, `created_at`, `updated_at`) VALUES
(31,	21,	15,	'Les Aiglons Régio',	'2022-10-13 15:43:45',	NULL),
(32,	23,	15,	'Les Dauphins Régio',	'2022-10-13 15:44:08',	NULL),
(33,	24,	15,	'Les Frelons Régio',	'2022-10-13 15:44:28',	NULL),
(34,	21,	14,	'Les Aiglons Loisirs',	'2022-10-13 15:44:49',	NULL),
(35,	26,	14,	'Lyon Loisirs',	'2022-10-13 15:45:07',	NULL),
(36,	25,	15,	'Seynod Régio',	'2022-10-13 15:47:07',	NULL),
(37,	21,	11,	'Les Aiglons U15',	'2022-10-13 15:48:13',	NULL),
(38,	24,	11,	'Les Frelons U15',	'2022-10-13 15:48:32',	NULL),
(39,	21,	17,	'Les Aiglons N3',	'2022-10-13 15:50:11',	NULL),
(40,	22,	17,	'Les Alchimistes N3',	'2022-10-13 15:50:27',	NULL),
(41,	21,	10,	'Les Aiglons U13 A',	'2022-10-13 16:06:58',	'2022-10-13 16:07:45'),
(42,	23,	10,	'Les Dauphins U13',	'2022-10-13 16:07:36',	NULL),
(43,	21,	10,	'Les Aiglons U13 B',	'2022-10-13 16:08:00',	NULL),
(44,	30,	18,	'Les Mantas N2',	'2022-10-13 16:09:38',	NULL),
(45,	27,	18,	'Les Abeilles N2',	'2022-10-13 16:10:08',	NULL),
(46,	28,	17,	'Salyens N3',	'2022-10-13 16:10:46',	NULL),
(47,	25,	18,	'Seynod N2',	'2022-10-13 16:11:17',	NULL),
(48,	21,	18,	'Les Aiglons N2',	'2022-10-13 16:11:32',	NULL),
(49,	29,	12,	'Diabolik U17',	'2022-10-13 16:24:20',	NULL),
(50,	27,	12,	'Abeilles U17',	'2022-10-13 16:24:57',	NULL),
(51,	30,	9,	'Mantas U11',	'2022-10-13 16:29:27',	NULL),
(52,	29,	10,	'Diabolik\'s U13',	'2022-10-13 16:29:50',	NULL);

DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(13,	'match amical',	'2022-10-13 15:20:16',	NULL),
(14,	'match de poule',	'2022-10-13 15:20:37',	NULL),
(15,	'quart de finale',	'2022-10-13 15:20:49',	NULL),
(16,	'demi finale',	'2022-10-13 15:21:00',	NULL),
(17,	'finale',	'2022-10-13 15:21:08',	NULL);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `licence_id` int(11) DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` int(11) DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sign_up_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  UNIQUE KEY `UNIQ_8D93D64926EF07C9` (`licence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `roles`, `password`, `licence_id`, `level`, `address`, `zip_code`, `longitude`, `latitude`, `created_at`, `updated_at`, `phone_number`, `sign_up_token`) VALUES
(33,	'user1',	'Referee',	'ref1@user.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	667867,	'D3',	'5 Rue du Stade, 16000 Angoulême',	55509,	7.6991209,	48.5866336,	'2022-10-13 10:42:11',	NULL,	'0601020304',	NULL),
(34,	'user2',	'Referee',	'ref2@user.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	829779,	'D3',	'10 Rue Maryse Bastié, 26000 Valence',	78404,	6.4662469,	46.361827,	'2022-10-13 10:42:11',	NULL,	'0604030201',	NULL),
(35,	'user3',	'TeamHead',	'th@user.fr',	'[\"ROLE_TEAMHEAD\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	NULL,	NULL,	'13 Av. Joseph Fallen, 13400 Aubagne',	58733,	2.3744378,	48.8356758,	'2022-10-13 10:42:11',	NULL,	'0703030303',	NULL),
(36,	'user4',	'Admin',	'admin@user.fr',	'[\"ROLE_ADMIN\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	NULL,	NULL,	'5 Rue du Stade, 16000 Angoulême',	83466,	-0.5983295,	44.8310771,	'2022-10-13 10:42:11',	NULL,	'0703030303',	NULL),
(37,	'Isaac',	'Besnard',	'bernadette72@laroche.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	642337,	'D1',	'7 Rue Jean Giono, 75013 Paris',	93838,	-1.65071179,	48.1325361,	'2022-10-13 10:42:11',	NULL,	'0763998860',	NULL),
(38,	'Martine',	'Lebrun',	'thierry.roy@orange.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	167944,	'D1',	'Avenue Maurice Martin, 33000 Bordeaux',	41546,	5.5650095,	43.2938845,	'2022-10-13 10:42:11',	NULL,	'+33 6 50 22 47 17',	NULL),
(39,	'Odette',	'Herve',	'augustin.mahe@hotmail.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	640733,	'D3',	'5 Rue du Stade, 16000 Angoulême',	97994,	0.176271,	45.6418096,	'2022-10-13 10:42:11',	NULL,	'0769578039',	NULL),
(40,	'Margaux',	'Auger',	'bertrand09@gmail.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	129039,	'D3',	'11 Rue Colette, 67200 Strasbourg',	14650,	-0.5983295,	44.8310771,	'2022-10-13 10:42:11',	NULL,	'0799324632',	NULL),
(41,	'Tristan',	'Le Roux',	'bertin.roland@sauvage.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	638051,	'D4',	'Rue Charles de Gaulle, 38760 VARCES',	92855,	5.6845032,	45.0892147,	'2022-10-13 10:42:11',	NULL,	'0755003239',	NULL),
(42,	'David',	'Lebon',	'qguillot@gmail.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	116838,	'D3',	'Rue Charles de Gaulle, 38760 VARCES',	13881,	-1.65071179,	48.1325361,	'2022-10-13 10:42:11',	NULL,	'+33 6 02 83 66 32',	NULL),
(43,	'Patrick',	'Pages',	'gabriel67@wanadoo.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	290930,	'D4',	'343 Rue de Marquillies, 59000 Lille',	82338,	3.0571769,	50.61448,	'2022-10-13 10:42:11',	NULL,	'+33 (0)7 82 97 24 27',	NULL),
(44,	'Vincent',	'Becker',	'marc44@hotmail.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	791359,	'D4',	'8 Avenue des Gayeulles, 35700 Rennes',	20129,	5.6845032,	45.0892147,	'2022-10-13 10:42:11',	NULL,	'+33 6 43 96 24 10',	NULL),
(45,	'Josette',	'Parent',	'maryse85@hotmail.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	278393,	'D3',	'Avenue Maurice Martin, 33000 Bordeaux',	79963,	4.9,	44.9333,	'2022-10-13 10:42:11',	NULL,	'+33 (0)7 72 64 22 13',	NULL),
(46,	'Susan',	'Hubert',	'afrancois@denis.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	687887,	'D4',	'7 Rue Jean Giono, 75013 Paris',	97873,	2.3744378,	48.8356758,	'2022-10-13 10:42:11',	NULL,	'+33 (0)6 76 56 27 23',	NULL),
(47,	'Hortense',	'Guillou',	'blanchard.edouard@denis.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	383895,	'D2',	'11 Rue Colette, 67200 Strasbourg',	59168,	4.9,	44.9333,	'2022-10-13 10:42:11',	NULL,	'06 30 77 30 48',	NULL),
(48,	'Antoine',	'Guilbert',	'gchretien@martel.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	468226,	'D1',	'343 Rue de Marquillies, 59000 Lille',	49124,	-1.65071179,	48.1325361,	'2022-10-13 10:42:11',	NULL,	'+33 6 75 60 85 70',	NULL),
(49,	'Éléonore',	'Barre',	'baudry.camille@jacques.net',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	495108,	'D4',	'8 Avenue des Gayeulles, 35700 Rennes',	51416,	6.0926087,	45.8822264,	'2022-10-13 10:42:11',	NULL,	'+33 7 56 02 64 25',	NULL),
(50,	'Manon',	'Pinto',	'dorothee.dupuy@live.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	906052,	'D2',	'54 avenue des Neigeos, 74600 ANNECY',	81537,	6.4662469,	46.361827,	'2022-10-13 10:42:11',	NULL,	'+33 (0)6 64 94 81 89',	NULL),
(51,	'Monique',	'Olivier',	'marie33@mendes.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	537059,	'D2',	'54 avenue des Neigeos, 74600 ANNECY',	51795,	2.3744378,	48.8356758,	'2022-10-13 10:42:11',	NULL,	'+33 (0)7 32 68 03 58',	NULL),
(52,	'Suzanne',	'Mendes',	'smuller@wanadoo.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	736591,	'D4',	'5 Rue du Stade, 16000 Angoulême',	12867,	7.6991209,	48.5866336,	'2022-10-13 10:42:11',	NULL,	'+33 (0)6 83 84 85 82',	NULL),
(53,	'Rémy',	'Delannoy',	'emile.besnard@hotmail.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	870846,	'D3',	'7 Rue Jean Giono, 75013 Paris',	98006,	-1.65071179,	48.1325361,	'2022-10-13 10:42:11',	NULL,	'0617689334',	NULL),
(54,	'Benoît',	'Guillaume',	'rraynaud@meunier.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	695569,	'D3',	'7 Rue Jean Giono, 75013 Paris',	10505,	6.4662469,	46.361827,	'2022-10-13 10:42:11',	NULL,	'+33 (0)7 81 45 78 09',	NULL),
(55,	'Anouk',	'Lecoq',	'ferrand.jeannine@yahoo.fr',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	732600,	'D1',	'10 Rue Maryse Bastié, 26000 Valence',	39954,	0.176271,	45.6418096,	'2022-10-13 10:42:11',	NULL,	'+33 7 55 37 11 05',	NULL),
(56,	'Adélaïde',	'Marin',	'franck.perrin@martinez.org',	'[\"ROLE_REFEREE\"]',	'$2y$13$Znq9b79/qWlzmKO4DjCtROwriD70ugPlLuX6LlCyOxcL17l0o41jq',	435891,	'D3',	'10 Rue Maryse Bastié, 26000 Valence',	98697,	5.6845032,	45.0892147,	'2022-10-13 10:42:11',	NULL,	'0698954701',	NULL),
(57,	'Ycare',	'Amel',	'ycar.amel@gg.com',	'[\"ROLE_REFEREE\"]',	'$2y$13$bxhDtzm2pPXMx0e05aqKn.MYdQ7hQW.o0cPmUB128GZ4iUy2I13zC',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2022-10-13 10:43:34',	NULL,	NULL,	'validate');

-- 2022-10-13 14:40:28