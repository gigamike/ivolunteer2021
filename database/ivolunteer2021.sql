-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2021 at 07:40 AM
-- Server version: 5.7.26
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ivolunteer2021`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 'Community Development', '2021-04-17 20:44:09', 0, '2021-04-17 20:46:58', NULL),
(2, 'Environment', '2021-04-17 20:44:09', 0, NULL, NULL),
(3, 'Health', '2021-04-17 20:44:57', 0, NULL, NULL),
(4, 'Education', '2021-04-17 20:45:10', 0, NULL, NULL),
(5, 'Disaster And Calamities', '2021-04-17 20:45:48', 0, NULL, NULL),
(6, 'Animal Welfare', '2021-04-17 20:46:33', 0, NULL, NULL),
(7, 'Children And Youth', '2021-04-17 20:46:54', 0, NULL, NULL),
(10, 'Digital And Technology', '2021-04-17 20:47:28', 0, NULL, NULL),
(11, 'Elderly', '2021-04-17 20:47:28', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_code` char(2) NOT NULL,
  `country_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country_code`, `country_name`) VALUES
(1, 'A1', 'Anonymous Proxy'),
(2, 'A2', 'Satellite Provider'),
(3, 'O1', 'Other Country'),
(4, 'AD', 'Andorra'),
(5, 'AE', 'United Arab Emirates'),
(6, 'AF', 'Afghanistan'),
(7, 'AG', 'Antigua and Barbuda'),
(8, 'AI', 'Anguilla'),
(9, 'AL', 'Albania'),
(10, 'AM', 'Armenia'),
(11, 'AO', 'Angola'),
(12, 'AP', 'Asia/Pacific Region'),
(13, 'AQ', 'Antarctica'),
(14, 'AR', 'Argentina'),
(15, 'AS', 'American Samoa'),
(16, 'AT', 'Austria'),
(17, 'AU', 'Australia'),
(18, 'AW', 'Aruba'),
(19, 'AX', 'Aland Islands'),
(20, 'AZ', 'Azerbaijan'),
(21, 'BA', 'Bosnia and Herzegovina'),
(22, 'BB', 'Barbados'),
(23, 'BD', 'Bangladesh'),
(24, 'BE', 'Belgium'),
(25, 'BF', 'Burkina Faso'),
(26, 'BG', 'Bulgaria'),
(27, 'BH', 'Bahrain'),
(28, 'BI', 'Burundi'),
(29, 'BJ', 'Benin'),
(30, 'BL', 'Saint Bartelemey'),
(31, 'BM', 'Bermuda'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BO', 'Bolivia'),
(34, 'BQ', 'Bonaire, Saint Eustatius and Saba'),
(35, 'BR', 'Brazil'),
(36, 'BS', 'Bahamas'),
(37, 'BT', 'Bhutan'),
(38, 'BV', 'Bouvet Island'),
(39, 'BW', 'Botswana'),
(40, 'BY', 'Belarus'),
(41, 'BZ', 'Belize'),
(42, 'CA', 'Canada'),
(43, 'CC', 'Cocos (Keeling) Islands'),
(44, 'CD', 'Congo, The Democratic Republic of the'),
(45, 'CF', 'Central African Republic'),
(46, 'CG', 'Congo'),
(47, 'CH', 'Switzerland'),
(48, 'CI', 'Cote d\'Ivoire'),
(49, 'CK', 'Cook Islands'),
(50, 'CL', 'Chile'),
(51, 'CM', 'Cameroon'),
(52, 'CN', 'China'),
(53, 'CO', 'Colombia'),
(54, 'CR', 'Costa Rica'),
(55, 'CU', 'Cuba'),
(56, 'CV', 'Cape Verde'),
(57, 'CW', 'Curacao'),
(58, 'CX', 'Christmas Island'),
(59, 'CY', 'Cyprus'),
(60, 'CZ', 'Czech Republic'),
(61, 'DE', 'Germany'),
(62, 'DJ', 'Djibouti'),
(63, 'DK', 'Denmark'),
(64, 'DM', 'Dominica'),
(65, 'DO', 'Dominican Republic'),
(66, 'DZ', 'Algeria'),
(67, 'EC', 'Ecuador'),
(68, 'EE', 'Estonia'),
(69, 'EG', 'Egypt'),
(70, 'EH', 'Western Sahara'),
(71, 'ER', 'Eritrea'),
(72, 'ES', 'Spain'),
(73, 'ET', 'Ethiopia'),
(74, 'EU', 'Europe'),
(75, 'FI', 'Finland'),
(76, 'FJ', 'Fiji'),
(77, 'FK', 'Falkland Islands (Malvinas)'),
(78, 'FM', 'Micronesia, Federated States of'),
(79, 'FO', 'Faroe Islands'),
(80, 'FR', 'France'),
(81, 'GA', 'Gabon'),
(82, 'GB', 'United Kingdom'),
(83, 'GD', 'Grenada'),
(84, 'GE', 'Georgia'),
(85, 'GF', 'French Guiana'),
(86, 'GG', 'Guernsey'),
(87, 'GH', 'Ghana'),
(88, 'GI', 'Gibraltar'),
(89, 'GL', 'Greenland'),
(90, 'GM', 'Gambia'),
(91, 'GN', 'Guinea'),
(92, 'GP', 'Guadeloupe'),
(93, 'GQ', 'Equatorial Guinea'),
(94, 'GR', 'Greece'),
(95, 'GS', 'South Georgia and the South Sandwich Islands'),
(96, 'GT', 'Guatemala'),
(97, 'GU', 'Guam'),
(98, 'GW', 'Guinea-Bissau'),
(99, 'GY', 'Guyana'),
(100, 'HK', 'Hong Kong'),
(101, 'HM', 'Heard Island and McDonald Islands'),
(102, 'HN', 'Honduras'),
(103, 'HR', 'Croatia'),
(104, 'HT', 'Haiti'),
(105, 'HU', 'Hungary'),
(106, 'ID', 'Indonesia'),
(107, 'IE', 'Ireland'),
(108, 'IL', 'Israel'),
(109, 'IM', 'Isle of Man'),
(110, 'IN', 'India'),
(111, 'IO', 'British Indian Ocean Territory'),
(112, 'IQ', 'Iraq'),
(113, 'IR', 'Iran, Islamic Republic of'),
(114, 'IS', 'Iceland'),
(115, 'IT', 'Italy'),
(116, 'JE', 'Jersey'),
(117, 'JM', 'Jamaica'),
(118, 'JO', 'Jordan'),
(119, 'JP', 'Japan'),
(120, 'KE', 'Kenya'),
(121, 'KG', 'Kyrgyzstan'),
(122, 'KH', 'Cambodia'),
(123, 'KI', 'Kiribati'),
(124, 'KM', 'Comoros'),
(125, 'KN', 'Saint Kitts and Nevis'),
(126, 'KP', 'Korea, Democratic People\'s Republic of'),
(127, 'KR', 'Korea, Republic of'),
(128, 'KW', 'Kuwait'),
(129, 'KY', 'Cayman Islands'),
(130, 'KZ', 'Kazakhstan'),
(131, 'LA', 'Lao People\'s Democratic Republic'),
(132, 'LB', 'Lebanon'),
(133, 'LC', 'Saint Lucia'),
(134, 'LI', 'Liechtenstein'),
(135, 'LK', 'Sri Lanka'),
(136, 'LR', 'Liberia'),
(137, 'LS', 'Lesotho'),
(138, 'LT', 'Lithuania'),
(139, 'LU', 'Luxembourg'),
(140, 'LV', 'Latvia'),
(141, 'LY', 'Libyan Arab Jamahiriya'),
(142, 'MA', 'Morocco'),
(143, 'MC', 'Monaco'),
(144, 'MD', 'Moldova, Republic of'),
(145, 'ME', 'Montenegro'),
(146, 'MF', 'Saint Martin'),
(147, 'MG', 'Madagascar'),
(148, 'MH', 'Marshall Islands'),
(149, 'MK', 'Macedonia'),
(150, 'ML', 'Mali'),
(151, 'MM', 'Myanmar'),
(152, 'MN', 'Mongolia'),
(153, 'MO', 'Macao'),
(154, 'MP', 'Northern Mariana Islands'),
(155, 'MQ', 'Martinique'),
(156, 'MR', 'Mauritania'),
(157, 'MS', 'Montserrat'),
(158, 'MT', 'Malta'),
(159, 'MU', 'Mauritius'),
(160, 'MV', 'Maldives'),
(161, 'MW', 'Malawi'),
(162, 'MX', 'Mexico'),
(163, 'MY', 'Malaysia'),
(164, 'MZ', 'Mozambique'),
(165, 'NA', 'Namibia'),
(166, 'NC', 'New Caledonia'),
(167, 'NE', 'Niger'),
(168, 'NF', 'Norfolk Island'),
(169, 'NG', 'Nigeria'),
(170, 'NI', 'Nicaragua'),
(171, 'NL', 'Netherlands'),
(172, 'NO', 'Norway'),
(173, 'NP', 'Nepal'),
(174, 'NR', 'Nauru'),
(175, 'NU', 'Niue'),
(176, 'NZ', 'New Zealand'),
(177, 'OM', 'Oman'),
(178, 'PA', 'Panama'),
(179, 'PE', 'Peru'),
(180, 'PF', 'French Polynesia'),
(181, 'PG', 'Papua New Guinea'),
(182, 'PH', 'Philippines'),
(183, 'PK', 'Pakistan'),
(184, 'PL', 'Poland'),
(185, 'PM', 'Saint Pierre and Miquelon'),
(186, 'PN', 'Pitcairn'),
(187, 'PR', 'Puerto Rico'),
(188, 'PS', 'Palestinian Territory'),
(189, 'PT', 'Portugal'),
(190, 'PW', 'Palau'),
(191, 'PY', 'Paraguay'),
(192, 'QA', 'Qatar'),
(193, 'RE', 'Reunion'),
(194, 'RO', 'Romania'),
(195, 'RS', 'Serbia'),
(196, 'RU', 'Russian Federation'),
(197, 'RW', 'Rwanda'),
(198, 'SA', 'Saudi Arabia'),
(199, 'SB', 'Solomon Islands'),
(200, 'SC', 'Seychelles'),
(201, 'SD', 'Sudan'),
(202, 'SE', 'Sweden'),
(203, 'SG', 'Singapore'),
(204, 'SH', 'Saint Helena'),
(205, 'SI', 'Slovenia'),
(206, 'SJ', 'Svalbard and Jan Mayen'),
(207, 'SK', 'Slovakia'),
(208, 'SL', 'Sierra Leone'),
(209, 'SM', 'San Marino'),
(210, 'SN', 'Senegal'),
(211, 'SO', 'Somalia'),
(212, 'SR', 'Suriname'),
(213, 'SS', 'South Sudan'),
(214, 'ST', 'Sao Tome and Principe'),
(215, 'SV', 'El Salvador'),
(216, 'SX', 'Sint Maarten'),
(217, 'SY', 'Syrian Arab Republic'),
(218, 'SZ', 'Swaziland'),
(219, 'TC', 'Turks and Caicos Islands'),
(220, 'TD', 'Chad'),
(221, 'TF', 'French Southern Territories'),
(222, 'TG', 'Togo'),
(223, 'TH', 'Thailand'),
(224, 'TJ', 'Tajikistan'),
(225, 'TK', 'Tokelau'),
(226, 'TL', 'Timor-Leste'),
(227, 'TM', 'Turkmenistan'),
(228, 'TN', 'Tunisia'),
(229, 'TO', 'Tonga'),
(230, 'TR', 'Turkey'),
(231, 'TT', 'Trinidad and Tobago'),
(232, 'TV', 'Tuvalu'),
(233, 'TW', 'Taiwan'),
(234, 'TZ', 'Tanzania, United Republic of'),
(235, 'UA', 'Ukraine'),
(236, 'UG', 'Uganda'),
(237, 'UM', 'United States Minor Outlying Islands'),
(238, 'US', 'United States'),
(239, 'UY', 'Uruguay'),
(240, 'UZ', 'Uzbekistan'),
(241, 'VA', 'Holy See (Vatican City State)'),
(242, 'VC', 'Saint Vincent and the Grenadines'),
(243, 'VE', 'Venezuela'),
(244, 'VG', 'Virgin Islands, British'),
(245, 'VI', 'Virgin Islands, U.S.'),
(246, 'VN', 'Vietnam'),
(247, 'VU', 'Vanuatu'),
(248, 'WF', 'Wallis and Futuna'),
(249, 'WS', 'Samoa'),
(250, 'YE', 'Yemen'),
(251, 'YT', 'Mayotte'),
(252, 'ZA', 'South Africa'),
(253, 'ZM', 'Zambia'),
(254, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `venue` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `latitude` varchar(25) DEFAULT NULL,
  `longitude` varchar(25) DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_type` enum('individual','team') NOT NULL,
  `volunteer_limit` int(10) UNSIGNED DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_mobile_no` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `category_id`, `name`, `description`, `venue`, `city`, `latitude`, `longitude`, `event_date`, `event_type`, `volunteer_limit`, `organization`, `contact_name`, `contact_email`, `contact_mobile_no`, `website_url`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 3, 'Payatas Feeding Program', 'Payatas Feeding Program For Children. Nutrition Program aims to improve the quality of life of the malnourished children coming from poor families by helping them achieve normal nutritional status through provision of free nutritious meals and therapeutic food', 'Payatas 2nd District', 'Quezon City', '14.718640', '121.107600', '2021-04-30', 'individual', 20, 'JCI Baras', 'Bel Padlan ', 'bel@ivolunteer.com.ph', '90812345678', 'https://www.ivolunteer.com.ph/', '2021-04-17 22:26:44', 2, '2021-04-25 06:31:31', NULL),
(4, 10, 'Tara Code Tayo', 'iVolunteer 2021 Hackathon. The goal of a hackathon is to create functioning software or hardware by the end of the event.', '25th Street, 5th Ave', 'Taguig', '14.5482136', '121.0466008', '2021-04-25', 'team', NULL, 'iVolunteer PH', 'JB Tan', 'jb@ivolunteer.com.ph', '9086087306', 'https://www.ivolunteer.com.ph/', '2021-04-24 06:55:23', 2, '2021-04-25 06:32:05', NULL),
(5, 3, 'Community Volunteers in the Cordilleran Hilltribe Philippines', 'The Cordillera Project Philippines is an outreach program that invites local and international volunteers to assist in delivering healthcare, educational and volunteer support to the hilltribe communities in the Cordillera Region. ', 'Burnhanm Park', 'Baguio City', '16.411394', '120.5939716', '2021-04-27', 'individual', 30, 'The Cordillera Project Philippines', 'technology@ivolunteer.com.ph', 'myersseanguy@gmail.com', '09393497587', 'https://www.ivolunteer.com.ph/time-volunteer/6650', '2021-04-25 06:12:48', 2, NULL, NULL),
(7, 2, 'Plant a tree, green the Earth ', 'Currently, the world faces the challenges of climate change and wildlife preservation, along with having to support billions of people. We need solutions. And trees could hold the answer.', 'San Andres, Victoria', 'Tarlac', '15.5899545', '120.6602534', '2021-04-29', 'individual', 30, 'JCI Mandaluyong', 'Pres. Glen', 'glen@jci.mandaluyong.org', '9086087306', 'https://jci.cc/', '2021-04-25 06:36:12', 2, '2021-04-25 06:37:08', NULL),
(8, 5, 'Tulong sa Baras Relief Distribution', '\"Tulong sa Baras\" aims to help people in Baras.', 'Baras', 'Rizal', '14.5365625', '121.2740813', '2021-04-27', 'individual', 30, 'JCI Baras', 'Pres Ninna', 'ninna@jcibaras.org', '9086087306', 'https://jci.cc/', '2021-04-25 06:40:24', 2, NULL, NULL),
(9, 1, 'Project Tanglaw: Light 100 Homes with Solar', 'Solar power to Pasay', 'Merville', 'Pasay City', '14.5067334', '121.0253215', '2021-04-25', 'individual', 30, 'SOLAR Hope', 'Abi Reyes', 'abi@ivolunteer.com.ph', '09393497587', 'https://www.ivolunteer.com.ph/time-volunteer/6650', '2021-04-25 06:48:35', 2, '2021-04-25 07:05:36', NULL),
(12, 2, 'Plant a tree in Luneta', 'Plant a tree program', 'Luneta', 'Manila', '14.5831177', '120.9794171', '2021-04-25', 'team', NULL, 'iVolunteer PH', 'JB Tan', 'jb@ivolunteer.com.ph', '09393497587', 'https://www.ivolunteer.com.ph/time-volunteer/6650', '2021-04-25 07:36:09', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_task`
--

CREATE TABLE `event_task` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `task` text,
  `points` int(10) UNSIGNED NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_task`
--

INSERT INTO `event_task` (`id`, `event_id`, `task`, `points`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 1, 'Crowd Control', 1, '2021-04-17 22:26:44', 2, NULL, NULL),
(2, 1, 'Cooking', 2, '2021-04-17 22:26:44', 2, NULL, NULL),
(3, 1, 'Food Arrangment', 1, '2021-04-17 22:26:44', 2, NULL, NULL),
(4, 1, 'Feeding Children', 1, '2021-04-17 22:26:44', 2, NULL, NULL),
(12, 4, 'Idea presentation for POC', 1, '2021-04-24 06:55:23', 2, '2021-04-25 07:01:58', NULL),
(13, 4, 'Code Submission in Repo', 1, '2021-04-24 06:55:23', 2, '2021-04-25 07:02:13', NULL),
(14, 4, 'Pitching', 1, '2021-04-24 06:55:23', 2, '2021-04-25 07:02:27', NULL),
(16, 5, 'Crowd control', 1, '2021-04-25 06:12:48', 2, NULL, NULL),
(17, 5, 'Medicine distribution', 1, '2021-04-25 06:12:48', 2, NULL, NULL),
(18, 5, 'Check up', 1, '2021-04-25 06:12:48', 2, NULL, NULL),
(21, 7, 'Plant a tree', 1, '2021-04-25 06:36:12', 2, NULL, NULL),
(22, 7, 'Complete equipment', 1, '2021-04-25 06:36:12', 2, NULL, NULL),
(23, 7, 'Early bird', 1, '2021-04-25 06:36:12', 2, NULL, NULL),
(24, 8, 'Crowd control', 1, '2021-04-25 06:40:24', 2, NULL, NULL),
(25, 8, 'Donation', 1, '2021-04-25 06:40:24', 2, NULL, NULL),
(26, 9, 'Crowd control', 1, '2021-04-25 06:48:35', 2, NULL, NULL),
(27, 9, 'Complete equipment', 1, '2021-04-25 06:48:35', 2, NULL, NULL),
(34, 12, 'Plant a tree', 1, '2021-04-25 07:36:09', 2, NULL, NULL),
(35, 12, 'Cleaning', 1, '2021-04-25 07:36:09', 2, NULL, NULL),
(36, 12, 'Complete equipment', 1, '2021-04-25 07:36:09', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_team`
--

CREATE TABLE `event_team` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `team_limit` int(10) UNSIGNED NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(20) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_team`
--

INSERT INTO `event_team` (`id`, `event_id`, `name`, `team_limit`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(6, 4, 'Team Gigamike', 3, '2021-04-24 06:55:23', 2, NULL, NULL),
(7, 4, 'Team Tom', 3, '2021-04-24 06:55:23', 2, NULL, NULL),
(8, 4, 'Team Tristan', 3, '2021-04-24 06:55:23', 2, NULL, NULL),
(16, 12, 'PlanTitas', 10, '2021-04-25 07:36:09', 2, NULL, NULL),
(17, 12, 'PlanTitos', 10, '2021-04-25 07:36:09', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_team_member`
--

CREATE TABLE `event_team_member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_team_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(20) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_team_member`
--

INSERT INTO `event_team_member` (`id`, `event_team_id`, `user_id`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(3, 17, 3, '2021-04-25 07:36:38', 3, NULL, NULL),
(4, 17, 8, '2021-04-25 07:38:35', 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('admin','member') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` enum('m','f') NOT NULL,
  `city` varchar(255) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `birth_date` date NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` enum('N','Y') NOT NULL,
  `referral_id` int(10) UNSIGNED DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `role`, `email`, `password`, `first_name`, `middle_name`, `last_name`, `gender`, `city`, `country_id`, `birth_date`, `title`, `mobile_no`, `salt`, `active`, `referral_id`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(2, 'admin', 'admin@gigamike.net', 'e698b4a0b08532cdff8443a4dd615588', 'Amah', 'Buenaventura', 'Galon', 'f', 'Rizal', 182, '2004-10-20', 'Accountant', '09086087306', 'kJ(26<$y>u01=1Su6V[t,GuDxMS=TCcAmkR%(V}FL/Wh?+_T`;', 'Y', NULL, '2019-10-27 09:14:48', 0, NULL, NULL),
(3, 'member', 'volunteer1@gigamike.net', '865fcd1a0812677c7e7dbcfaf5eb78f4', 'Mik', 'Tupas', 'Galon', 'm', 'Paranaque', 182, '1986-07-29', 'Dev', '9086087306', 'mz+G}|C+VqA|xPZRT8ueliw[3]V2:[2KLx$1D>|`2n}x[/gMQj', 'Y', 0, '2021-04-17 22:41:34', 0, NULL, NULL),
(5, 'member', 'volunteer3@gigamike.net', '7327124065fec67eda886f306a975533', 'Tristan', 'Rosales', 'Rosales', 'm', 'Paranaque', 182, '1985-04-17', 'Dev', '9086087306', '*G/?K7.=v6*wak;fC^LeQJv_c}2xY6&Dn49Q&ro{z3>l=1|r0Z', 'Y', 0, '2021-04-17 22:57:15', 0, NULL, NULL),
(7, 'member', 'volunteer2@gigamike.net', '1cf977d85a7791d03a09ff024388b53c', 'Amah', 'Santiago', 'Buenaventura', 'm', 'Paranaque', 182, '1986-04-21', 'Accountant', '906087306', 'Lxm!sbGtbGt`oO?A#aAUmWJ\'jg[\'8%[{;(.Ik<SCt/wHW`TPSb', 'Y', 0, '2021-04-18 01:50:22', 0, NULL, NULL),
(8, 'member', 'volunteer5@gigamike.net', 'ed21d7fd1cd2fed084a8b2038b085ab2', 'Tom', 'T', 'Chua', 'm', 'Antipolo', 182, '1986-04-01', 'Dev', '1234567890', '\"YkJof1,12gh9G.pD,u[\\PYs+h2k#g)Rer@-VE3`019|~~R~E4', 'Y', 3, '2021-04-25 07:38:05', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_event`
--

CREATE TABLE `user_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attend_datetime` datetime DEFAULT NULL,
  `user_code` int(6) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(20) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_event`
--

INSERT INTO `user_event` (`id`, `event_id`, `user_id`, `attend_datetime`, `user_code`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(16, 9, 3, '2021-04-25 07:19:52', NULL, '2021-04-25 07:18:54', 3, NULL, NULL),
(18, 12, 3, '2021-04-25 07:39:15', NULL, '2021-04-25 07:36:38', 3, NULL, NULL),
(19, 12, 8, NULL, NULL, '2021-04-25 07:38:35', 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_event_task`
--

CREATE TABLE `user_event_task` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_user_id` bigint(20) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_event_task`
--

INSERT INTO `user_event_task` (`id`, `event_task_id`, `user_id`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(3, 26, 3, '2021-04-25 07:20:13', 2, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category` (`category`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_date` (`event_date`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `created_user_id` (`created_user_id`),
  ADD KEY `name` (`name`),
  ADD KEY `event_type` (`event_type`),
  ADD KEY `fk_event_category_id` (`category_id`),
  ADD KEY `latitude` (`latitude`),
  ADD KEY `longitude` (`longitude`),
  ADD KEY `organization` (`organization`),
  ADD KEY `city` (`city`);

--
-- Indexes for table `event_task`
--
ALTER TABLE `event_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `created_user_id` (`created_user_id`),
  ADD KEY `fk_event_task_event_id` (`event_id`),
  ADD KEY `points` (`points`);

--
-- Indexes for table `event_team`
--
ALTER TABLE `event_team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `name` (`name`),
  ADD KEY `fk_event_team_event_id` (`event_id`);

--
-- Indexes for table `event_team_member`
--
ALTER TABLE `event_team_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `fk_event_team_member_event_team_id` (`event_team_id`),
  ADD KEY `fk_event_team_member_user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `role` (`role`),
  ADD KEY `referral_id` (`referral_id`),
  ADD KEY `first_name` (`first_name`),
  ADD KEY `last_name` (`last_name`);

--
-- Indexes for table `user_event`
--
ALTER TABLE `user_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`user_code`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `fk_user_event_user_id` (`user_id`),
  ADD KEY `attend_datetime` (`attend_datetime`);

--
-- Indexes for table `user_event_task`
--
ALTER TABLE `user_event_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_datetime` (`created_datetime`),
  ADD KEY `fk_user_event_task_event_task_id` (`event_task_id`),
  ADD KEY `fk_user_event_task_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `event_task`
--
ALTER TABLE `event_task`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `event_team`
--
ALTER TABLE `event_team`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `event_team_member`
--
ALTER TABLE `event_team_member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_event`
--
ALTER TABLE `user_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_event_task`
--
ALTER TABLE `user_event_task`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_task`
--
ALTER TABLE `event_task`
  ADD CONSTRAINT `fk_event_task_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_team`
--
ALTER TABLE `event_team`
  ADD CONSTRAINT `fk_event_team_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_team_member`
--
ALTER TABLE `event_team_member`
  ADD CONSTRAINT `fk_event_team_member_event_team_id` FOREIGN KEY (`event_team_id`) REFERENCES `event_team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_team_member_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_event`
--
ALTER TABLE `user_event`
  ADD CONSTRAINT `fk_log_user_event_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_event_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_event_task`
--
ALTER TABLE `user_event_task`
  ADD CONSTRAINT `fk_user_event_task_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_event_task_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
