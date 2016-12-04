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

INSERT INTO `answer` (`id`, `teamId`, `challengeId`, `code`, `text`, `image`, `correct`, `created`) VALUES
  (136,	31,	30,	NULL,	'',	'answer.57cfb172b335b2.88401045.jpg',	1,	1473229170),
  (137,	31,	30,	NULL,	'7',	'answer.57cfb2065d8914.48766956.jpg',	1,	1473229318),
  (138,	33,	40,	NULL,	'',	'answer.57cfb3eee33763.01576882.jpg',	1,	1473229806),
  (139,	31,	36,	NULL,	'',	'answer.57cfb4016b6c01.01838826.jpg',	1,	1473229825),
  (140,	38,	51,	NULL,	'',	'answer.57cfb44132db95.43058419.jpg',	1,	1473229889),
  (141,	33,	55,	NULL,	'This is Richie sembora n 18 gold disk ',	'answer.57cfb490114795.78022947.jpg',	1,	1473229968),
  (142,	31,	36,	NULL,	'Surface core i7 ',	'answer.57cfb4a3019ad0.93140524.jpg',	1,	1473229987),
  (143,	38,	51,	NULL,	'EHS',	'answer.57cfb560bc78a4.07629408.jpg',	1,	1473230176),
  (144,	32,	42,	NULL,	'',	'answer.57cfb5b227d238.12384961.jpg',	1,	1473230258),
  (145,	32,	31,	NULL,	'1. Hot air balloon\n2. The gate \n3 . The skeleton leg\n4.6 \n5. Seashell\n\n3 program for partner\n1. Mega bucks\n2. Surface deployment and adoption BIF\n3. Apple compete BIf\n',	'answer.57cfb61997f634.20232108.jpg',	1,	1473230361),
  (146,	38,	30,	NULL,	' 7',	'answer.57cfb63f0f4cf8.83698376.jpg',	1,	1473230399),
  (147,	38,	30,	NULL,	' 7',	'answer.57cfb6935e8664.16736721.jpg',	1,	1473230483),
  (148,	31,	53,	NULL,	'Optimised ',	'answer.57cfb69881f1e0.97059063.jpg',	1,	1473230488),
  (149,	38,	30,	NULL,	' Seven',	'answer.57cfb6c59ffd45.32713637.jpg',	1,	1473230533),
  (150,	38,	30,	NULL,	' Seven',	'answer.57cfb71f32af73.84107502.jpg',	1,	1473230623),
  (151,	38,	30,	NULL,	' Seven',	'answer.57cfb77ecbd5d2.00492076.jpg',	1,	1473230718),
  (152,	31,	53,	NULL,	'Optimised ',	'answer.57cfb7b6bad272.94314179.jpg',	1,	1473230774),
  (153,	32,	40,	NULL,	'',	'answer.57cfb9003600c2.25756839.jpg',	1,	1473231104),
  (154,	33,	31,	NULL,	'',	'answer.57cfb990758744.48118044.jpg',	1,	1473231248),
  (155,	32,	55,	NULL,	'',	'answer.57cfb9a0447c74.50046971.jpg',	1,	1473231264),
  (156,	31,	56,	NULL,	'Bars',	'answer.57cfb9a40cf117.70947866.jpg',	1,	1473231268),
  (157,	32,	55,	NULL,	'18',	'answer.57cfb9be516147.94429022.jpg',	1,	1473231294),
  (158,	33,	31,	NULL,	'Cloud,gate,right leg,7. Sea shell',	'answer.57cfb9d54c7bb9.74946901.jpg',	1,	1473231317),
  (159,	32,	55,	NULL,	'18',	'answer.57cfb9e4603b39.58923601.jpg',	1,	1473231332),
  (160,	33,	31,	NULL,	'Cloud,gate,right leg,7. Sea shell\nDocking, type cover ',	'answer.57cfb9f8c20f26.59051738.jpg',	1,	1473231352),
  (161,	38,	36,	NULL,	'They come with core i5 model and up not I\'m core m',	'answer.57cfba0e278283.07929496.jpg',	1,	1473231374),
  (162,	33,	42,	NULL,	'Merdeka promo, surface starter and laptop replacement promo',	'answer.57cfba5d43bd13.69492185.jpg',	1,	1473231453),
  (163,	33,	42,	NULL,	'Merdeka promo, surface starter and laptop replacement promo',	'answer.57cfbab8eabb46.09741357.jpg',	1,	1473231544),
  (164,	38,	53,	NULL,	'',	'answer.57cfbb13ddf5e6.38278404.jpg',	1,	1473231635),
  (165,	33,	31,	NULL,	'Cloud,gate,right leg,7. Sea shell\nDocking, type cover ',	'answer.57cfbb1d705622.69472934.jpg',	1,	1473231645),
  (166,	33,	42,	NULL,	'Merdeka promo, surface starter and laptop replacement promo',	'answer.57cfbb289893c6.39123775.jpg',	1,	1473231656),
  (167,	33,	55,	NULL,	'This is Richie sembora n 18 gold disk ',	'answer.57cfbb341acf37.01644305.jpg',	1,	1473231668),
  (168,	33,	42,	NULL,	'Merdeka promo, surface starter and laptop replacement promo,accessories promo',	'answer.57cfbb99952940.30374867.jpg',	1,	1473231769),
  (169,	38,	30,	NULL,	' Seven',	'answer.57cfbbe233ec83.83979018.jpg',	1,	1473231842),
  (170,	31,	34,	NULL,	'Hand ',	'answer.57cfbc2b083a26.65213333.jpg',	1,	1473231915),
  (171,	33,	42,	NULL,	'Demo program\nBiF\nMegabucks \n\n',	'answer.57cfbd5c330b72.89153363.jpg',	1,	1473232220),
  (172,	32,	39,	NULL,	'',	'answer.57cfbdcdcc9d52.61247460.jpg',	1,	1473232333),
  (173,	38,	34,	NULL,	'',	'answer.57cfbdd9f0b463.69842576.jpg',	1,	1473232345),
  (174,	31,	54,	NULL,	'Blessing',	'answer.57cfbdfb630310.08529936.jpg',	1,	1473232379),
  (175,	33,	39,	NULL,	'',	'answer.57cfbf7e5b7736.61290149.jpg',	1,	1473232766),
  (176,	31,	38,	NULL,	'',	'answer.57cfc012bba4a3.72575832.jpg',	1,	1473232914),
  (177,	38,	54,	NULL,	'',	'answer.57cfc12964dea3.21850964.jpg',	1,	1473233193),
  (178,	32,	38,	NULL,	'',	'answer.57cfc14ec12f50.04671569.jpg',	1,	1473233230),
  (179,	33,	38,	NULL,	'',	'answer.57cfc26d2013e3.44996819.jpg',	1,	1473233517),
  (180,	31,	39,	NULL,	'',	'answer.57cfc2a7e10414.00960397.jpg',	1,	1473233575),
  (181,	32,	54,	NULL,	'',	'answer.57cfc312afd8e9.82999915.jpg',	1,	1473233682),
  (182,	38,	38,	NULL,	'',	'answer.57cfc38ac2ba41.70376823.jpg',	1,	1473233802),
  (183,	32,	34,	NULL,	'',	'answer.57cfc40333dd13.17361488.jpg',	1,	1473233923),
  (184,	33,	54,	NULL,	'',	'answer.57cfc43f06fa54.17102044.jpg',	1,	1473233983),
  (185,	38,	39,	NULL,	'',	'answer.57cfc4a7d7c6f3.77828074.jpg',	1,	1473234087),
  (186,	38,	53,	NULL,	'',	'answer.57cfc52ed02964.22461959.jpg',	1,	1473234222),
  (187,	31,	37,	NULL,	'',	'answer.57cfc5871fc5c9.86372637.jpg',	1,	1473234311),
  (188,	32,	57,	NULL,	'',	'answer.57cfc59d0fd4f6.25988153.jpg',	1,	1473234333),
  (189,	33,	34,	NULL,	'',	'answer.57cfc5ef4df856.81766096.jpg',	1,	1473234415),
  (190,	33,	57,	NULL,	'',	'answer.57cfc6ce5326c5.87980177.jpg',	1,	1473234638),
  (191,	32,	53,	NULL,	'',	'answer.57cfc765d6a3b7.10672987.jpg',	1,	1473234789),
  (192,	33,	57,	NULL,	'',	'answer.57cfc76709dcb6.98431893.jpg',	1,	1473234791),
  (193,	32,	53,	NULL,	'Optimize ',	'answer.57cfc7e1e145c8.80898850.jpg',	1,	1473234913),
  (194,	38,	37,	NULL,	'',	'answer.57cfc801e1ac72.37384555.jpg',	1,	1473234945),
  (195,	33,	53,	NULL,	'Optimize ',	'answer.57cfc8dd5a71b2.92730475.jpg',	1,	1473235165),
  (196,	38,	37,	NULL,	'Type Cover & Docking',	'answer.57cfc8ed1b4213.80967155.jpg',	1,	1473235181),
  (197,	31,	31,	NULL,	'',	'answer.57cfc96babc315.25812050.jpg',	1,	1473235307),
  (198,	33,	36,	NULL,	'I7',	'answer.57cfc97b9b2564.22964063.jpg',	1,	1473235323),
  (199,	32,	36,	NULL,	'',	'answer.57cfc992d847b3.75779002.jpg',	1,	1473235346),
  (200,	33,	36,	NULL,	'I7',	'answer.57cfc9ab43c117.42220354.jpg',	1,	1473235371),
  (201,	32,	36,	NULL,	'I7',	'answer.57cfc9b0e651f5.86272359.jpg',	1,	1473235376),
  (202,	33,	36,	NULL,	'I7',	'answer.57cfca2b1c5b97.73673480.jpg',	1,	1473235499),
  (203,	31,	31,	NULL,	'',	'answer.57cfcb0180e485.24480522.jpg',	1,	1473235713),
  (204,	33,	30,	NULL,	'',	'answer.57cfcbdbb6ee11.74274119.jpg',	1,	1473235931),
  (205,	31,	42,	NULL,	'',	'answer.57cfcbe154cd48.27460662.jpg',	1,	1473235937),
  (206,	32,	30,	NULL,	'',	'answer.57cfcbf88dbb12.25568040.jpg',	1,	1473235960),
  (207,	33,	30,	NULL,	'7',	'answer.57cfcbffa793c6.98997907.jpg',	1,	1473235967),
  (208,	32,	30,	NULL,	'7',	'answer.57cfcc0486e864.13920487.jpg',	1,	1473235972),
  (209,	33,	30,	NULL,	'7',	'answer.57cfcc23707426.52720917.jpg',	1,	1473236003),
  (210,	31,	42,	NULL,	'',	'answer.57cfcc351c7a58.80584474.jpg',	1,	1473236021),
  (211,	31,	42,	NULL,	'',	'answer.57cfcc63187453.80244749.jpg',	1,	1473236067),
  (212,	31,	42,	NULL,	'Rock',	'answer.57cfcc77aedf52.62848469.jpg',	1,	1473236087),
  (213,	33,	51,	NULL,	'',	'answer.57cfcd839dcf18.36139362.jpg',	1,	1473236355),
  (214,	32,	51,	NULL,	'',	'answer.57cfcdac7ba674.11657773.jpg',	1,	1473236396),
  (215,	33,	51,	NULL,	'Ehs ',	'answer.57cfcdb85cfbc9.03091424.jpg',	1,	1473236408),
  (216,	38,	31,	NULL,	'Balloons\nsteel Cage\nLeg\n7\nShells \n',	'answer.57cfcdd812a089.93095303.jpg',	1,	1473236440),
  (217,	32,	51,	NULL,	'Complete for business',	'answer.57cfcdf57a6d25.76841469.jpg',	1,	1473236469),
  (218,	32,	51,	NULL,	'Complete for business',	'answer.57cfce209909b0.39861560.jpg',	1,	1473236512),
  (219,	42,	54,	NULL,	'Vzgxhx',	'answer.57d8d4b10a93b7.22325711.jpg',	1,	1473828017),
  (220,	42,	31,	NULL,	'Just some text',	NULL,	1,	1473828132),
  (221,	42,	32,	NULL,	NULL,	'answer.57d8d5678bb689.24618876.jpg',	1,	1473828199),
  (222,	42,	54,	NULL,	'Vzgxhx penia',	'answer.57d8d4b10a93b7.22325711.jpg',	1,	1473829489),
  (223,	42,	54,	NULL,	NULL,	'answer.57d8dab8b706e4.65931580.jpg',	1,	1473829560),
  (224,	42,	35,	NULL,	NULL,	'answer.57d8ff757031a1.77205615.jpg',	1,	1473838965),
  (225,	42,	37,	NULL,	'Hdjudr\nFidjjr\n\n\n\nDjjdjdjrhej',	'answer.57d90655b9b8a1.60252170.jpg',	1,	1473840725),
  (226,	42,	37,	NULL,	'Hdjudr\nFidjjrg\n\n\n\nDjjdjdjrhej',	'answer.57d90655b9b8a1.60252170.jpg',	1,	1473848201),
  (227,	42,	54,	NULL,	NULL,	'answer.57d923add909d8.11712326.jpg',	1,	1473848237),
  (228,	42,	54,	NULL,	'Yduf',	'answer.57d923add909d8.11712326.jpg',	1,	1473848544),
  (229,	42,	54,	NULL,	'Yduf',	'answer.57d923add909d8.11712326.jpg',	1,	1473849143),
  (230,	42,	54,	NULL,	'Ydufggy',	'answer.57d923add909d8.11712326.jpg',	1,	1473849217),
  (231,	42,	54,	NULL,	'GghYdufggy',	'answer.57d923add909d8.11712326.jpg',	1,	1473849227),
  (237,	42,	34,	NULL,	'Jfififi',	NULL,	1,	1474302550),
  (242,	42,	54,	NULL,	'GghYdufggyghhgg',	'answer.57d923add909d8.11712326.jpg',	1,	1474358662),
  (301,	53,	35,	NULL,	'test1',	NULL,	1,	1475042747),
  (302,	53,	37,	NULL,	'text`1',	'answer.57eb5e471e25a7.47205165.jpg',	1,	1475042887),
  (303,	53,	59,	NULL,	'234',	NULL,	1,	1475042988),
  (304,	53,	59,	NULL,	'234fdbdf',	NULL,	1,	1475042997),
  (305,	53,	35,	NULL,	'test1ewfewfew',	NULL,	1,	1475043015),
  (306,	53,	59,	NULL,	'234fdbdfdef',	NULL,	1,	1475043899),
  (332,	31,	30,	NULL,	'7',	'answer.57ef2cf2d24a15.02567440.jpg',	1,	1475292402),
  (350,	61,	37,	NULL,	'',	'answer.57fc6e34551024.60897716.jpg',	1,	1476161076),
  (351,	63,	37,	NULL,	'smelly',	'answer.57fc6e43bf84a0.54339740.jpg',	1,	1476161091),
  (352,	62,	37,	NULL,	'It\'s ticklish!',	'answer.57fc6e5338ab94.38976338.jpg',	1,	1476161107),
  (353,	61,	37,	NULL,	'',	'answer.57fc6e59e778c6.43901552.jpg',	1,	1476161113),
  (354,	62,	37,	NULL,	'It\'s ticklish!',	'answer.57fc6e706a9435.74036807.jpg',	1,	1476161136),
  (355,	62,	34,	NULL,	'Mbo Kadek\'s big bum!',	'answer.57fc6eaeb95770.26742044.jpg',	1,	1476161198),
  (356,	61,	34,	NULL,	'',	'answer.57fc6f534ccb58.97767850.jpg',	1,	1476161363),
  (357,	61,	34,	NULL,	'',	'answer.57fc6f6fe9f450.68166378.jpg',	1,	1476161391),
  (358,	63,	34,	NULL,	'apa?',	'answer.57fc6f7ff3d0b0.96931078.jpg',	1,	1476161407),
  (359,	62,	35,	NULL,	'The sweetest gay couple alive!',	'answer.57fc6f85bceaa5.33321264.jpg',	1,	1476161413),
  (360,	62,	41,	NULL,	'Kepala Botak',	'answer.57fc70d356e9a4.26212801.jpg',	1,	1476161747),
  (361,	61,	41,	NULL,	'',	'answer.57fc70d4852b63.07422003.jpg',	1,	1476161748),
  (362,	61,	41,	NULL,	'Hello mother',	'answer.57fc70e83d9e66.69892436.jpg',	1,	1476161768),
  (364,	63,	41,	NULL,	'I\'m special, got 3 save buttons',	'answer.57fc7113a293c1.33142876.jpg',	1,	1476161811),
  (365,	61,	41,	NULL,	'Hello mother',	'answer.57fc71222c3ac6.53617250.jpg',	1,	1476161826),
  (367,	63,	41,	NULL,	'I\'m not special anymore',	'answer.57fc7187451ee0.83521811.jpg',	1,	1476161927),
  (368,	61,	41,	NULL,	'Hello mother',	'answer.57fc71b31f6929.74474283.jpg',	1,	1476161971),
  (369,	62,	30,	NULL,	NULL,	'answer.57fc72085e67a6.22637067.jpg',	1,	1476162056),
  (370,	61,	30,	NULL,	'',	'answer.57fc720880c990.67343248.jpg',	1,	1476162056),
  (371,	61,	30,	NULL,	'One guy at a time',	'answer.57fc72184173b9.59036479.jpg',	1,	1476162072),
  (372,	63,	30,	NULL,	'found the fat guy, not coconut trees though',	'answer.57fc721fe06a74.88817443.jpg',	1,	1476162079),
  (373,	61,	33,	NULL,	'4 drums and a guitar!',	'answer.57fc73fcebb723.58216506.jpg',	1,	1476162556),
  (374,	63,	33,	NULL,	'Samsung sucks',	'answer.57fc74261baa52.54363018.jpg',	1,	1476162598),
  (375,	62,	36,	NULL,	'nzns',	NULL,	1,	1476162698),
  (376,	62,	36,	NULL,	'test test',	'answer.57fc74e25e10e5.98812234.jpg',	1,	1476162786),
  (377,	62,	30,	NULL,	NULL,	'answer.57fc758011ea81.35621535.jpg',	1,	1476162944),
  (378,	62,	32,	NULL,	'Spot some wrinkles there?',	'answer.57fc76db845c39.43160185.jpg',	1,	1476163291),
  (379,	62,	33,	NULL,	NULL,	'answer.57fc770b359652.58550115.jpg',	1,	1476163339),
  (380,	63,	32,	NULL,	'Microsoft',	NULL,	1,	1476163371),
  (381,	63,	32,	NULL,	'Microsoft',	NULL,	1,	1476163383),
  (382,	63,	32,	NULL,	'Microsoft',	'answer.57fc77419f58f4.70004207.jpg',	1,	1476163393),
  (383,	61,	32,	NULL,	'',	'answer.57fc77bd995023.83391286.jpg',	1,	1476163517),
  (384,	61,	32,	NULL,	'',	'answer.57fc77c4e37bc0.37767822.jpg',	1,	1476163524),
  (385,	61,	32,	NULL,	'Don\'t leave me this way ',	'answer.57fc77fce655f0.77175990.jpg',	1,	1476163580),
  (386,	61,	32,	NULL,	'Don\'t leave me this way ',	'answer.57fc78247299e2.20805721.jpg',	1,	1476163620),
  (387,	61,	32,	NULL,	'Don\'t leave me this way ',	'answer.57fc782864e598.29767054.jpg',	1,	1476163624),
  (388,	61,	36,	NULL,	'',	'answer.57fc786dd564b6.73214065.jpg',	1,	1476163693),
  (389,	61,	36,	NULL,	'',	'answer.57fc787205a675.24131501.jpg',	1,	1476163698),
  (390,	62,	36,	NULL,	'test test',	'answer.57fc7891793822.73170963.jpg',	1,	1476163729),
  (391,	62,	33,	NULL,	'Spade of Bamboo',	'answer.57fc78ca8079e2.52380445.jpg',	1,	1476163786),
  (392,	61,	41,	NULL,	'Hello mother',	'answer.57fc78e2b0eb23.57583372.jpg',	1,	1476163810),
  (393,	61,	41,	NULL,	'Hello mother',	'answer.57fc79005dfec5.16250603.jpg',	1,	1476163840),
  (394,	61,	41,	NULL,	'Hello mother',	'answer.57fc790cf24285.06823545.jpg',	1,	1476163852),
  (395,	63,	36,	NULL,	'landscaping fucks with the app',	NULL,	1,	1476163859),
  (396,	63,	36,	NULL,	'landscaping fucks with the app',	'answer.57fc79259d7809.90961075.jpg',	1,	1476163877),
  (397,	61,	30,	NULL,	'11',	'answer.57fc792a3a9759.43938193.jpg',	1,	1476163882),
  (398,	63,	36,	NULL,	'landscaping fucks with the app',	'answer.57fc79259d7809.90961075.jpg',	1,	1476163894),
  (399,	61,	37,	NULL,	'',	'answer.57fc7986a33f36.21606361.jpg',	1,	1476163974),
  (400,	61,	37,	NULL,	'',	'answer.57fc7998e47b90.92912135.jpg',	1,	1476163992),
  (401,	61,	40,	NULL,	'',	'answer.57fc79d6625eb5.89649667.jpg',	1,	1476164054),
  (402,	61,	40,	NULL,	'',	'answer.57fc79e174de20.80131542.jpg',	1,	1476164065),
  (403,	61,	40,	NULL,	'',	'answer.57fc79ee626901.00435402.jpg',	1,	1476164078),
  (405,	62,	40,	NULL,	NULL,	'answer.57fc7a4eaf3a58.56710737.jpg',	1,	1476164174),
  (406,	63,	40,	NULL,	'smallest guitar on the planet',	'answer.57fc7a6da959d1.80546760.jpg',	1,	1476164205),
  (407,	61,	41,	NULL,	'Hello mother',	'answer.57fc7a73b1b232.85451469.jpg',	1,	1476164211),
  (408,	61,	41,	NULL,	'Hello mother',	'answer.57fc7a81a84784.71765482.jpg',	1,	1476164225),
  (409,	61,	41,	NULL,	'Hello mother',	'answer.57fc7a8ede3fa7.88621841.jpg',	1,	1476164238),
  (410,	62,	39,	NULL,	NULL,	'answer.57fc7af3718a32.15926418.jpg',	1,	1476164339),
  (417,	67,	31,	NULL,	'it\'s blue ',	'answer.580477f3cff013.33622684.jpg',	1,	1476687859),
  (418,	67,	34,	NULL,	NULL,	'answer.580478245f2616.77105988.jpg',	1,	1476687908),
  (419,	67,	32,	NULL,	'sticky ',	NULL,	1,	1476687935),
  (420,	67,	30,	NULL,	NULL,	'answer.58047881c55681.26299772.jpg',	1,	1476688001),
  (421,	67,	34,	NULL,	'bhhbbb',	'answer.580478245f2616.77105988.jpg',	1,	1476688136),
  (422,	67,	31,	NULL,	'it\'s blue  da buh dee',	'answer.580477f3cff013.33622684.jpg',	1,	1476688155),
  (423,	67,	37,	NULL,	NULL,	'answer.5804793220a5a0.58487830.jpg',	1,	1476688178),
  (424,	67,	37,	NULL,	NULL,	'answer.580479333a19d3.93531756.jpg',	1,	1476688179),
  (425,	67,	41,	NULL,	NULL,	'answer.58047a827bc573.99347883.jpg',	1,	1476688514),
  (426,	67,	30,	NULL,	NULL,	'answer.58047af1ae0f97.65979011.jpg',	1,	1476688625),
  (433,	68,	34,	NULL,	'',	'answer.5805c53747bf34.26582509.jpg',	1,	1476773175),
  (434,	68,	34,	NULL,	'',	'answer.5805c53f376c82.56183913.jpg',	1,	1476773183),
  (435,	68,	34,	NULL,	'',	'answer.5806e3e47d4ac0.23512341.jpg',	1,	1476846564),
  (436,	68,	34,	NULL,	'',	'answer.5806e3f15d9882.07506359.jpg',	1,	1476846577),
  (437,	68,	55,	NULL,	'',	'answer.5806e82f2a1583.62190446.jpg',	1,	1476847663),
  (438,	68,	55,	NULL,	'',	'answer.5806e831a8f2a2.02200134.jpg',	1,	1476847665),
  (439,	68,	32,	NULL,	'',	'answer.5806ea7a9a1c75.31047915.jpg',	1,	1476848250),
  (440,	67,	32,	NULL,	'sticky ',	'answer.5806ea93593593.47294266.jpg',	1,	1476848275),
  (441,	68,	32,	NULL,	'',	'answer.5806eaae72c504.11092186.jpg',	1,	1476848302),
  (442,	68,	34,	NULL,	'',	'answer.5806eb4be4d338.69872573.jpg',	1,	1476848459),
  (443,	68,	32,	NULL,	'',	'answer.580817949e9fc9.39352421.jpg',	1,	1476925332),
  (444,	68,	32,	NULL,	'',	'answer.5808179cb6a8d5.21650699.jpg',	1,	1476925340),
  (445,	67,	30,	NULL,	NULL,	'answer.58115e2dd36f41.63471582.jpg',	1,	1477533229),
  (446,	71,	40,	NULL,	'',	'answer.58193d066ee2d7.89443067.jpg',	1,	1478049030),
  (447,	71,	40,	NULL,	'',	'answer.58193d189fbca0.37827598.jpg',	1,	1478049048),
  (448,	70,	40,	NULL,	'',	'answer.58193d26507737.63066157.jpg',	1,	1478049062),
  (449,	70,	40,	NULL,	'',	'answer.58193d33110aa7.48236035.jpg',	1,	1478049075),
  (450,	71,	55,	NULL,	'',	'answer.58193da7da5e06.55221262.jpg',	1,	1478049191),
  (451,	71,	55,	NULL,	'',	'answer.58193daf4cffc4.21512508.jpg',	1,	1478049199),
  (452,	70,	55,	NULL,	'',	'answer.58193dc6636d21.06399417.jpg',	1,	1478049222),
  (453,	70,	55,	NULL,	'',	'answer.58193ddc3a7669.38707758.jpg',	1,	1478049244),
  (454,	70,	37,	NULL,	'',	'answer.581940a6a9f367.06796979.jpg',	1,	1478049958),
  (455,	70,	37,	NULL,	'',	'answer.581940aad5c1d9.07650869.jpg',	1,	1478049962),
  (456,	70,	37,	NULL,	'',	'answer.581940e3359802.16242641.jpg',	1,	1478050019),
  (457,	70,	37,	NULL,	'',	'answer.581940fd5e14c7.03386606.jpg',	1,	1478050045),
  (458,	71,	31,	NULL,	'',	'answer.581943bcc49768.79867112.jpg',	1,	1478050748),
  (459,	71,	31,	NULL,	'',	'answer.581943db35c928.20058446.jpg',	1,	1478050779),
  (460,	70,	31,	NULL,	'',	'answer.581945450ee0f0.37422151.jpg',	1,	1478051141),
  (461,	70,	31,	NULL,	'1. Red and blue\n2. Seahorse and shark\n3. 20 two handed robots',	'answer.5819457eb5ae84.87344867.jpg',	1,	1478051198),
  (462,	71,	37,	NULL,	'',	'answer.58194650d5e525.10571940.jpg',	1,	1478051408),
  (463,	71,	37,	NULL,	'',	'answer.58194659619f36.51115414.jpg',	1,	1478051417),
  (464,	70,	39,	NULL,	'',	'answer.581947274567f1.80329717.jpg',	1,	1478051623),
  (465,	70,	39,	NULL,	'',	'answer.5819472fe948a4.95118919.jpg',	1,	1478051631),
  (466,	71,	39,	NULL,	'',	'answer.581947bbf2cad5.15298430.jpg',	1,	1478051771),
  (467,	71,	39,	NULL,	'',	'answer.581947be5ea504.13011888.jpg',	1,	1478051774),
  (468,	70,	38,	NULL,	'',	'answer.581948d3262de6.57687402.jpg',	1,	1478052051),
  (469,	70,	38,	NULL,	'',	'answer.581948e141cdc3.17375921.jpg',	1,	1478052065),
  (470,	70,	38,	NULL,	'',	'answer.581949164c03e0.63507655.jpg',	1,	1478052118),
  (471,	70,	38,	NULL,	'',	'answer.581949369c3154.97950268.jpg',	1,	1478052150),
  (472,	71,	38,	NULL,	'',	'answer.58194a327cdda3.37061331.jpg',	1,	1478052402),
  (473,	71,	38,	NULL,	'',	'answer.58194a5a0c1b46.41446383.jpg',	1,	1478052442),
  (474,	70,	34,	NULL,	'',	'answer.58194aca508ea7.65679665.jpg',	1,	1478052554),
  (475,	70,	34,	NULL,	'',	'answer.58194acfe553e6.57737209.jpg',	1,	1478052559),
  (476,	71,	34,	NULL,	'',	'answer.58194afaf07ea1.63496475.jpg',	1,	1478052602),
  (477,	71,	34,	NULL,	'',	'answer.58194b068bc8d5.22297189.jpg',	1,	1478052614),
  (478,	70,	34,	NULL,	'',	'answer.58194b1041a034.24082549.jpg',	1,	1478052624),
  (479,	70,	34,	NULL,	'',	'answer.58194b195fd627.59649290.jpg',	1,	1478052633),
  (480,	71,	34,	NULL,	'',	'answer.58194c48852ab4.90967625.jpg',	1,	1478052936),
  (481,	71,	34,	NULL,	'',	'answer.58194c4ae1d6d9.30174811.jpg',	1,	1478052938),
  (482,	70,	57,	NULL,	'',	'answer.58194cff6b5ce9.63021251.jpg',	1,	1478053119),
  (483,	70,	57,	NULL,	'',	'answer.58194d01a94f15.51049501.jpg',	1,	1478053121),
  (484,	70,	53,	NULL,	'',	'answer.58194e6928f6c4.25772955.jpg',	1,	1478053481),
  (485,	70,	53,	NULL,	'',	'answer.58194e6d362e74.16137618.jpg',	1,	1478053485),
  (486,	71,	57,	NULL,	'',	'answer.58194f0943ee91.87257712.jpg',	1,	1478053641),
  (487,	71,	57,	NULL,	'',	'answer.58194f0cc89582.49980434.jpg',	1,	1478053644),
  (488,	71,	57,	NULL,	'',	'answer.58194f0d9d30d7.59705585.jpg',	1,	1478053645),
  (489,	71,	53,	NULL,	'',	'answer.58194f9ec7bec4.94931574.jpg',	1,	1478053790),
  (490,	71,	53,	NULL,	'',	'answer.58194fd6d7f590.79162875.jpg',	1,	1478053846),
  (491,	70,	30,	NULL,	'',	'answer.5819502f32ca46.90090004.jpg',	1,	1478053935),
  (492,	70,	30,	NULL,	'7 trees',	'answer.5819503570fe85.79930822.jpg',	1,	1478053941),
  (493,	71,	53,	NULL,	'',	'answer.581950912147b8.68247870.jpg',	1,	1478054033),
  (494,	71,	53,	NULL,	'',	'answer.58195095b1d4f5.38433920.jpg',	1,	1478054037),
  (495,	71,	53,	NULL,	'',	'answer.581950983934c5.14502197.jpg',	1,	1478054040),
  (496,	71,	30,	NULL,	'',	'answer.58195157a0ed49.78000626.jpg',	1,	1478054231),
  (497,	71,	30,	NULL,	'',	'answer.58195160959519.69360432.jpg',	1,	1478054240),
  (498,	68,	30,	NULL,	'',	'answer.583d04ba60c4c1.25779643.jpg',	1,	1480393914);

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