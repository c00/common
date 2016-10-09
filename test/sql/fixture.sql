-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teamId` int(11) NOT NULL,
  `challengeId` int(11) NOT NULL,
  `code` varchar(45) DEFAULT NULL,
  `text` text,
  `image` varchar(100) DEFAULT NULL,
  `created` int(11) NOT NULL,
  `correct` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `challenge`;
CREATE TABLE `challenge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `long` varchar(100) NOT NULL,
  `lat` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `googleName` varchar(150) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `active` int(11) NOT NULL,
  `unlockMethod` varchar(45) NOT NULL DEFAULT 'code',
  `answerMethod` varchar(45) NOT NULL DEFAULT 'text',
  `unlockParent` varchar(45) DEFAULT NULL,
  `unlockCode` varchar(45) NOT NULL,
  `answerCode` varchar(45) DEFAULT NULL,
  `comesAfterChallengeId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unlockCode_UNIQUE` (`unlockCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `challenge` (`id`, `long`, `lat`, `name`, `description`, `googleName`, `image`, `active`, `unlockMethod`, `answerMethod`, `unlockParent`, `unlockCode`, `answerCode`, `comesAfterChallengeId`) VALUES
  (1,	'115.20606994629',	'-8.6818332048244',	'FOO MOFO',	'q',	'Jl. Pulau Sayang No.26, Dauh Puri Kauh, Denpasar Bar., Kota Denpasar, Bali 80114, Indonesia',	'challenge.57da25343808d1.88798582.jpg',	1,	'code',	'picture',	NULL,	'foo123',	NULL,	NULL),
  (2,	'115.1762008667',	'-8.6424620310207',	'New challenge!',	'Bladiebla',	'Jl. Muding Batu Sangian IV No.21X, Kerobokan Kaja, Kuta Utara, Kabupaten Badung, Bali 80361, Indonesia',	'challenge.57da2ada99ddc4.43827954.jpg',	1,	'code',	'picture',	NULL,	'Things and stuff.',	NULL,	NULL),
  (3,	'115.16521453857',	'-8.6848876924207',	'Hangman first',	'bladiebla',	'Jl. Drupadi No.6, Seminyak, Kuta, Kabupaten Badung, Bali 80361, Indonesia',	'challenge.57e4cb8ebe9ae6.28584764.jpg',	1,	'hangman',	'picture',	NULL,	'8eb18932',	NULL,	37);

DROP TABLE IF EXISTS `color`;
CREATE TABLE `color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `hex` varchar(7) NOT NULL,
  `forecolor` varchar(7) NOT NULL DEFAULT '#000000',
  `dark` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `color` (`id`, `name`, `hex`, `forecolor`, `dark`) VALUES
  (1,	'Blue',	'#158cba',	'#ffffff',	1),
  (2,	'Red',	'#ff291c',	'#ffffff',	1),
  (3,	'Purple',	'#a81cff',	'#ffffff',	1),
  (4,	'Yellow',	'#ffee1c',	'#000000',	0),
  (5,	'Green',	'#28b62c',	'#ffffff',	1),
  (6,	'White',	'#f9f9f9',	'#000000',	0),
  (7,	'Orange',	'#ff851b',	'#ffffff',	1);

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `long` varchar(100) NOT NULL,
  `lat` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `googleName` varchar(150) DEFAULT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `location` (`id`, `long`, `lat`, `name`, `description`, `googleName`, `active`) VALUES
  (1,	'172.60208129883',	'-43.537598280095',	'Nergens',	NULL,	'107 Blenheim Rd, Riccarton, Christchurch 8041, New Zealand',	1),
  (2,	'115.25035858154',	'-8.6719907979133',	'Another place',	'Here at the dojo',	'Jl. Sedap Malam No.531, Sanur Kaja, Denpasar Sel., Kota Denpasar, Bali 80239, Indonesia',	1);

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `teamId` int(11) NOT NULL,
  `fromTeam` int(11) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `text` text,
  `teamId` int(11) DEFAULT NULL,
  `challengeId` int(11) DEFAULT NULL,
  `answerId` int(11) DEFAULT NULL,
  `messageId` int(11) DEFAULT NULL,
  `date` int(11) NOT NULL,
  `read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `token` varchar(100) NOT NULL,
  `created` int(11) NOT NULL,
  `expires` int(11) NOT NULL,
  `renewed` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `notificationsChecked` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `team` (`id`, `code`, `name`, `image`, `active`) VALUES
  (1,	'aapjes44',	'The Dudemeisters',	'team.57f9f27b326a01.68481219.jpg',	1),
  (2,	'cattle6',	'The Chimpmunks',	NULL,	1),
  (3,	'twig56',	'Crazy Horses',	'team.578f553e0f2b65.87624891.jpg',	1);

DROP TABLE IF EXISTS `teamchallenge`;
CREATE TABLE `teamchallenge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teamId` int(11) NOT NULL,
  `challengeId` int(11) NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'locked',
  `latestAnswerId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `teamsession`;
CREATE TABLE `teamsession` (
  `token` varchar(100) NOT NULL,
  `created` int(11) NOT NULL,
  `expires` int(11) NOT NULL,
  `renewed` int(11) NOT NULL,
  `teamId` int(11) NOT NULL,
  `deviceId` varchar(255) DEFAULT NULL,
  `deviceType` varchar(45) NOT NULL DEFAULT 'unknown',
  `messagesChecked` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `isAdmin` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id`, `email`, `firstName`, `lastName`, `password`, `isAdmin`, `active`, `created`) VALUES
  (1,	'coo@covle.com',	'Co',	'Tekkel',	'$2y$10$C5HZpETJUGaVAFemG0aB1OI0Cq6qbnFk5lpNVaJ4cjFN3./nzES2q',	1,	1,	0);

-- 2016-10-09 13:50:20