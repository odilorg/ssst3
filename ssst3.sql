-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2025 at 11:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ssst3`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `created_at`, `updated_at`, `name`) VALUES
(1, '2025-01-10 16:43:28', '2025-01-10 16:43:28', 'A/C'),
(2, '2025-01-11 05:48:59', '2025-01-11 05:48:59', 'TV'),
(3, '2025-01-11 05:49:13', '2025-01-11 05:49:13', 'Air Condition'),
(4, '2025-01-11 05:49:44', '2025-01-11 05:49:44', 'WiFi'),
(5, '2025-01-11 06:09:01', '2025-01-11 06:09:01', 'Garden'),
(6, '2025-01-11 06:09:45', '2025-01-11 06:09:45', 'Terrace'),
(7, '2025-01-13 10:04:08', '2025-01-13 10:04:08', 'swimming pool'),
(8, '2025-06-08 12:45:03', '2025-06-08 12:45:03', 'Desk'),
(9, '2025-06-08 12:45:15', '2025-06-08 12:45:15', 'Slippers'),
(10, '2025-06-08 12:45:21', '2025-06-08 12:45:21', 'Phone'),
(11, '2025-06-08 12:47:04', '2025-06-08 12:47:04', 'Electric kettle'),
(12, '2025-06-08 12:47:11', '2025-06-08 12:47:11', 'Hairdryer'),
(13, '2025-06-08 12:47:21', '2025-06-08 12:47:21', 'Wardrobe or closet');

-- --------------------------------------------------------

--
-- Table structure for table `amenity_room`
--

CREATE TABLE `amenity_room` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `amenity_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenity_room`
--

INSERT INTO `amenity_room` (`id`, `created_at`, `updated_at`, `amenity_id`, `room_id`) VALUES
(2, NULL, NULL, 1, 3),
(3, NULL, NULL, 3, 5),
(4, NULL, NULL, 2, 5),
(5, NULL, NULL, 4, 5),
(6, NULL, NULL, 6, 6),
(7, NULL, NULL, 2, 6),
(8, NULL, NULL, 4, 6),
(9, NULL, NULL, 3, 6),
(10, NULL, NULL, 3, 7),
(11, NULL, NULL, 6, 7),
(12, NULL, NULL, 4, 7),
(13, NULL, NULL, 2, 7),
(14, NULL, NULL, 3, 8),
(15, NULL, NULL, 5, 8),
(16, NULL, NULL, 6, 8),
(17, NULL, NULL, 2, 8),
(18, NULL, NULL, 4, 8),
(19, NULL, NULL, 2, 9),
(20, NULL, NULL, 4, 9),
(21, NULL, NULL, 3, 9),
(22, NULL, NULL, 3, 10),
(23, NULL, NULL, 2, 10),
(24, NULL, NULL, 4, 10),
(25, NULL, NULL, 3, 11),
(26, NULL, NULL, 2, 11),
(27, NULL, NULL, 4, 11),
(28, NULL, NULL, 3, 12),
(29, NULL, NULL, 2, 12),
(30, NULL, NULL, 4, 12),
(31, NULL, NULL, 3, 13),
(32, NULL, NULL, 2, 13),
(33, NULL, NULL, 4, 13),
(34, NULL, NULL, 3, 14),
(35, NULL, NULL, 2, 14),
(36, NULL, NULL, 4, 14),
(37, NULL, NULL, 2, 15),
(38, NULL, NULL, 4, 15),
(39, NULL, NULL, 3, 15),
(40, NULL, NULL, 3, 16),
(41, NULL, NULL, 2, 16),
(42, NULL, NULL, 4, 16),
(43, NULL, NULL, 3, 17),
(44, NULL, NULL, 2, 17),
(45, NULL, NULL, 4, 17),
(46, NULL, NULL, 3, 18),
(47, NULL, NULL, 6, 18),
(48, NULL, NULL, 2, 18),
(49, NULL, NULL, 4, 18),
(50, NULL, NULL, 3, 19),
(51, NULL, NULL, 2, 19),
(52, NULL, NULL, 4, 19),
(53, NULL, NULL, 3, 20),
(54, NULL, NULL, 7, 20),
(55, NULL, NULL, 2, 20),
(56, NULL, NULL, 4, 20),
(57, NULL, NULL, 7, 21),
(58, NULL, NULL, 2, 21),
(59, NULL, NULL, 4, 21),
(60, NULL, NULL, 3, 22),
(61, NULL, NULL, 7, 22),
(62, NULL, NULL, 2, 22),
(63, NULL, NULL, 4, 22),
(64, NULL, NULL, 3, 23),
(65, NULL, NULL, 2, 23),
(66, NULL, NULL, 4, 23),
(67, NULL, NULL, 2, 24),
(68, NULL, NULL, 4, 24),
(69, NULL, NULL, 3, 24),
(70, NULL, NULL, 3, 25),
(71, NULL, NULL, 2, 25),
(72, NULL, NULL, 4, 25),
(73, NULL, NULL, 2, 26),
(74, NULL, NULL, 3, 26),
(75, NULL, NULL, 4, 26),
(76, NULL, NULL, 3, 27),
(77, NULL, NULL, 7, 27),
(78, NULL, NULL, 2, 27),
(79, NULL, NULL, 4, 27),
(80, NULL, NULL, 3, 28),
(81, NULL, NULL, 2, 28),
(82, NULL, NULL, 4, 28),
(83, NULL, NULL, 7, 28),
(84, NULL, NULL, 3, 29),
(85, NULL, NULL, 2, 29),
(86, NULL, NULL, 4, 29),
(87, NULL, NULL, 3, 30),
(88, NULL, NULL, 2, 30),
(89, NULL, NULL, 4, 30),
(90, NULL, NULL, 4, 31),
(91, NULL, NULL, 2, 31),
(92, NULL, NULL, 3, 31),
(93, NULL, NULL, 3, 32),
(94, NULL, NULL, 2, 32),
(95, NULL, NULL, 4, 32),
(96, NULL, NULL, 3, 33),
(97, NULL, NULL, 2, 33),
(98, NULL, NULL, 4, 33),
(99, NULL, NULL, 3, 34),
(100, NULL, NULL, 2, 34),
(101, NULL, NULL, 4, 34),
(102, NULL, NULL, 3, 35),
(103, NULL, NULL, 2, 35),
(104, NULL, NULL, 4, 35),
(105, NULL, NULL, 3, 36),
(106, NULL, NULL, 2, 36),
(107, NULL, NULL, 4, 36),
(108, NULL, NULL, 3, 37),
(109, NULL, NULL, 2, 37),
(110, NULL, NULL, 4, 37),
(111, NULL, NULL, 3, 38),
(112, NULL, NULL, 2, 38),
(113, NULL, NULL, 4, 38),
(114, NULL, NULL, 3, 39),
(115, NULL, NULL, 2, 39),
(116, NULL, NULL, 4, 39),
(117, NULL, NULL, 3, 40),
(118, NULL, NULL, 2, 40),
(119, NULL, NULL, 4, 40),
(120, NULL, NULL, 3, 41),
(121, NULL, NULL, 7, 41),
(122, NULL, NULL, 2, 41),
(123, NULL, NULL, 4, 41),
(124, NULL, NULL, 6, 41),
(125, NULL, NULL, 5, 41),
(126, NULL, NULL, 3, 42),
(127, NULL, NULL, 5, 42),
(128, NULL, NULL, 7, 42),
(129, NULL, NULL, 6, 42),
(130, NULL, NULL, 2, 42),
(131, NULL, NULL, 4, 42),
(132, NULL, NULL, 3, 43),
(133, NULL, NULL, 2, 43),
(134, NULL, NULL, 4, 43),
(135, NULL, NULL, 3, 44),
(136, NULL, NULL, 2, 44),
(137, NULL, NULL, 4, 44),
(138, NULL, NULL, 3, 45),
(139, NULL, NULL, 2, 45),
(140, NULL, NULL, 4, 45),
(141, NULL, NULL, 7, 46),
(142, NULL, NULL, 2, 46),
(143, NULL, NULL, 4, 46),
(144, NULL, NULL, 3, 47),
(145, NULL, NULL, 2, 47),
(146, NULL, NULL, 4, 47),
(147, NULL, NULL, 3, 48),
(148, NULL, NULL, 2, 48),
(149, NULL, NULL, 4, 48),
(150, NULL, NULL, 3, 49),
(151, NULL, NULL, 2, 49),
(152, NULL, NULL, 4, 49),
(153, NULL, NULL, 3, 50),
(154, NULL, NULL, 2, 50),
(155, NULL, NULL, 4, 50),
(156, NULL, NULL, 6, 50),
(157, NULL, NULL, 3, 51),
(158, NULL, NULL, 2, 51),
(159, NULL, NULL, 4, 51),
(160, NULL, NULL, 3, 52),
(161, NULL, NULL, 2, 52),
(162, NULL, NULL, 4, 52),
(163, NULL, NULL, 3, 53),
(164, NULL, NULL, 2, 53),
(165, NULL, NULL, 4, 53),
(166, NULL, NULL, 3, 54),
(167, NULL, NULL, 2, 54),
(168, NULL, NULL, 4, 54),
(169, NULL, NULL, 5, 54),
(170, NULL, NULL, 3, 55),
(171, NULL, NULL, 2, 55),
(172, NULL, NULL, 4, 55),
(173, NULL, NULL, 3, 56),
(174, NULL, NULL, 2, 56),
(175, NULL, NULL, 4, 56),
(176, NULL, NULL, 3, 57),
(177, NULL, NULL, 2, 57),
(178, NULL, NULL, 4, 57),
(179, NULL, NULL, 3, 58),
(180, NULL, NULL, 2, 58),
(181, NULL, NULL, 4, 58),
(182, NULL, NULL, 3, 59),
(183, NULL, NULL, 2, 59),
(184, NULL, NULL, 4, 59),
(185, NULL, NULL, 3, 60),
(186, NULL, NULL, 2, 60),
(187, NULL, NULL, 4, 60),
(188, NULL, NULL, 2, 61),
(189, NULL, NULL, 4, 61),
(190, NULL, NULL, 3, 61),
(191, NULL, NULL, 3, 62),
(192, NULL, NULL, 2, 62),
(193, NULL, NULL, 4, 62),
(194, NULL, NULL, 3, 63),
(195, NULL, NULL, 2, 63),
(196, NULL, NULL, 4, 63),
(197, NULL, NULL, 3, 64),
(198, NULL, NULL, 2, 64),
(199, NULL, NULL, 4, 64),
(200, NULL, NULL, 3, 65),
(201, NULL, NULL, 2, 65),
(202, NULL, NULL, 4, 65),
(203, NULL, NULL, 3, 66),
(204, NULL, NULL, 2, 66),
(205, NULL, NULL, 4, 66),
(206, NULL, NULL, 3, 67),
(207, NULL, NULL, 2, 67),
(208, NULL, NULL, 4, 67),
(209, NULL, NULL, 3, 68),
(210, NULL, NULL, 2, 68),
(211, NULL, NULL, 4, 68),
(212, NULL, NULL, 3, 69),
(213, NULL, NULL, 2, 69),
(214, NULL, NULL, 4, 69),
(215, NULL, NULL, 3, 70),
(216, NULL, NULL, 2, 70),
(217, NULL, NULL, 4, 70),
(218, NULL, NULL, 3, 71),
(219, NULL, NULL, 2, 71),
(220, NULL, NULL, 4, 71),
(221, NULL, NULL, 3, 72),
(222, NULL, NULL, 2, 72),
(223, NULL, NULL, 4, 72),
(224, NULL, NULL, 3, 73),
(225, NULL, NULL, 2, 73),
(226, NULL, NULL, 4, 73),
(227, NULL, NULL, 3, 74),
(228, NULL, NULL, 6, 74),
(229, NULL, NULL, 2, 74),
(230, NULL, NULL, 4, 74),
(231, NULL, NULL, 3, 75),
(232, NULL, NULL, 2, 75),
(233, NULL, NULL, 4, 75),
(234, NULL, NULL, 3, 76),
(235, NULL, NULL, 2, 76),
(236, NULL, NULL, 4, 76),
(237, NULL, NULL, 2, 77),
(238, NULL, NULL, 4, 77),
(239, NULL, NULL, 3, 77),
(240, NULL, NULL, 3, 78),
(241, NULL, NULL, 2, 78),
(242, NULL, NULL, 4, 78),
(243, NULL, NULL, 3, 79),
(244, NULL, NULL, 2, 79),
(245, NULL, NULL, 4, 79),
(246, NULL, NULL, 3, 80),
(247, NULL, NULL, 6, 80),
(248, NULL, NULL, 4, 80),
(249, NULL, NULL, 3, 81),
(250, NULL, NULL, 2, 81),
(251, NULL, NULL, 4, 81),
(252, NULL, NULL, 3, 82),
(253, NULL, NULL, 2, 82),
(254, NULL, NULL, 4, 82),
(255, NULL, NULL, 3, 83),
(256, NULL, NULL, 7, 83),
(257, NULL, NULL, 2, 83),
(258, NULL, NULL, 4, 83),
(259, NULL, NULL, 3, 84),
(260, NULL, NULL, 7, 84),
(261, NULL, NULL, 2, 84),
(262, NULL, NULL, 4, 84),
(263, NULL, NULL, 3, 85),
(264, NULL, NULL, 2, 85),
(265, NULL, NULL, 4, 85),
(266, NULL, NULL, 2, 86),
(267, NULL, NULL, 3, 86),
(268, NULL, NULL, 4, 86),
(269, NULL, NULL, 3, 87),
(270, NULL, NULL, 4, 87),
(271, NULL, NULL, 2, 87),
(272, NULL, NULL, 3, 88),
(273, NULL, NULL, 2, 88),
(274, NULL, NULL, 4, 88),
(275, NULL, NULL, 3, 89),
(276, NULL, NULL, 2, 89),
(277, NULL, NULL, 4, 89),
(278, NULL, NULL, 3, 90),
(279, NULL, NULL, 2, 90),
(280, NULL, NULL, 4, 90),
(281, NULL, NULL, 3, 91),
(282, NULL, NULL, 7, 91),
(283, NULL, NULL, 2, 91),
(284, NULL, NULL, 4, 91),
(285, NULL, NULL, 3, 92),
(286, NULL, NULL, 7, 92),
(287, NULL, NULL, 2, 92),
(288, NULL, NULL, 4, 92),
(289, NULL, NULL, 3, 93),
(290, NULL, NULL, 2, 93),
(291, NULL, NULL, 4, 93),
(292, NULL, NULL, 3, 94),
(293, NULL, NULL, 2, 94),
(294, NULL, NULL, 4, 94),
(295, NULL, NULL, 3, 95),
(296, NULL, NULL, 2, 95),
(297, NULL, NULL, 4, 95),
(298, NULL, NULL, 2, 96),
(299, NULL, NULL, 4, 96),
(300, NULL, NULL, 3, 96),
(301, NULL, NULL, 3, 97),
(302, NULL, NULL, 2, 97),
(303, NULL, NULL, 4, 97),
(304, NULL, NULL, 3, 98),
(305, NULL, NULL, 2, 98),
(306, NULL, NULL, 4, 98),
(307, NULL, NULL, 7, 99),
(308, NULL, NULL, 2, 99),
(309, NULL, NULL, 4, 99),
(310, NULL, NULL, 5, 99),
(311, NULL, NULL, 3, 100),
(312, NULL, NULL, 5, 100),
(313, NULL, NULL, 2, 100),
(314, NULL, NULL, 4, 100),
(315, NULL, NULL, 3, 101),
(316, NULL, NULL, 2, 101),
(317, NULL, NULL, 4, 101),
(318, NULL, NULL, 3, 102),
(319, NULL, NULL, 2, 102),
(320, NULL, NULL, 4, 102),
(321, NULL, NULL, 7, 102),
(322, NULL, NULL, 3, 103),
(323, NULL, NULL, 2, 103),
(324, NULL, NULL, 4, 103),
(325, NULL, NULL, 3, 104),
(326, NULL, NULL, 2, 104),
(327, NULL, NULL, 4, 104),
(328, NULL, NULL, 3, 105),
(329, NULL, NULL, 4, 105),
(330, NULL, NULL, 2, 105),
(331, NULL, NULL, 3, 106),
(332, NULL, NULL, 2, 106),
(333, NULL, NULL, 4, 106),
(334, NULL, NULL, 3, 107),
(335, NULL, NULL, 2, 107),
(336, NULL, NULL, 4, 107),
(337, NULL, NULL, 3, 108),
(338, NULL, NULL, 2, 108),
(339, NULL, NULL, 4, 108),
(340, NULL, NULL, 3, 109),
(341, NULL, NULL, 2, 109),
(342, NULL, NULL, 4, 109),
(346, NULL, NULL, 3, 111),
(347, NULL, NULL, 2, 111),
(348, NULL, NULL, 4, 111),
(349, NULL, NULL, 3, 112),
(350, NULL, NULL, 2, 112),
(351, NULL, NULL, 4, 112),
(352, NULL, NULL, 3, 113),
(353, NULL, NULL, 2, 113),
(354, NULL, NULL, 4, 113),
(355, NULL, NULL, 3, 114),
(356, NULL, NULL, 2, 114),
(357, NULL, NULL, 4, 114),
(358, NULL, NULL, 3, 115),
(359, NULL, NULL, 2, 115),
(360, NULL, NULL, 4, 115),
(361, NULL, NULL, 2, 116),
(362, NULL, NULL, 4, 116),
(363, NULL, NULL, 3, 116),
(364, NULL, NULL, 2, 117),
(365, NULL, NULL, 4, 117),
(366, NULL, NULL, 3, 117),
(367, NULL, NULL, 3, 118),
(368, NULL, NULL, 2, 118),
(369, NULL, NULL, 4, 118),
(370, NULL, NULL, 3, 119),
(371, NULL, NULL, 2, 119),
(372, NULL, NULL, 4, 119),
(373, NULL, NULL, 3, 120),
(374, NULL, NULL, 2, 120),
(375, NULL, NULL, 4, 120),
(376, NULL, NULL, 3, 121),
(377, NULL, NULL, 2, 121),
(378, NULL, NULL, 4, 121),
(379, NULL, NULL, 6, 121),
(380, NULL, NULL, 3, 122),
(381, NULL, NULL, 2, 122),
(382, NULL, NULL, 4, 122),
(383, NULL, NULL, 6, 122),
(384, NULL, NULL, 3, 123),
(385, NULL, NULL, 2, 123),
(386, NULL, NULL, 4, 123),
(387, NULL, NULL, 3, 124),
(388, NULL, NULL, 2, 124),
(389, NULL, NULL, 4, 124),
(390, NULL, NULL, 3, 125),
(391, NULL, NULL, 5, 125),
(392, NULL, NULL, 7, 125),
(393, NULL, NULL, 6, 125),
(394, NULL, NULL, 2, 125),
(395, NULL, NULL, 4, 125),
(396, NULL, NULL, 3, 126),
(397, NULL, NULL, 5, 126),
(398, NULL, NULL, 7, 126),
(399, NULL, NULL, 6, 126),
(400, NULL, NULL, 2, 126),
(401, NULL, NULL, 4, 126),
(402, NULL, NULL, 3, 127),
(403, NULL, NULL, 2, 127),
(404, NULL, NULL, 4, 127),
(405, NULL, NULL, 3, 128),
(406, NULL, NULL, 2, 128),
(407, NULL, NULL, 4, 128),
(408, NULL, NULL, 3, 129),
(409, NULL, NULL, 4, 129),
(410, NULL, NULL, 2, 129),
(411, NULL, NULL, 3, 130),
(412, NULL, NULL, 2, 130),
(413, NULL, NULL, 4, 130),
(414, NULL, NULL, 3, 131),
(415, NULL, NULL, 2, 131),
(416, NULL, NULL, 4, 131),
(417, NULL, NULL, 3, 132),
(418, NULL, NULL, 2, 132),
(419, NULL, NULL, 4, 132),
(420, NULL, NULL, 3, 133),
(421, NULL, NULL, 2, 133),
(422, NULL, NULL, 4, 133),
(423, NULL, NULL, 3, 134),
(424, NULL, NULL, 2, 134),
(425, NULL, NULL, 4, 134),
(426, NULL, NULL, 3, 135),
(427, NULL, NULL, 2, 135),
(428, NULL, NULL, 4, 135),
(429, NULL, NULL, 3, 136),
(430, NULL, NULL, 2, 136),
(431, NULL, NULL, 4, 136),
(432, NULL, NULL, 3, 137),
(433, NULL, NULL, 2, 137),
(434, NULL, NULL, 4, 137),
(435, NULL, NULL, 3, 138),
(436, NULL, NULL, 2, 138),
(437, NULL, NULL, 4, 138),
(438, NULL, NULL, 7, 139),
(439, NULL, NULL, 3, 139),
(440, NULL, NULL, 2, 139),
(441, NULL, NULL, 4, 139),
(442, NULL, NULL, 3, 140),
(443, NULL, NULL, 2, 140),
(444, NULL, NULL, 4, 140),
(445, NULL, NULL, 3, 141),
(446, NULL, NULL, 2, 141),
(447, NULL, NULL, 4, 141),
(448, NULL, NULL, 3, 142),
(449, NULL, NULL, 2, 142),
(450, NULL, NULL, 4, 142),
(451, NULL, NULL, 3, 143),
(452, NULL, NULL, 2, 143),
(453, NULL, NULL, 4, 143),
(454, NULL, NULL, 3, 144),
(455, NULL, NULL, 2, 144),
(456, NULL, NULL, 4, 144),
(457, NULL, NULL, 3, 145),
(458, NULL, NULL, 2, 145),
(459, NULL, NULL, 4, 145),
(460, NULL, NULL, 3, 146),
(461, NULL, NULL, 2, 146),
(462, NULL, NULL, 4, 146),
(463, NULL, NULL, 3, 147),
(464, NULL, NULL, 2, 147),
(465, NULL, NULL, 4, 147),
(466, NULL, NULL, 3, 148),
(467, NULL, NULL, 2, 148),
(468, NULL, NULL, 4, 148),
(469, NULL, NULL, 3, 149),
(470, NULL, NULL, 2, 149),
(471, NULL, NULL, 4, 149),
(472, NULL, NULL, 3, 150),
(473, NULL, NULL, 2, 150),
(474, NULL, NULL, 4, 150),
(475, NULL, NULL, 4, 151),
(476, NULL, NULL, 4, 152),
(477, NULL, NULL, 4, 153),
(478, NULL, NULL, 3, 154),
(479, NULL, NULL, 7, 154),
(480, NULL, NULL, 2, 154),
(481, NULL, NULL, 4, 154),
(482, NULL, NULL, 3, 155),
(483, NULL, NULL, 2, 155),
(484, NULL, NULL, 4, 155),
(485, NULL, NULL, 3, 156),
(486, NULL, NULL, 5, 156),
(487, NULL, NULL, 2, 156),
(488, NULL, NULL, 4, 156),
(489, NULL, NULL, 3, 157),
(490, NULL, NULL, 2, 157),
(491, NULL, NULL, 4, 157),
(492, NULL, NULL, 3, 158),
(493, NULL, NULL, 2, 158),
(494, NULL, NULL, 4, 158),
(495, NULL, NULL, 3, 159),
(496, NULL, NULL, 2, 159),
(497, NULL, NULL, 4, 159),
(498, NULL, NULL, 3, 160),
(499, NULL, NULL, 2, 160),
(500, NULL, NULL, 4, 160),
(501, NULL, NULL, 3, 161),
(502, NULL, NULL, 2, 161),
(503, NULL, NULL, 4, 161),
(504, NULL, NULL, 2, 162),
(505, NULL, NULL, 4, 162),
(506, NULL, NULL, 2, 163),
(507, NULL, NULL, 4, 163),
(508, NULL, NULL, 4, 164),
(509, NULL, NULL, 2, 164),
(510, NULL, NULL, 4, 165),
(511, NULL, NULL, 2, 165),
(512, NULL, NULL, 4, 166),
(513, NULL, NULL, 2, 166),
(514, NULL, NULL, 2, 167),
(515, NULL, NULL, 4, 167),
(516, NULL, NULL, 4, 170),
(517, NULL, NULL, 2, 170),
(518, NULL, NULL, 4, 171),
(519, NULL, NULL, 2, 171),
(520, NULL, NULL, 3, 172),
(521, NULL, NULL, 2, 172),
(522, NULL, NULL, 4, 172),
(523, NULL, NULL, 3, 173),
(524, NULL, NULL, 2, 173),
(525, NULL, NULL, 4, 173),
(526, NULL, NULL, 3, 174),
(527, NULL, NULL, 2, 174),
(528, NULL, NULL, 4, 174),
(529, NULL, NULL, 3, 175),
(530, NULL, NULL, 2, 175),
(531, NULL, NULL, 4, 175),
(532, NULL, NULL, 3, 176),
(533, NULL, NULL, 2, 176),
(534, NULL, NULL, 4, 176),
(535, NULL, NULL, 3, 177),
(536, NULL, NULL, 2, 177),
(537, NULL, NULL, 4, 177),
(538, NULL, NULL, 3, 178),
(539, NULL, NULL, 2, 178),
(540, NULL, NULL, 4, 178),
(541, NULL, NULL, 3, 179),
(542, NULL, NULL, 5, 179),
(543, NULL, NULL, 7, 179),
(544, NULL, NULL, 2, 179),
(545, NULL, NULL, 4, 179),
(546, NULL, NULL, 3, 181),
(547, NULL, NULL, 7, 181),
(548, NULL, NULL, 2, 181),
(549, NULL, NULL, 4, 181),
(550, NULL, NULL, 6, 181),
(551, NULL, NULL, 3, 182),
(552, NULL, NULL, 6, 182),
(553, NULL, NULL, 2, 182),
(554, NULL, NULL, 4, 182),
(555, NULL, NULL, 1, 183),
(556, NULL, NULL, 3, 183),
(557, NULL, NULL, 2, 183),
(558, NULL, NULL, 4, 183),
(559, NULL, NULL, 1, 184),
(560, NULL, NULL, 3, 184),
(561, NULL, NULL, 2, 184),
(562, NULL, NULL, 1, 185),
(563, NULL, NULL, 2, 185),
(564, NULL, NULL, 4, 185),
(565, NULL, NULL, 8, 185),
(566, NULL, NULL, 9, 185),
(567, NULL, NULL, 10, 185),
(568, NULL, NULL, 1, 186),
(569, NULL, NULL, 8, 186),
(570, NULL, NULL, 10, 186),
(571, NULL, NULL, 9, 186),
(572, NULL, NULL, 2, 186),
(573, NULL, NULL, 4, 186),
(574, NULL, NULL, 11, 186),
(575, NULL, NULL, 12, 186),
(576, NULL, NULL, 13, 186),
(577, NULL, NULL, 1, 187),
(578, NULL, NULL, 11, 187),
(579, NULL, NULL, 12, 187),
(580, NULL, NULL, 10, 187),
(581, NULL, NULL, 9, 187),
(582, NULL, NULL, 2, 187),
(583, NULL, NULL, 13, 187),
(584, NULL, NULL, 4, 187),
(585, NULL, NULL, 8, 187),
(586, NULL, NULL, 3, 188),
(587, NULL, NULL, 8, 188),
(588, NULL, NULL, 11, 188),
(589, NULL, NULL, 12, 188),
(590, NULL, NULL, 10, 188),
(591, NULL, NULL, 9, 188),
(592, NULL, NULL, 2, 188),
(593, NULL, NULL, 4, 188),
(594, NULL, NULL, 13, 188),
(595, NULL, NULL, 3, 189),
(596, NULL, NULL, 8, 189),
(597, NULL, NULL, 11, 189),
(598, NULL, NULL, 12, 189),
(599, NULL, NULL, 10, 189),
(600, NULL, NULL, 9, 189),
(601, NULL, NULL, 2, 189),
(602, NULL, NULL, 4, 189),
(603, NULL, NULL, 6, 189),
(604, NULL, NULL, 13, 189),
(605, NULL, NULL, 3, 190),
(606, NULL, NULL, 11, 190),
(607, NULL, NULL, 12, 190),
(608, NULL, NULL, 10, 190),
(609, NULL, NULL, 9, 190),
(610, NULL, NULL, 2, 190),
(611, NULL, NULL, 13, 190),
(612, NULL, NULL, 4, 190),
(613, NULL, NULL, 1, 191),
(614, NULL, NULL, 8, 191),
(615, NULL, NULL, 5, 191),
(616, NULL, NULL, 11, 191),
(617, NULL, NULL, 10, 191),
(618, NULL, NULL, 2, 191),
(619, NULL, NULL, 6, 191),
(620, NULL, NULL, 4, 191),
(621, NULL, NULL, 13, 191),
(622, NULL, NULL, 9, 191),
(623, NULL, NULL, 12, 191),
(624, NULL, NULL, 1, 192),
(625, NULL, NULL, 3, 192),
(626, NULL, NULL, 8, 192),
(627, NULL, NULL, 11, 192),
(628, NULL, NULL, 12, 192),
(629, NULL, NULL, 1, 193),
(630, NULL, NULL, 3, 193),
(631, NULL, NULL, 11, 193),
(632, NULL, NULL, 3, 194),
(633, NULL, NULL, 1, 194);

-- --------------------------------------------------------

--
-- Table structure for table `amenity_transport`
--

CREATE TABLE `amenity_transport` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `transport_id` bigint(20) UNSIGNED NOT NULL,
  `amenity_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenity_transport`
--

INSERT INTO `amenity_transport` (`id`, `created_at`, `updated_at`, `transport_id`, `amenity_id`) VALUES
(1, NULL, NULL, 2, 1),
(2, NULL, NULL, 2, 2),
(3, NULL, NULL, 7, 1),
(4, NULL, NULL, 7, 2),
(5, NULL, NULL, 7, 3),
(6, NULL, NULL, 8, 1),
(7, NULL, NULL, 8, 3),
(8, NULL, NULL, 8, 2),
(9, NULL, NULL, 9, 1),
(10, NULL, NULL, 9, 3),
(11, NULL, NULL, 9, 2),
(12, NULL, NULL, 10, 1),
(13, NULL, NULL, 10, 3),
(14, NULL, NULL, 10, 2),
(15, NULL, NULL, 11, 1),
(16, NULL, NULL, 11, 3),
(17, NULL, NULL, 11, 2),
(18, NULL, NULL, 11, 4),
(19, NULL, NULL, 12, 1),
(20, NULL, NULL, 12, 3),
(21, NULL, NULL, 12, 2),
(22, NULL, NULL, 12, 4),
(23, NULL, NULL, 13, 1),
(24, NULL, NULL, 13, 3),
(25, NULL, NULL, 13, 2),
(26, NULL, NULL, 13, 4),
(27, NULL, NULL, 14, 1),
(28, NULL, NULL, 14, 3),
(29, NULL, NULL, 14, 2),
(30, NULL, NULL, 15, 1),
(31, NULL, NULL, 15, 3),
(32, NULL, NULL, 15, 2),
(33, NULL, NULL, 16, 1),
(34, NULL, NULL, 16, 3),
(35, NULL, NULL, 16, 2),
(36, NULL, NULL, 17, 1),
(37, NULL, NULL, 17, 3),
(38, NULL, NULL, 17, 2),
(39, NULL, NULL, 18, 1),
(40, NULL, NULL, 18, 3),
(41, NULL, NULL, 19, 1),
(42, NULL, NULL, 19, 3),
(43, NULL, NULL, 20, 1),
(44, NULL, NULL, 20, 3);

-- --------------------------------------------------------

--
-- Table structure for table `booking_requests`
--

CREATE TABLE `booking_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) DEFAULT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tour_voucher_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_requests`
--

INSERT INTO `booking_requests` (`id`, `request_number`, `tour_id`, `date`, `file_name`, `created_at`, `updated_at`, `tour_voucher_file`) VALUES
(2, '20250626-0001', 44, '2025-06-26', 'booking_request_2.pdf', '2025-06-26 06:53:17', '2025-06-26 06:53:22', 'tour_voucher_2.pdf'),
(3, '20250626-0002', 44, '2025-06-26', 'booking_request_3.pdf', '2025-06-26 07:02:49', '2025-06-26 07:02:50', NULL),
(4, '20250626-0003', 44, '2025-06-26', 'booking_request_4.pdf', '2025-06-26 07:08:48', '2025-06-26 07:09:51', 'tour_voucher_4.pdf'),
(5, '20250822-0001', 45, '2025-08-25', NULL, '2025-08-22 03:35:53', '2025-08-22 03:35:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1758873864),
('a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1758873864;', 1758873864),
('jahongir_app_cache_044fca848b233146520c7a2169a065cfb06f3346', 'i:1;', 1738325018),
('jahongir_app_cache_044fca848b233146520c7a2169a065cfb06f3346:timer', 'i:1738325018;', 1738325018),
('jahongir_app_cache_09c5663f8043ea106ea239879ba8ef83620a4123', 'i:1;', 1740627673),
('jahongir_app_cache_09c5663f8043ea106ea239879ba8ef83620a4123:timer', 'i:1740627673;', 1740627673),
('jahongir_app_cache_153593627c5a3141ad52fc3d40bf363080881b87', 'i:1;', 1740144338),
('jahongir_app_cache_153593627c5a3141ad52fc3d40bf363080881b87:timer', 'i:1740144338;', 1740144338),
('jahongir_app_cache_30376e2420517360b7912a9d436361f2666cc570', 'i:2;', 1740143871),
('jahongir_app_cache_30376e2420517360b7912a9d436361f2666cc570:timer', 'i:1740143871;', 1740143871),
('jahongir_app_cache_3177980db03bcc3535023e0aedf2a6c79193da12', 'i:1;', 1750742851),
('jahongir_app_cache_3177980db03bcc3535023e0aedf2a6c79193da12:timer', 'i:1750742851;', 1750742851),
('jahongir_app_cache_51ca0fd828008b307f0529c52a409ccb6327df2d', 'i:1;', 1740464236),
('jahongir_app_cache_51ca0fd828008b307f0529c52a409ccb6327df2d:timer', 'i:1740464236;', 1740464236),
('jahongir_app_cache_64bae16f6665fc272385bfcd481edecc41e76f88', 'i:1;', 1738304392),
('jahongir_app_cache_64bae16f6665fc272385bfcd481edecc41e76f88:timer', 'i:1738304392;', 1738304392),
('jahongir_app_cache_69f049bcb7005d2f9716cf0a53992dbe3bae426c', 'i:1;', 1739948874),
('jahongir_app_cache_69f049bcb7005d2f9716cf0a53992dbe3bae426c:timer', 'i:1739948871;', 1739948874),
('jahongir_app_cache_6a94c1495411d80253dd1450afaf8fda4b73bd15', 'i:1;', 1750786068),
('jahongir_app_cache_6a94c1495411d80253dd1450afaf8fda4b73bd15:timer', 'i:1750786068;', 1750786068),
('jahongir_app_cache_9226bb3cf703587b24233b6b94de9af485dfd477', 'i:1;', 1738583953),
('jahongir_app_cache_9226bb3cf703587b24233b6b94de9af485dfd477:timer', 'i:1738583953;', 1738583953),
('jahongir_app_cache_95a382cc17fc2af5046e0f6d93bbfa85501ada2d', 'i:1;', 1739963679),
('jahongir_app_cache_95a382cc17fc2af5046e0f6d93bbfa85501ada2d:timer', 'i:1739963679;', 1739963679),
('jahongir_app_cache_a0ad7d87a838aca0243e57b45cbe02ac7329fd29', 'i:1;', 1749484529),
('jahongir_app_cache_a0ad7d87a838aca0243e57b45cbe02ac7329fd29:timer', 'i:1749484529;', 1749484529),
('jahongir_app_cache_aba171b8c02b49c12c061a1cf81b975783bcdf6c', 'i:1;', 1738321444),
('jahongir_app_cache_aba171b8c02b49c12c061a1cf81b975783bcdf6c:timer', 'i:1738321444;', 1738321444),
('jahongir_app_cache_b25a8568aae7f8029116469aee65123b4f24f93b', 'i:1;', 1747051353),
('jahongir_app_cache_b25a8568aae7f8029116469aee65123b4f24f93b:timer', 'i:1747051353;', 1747051353),
('jahongir_app_cache_bb5948fb8ac20e540c59df0ea7e5d270f895bed5', 'i:1;', 1738819124),
('jahongir_app_cache_bb5948fb8ac20e540c59df0ea7e5d270f895bed5:timer', 'i:1738819124;', 1738819124),
('jahongir_app_cache_c33f6437eb04d822a6a23070d11e4d6ea8c575a3', 'i:1;', 1745236510),
('jahongir_app_cache_c33f6437eb04d822a6a23070d11e4d6ea8c575a3:timer', 'i:1745236510;', 1745236510),
('jahongir_app_cache_c686205b50bad7e8009b774020bd373e04a15190', 'i:1;', 1746693693),
('jahongir_app_cache_c686205b50bad7e8009b774020bd373e04a15190:timer', 'i:1746693693;', 1746693693),
('jahongir_app_cache_c6e741e3ab97ea8550c74592f7855ec521d1f004', 'i:1;', 1738844148),
('jahongir_app_cache_c6e741e3ab97ea8550c74592f7855ec521d1f004:timer', 'i:1738844148;', 1738844148),
('jahongir_app_cache_c7e30150b6bf587120b34eccfc9db2bef3ca0f5d', 'i:1;', 1751266572),
('jahongir_app_cache_c7e30150b6bf587120b34eccfc9db2bef3ca0f5d:timer', 'i:1751266572;', 1751266572),
('jahongir_app_cache_cb9a251a8ca68fbbbf1869a489086ed5ec3d2985', 'i:1;', 1740560161),
('jahongir_app_cache_cb9a251a8ca68fbbbf1869a489086ed5ec3d2985:timer', 'i:1740560161;', 1740560161),
('jahongir_app_cache_cda2dd9cebec207b1d7f08d18610d8e4b2b53e5e', 'i:1;', 1747367543),
('jahongir_app_cache_cda2dd9cebec207b1d7f08d18610d8e4b2b53e5e:timer', 'i:1747367543;', 1747367543),
('jahongir_app_cache_d1f7c2160ef2512861505739b2a6800721d8f13c', 'i:1;', 1751045810),
('jahongir_app_cache_d1f7c2160ef2512861505739b2a6800721d8f13c:timer', 'i:1751045810;', 1751045810),
('jahongir_app_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1750921744),
('jahongir_app_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1750921744;', 1750921744),
('jahongir_app_cache_f1cd26bbb762987baa38bff81023c2c66ea2e712', 'i:1;', 1749451672),
('jahongir_app_cache_f1cd26bbb762987baa38bff81023c2c66ea2e712:timer', 'i:1749451671;', 1749451671),
('jahongir_app_cache_f7e9844b2191235a476b04c0c943100885fb66cf', 'i:1;', 1742183685),
('jahongir_app_cache_f7e9844b2191235a476b04c0c943100885fb66cf:timer', 'i:1742183685;', 1742183685),
('jahongir_app_cache_f955b833aaafd612a05b76b98a1319dbc51b2fe4', 'i:4;', 1738472138),
('jahongir_app_cache_f955b833aaafd612a05b76b98a1319dbc51b2fe4:timer', 'i:1738472138;', 1738472138),
('jahongir_app_cache_fc90407502ffb8aa8ec3c58d009566eb027cd59f', 'i:1;', 1744002674),
('jahongir_app_cache_fc90407502ffb8aa8ec3c58d009566eb027cd59f:timer', 'i:1744002674;', 1744002674);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `created_at`, `updated_at`, `name`, `description`, `images`) VALUES
(2, '2025-01-11 05:16:30', '2025-01-11 05:52:58', 'Tashkent', NULL, '[]'),
(3, '2025-01-11 05:16:47', '2025-01-11 05:16:47', 'Samarkand', NULL, '[]'),
(4, '2025-01-11 05:17:09', '2025-01-11 05:39:03', 'Bukhara', NULL, '[]'),
(5, '2025-01-11 05:17:35', '2025-01-11 05:53:17', 'Khiva', NULL, '[]'),
(6, '2025-01-11 05:54:14', '2025-01-30 10:07:40', 'Almata', NULL, '[]'),
(8, '2025-01-11 05:54:38', '2025-01-11 05:54:38', 'Navoiy', NULL, '[]'),
(9, '2025-01-11 05:55:01', '2025-01-11 05:55:01', 'Fergana', NULL, '[]'),
(10, '2025-01-11 05:55:19', '2025-01-11 05:55:19', 'Nukus', NULL, '[]'),
(11, '2025-01-18 07:10:22', '2025-01-18 07:10:22', 'Termez', NULL, '[]'),
(12, '2025-01-21 08:08:58', '2025-01-21 08:08:58', 'Nurata', NULL, '[]'),
(13, '2025-01-21 08:10:01', '2025-01-21 08:10:01', 'Shaxrisabz', NULL, '[]'),
(14, '2025-01-21 08:10:29', '2025-01-21 09:29:33', 'Fergana', NULL, '[]'),
(15, '2025-01-21 09:48:10', '2025-01-21 09:48:10', 'Namangan', NULL, '[]'),
(16, '2025-01-21 09:48:49', '2025-01-21 09:48:49', 'Andijan', NULL, '[]'),
(17, '2025-01-22 11:11:57', '2025-01-22 11:11:57', 'Urgench', NULL, '[]'),
(18, '2025-01-24 08:49:24', '2025-01-24 08:49:24', 'Karakalpakistan', NULL, '[]'),
(19, '2025-01-29 13:02:50', '2025-01-29 13:02:50', 'Yangigazgan', NULL, '[]'),
(20, '2025-01-30 10:08:02', '2025-01-30 10:08:02', 'Dushanbe', NULL, '[]'),
(21, '2025-01-30 10:08:18', '2025-01-30 10:08:18', 'Ashxabod', NULL, '[]'),
(22, '2025-01-30 10:08:34', '2025-01-30 10:08:34', 'Chengdu', NULL, '[]'),
(23, '2025-01-30 10:08:47', '2025-01-30 10:08:47', 'Bishkek', NULL, '[]'),
(24, '2025-01-30 10:09:01', '2025-01-30 10:09:01', 'Xi\'an', NULL, '[]'),
(25, '2025-01-30 10:09:11', '2025-01-30 10:09:11', 'Pekin', NULL, '[]'),
(26, '2025-01-30 10:09:23', '2025-01-30 10:09:23', 'Guangzhou', NULL, '[]'),
(27, '2025-01-30 10:37:25', '2025-01-30 10:37:25', 'Gijduvan', NULL, '[]'),
(28, '2025-01-30 11:04:16', '2025-01-30 11:04:16', 'Qalqonota', NULL, '[]'),
(29, '2025-01-30 11:35:48', '2025-01-30 11:35:48', 'Qo\'shrobot', NULL, '[]'),
(30, '2025-01-31 06:10:25', '2025-01-31 06:10:25', 'Chimgan', NULL, '[]'),
(31, '2025-01-31 06:10:39', '2025-01-31 06:10:39', 'Turkmanobod', NULL, '[]'),
(32, '2025-09-26 03:40:00', '2025-09-26 03:40:00', 'Khiva - Bukhara', NULL, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `city_distances`
--

CREATE TABLE `city_distances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `city_from_to` varchar(255) NOT NULL,
  `distance_km` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city_distances`
--

INSERT INTO `city_distances` (`id`, `created_at`, `updated_at`, `city_from_to`, `distance_km`) VALUES
(1, '2025-02-15 08:22:59', '2025-02-15 08:22:59', 'Samarkand-Tashkent', 300),
(2, '2025-02-15 08:23:29', '2025-02-15 08:23:29', 'Samarkand-Bukhara', 300),
(3, '2025-02-15 08:23:48', '2025-02-15 08:23:48', 'Samarkand', 100),
(4, '2025-02-15 08:24:03', '2025-02-15 08:24:03', 'Samarkand 2', 0),
(5, '2025-02-20 10:34:56', '2025-02-20 10:34:56', 'Samarqand-Urganch', 750),
(6, '2025-02-20 10:44:31', '2025-02-20 10:44:31', 'Samarqand-Termiz', 450),
(7, '2025-02-20 10:44:48', '2025-02-20 10:45:31', 'Samarqand-Jizzax', 100),
(8, '2025-02-20 10:47:35', '2025-02-20 11:15:54', 'Toshkent-KPP Oybek', 100),
(9, '2025-02-20 10:52:44', '2025-02-20 11:04:58', 'Toshkent-Navoi', 500),
(10, '2025-02-20 11:08:37', '2025-02-20 11:08:37', 'Samarqand-KPP Panjikend', 100),
(11, '2025-02-20 11:09:18', '2025-02-20 11:09:18', 'KPP Panjikend-Samarqand', 100),
(12, '2025-02-20 11:09:52', '2025-02-20 11:09:52', 'Samarqand-Aydarkol', 250),
(13, '2025-02-20 11:10:47', '2025-02-20 11:10:47', 'Samarqand-Navoi', 200),
(14, '2025-02-20 11:11:28', '2025-02-20 11:11:28', 'Samarqand-Nukus', 800),
(15, '2025-02-20 11:12:03', '2025-02-20 11:12:03', 'Samarqand-Shaxrisabz', 150),
(16, '2025-02-20 11:12:57', '2025-02-20 11:12:57', 'Samarqand-Sirdaryo', 200),
(17, '2025-02-20 11:13:42', '2025-02-20 11:13:42', 'Samarqand-Qarshi', 150),
(18, '2025-02-20 11:16:22', '2025-02-20 11:16:22', 'KPP Oybek-Toshkent', 100),
(19, '2025-02-20 11:17:46', '2025-02-20 11:17:46', 'Chernaevka-Toshkent', 100),
(20, '2025-02-20 11:20:05', '2025-02-20 11:20:05', 'Toshkent-Sirdaryo', 100),
(21, '2025-02-20 11:20:58', '2025-02-20 11:20:58', 'Toshkent-Buxoro', 600),
(22, '2025-02-20 11:21:24', '2025-02-20 11:21:24', 'Toshkent-Qarshi', 450),
(23, '2025-02-20 11:22:19', '2025-02-20 11:22:19', 'Toshkent-Termiz', 750),
(24, '2025-02-20 11:23:12', '2025-02-20 11:23:12', 'Toshkent-Urganch', 1100),
(25, '2025-02-20 11:24:07', '2025-02-20 11:24:07', 'Toshkent-Nukus', 1100),
(26, '2025-02-20 12:03:06', '2025-02-20 12:03:33', 'Shaxrisabz-Buxoro', 300),
(27, '2025-02-20 12:04:04', '2025-02-20 12:04:04', 'Shaxrisabz-Samarqand', 150),
(28, '2025-02-20 12:05:28', '2025-02-20 12:05:28', 'Termiz-Samarqand', 450),
(29, '2025-02-20 12:06:57', '2025-02-20 12:06:57', 'Termiz-Qarshi', 300),
(30, '2025-02-20 12:07:20', '2025-02-20 12:07:20', 'Termiz-Buxopo', 600),
(31, '2025-02-20 12:08:47', '2025-02-20 12:08:47', 'Termiz-Shaxrisabz', 300),
(32, '2025-02-20 12:09:53', '2025-02-20 12:09:53', 'Qarshi-Termiz', 300),
(33, '2025-02-20 12:10:19', '2025-02-20 12:10:19', 'Qarshi-Samarqand', 150),
(34, '2025-02-20 12:10:54', '2025-02-20 12:10:54', 'Qarshi-Buxoro', 300),
(35, '2025-02-20 12:19:20', '2025-02-20 12:19:20', 'Buxoro-Samarqand', 300),
(36, '2025-02-20 12:19:52', '2025-02-20 12:19:52', 'Buxoro-Qarshi', 300),
(37, '2025-02-20 12:20:32', '2025-02-20 12:20:32', 'Buxoro-Shaxrisabz', 300),
(38, '2025-02-20 12:21:00', '2025-02-20 12:21:00', 'Buxoro-Termiz', 600),
(39, '2025-02-20 12:21:58', '2025-02-20 12:21:58', 'Buxoro-Urganch', 450),
(40, '2025-02-20 12:22:19', '2025-02-20 12:22:19', 'Buxoro-Nukus', 500),
(41, '2025-02-20 12:27:47', '2025-02-20 12:27:47', 'Buxoro-Navoi', 100),
(42, '2025-02-20 12:28:15', '2025-02-20 12:28:15', 'Buxoro-Aydarkol', 250),
(43, '2025-02-20 12:28:44', '2025-02-20 12:28:44', 'Buxoro-Toshkent', 600),
(44, '2025-02-20 12:30:14', '2025-02-20 12:30:14', 'Navoi-Buxoro', 100),
(45, '2025-02-20 12:30:35', '2025-02-20 12:30:35', 'Navoi-Samarqand', 200),
(46, '2025-02-20 12:31:51', '2025-02-20 12:31:51', 'Aydarkol-Buxoro', 250),
(47, '2025-02-20 12:32:17', '2025-02-20 12:32:17', 'Aydarkol-Samarqand', 250),
(48, '2025-02-20 12:33:27', '2025-02-20 12:33:27', 'Toshkent', 100),
(49, '2025-02-20 12:33:52', '2025-02-20 12:33:52', 'Buxoro', 100),
(50, '2025-02-20 12:34:06', '2025-02-20 12:34:06', 'Buxoro 2', 0),
(51, '2025-02-20 12:35:14', '2025-02-20 12:35:14', 'Urganch-Xiva', 40),
(52, '2025-02-20 12:37:26', '2025-02-20 12:37:26', 'Urganch-Ayaz qala-Xiva', 220),
(53, '2025-02-20 12:39:04', '2025-02-20 12:39:26', 'Urganch-Nukus', 170),
(54, '2025-02-20 12:42:05', '2025-02-20 12:42:32', 'Nukus-Xiva', 200),
(55, '2025-02-20 12:44:14', '2025-02-20 12:44:14', 'Nukus-Ayaz qala-Xiva', 300),
(56, '2025-02-20 12:51:07', '2025-02-20 12:51:07', 'Xiva-Ayaz qala-Nukus', 300),
(57, '2025-02-20 13:08:47', '2025-02-20 13:08:47', 'Xiva-Urganch', 40),
(58, '2025-02-20 13:09:20', '2025-02-20 13:09:20', 'Xiva-Ayaz qala-Xiva', 220),
(59, '2025-02-21 05:05:01', '2025-02-21 05:05:01', 'Toshkent-Samarqand', 300);

-- --------------------------------------------------------

--
-- Table structure for table `city_tour_day`
--

CREATE TABLE `city_tour_day` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_day_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city_tour_day`
--

INSERT INTO `city_tour_day` (`id`, `tour_day_id`, `city_id`, `created_at`, `updated_at`) VALUES
(8, 19, 2, '2025-01-30 05:15:00', '2025-01-30 05:15:00'),
(9, 20, 2, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(10, 20, 3, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(11, 21, 3, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(12, 21, 2, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(13, 22, 2, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(14, 9, 2, '2025-01-30 05:53:27', '2025-01-30 05:53:27'),
(15, 10, 2, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(16, 11, 2, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(17, 11, 3, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(18, 12, 3, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(19, 13, 3, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(20, 13, 2, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(21, 14, 2, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(22, 23, 2, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(23, 24, 2, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(24, 24, 3, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(25, 25, 3, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(26, 26, 3, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(27, 26, 4, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(28, 27, 4, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(29, 27, 2, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(30, 28, 2, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(31, 29, 2, '2025-01-30 07:45:40', '2025-01-30 07:45:40'),
(32, 30, 2, '2025-01-30 07:45:40', '2025-01-30 07:45:40'),
(33, 30, 4, '2025-01-30 07:45:40', '2025-01-30 07:45:40'),
(34, 31, 4, '2025-01-30 07:45:40', '2025-01-30 07:45:40'),
(35, 32, 6, '2025-01-30 10:55:10', '2025-01-30 10:55:10'),
(36, 32, 2, '2025-01-30 10:55:10', '2025-01-30 10:55:10'),
(37, 33, 2, '2025-01-30 10:55:10', '2025-01-30 10:55:10'),
(38, 33, 20, '2025-01-30 10:55:10', '2025-01-30 10:55:10'),
(39, 34, 20, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(40, 34, 3, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(41, 35, 3, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(42, 36, 3, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(43, 36, 4, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(44, 37, 4, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(45, 37, 21, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(46, 38, 21, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(47, 38, 5, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(48, 39, 5, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(49, 39, 17, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(50, 39, 2, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(51, 40, 2, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(52, 40, 6, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(54, 28, 25, '2025-01-30 11:00:37', '2025-01-30 11:00:37'),
(55, 19, 22, '2025-01-30 11:02:06', '2025-01-30 11:02:06'),
(56, 22, 22, '2025-01-30 11:02:06', '2025-01-30 11:02:06'),
(60, 45, 4, '2025-01-30 11:17:36', '2025-01-30 11:17:36'),
(61, 46, 27, '2025-01-30 11:17:36', '2025-01-30 11:17:36'),
(62, 47, 28, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(63, 48, 28, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(64, 48, 12, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(65, 14, 22, '2025-01-30 11:25:30', '2025-01-30 11:25:30'),
(71, 54, 19, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(72, 55, 19, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(73, 55, 29, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(74, 56, 3, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(75, 57, 3, '2025-01-30 12:08:54', '2025-01-30 12:08:54'),
(76, 58, 3, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(77, 59, 3, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(78, 60, 3, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(79, 60, 2, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(80, 61, 2, '2025-01-30 12:33:55', '2025-01-30 12:33:55'),
(81, 62, 22, '2025-01-31 05:26:37', '2025-01-31 05:26:37'),
(82, 62, 2, '2025-01-31 05:26:37', '2025-01-31 05:26:37'),
(83, 63, 2, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(84, 63, 4, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(85, 64, 4, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(86, 64, 3, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(87, 65, 3, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(88, 66, 3, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(89, 66, 2, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(90, 67, 2, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(91, 67, 22, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(92, 68, 2, '2025-01-31 05:35:46', '2025-01-31 05:35:46'),
(93, 69, 2, '2025-01-31 05:35:46', '2025-01-31 05:35:46'),
(94, 70, 6, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(95, 70, 2, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(96, 71, 2, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(97, 71, 3, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(98, 72, 3, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(99, 73, 3, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(100, 73, 4, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(101, 74, 4, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(102, 75, 21, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(103, 75, 5, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(104, 76, 5, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(105, 76, 2, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(106, 76, 17, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(107, 77, 2, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(108, 77, 6, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(109, 78, 24, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(110, 78, 3, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(111, 79, 3, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(112, 79, 4, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(113, 80, 4, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(114, 80, 3, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(115, 81, 3, '2025-01-31 06:34:04', '2025-01-31 06:34:04'),
(116, 81, 24, '2025-01-31 06:34:04', '2025-01-31 06:34:04'),
(117, 82, 25, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(118, 82, 2, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(119, 83, 2, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(120, 83, 3, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(121, 84, 3, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(122, 85, 3, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(123, 85, 4, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(124, 86, 4, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(125, 86, 5, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(126, 87, 5, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(127, 87, 17, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(128, 87, 2, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(129, 88, 2, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(130, 88, 25, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(133, 91, 31, '2025-01-31 11:23:40', '2025-01-31 11:23:40'),
(134, 91, 5, '2025-01-31 11:23:40', '2025-01-31 11:23:40'),
(141, 98, 30, '2025-01-31 11:46:17', '2025-01-31 11:46:17'),
(142, 99, 30, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(143, 100, 30, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(144, 100, 4, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(145, 101, 4, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(146, 102, 4, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(147, 103, 12, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(153, 108, 5, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(154, 109, 5, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(155, 109, 4, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(156, 110, 4, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(157, 111, 4, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(158, 111, 3, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(159, 112, 3, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(160, 113, 3, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(161, 113, 2, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(162, 114, 2, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(163, 114, 6, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(164, 115, 6, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(165, 115, 2, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(166, 116, 2, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(167, 116, 5, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(168, 117, 5, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(169, 117, 31, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(170, 118, 31, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(171, 118, 4, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(172, 119, 4, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(173, 119, 3, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(174, 120, 3, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(175, 121, 3, '2025-02-01 07:47:29', '2025-02-01 07:47:29'),
(176, 121, 2, '2025-02-01 07:47:29', '2025-02-01 07:47:29'),
(177, 122, 2, '2025-02-01 07:47:29', '2025-02-01 07:47:29'),
(178, 122, 22, '2025-02-01 07:47:29', '2025-02-01 07:47:29'),
(179, 123, 2, '2025-02-04 11:49:22', '2025-02-04 11:49:22'),
(180, 124, 2, '2025-02-04 12:43:11', '2025-02-04 12:43:11'),
(181, 125, 3, '2025-02-11 10:06:27', '2025-02-11 10:06:27'),
(183, 127, 3, '2025-02-11 10:10:50', '2025-02-11 10:10:50'),
(184, 128, 4, '2025-02-11 10:48:32', '2025-02-11 10:48:32'),
(185, 129, 4, '2025-02-11 10:57:21', '2025-02-11 10:57:21'),
(186, 130, 4, '2025-02-11 10:57:22', '2025-02-11 10:57:22'),
(187, 130, 2, '2025-02-11 10:57:22', '2025-02-11 10:57:22'),
(188, 131, 2, '2025-02-11 10:57:22', '2025-02-11 10:57:22'),
(189, 132, 22, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(190, 132, 2, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(191, 133, 2, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(192, 133, 20, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(193, 134, 20, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(194, 134, 3, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(195, 135, 3, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(196, 135, 4, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(197, 136, 4, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(198, 136, 31, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(199, 137, 31, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(200, 137, 5, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(201, 138, 5, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(202, 138, 2, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(203, 139, 2, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(204, 139, 22, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(205, 140, 3, '2025-02-19 11:14:57', '2025-02-19 11:14:57'),
(206, 141, 6, '2025-02-19 11:17:00', '2025-02-19 11:17:00'),
(207, 142, 24, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(208, 142, 2, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(209, 143, 2, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(210, 143, 3, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(211, 144, 3, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(212, 145, 3, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(213, 145, 4, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(214, 146, 4, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(215, 146, 2, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(216, 147, 2, '2025-02-21 13:35:30', '2025-02-21 13:35:30'),
(217, 148, 24, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(218, 148, 3, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(219, 149, 3, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(220, 150, 3, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(221, 150, 4, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(222, 151, 4, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(223, 152, 4, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(224, 152, 11, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(225, 153, 11, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(226, 154, 11, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(227, 154, 31, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(231, 158, 2, '2025-06-26 06:53:02', '2025-06-26 06:53:02'),
(232, 159, 3, '2025-06-26 06:54:51', '2025-06-26 06:54:51'),
(233, 160, 13, '2025-08-22 03:35:31', '2025-08-22 03:35:31'),
(234, 161, 2, '2025-09-26 03:46:09', '2025-09-26 03:46:09'),
(237, 164, 5, '2025-09-26 03:54:52', '2025-09-26 03:54:52'),
(238, 165, 5, '2025-09-26 03:54:52', '2025-09-26 03:54:52'),
(239, 166, 4, '2025-09-26 04:04:15', '2025-09-26 04:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_operator` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `inn` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_mfo` varchar(255) DEFAULT NULL,
  `director_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `is_operator`, `created_at`, `updated_at`, `name`, `address_street`, `address_city`, `phone`, `email`, `inn`, `account_number`, `bank_name`, `bank_mfo`, `director_name`, `logo`, `license_number`) VALUES
(1, 0, '2025-05-16 03:52:01', '2025-06-26 07:08:14', 'Sheherazade Sam Star Tour', '75 Ulugbek str.,', 'Samarkand', '+998 66 233 27 40', 'info@sheherazade-tour.com', '302222', '803', 'Asakabank', '130', 'Jahangirov Tolib ', '01JVBKS95TPNJZ4S6GCT8HT2F4.png', NULL),
(2, 1, '2025-06-26 07:08:07', '2025-06-26 07:08:07', 'СП «Jaxongir travel»', 'Chirokchi 4', 'Samarkand', '+998915550808', 'odilorg@gmail.com', '300965341', '20208000704734557001', 'Hamkorbank ОАТB Andijon f-li', '00083', 'Jahangirov O. Sh.', '01JYNH1C3R4RX3P1VZRJ5QYRC3.png', 'NJH6775');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `created_at`, `updated_at`, `name`, `email`, `phone`, `address`) VALUES
(1, '2025-01-10 16:50:09', '2025-01-10 16:50:09', 'Alain Migus', 'tolib71@mail.ru', '+998902115854', 'Amir Timur 164/11'),
(2, '2025-01-13 11:07:45', '2025-01-13 11:07:45', 'Odil', 'odilorg@gmail.com', '998915550808', 'Chiroqchi 11 '),
(3, '2025-01-13 11:32:26', '2025-01-13 11:32:26', 'GUANGZHOU  Aero Meng', 'aero_meng@gzl.com.cn', '862086089947    8615017564445 ', '1,Lejia Rd, jichang Rd West, Guangzhou, P.R. China Pc:510403'),
(4, '2025-01-21 09:35:10', '2025-01-21 09:57:40', 'Sasha Zhongtong', 'sqmzt@sina.com', '+998900620888', 'Liaocheng, Shandong, China'),
(5, '2025-01-21 09:51:17', '2025-01-21 09:51:17', 'Stella Mark travel ', 'stellagu88@163.com', '+8618108189272', 'Chengdu, Sichuan , China'),
(6, '2025-01-26 13:01:40', '2025-02-01 06:40:58', 'Shamihon', 'sh@hs.com', '987878787', 'Tashkent'),
(7, '2025-01-29 12:54:20', '2025-01-29 12:54:20', 'Olim', 'info@sss-tour.com', '+998885480080', 'Shota Rustaveli 45'),
(8, '2025-02-01 06:31:17', '2025-02-01 06:31:17', 'GAOLONG + AYYUB ', '295861722@qq.com', '13709298506', 'CHINA, XI\'AN'),
(9, '2025-02-20 10:48:11', '2025-02-20 10:48:11', 'ALI GUANGZHOU MOUSLIM', 'musharraf.hokimovna@mail.ru', '+998 90 104 62 01', 'CHINA, GUANGZHOU');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `license_expiry_date` varchar(255) DEFAULT NULL,
  `license_image` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `created_at`, `updated_at`, `name`, `email`, `phone`, `address`, `license_number`, `license_expiry_date`, `license_image`, `profile_image`) VALUES
(1, '2025-01-13 11:15:40', '2025-01-14 04:26:47', 'SHODMONOV  JAMSHID', NULL, '+998993120741', NULL, 'AF 1767363', '14.08.2030', '01JHHH0TQS6KF5V35DK048MNCX.jpg', NULL),
(2, '2025-01-13 11:18:02', '2025-01-13 12:54:13', 'ESHONQULOV BAXODIR', NULL, '+998994160648', NULL, 'AF 4907663', '06.04.2032', '01JHFVG44388EMJBNVWPKQ2BWZ.jpg', NULL),
(3, '2025-01-13 11:36:24', '2025-01-13 11:36:24', 'YUSUPOV XASAN', NULL, '+998902284056', NULL, 'AD 6301731', '27.02.2034', '01JHFQ6QTG1JSFQ9AS5JSFH5RZ.jpg', NULL),
(4, '2025-01-13 11:51:16', '2025-01-13 11:51:16', 'QUVONDIQOV MUSOQUL', NULL, '+998 99 552 93 60', NULL, NULL, NULL, NULL, NULL),
(5, '2025-01-13 11:52:06', '2025-01-14 04:27:35', 'POLATOV OYNAZAR', NULL, '+998 93 239 99 95', NULL, 'AG 0905391', '04.04.2033', '01JHHH29JXAG6MGEZCQS30KXA8.jpg', NULL),
(6, '2025-01-13 11:52:42', '2025-01-13 12:54:30', 'ERGASHOV XALIMJON', NULL, '+998 97 927 96 70', NULL, 'AF 0833209', '19.06.2029', '01JHFVHERGPR2KNFHCHJ71GSJE.jpg', NULL),
(7, '2025-01-13 11:53:58', '2025-01-13 12:54:47', 'SULAYMONOV G`IYOS', NULL, '+998 94 406 09 00', NULL, 'AF 0762614', '17.05.2029', '01JHFVJS2J3HGTTG1NGBMMRC4M.jpg', NULL),
(8, '2025-01-13 11:54:42', '2025-01-13 11:54:42', 'OMONOV ZOIR', NULL, '+998 94 240 83 97', NULL, NULL, NULL, NULL, NULL),
(9, '2025-01-13 11:57:57', '2025-01-13 12:55:14', 'ABDULLAYEV ABDUQAXXOR', NULL, '+998 93 344 68 79', NULL, 'AA 0205357', '25.04.2028', '01JHFVM5EWRPTHM431ZH0CFVG9.jpg', NULL),
(10, '2025-01-13 11:58:58', '2025-01-13 12:56:04', 'ATABOEV XURSHID', NULL, '+998 97 288 16 11', NULL, 'AF 4780877', '09.03.2032', '01JHFVRM4BNEBKP86F5Y53HG06.jpg', NULL),
(11, '2025-01-13 11:59:53', '2025-01-13 12:56:49', 'JALOLOV SARVAR', NULL, '+998 93 722 27 50', NULL, 'AF 1109038', '11.10.2029', '01JHFVT057ZKT14Y1PX0GE7V8E.jpg', NULL),
(12, '2025-01-13 12:00:44', '2025-01-14 04:28:45', 'YAKUBOV ISLOM', NULL, '+998 99 023 16 93', NULL, 'AF 3526134', '22.11.2031', '01JHHH4D77RKWZGDEK230FYA1Y.jpg', NULL),
(13, '2025-01-13 12:01:23', '2025-01-13 12:58:03', 'SIROJOV SANJAR', NULL, '+998 90 191 29 97', NULL, 'AG 0100173', '13.10.2032', '01JHFVW8RB3WDS4381D2JYB06H.jpg', NULL),
(14, '2025-01-13 12:02:19', '2025-01-13 12:58:53', 'KAMOLOV AKMAL', NULL, '+998 99 738 75 24', NULL, 'AF 0076952', '07.09.2028', '01JHFVXRWWZJMXEVD3Y4XT83XJ.jpg', NULL),
(15, '2025-01-13 12:03:10', '2025-01-13 12:59:36', 'XUDOYBERDIEV MAVLON', NULL, '+998 94 522 42 80', NULL, 'AF 0294853', '30.11.2028', '01JHFVZ322YGAS2V8D1X13C8WM.jpg', NULL),
(16, '2025-01-13 12:03:47', '2025-01-13 13:00:18', 'KARIMOV FERUZ', NULL, '+998 94 186 72 74', NULL, 'AF 5082750', '20.05.2032', '01JHFW0CHDBH4DV2G2KA9S4AH2.jpg', NULL),
(17, '2025-01-13 12:04:52', '2025-01-13 13:24:26', 'QO`SHOQOV ELYOR', NULL, '+998 95 188 21 88', NULL, 'AG 1983859', '27.02.2034', '01JHFXCHXD91PE45PT9M8E5090.jpg', NULL),
(18, '2025-01-13 12:05:39', '2025-01-13 13:01:31', 'SAMANDAROV UCHQUN', NULL, '+998 94 079 74 12', NULL, 'AF 0452329', '28.12.2028', '01JHFW2KDCPHV325NGDEZZPJGM.jpg', NULL),
(19, '2025-01-13 12:09:57', '2025-01-13 12:09:57', 'ZAYNITDINOV NURIDDIN', NULL, '+998 90 445 10 92', NULL, NULL, NULL, NULL, NULL),
(20, '2025-09-26 03:11:44', '2025-09-26 03:11:44', 'Ilhom', 'ilhom@ok.com', '+9985845454', 'samarkand', 'sds545', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `estimates`
--

CREATE TABLE `estimates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estimate_number` varchar(255) NOT NULL,
  `estimate_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `markup` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `estimates`
--

INSERT INTO `estimates` (`id`, `created_at`, `updated_at`, `estimate_number`, `estimate_date`, `notes`, `file_name`, `customer_id`, `tour_id`, `number`, `markup`) VALUES
(15, '2025-01-21 11:07:06', '2025-01-21 11:07:06', 'EST15012025', '2025-01-21', 'Sasha uchun Tour', 'estimate_15.pdf', 4, 8, 'EST-2025-015', NULL),
(16, '2025-01-21 11:20:14', '2025-01-21 11:20:14', 'EST16012025', '2025-01-21', NULL, 'estimate_16.pdf', 4, 8, 'EST-2025-016', NULL),
(25, '2025-01-30 05:18:51', '2025-01-30 05:18:51', 'EST25012025', '2025-01-30', NULL, 'estimate_25.pdf', 5, 12, 'EST-2025-025', 10),
(26, '2025-01-30 05:54:24', '2025-01-30 05:54:24', 'EST26012025', '2025-01-30', NULL, 'estimate_26.pdf', 5, 8, 'EST-2025-026', 15),
(27, '2025-01-30 11:05:57', '2025-01-30 11:05:57', 'EST27012025', '2025-01-30', NULL, 'estimate_27.pdf', 5, 13, 'EST-2025-027', 10),
(28, '2025-01-30 11:07:23', '2025-01-30 11:07:23', 'EST28012025', '2025-01-30', NULL, 'estimate_28.pdf', 5, 15, 'EST-2025-028', 10),
(29, '2025-02-01 05:13:07', '2025-02-01 05:13:07', 'EST29022025', '2025-02-01', NULL, NULL, 5, 22, 'EST-2025-029', NULL),
(30, '2025-02-01 07:48:12', '2025-02-01 07:48:12', 'EST30022025', '2025-02-01', NULL, 'estimate_30.pdf', 5, 34, 'EST-2025-030', NULL),
(31, '2025-02-19 07:39:48', '2025-02-19 07:39:48', 'EST31022025', '2025-02-19', NULL, 'estimate_31.pdf', 5, 36, 'EST-2025-031', 10),
(33, '2025-02-19 11:18:29', '2025-02-19 11:18:29', 'EST33022025', '2023-12-22', 'Eiusmod dolorum elit', 'estimate_33.pdf', 3, 37, 'EST-2025-033', NULL),
(34, '2025-02-20 10:48:34', '2025-02-20 10:48:34', 'EST34022025', '2025-02-20', NULL, 'estimate_34.pdf', 9, 40, 'EST-2025-034', 10),
(35, '2025-02-21 13:36:35', '2025-02-21 13:36:35', 'EST35022025', '2025-02-21', 'rich client  ham good money', 'estimate_35.pdf', 1, 41, 'EST-2025-035', 25),
(36, '2025-03-11 07:23:20', '2025-03-11 07:23:20', 'EST36032025', '2025-03-11', NULL, NULL, 9, 42, 'EST-2025-036', 10),
(37, '2025-03-11 07:23:41', '2025-03-11 07:23:41', 'EST37032025', '2025-03-11', NULL, NULL, 9, 42, 'EST-2025-037', 10),
(38, '2025-03-11 07:37:53', '2025-03-11 07:37:53', 'EST38032025', '2025-03-11', NULL, NULL, 9, 42, 'EST-2025-038', NULL),
(39, '2025-05-14 07:35:02', '2025-05-14 07:35:02', 'EST39052025', '2025-05-14', NULL, NULL, 1, 17, 'EST-2025-039', 30),
(40, '2025-05-14 07:35:11', '2025-05-14 07:35:11', 'EST40052025', '2025-05-14', NULL, NULL, 1, 14, 'EST-2025-040', 30),
(41, '2025-05-14 07:35:22', '2025-05-14 07:35:22', 'EST41052025', '2025-05-14', NULL, 'estimate_41.pdf', 1, 34, 'EST-2025-041', 30);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_marketing` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`price_types`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `daily_rate`, `created_at`, `updated_at`, `is_marketing`, `phone`, `email`, `address`, `city`, `image`, `price_types`) VALUES
(2, 'KAZAKOV AKBAR', 80.00, '2025-01-13 11:44:48', '2025-01-13 11:44:48', 0, '+998979192900', 'akbar@yahoo.com', 'BAM', 'Samarkand', '01JHFQP4TND6JJGP3VREAH9H95.jpg', 'null'),
(3, 'Baxramova Oyshirin', 120.00, '2025-01-21 09:26:39', '2025-02-01 04:56:31', 1, '+998901046201', 'Oyshirin@sss-tour.com', 'Sh.Rustaveli 45', 'Samarkand, Toshkent, Bukhoro, Xiva ', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(4, 'Tojiboyev Islom', 120.00, '2025-01-21 10:07:48', '2025-02-01 04:55:23', 1, '+998 93 999 36 08', 'islomtojiboyevodilovich@gmail.com', 'Samarqand shahri', 'Samarkand', '01JJ45A8K5QR5FTMF1B2MFB7AM.jpg', '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(5, 'Zikirov Islomjon', 100.00, '2025-01-21 10:14:28', '2025-02-01 04:54:48', 1, '+998 99 311 11 73', 'zikirov06@gmail.com', 'Samarqand shahri', 'Samarkand', '01JJS0Y1229K3NDB2Z6G5TRGFP.JPG', '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(6, 'Baxronova Zarina', 120.00, '2025-01-22 06:48:15', '2025-02-01 04:54:12', 1, '+998938300181', 'zarinabaxranova18@gmail.com', 'Samarqand shahri', 'Samarkand', '01JJ6C9KGER55A5PZRXTK1RPBJ.jpg', '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(7, 'Erkin Isroyilov', 120.00, '2025-01-22 06:52:27', '2025-02-01 04:52:41', 1, '+998 99 793 05 96', 'erkinisroyilov29@gmail.com', 'Samarqand shahri', 'Samarkand', '01JJ6CH9KW37WQCAB1MV8638QZ.jpg', '[{\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}, {\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}]'),
(8, 'Karimov Azizbek', 120.00, '2025-01-22 06:54:34', '2025-02-01 04:56:50', 1, '+998 93 747 00 05', 'Karimovazizbekk052@gmail.com', 'Khiva', 'Khiva', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(9, 'Allaberdieva Vazira', 120.00, '2025-01-22 06:59:08', '2025-02-01 04:58:04', 1, '+998 90 502 66 61', 'vazialla94@gmail.com', 'Samarqand shahri', 'Samarkand', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(10, 'Abduanov Ganisher', 80.00, '2025-01-30 06:45:23', '2025-02-19 11:14:41', 1, '+998 99 703 82 02', 'yo\'q', NULL, 'Samarkand ', NULL, '[{\"price\": \"50\", \"price_type_name\": \"per_daily\"}]'),
(11, 'Islom gid Tashkent', 120.00, '2025-01-31 06:44:22', '2025-02-05 06:18:48', 1, '+998 99 090 31 40', 'islomguide@.gmail.com', NULL, 'Tashkent', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(12, 'Shoxruh Domlo', 0.00, '2025-02-01 05:52:32', '2025-02-01 05:53:06', 1, '+998 33 388 88 66', 'shoxruhnorbotayev@gmail.com', 'Samarqand shahri', 'Samarqand', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(13, 'Gulrux Rahmatullayeva', 0.00, '2025-02-01 06:04:27', '2025-02-01 06:04:27', 1, '+998 91 545 04 50', 'gulruxrahmat@gmail.com', 'Samarqand shahri', 'Samarqand', NULL, '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"70\", \"price_type_name\": \"halfday\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}]'),
(14, 'Toyirova Musharraf', 0.00, '2025-02-05 06:16:54', '2025-02-05 06:16:54', 1, '+998 99 709 01 49', 'musharraf.hokimovna@mail.ru', 'Samarqand shahar, Gagarin ko\'chasi 89-86', 'Samarqand shahar', '01JKAC28WVBDHMNJ7XNEMS6P58.jpg', '[{\"price\": \"120\", \"price_type_name\": \"per_daily\"}, {\"price\": \"50\", \"price_type_name\": \"pickup_dropoff\"}, {\"price\": \"80\", \"price_type_name\": \"halfday\"}]'),
(15, 'Ruhshona', 0.00, '2025-06-24 05:42:29', '2025-09-26 03:52:38', 1, '901001827', 'ok@gmail.com', 'Samarkand', 'samarkand', NULL, '[{\"price\":\"70\",\"price_type_name\":\"per_daily\"}]'),
(16, 'Hakimova Shohsanam', 0.00, '2025-06-24 05:44:17', '2025-09-26 03:52:19', 1, '91 447 33 02', 'oaf@gmail.com', 'Buxoror', 'Buxoro', NULL, '[{\"price\":\"70\",\"price_type_name\":\"per_daily\"}]'),
(17, 'Nafosat', 0.00, '2025-09-26 03:52:02', '2025-09-26 03:52:02', 1, '90 358 39 63', 'ok@ok.com', 'khiva', 'khiva', NULL, '[{\"price_type_name\":\"per_daily\",\"price\":\"70\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `guide_spoken_language`
--

CREATE TABLE `guide_spoken_language` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `guide_id` bigint(20) UNSIGNED NOT NULL,
  `spoken_language_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_spoken_language`
--

INSERT INTO `guide_spoken_language` (`id`, `created_at`, `updated_at`, `guide_id`, `spoken_language_id`) VALUES
(2, '2025-01-13 11:44:48', '2025-01-13 11:44:48', 2, 6),
(3, '2025-01-13 11:44:48', '2025-01-13 11:44:48', 2, 2),
(4, '2025-01-21 09:26:39', '2025-01-21 09:26:39', 3, 5),
(5, '2025-01-21 09:26:39', '2025-01-21 09:26:39', 3, 3),
(6, '2025-01-21 10:07:48', '2025-01-21 10:07:48', 4, 5),
(7, '2025-01-21 10:14:28', '2025-01-21 10:14:28', 5, 5),
(8, '2025-01-22 06:48:15', '2025-01-22 06:48:15', 6, 5),
(9, '2025-01-22 06:52:27', '2025-01-22 06:52:27', 7, 5),
(10, '2025-01-22 06:54:34', '2025-01-22 06:54:34', 8, 5),
(11, '2025-01-22 06:59:08', '2025-01-22 06:59:08', 9, 5),
(12, '2025-01-30 06:45:23', '2025-01-30 06:45:23', 10, 1),
(13, '2025-01-31 06:44:22', '2025-01-31 06:44:22', 11, 5),
(14, '2025-02-01 05:52:32', '2025-02-01 05:52:32', 12, 5),
(15, '2025-02-01 06:04:27', '2025-02-01 06:04:27', 13, 5),
(16, '2025-02-05 06:16:54', '2025-02-05 06:16:54', 14, 5),
(17, '2025-06-24 05:42:29', '2025-06-24 05:42:29', 15, 8),
(18, '2025-06-24 05:42:29', '2025-06-24 05:42:29', 15, 9),
(19, '2025-06-24 05:44:17', '2025-06-24 05:44:17', 16, 8),
(20, '2025-09-26 03:52:03', '2025-09-26 03:52:03', 17, 8);

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` enum('bed_breakfast','3_star','4_star','5_star') NOT NULL,
  `type` enum('bed_breakfast','3_star','4_star','5_star') DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `address`, `created_at`, `updated_at`, `category`, `type`, `city_id`, `description`, `phone`, `email`, `website`, `images`, `company_id`) VALUES
(2, 'Shamsan', 'Yangi Qo\'yliq ko\'chasi 1B, 100080, Тоshkent, Toshkent', '2025-01-11 05:20:40', '2025-01-24 10:08:16', 'bed_breakfast', '4_star', 2, 'For chinese', '+998909078844', 'book@shamsan.uz', NULL, '[\"01JJBWH96SYQARV61SW30DAQ1A.jpg\"]', NULL),
(3, 'Bentley Hotel', '2A Mirzakalon Ismoiliy Street, Tashkent/Uzbekistan', '2025-01-11 06:00:36', '2025-01-11 06:00:36', 'bed_breakfast', '4_star', 2, 'Bentley Tashkent Hotel consists of 62 comfortable and well equipped rooms, an upscale restaurant, conference and meeting rooms, a swimming pool, gym, spa and wellness center.', '998 95 255 00 11', 'sales@bentleyhotel.uz', NULL, '[\"01JH9Z6DY97BYW19V8324KGZNX.jpg\"]', NULL),
(4, 'Avant Hotel', 'Askiya Street 12, Tashkent', '2025-01-11 06:16:23', '2025-01-11 06:16:23', 'bed_breakfast', '3_star', 2, 'Early check-in:\n00:00 - 06:59: 100% from the cost per night\n07:00 - 11:59: 50% from the cost per night\nLate check-out:\n14:00 - 17:59: 50% from the cost per night\n18:00 - 23:59: 100% from the cost per night', '998 55 517 50 00', ' avantterracehotel@gmail.com 24/7', NULL, '[\"01JHA03ASYF347KZJB1201MH87.jpg\"]', NULL),
(5, 'Holiday Inn Tashkent City', ' 3 Ukchi street Tashkent, 100017 Uzbekistan', '2025-01-13 07:13:24', '2025-01-24 10:21:14', 'bed_breakfast', '4_star', 2, NULL, '+99871 205 20 00', 'reservation@hitc.uz', NULL, '[\"01JJBX912EZQ3SGHWEAF60JX69.jpg\"]', NULL),
(6, 'Royal Sebzor Hotel', 'г. Ташкент, Ул. Тахтапуль, 41', '2025-01-13 09:40:48', '2025-09-26 03:33:45', 'bed_breakfast', '4_star', 2, 'до 18:00 бесплатная отмена бронирования', '+99899 188 71 10', 'royal.sebzor@gmail.com', NULL, '[]', 2),
(7, 'Europe Hotel Tashkent', '\"Shohjahon 58, 100100, Tashkent   4.2 км до центра города\"	', '2025-01-13 09:52:00', '2025-01-13 09:52:00', 'bed_breakfast', '3_star', 2, NULL, '+99897 330 88 88', 'book@europehotel.uz', NULL, '[\"01JHFH7K89W8AWGF6PBPY8C3GW.jpg\"]', NULL),
(8, 'Marwa Hotel Tashkent', 'Uzbekistan, Tashkent, Almazar district, 12 Lyangar street', '2025-01-13 11:03:38', '2025-01-24 10:17:08', 'bed_breakfast', '3_star', 2, NULL, '+998995207007', 'marwahoteltashkent@gmail.com', NULL, '[\"01JHFNARCDM76SJW1PB16Q7SNV.webp\"]', NULL),
(9, 'Al Anvar Hotel', 'г. Ташкент, ул. Юсуф Хос Ходжиба, д. 65', '2025-01-13 11:09:04', '2025-01-13 11:09:04', 'bed_breakfast', '4_star', 2, NULL, '+998995120660', 'sales@alanvarhotel.uz', NULL, '[\"01JHFNMP5YY379KXG6XESWTRAQ.webp\"]', NULL),
(10, 'Gabrielle INTERNATIONAL', '43 Shota Rustaveli, street, Tashkent, Uzbekistan', '2025-01-13 11:17:20', '2025-01-13 11:17:20', 'bed_breakfast', '3_star', 2, NULL, '+998 (71) 255-91-19', '@gabrielle.com', NULL, '[\"01JHFP3V2TYJASCQ5Y8Q29M9F9.jpg\"]', NULL),
(11, 'City  Palace', 'Amir Temur Street 15, 100000, Tashkent', '2025-01-13 11:50:50', '2025-01-13 11:50:50', 'bed_breakfast', '5_star', 2, NULL, '+99855 511-30-00', 'info@citypal', NULL, '[\"01JHFR15ZCWB6S1F8CXX0PTSVA.jpg\"]', NULL),
(12, 'LOTTE City Hotels Tashkent Palace', ' Узбекистан, 100029, г. Ташкент, улица Буюк Турон,', '2025-01-13 12:05:38', '2025-01-13 12:05:38', 'bed_breakfast', '4_star', 2, NULL, '998 90 937 80 00	', 'yo\'q', NULL, '[\"01JHFRW8Z8H46S666EC2QR1QPB.jpg\"]', NULL),
(13, 'Simma Hotel', 'Сергели 5А, улица Навруз 22, Узгариш КФЙ, 100088 Ташкент, Узбекистан', '2025-01-13 13:09:02', '2025-01-13 13:09:02', 'bed_breakfast', '3_star', 2, NULL, '+998 71 207 29 99 +998 99 404 44 44', 'info@simma.uz', NULL, '[\"01JHFWGC81DH4HM2JQQCRQDYBR.jpg\"]', NULL),
(14, 'Arda Plaza Toshkent', 'г. Ташкент, Яккасарайский район, Кичик Халка Йули, д ', '2025-01-13 13:13:07', '2025-01-24 10:29:42', 'bed_breakfast', '3_star', 2, NULL, '95 146 00 44', 'ardaplazahotel@gmail.com', NULL, '[\"01JJBXRGV7Y3Z8SKP284VC25XH.jpg\"]', NULL),
(15, 'Regal Stay', '\"Малая кольцевая  дорога  100022, Tashkent\"	', '2025-01-14 07:22:28', '2025-01-14 07:22:28', 'bed_breakfast', '4_star', 2, NULL, '998 90 000 00 90', 'info@regalstay.uz', NULL, '[\"01JHHV2GMM5F2AFTABAPKNK9FX.jpg\"]', NULL),
(16, 'Wyndham Tashkent', '\"Tashkent Amir Temur Str., C-4, No. 7/8  2,5 км от центра\"	', '2025-01-14 08:12:14', '2025-01-14 08:12:14', 'bed_breakfast', '4_star', 2, NULL, '78 120 37 00	', '@whyndam', NULL, '[\"01JHHXXM6P528KP0BYKT12YNZJ.jpg\"]', NULL),
(17, 'Grand Mir hotel', 'Tashkent, Yakkasaray district, Mirobad St., 2', '2025-01-14 08:16:43', '2025-01-14 08:16:43', 'bed_breakfast', '4_star', 2, NULL, '78 140 20 00', 'info@grandmirhotel.uz', NULL, '[\"01JHHY5TSRTCBF4GZZ2BQ8YKCP.webp\"]', NULL),
(18, 'Rakat Plaza', 'Muqimiy ko\'chasi 40, 100100	', '2025-01-14 10:04:52', '2025-01-14 10:04:52', 'bed_breakfast', '4_star', 2, NULL, '71 253 00 80', ' info@rakatplaza.uz', NULL, '[\"01JHJ4BW7779YDS8J971FA0HMQ.jpg\"]', NULL),
(19, 'Diamond Hotel Tashkent', 'Чиланзарский район, ул. Чупон ота 70А Ташкент Чиланзар 2 кв, 100115', '2025-01-14 10:14:05', '2025-01-14 10:14:05', 'bed_breakfast', '3_star', 2, NULL, '+998712770707', 'diamond.tashkent.hotel@mail.ru', NULL, '[\"01JHJ4WQY6MMA7ASQ94PQY4TXK.jpg\"]', NULL),
(20, 'The Heritage Tashkent Hotel', 'г. Ташкент, ул. Яккасарай, дом 42-44', '2025-01-14 10:29:27', '2025-01-14 10:29:27', 'bed_breakfast', '3_star', 2, NULL, '55 506 05 08', 'theheritagehoteltashkent@gmail.com', NULL, '[\"01JHJ5RWGG49GT288ZGYN6Y3Y5.jpg\"]', NULL),
(21, 'Praga Hotel	', 'Yakkasaray district, st. Akramkhodzhaeva, 21, Tashkent, Uzbekistan', '2025-01-14 10:40:03', '2025-01-14 10:40:03', 'bed_breakfast', '3_star', 2, NULL, '71 253 00 27', 'info@pragahotel.uz', NULL, '[\"01JHJ6C9SEHA5CEXD1M6MNAD36.jpg\"]', NULL),
(22, 't-city presidential hotel', 'Узбекистан, г.Ташкент, улица Ислама Каримова, Башня Узпромстройбанка', '2025-01-14 11:06:55', '2025-01-14 11:06:55', 'bed_breakfast', '5_star', 2, NULL, '78 140 38 38', 'info@t-citypresidentialhotel.uz', NULL, '[\"01JHJ7XFJKMD1ZB3132MWQQX5C.jpg\"]', NULL),
(23, 'Alliance Hotel Tashkent', ' 100100 Tashkent, Uzbekistan Vosit Vohidov, 110', '2025-01-14 11:14:28', '2025-01-14 11:14:28', 'bed_breakfast', '3_star', 2, NULL, '+998 71 255 17 02', 'alliancehoteltashkent@gmail.com', NULL, '[\"01JHJ8B9WAEG08EX8X2X7MKJMT.jpg\"]', NULL),
(24, 'Oazis Asaka Hotel', 'Яшнабадский район, ул.Фаргона йули, дом 23', '2025-01-14 11:19:38', '2025-01-24 10:34:57', 'bed_breakfast', '3_star', 2, NULL, '77 183 00 07', 'yoq', NULL, '[\"01JJBY24R6PPK14JZ1RPM62S5A.webp\"]', NULL),
(25, 'MaxWell Hotel&SPA', 'г. Ташкент, Яшнабадский район, ул. Авиасозлар 3/3Б', '2025-01-14 11:25:27', '2025-01-24 10:36:58', 'bed_breakfast', '3_star', 2, NULL, '78 888 00 00', 'maxwellhotel@mail.ru', NULL, '[\"01JJBY5TQ84FHX33ASSSFNQ2FN.jpg\"]', NULL),
(26, 'Hotel 1946 Ташкент', 'home, Furkat Street 4, 100021, Tashkent', '2025-01-14 11:28:38', '2025-01-24 10:40:38', 'bed_breakfast', '3_star', 2, NULL, '88 871 00 00', 'hotel1946@inbox.ru', NULL, '[\"01JJBYCHCC0XH00XRAJ43FNKAM.jpg\"]', NULL),
(27, 'Marhabo boutique hotel', 'QCFF+V55 Хужа Порсо, Bukhara', '2025-01-14 11:33:35', '2025-01-14 11:33:35', 'bed_breakfast', 'bed_breakfast', 4, NULL, '88 301 00 70', 'yoq', NULL, '[\"01JHJ9EA21RA7J27F3J8PZ37GH.webp\"]', NULL),
(28, 'Mercure Meridian', ' ул. К. Муртазоева 1А, 200119, Bukhara, Bukhara Region', '2025-01-14 11:38:30', '2025-01-24 10:52:06', 'bed_breakfast', '3_star', 4, NULL, '91 312 10 00', 'yoq', NULL, '[\"01JJBZ1H0RVC8DTZST1P17CPVQ.jpg\"]', NULL),
(29, 'Kukaldosh boutique', 'Bukhara, M. Ambar', '2025-01-14 11:41:33', '2025-01-14 11:41:33', 'bed_breakfast', '3_star', 4, NULL, '65 224 53 99', 'kukaldosh@list.ru', NULL, '[\"01JHJ9WX0JEY8M436DYD3AFC9Z.jpeg\"]', NULL),
(30, 'Bobosh boutique', 'Xuja Rushnoi street, 12/1, 200118, Bukhara', '2025-01-14 11:44:30', '2025-01-14 11:44:30', 'bed_breakfast', '3_star', 4, NULL, '90 414 28 88', 'yoq', NULL, '[\"01JHJA2A10M08Z7ZW8G8WSF1QK.jpg\"]', NULL),
(31, 'Sahid Zarafshon', 'Muhammad Iqbal st 7, 200100, Bukhara, Bukhara Region', '2025-01-14 11:52:26', '2025-01-14 11:52:26', 'bed_breakfast', '4_star', 4, NULL, '65 505 50 00', 'info@sahidzarafshon.com', NULL, '[]', NULL),
(32, 'Asia Bukhara', 'Mehtar Ambar St 55, 200118, Bukhara, Bukhara Region', '2025-01-14 11:56:27', '2025-01-14 11:56:27', 'bed_breakfast', '3_star', 4, NULL, '65 224 64 31', 'yo\'q', NULL, '[\"01JHJAR67BGH4XKSHNZVA99V35.jpg\"]', NULL),
(33, 'Ark hotel', 'Абу Хавз Кабир, Bukhara', '2025-01-14 12:06:13', '2025-01-14 12:06:13', 'bed_breakfast', '4_star', 4, NULL, '65 505 77 77', 'yoq', NULL, '[]', NULL),
(34, 'Minzifa hotel', 'Eshoni Pir St 63, 200118, Bukhara, Bukhara Region', '2025-01-14 12:20:16', '2025-01-14 12:20:16', 'bed_breakfast', '4_star', 4, NULL, '93 477 08 00', ' Eshoni Pir 63 Bukhara 200118, Uzbekistan', NULL, '[]', NULL),
(35, 'Shahriston HOTEL', 'yo\'q', '2025-01-14 12:29:59', '2025-01-14 12:29:59', 'bed_breakfast', '3_star', 4, NULL, '93 454 77 67', 'something@gmail.com', NULL, '[]', NULL),
(36, 'Malika hotel', '25 Gavkushon Street 200118 Bukhara, Buxoro Uzbekistan (UZ)', '2025-01-14 12:34:33', '2025-01-14 12:34:33', 'bed_breakfast', '4_star', 4, NULL, '65 224 62 56', 'malika-bukhara@mail.ru', NULL, '[]', NULL),
(37, 'Farovon khiva ', 'Xorazm viloyati, Xiva shahri, Kiyot mahallasi, Buyuk yol ko\'chasi, 1-A uy', '2025-01-14 12:37:59', '2025-01-14 12:37:59', 'bed_breakfast', '4_star', 5, NULL, '+998622277878', 'reservation@farovonkhiva.uz', NULL, '[]', NULL),
(38, 'Arkanchi hotel', '10 Pakhlavon Makhmoud str., Khiva, Uzbekistan', '2025-01-14 12:40:29', '2025-01-14 12:40:29', 'bed_breakfast', '4_star', 5, NULL, '55 602 32 22', 'info@hotel-arkanchi.uz', NULL, '[]', NULL),
(39, 'Khorezm palace', 'Al-Beruny Street 2, 220100, Urgench, Xorazm Region', '2025-01-14 12:42:57', '2025-01-14 12:42:57', 'bed_breakfast', '4_star', 5, NULL, '62 224 99 99', 'yo\'q', NULL, '[]', NULL),
(40, 'Grand Vizir', 'Unnamed Road, Khiva, Xorazm Region', '2025-01-14 12:47:48', '2025-01-14 12:47:48', 'bed_breakfast', '4_star', 5, NULL, '77 044 64 64', 'Grandvizirhotel@gmail.com', NULL, '[]', NULL),
(41, 'Annex Hotel', 'Tashpulatov street 121, 220900, Xiva, Xorazm Viloyati', '2025-01-15 04:42:54', '2025-01-15 04:42:54', 'bed_breakfast', 'bed_breakfast', 5, NULL, '91 427 09 99', 'yo\'q', NULL, '[\"01JHM4B1HPZ165Q5PGXQA3G33N.jpg\"]', NULL),
(42, 'Meros boutique', 'Улица Гулобод 5 140129 Самарканд, Узбекистан', '2025-01-15 04:47:25', '2025-01-15 04:47:25', 'bed_breakfast', 'bed_breakfast', 5, NULL, '66 239 99 11', 'meros.hotel@gmail.com', NULL, '[\"01JHM4KA157SVPSEZG5Z9SAYCF.jpeg\"]', NULL),
(43, 'Erkin Palace', '  K. Yakubov str, Khiva Khorezm region, Uzbekistan', '2025-01-15 04:51:13', '2025-01-15 04:51:13', 'bed_breakfast', '3_star', 5, NULL, '+998 (62) 377 66 62', 'contact@erkinpalace.uz', NULL, '[\"01JHM4T8JG8A9H37RP8ZW76KZG.jpg\"]', NULL),
(44, 'Khiva Residence', 'Янги турмуш ул Нажмиддин Кубро, 220900, Khiva', '2025-01-15 04:54:15', '2025-01-15 04:54:15', 'bed_breakfast', '3_star', 5, NULL, '78 113 64 67', 'hotel@khivaresidence.uz', NULL, '[\"01JHM4ZTYSHWWA5H3BAAXFQN2V.jpg\"]', NULL),
(45, 'khans palace hotel ', 'P.Maxmud 30, 220900, Khiva, Xorazm Region', '2025-01-15 04:57:32', '2025-01-15 04:57:32', 'bed_breakfast', '3_star', 5, NULL, '99 197 89 09', 'yo\'q', NULL, '[\"01JHM55VDN3NHBC682VD0CXAZ8.jpg\"]', NULL),
(46, 'Polvon Qori boutique', 'кори дом24, улица Палван, 220900, Xiva, Xorazm Viloyati', '2025-01-15 05:01:31', '2025-01-15 05:01:31', 'bed_breakfast', '3_star', 5, NULL, ' 91 998 89 99', 'hotelpolvonqori@gmail.com', NULL, '[\"01JHM5D4RPGKG5MGH64EPD44HB.jpg\"]', NULL),
(47, 'Muso To\'ra', '99H6+P3P Ichan Qala mahalla, Bo\'yoqchilar ko\'chasi, 220900, Khiva, Xorazm Region', '2025-01-15 05:04:50', '2025-01-15 05:04:50', 'bed_breakfast', '3_star', 5, NULL, ' 99 526 11 21', 'yo\'q', NULL, '[\"01JHM5K6XBRXZ7QCB0RK7909V2.jpg\"]', NULL),
(48, 'Turon lux hotel', '12 дом, Гагарин кўчаси проезд 2 12 улица Гагарина, 2 проезд, 140100, Samarkand', '2025-01-16 09:56:18', '2025-01-16 09:56:18', 'bed_breakfast', '3_star', 3, NULL, '91 701 09 90', 'yo\'q', NULL, '[\"01JHQ8NKV0W0RPFNJ8AN16XV6N.jpg\"]', NULL),
(49, 'SULTAN BOUTIQUE Hotel', '143-uy, Boburshox ko\'chasi, Madaniyat MFY, Andijon shahar', '2025-01-16 10:06:59', '2025-01-16 10:06:59', 'bed_breakfast', '4_star', 3, NULL, '66 239 11 88', 'info@sultanhotel.uz', NULL, '[\"01JHQ995RN53GGKJJK1APRNNKA.jpg\"]', NULL),
(50, 'City hotel ', 'University Boulevard 19 A, 140129, Samarkand, Samarqand Region', '2025-01-16 10:09:45', '2025-01-16 10:09:45', 'bed_breakfast', '3_star', 3, NULL, '66 239 82 82', 'yo\'q', NULL, '[\"01JHQ9E7HBKWF21M061MK7F6TT.jpg\"]', NULL),
(51, 'Zilol Baxt', 'Самарканд, ул, Гулобод 5', '2025-01-16 10:12:49', '2025-01-16 10:12:49', 'bed_breakfast', '3_star', 3, NULL, '66 239 85 14', 'info@hotelzilolbaxt.uz', NULL, '[\"01JHQ9KVCJWNTF6YQJ0KSG5MZQ.jpg\"]', NULL),
(52, 'Hilton garden Inn Samarkand', '2 Dakhbed Yuli Street, Samarkand, 140130, Uzbekistan, Opens new tab', '2025-01-16 10:16:17', '2025-01-16 10:16:17', 'bed_breakfast', '4_star', 3, NULL, '55 704 07 07', 'yo\'q', NULL, '[]', NULL),
(53, 'Movinpick', '53 Shokhrukh Str Samarkand Samarkand Vilayat, 140100, Samarqand', '2025-01-16 10:22:28', '2025-01-16 10:22:28', 'bed_breakfast', '5_star', 3, NULL, '55 703 08 08', 'HC076-SL@accor.com', NULL, '[\"01JHQA5HBZD2S2GXQEKC2DQC87.jpg\"]', NULL),
(54, 'Orient Star Khiva ', 'PAKHLAVAN MAHMUD STR. 1, Khiva, 220900, 220900', '2025-01-16 10:28:16', '2025-01-16 10:28:16', 'bed_breakfast', '3_star', 5, NULL, '+998975276859', 'orientstarkhiva@gmail.com', NULL, '[\"01JHQAG558RKGP7QM1D58RJE24.jpg\"]', NULL),
(55, 'Hotel Zarafshon Boutique', 'Pakhlavon Mahmud street 60, Khiva, Uzbekistan (inside of Itchan Kala) Number of rooms: 26', '2025-01-16 10:32:47', '2025-01-16 10:32:47', 'bed_breakfast', '4_star', 5, NULL, '71 200 02 99', 'info@centralasia-travel.com', NULL, '[]', NULL),
(56, 'Hotel Azia Fergana', 'Ulitsa Dzhomi 26, 150100, Fergana, Fergana Region', '2025-01-18 04:48:01', '2025-01-18 04:48:01', 'bed_breakfast', '4_star', 9, NULL, '73 244 13 26', 'yo\'q', NULL, '[\"01JHVVTJVZPG61PQ51EDNXK3VQ.jpg\"]', NULL),
(57, 'Chinor hotel', 'Burxoniddin, marg\'iloniy 39, 150100, Fergana Region', '2025-01-18 04:52:23', '2025-01-18 04:52:23', 'bed_breakfast', 'bed_breakfast', 9, NULL, '94 394 77 67', 'yo\'q', NULL, '[]', NULL),
(58, 'Farg\'ona Do\'stlik hotel', '150100, O\'zbekiston, Farg\'ona viloyati, Farg\'ona, Sohibkiron Temur ko\'chasi, 30', '2025-01-18 04:55:33', '2025-01-18 04:55:33', 'bed_breakfast', 'bed_breakfast', 9, NULL, '+998 (95) 401-97-97', 'yo\'q', NULL, '[]', NULL),
(60, 'GSR hotel', '150100, Uzbekistan, Fergana region, Ferghana, Street Marifat, 43A', '2025-01-18 05:04:07', '2025-01-18 05:04:07', 'bed_breakfast', '3_star', 9, NULL, '+998 (99) 990-70-54', 'yo\'q', NULL, '[]', NULL),
(61, 'Major hotel', 'Фергана, ул. Аль-Фергани, 104', '2025-01-18 05:15:01', '2025-01-18 05:15:01', 'bed_breakfast', '3_star', 9, NULL, '+998 99 022 30 22', 'yo\'q', NULL, '[]', NULL),
(62, 'East hotel', 'CQJC+534, Kirgili, Fergana Region', '2025-01-18 05:23:09', '2025-01-18 05:23:09', 'bed_breakfast', 'bed_breakfast', 9, NULL, '73 242 60 65', 'yo\'q', NULL, '[]', NULL),
(63, 'Tantana hotel', 'дом 122, Улица Алъ, 150100, Fergana, Fergana Region', '2025-01-18 05:27:09', '2025-01-18 05:27:09', 'bed_breakfast', '3_star', 9, NULL, '73 244 78 78', 'yo\'q', NULL, '[]', NULL),
(64, 'Grand Fergana hotel', 'ул. Янги Турон дом №15, Farg\'ona, Farg\'ona Viloyati', '2025-01-18 05:30:09', '2025-01-18 05:30:09', 'bed_breakfast', '3_star', 9, NULL, '78 229 75 55', 'yo\'q', NULL, '[]', NULL),
(65, 'Grand Plaza hotel Reikardz', 'University Boulevard 7, 140100, Samarkand, Samarqand Region', '2025-01-22 10:47:41', '2025-02-05 06:35:38', 'bed_breakfast', '4_star', 3, NULL, '94 540 05 10', 'uzsales@reikartz.com', NULL, '[\"01JJ6T00AYAVTQA51AFARMQYG6.jpg\"]', NULL),
(66, 'Hilton Samarkand Regency', 'M365+9PV, Konigil Massif, 140319, Samarqand, Samarqand viloyati', '2025-01-22 11:30:32', '2025-01-22 11:30:32', 'bed_breakfast', '4_star', 3, NULL, '55 705 70 10', 'yo\'q', NULL, '[]', NULL),
(67, 'Gur Emir Palace Boutique Hotel', 'улица Шохрух кучаси 126, 140101, Samarkand, Samarqand Region', '2025-01-22 11:47:53', '2025-01-22 11:47:53', 'bed_breakfast', '3_star', 3, NULL, '91 030 80 88', 'guremirhotel@gmail.com', NULL, '[]', NULL),
(68, 'Garden Inn Samarkand Afrosiyob', 'Rowing Channel, 140319, Samarqand, Samarqand viloyati', '2025-01-22 11:56:01', '2025-01-22 11:56:01', 'bed_breakfast', '4_star', 3, NULL, '55 705 70 51', 'yo\'q', NULL, '[]', NULL),
(69, 'Savitsky Plaza', 'Samarkand, Samarqand Region', '2025-01-22 12:03:41', '2025-01-22 12:03:41', 'bed_breakfast', '4_star', 3, NULL, '55 705 70 20', 'yo\'q', NULL, '[]', NULL),
(70, 'Asia Palace Shahrisabz', 'Ипак йули 54А, 181300, Shahrisabz', '2025-01-23 11:52:35', '2025-01-23 11:52:35', 'bed_breakfast', 'bed_breakfast', 6, NULL, '91 958 07 36', 'yo\'q', NULL, '[]', NULL),
(71, 'Sahar boutique hotel', 'B. Naqshband 144, 200100, Bukhara', '2025-01-24 04:49:22', '2025-01-24 04:49:22', 'bed_breakfast', 'bed_breakfast', 4, NULL, ' 94 125 11 22', 'yo\'q', NULL, '[]', NULL),
(72, 'Shoxjahon hotel', 'Suzangaron St 110, 140139, Samarkand, Samarqand Region', '2025-01-24 04:54:27', '2025-01-24 04:54:27', 'bed_breakfast', '3_star', 3, NULL, '55 705 00 01', 'yo\'q', NULL, '[]', NULL),
(73, 'Karvon hotel', '17, Pendjikent st., 140100, Samarqand', '2025-01-24 05:08:49', '2025-01-24 05:08:49', 'bed_breakfast', '3_star', 3, NULL, '662350101', 'yo\'q', NULL, '[]', NULL),
(74, 'Human Hotel', 'Ivliev St 52, 100070, Tashkent', '2025-01-24 05:12:45', '2025-01-24 05:12:45', 'bed_breakfast', '3_star', 2, NULL, '99 523 00 90', 'yo\'q', NULL, '[]', NULL),
(75, 'Jahon Palace ', 'Gagarina Street 107, Samarkand 140100', '2025-01-24 05:34:36', '2025-01-24 05:34:36', 'bed_breakfast', '3_star', 3, NULL, '66 234 07 08', 'info@jahon-palace.com', NULL, '[]', NULL),
(76, 'Khiva Silk Road hotel', 'Koy Darvaza, Khiva, Xorazm Region', '2025-01-24 05:38:09', '2025-01-24 05:38:09', 'bed_breakfast', 'bed_breakfast', 5, NULL, '91 277 77 87', 'yo\'q', NULL, '[]', NULL),
(77, 'Hotel Maroqanda', 'Bukhara Str 141, 140100, Samarqand, Samarqand viloyati', '2025-01-24 05:43:23', '2025-01-24 05:43:23', 'bed_breakfast', 'bed_breakfast', 3, NULL, '66 235 11 29', 'yo\'q', NULL, '[]', NULL),
(78, 'Azimut hotel', 'Shota Rustaveli St 1, 111000, Тоshkent, Toshkent', '2025-01-24 05:50:08', '2025-01-24 05:50:08', 'bed_breakfast', '3_star', 2, NULL, '78 141 99 99', 'yo\'q', NULL, '[]', NULL),
(79, 'Maqom Plaza hotel', '3R3C+G2G, Shahrisabz, Qashqadaryo Region', '2025-01-24 07:07:40', '2025-01-24 07:07:40', 'bed_breakfast', '3_star', 6, NULL, '00', 'yo\'q', NULL, '[]', NULL),
(80, 'Xorazm Palace', 'Al-Beruny Street 2, 220100, Urgench, Xorazm Region', '2025-01-24 09:59:50', '2025-01-24 09:59:50', 'bed_breakfast', '4_star', 17, NULL, '62 224 99 99', 'yo\'q', NULL, '[]', NULL),
(81, 'Yurt Sputnik Navoiy', 'Yagigazgan', '2025-01-29 13:07:20', '2025-01-29 13:07:20', 'bed_breakfast', 'bed_breakfast', 19, 'Yurta ', '+998885480080', 'info@sss-tour.com', NULL, '[]', NULL),
(82, 'Lia! by Minyoun Stars of Ulugbek', 'Samarkand, Samarqand Region', '2025-01-30 04:32:37', '2025-01-30 04:32:37', 'bed_breakfast', '4_star', 3, NULL, '55 705 70 30', 'yo\'q', NULL, '[]', NULL),
(83, 'Silk Road by Minyoun', 'Samarkand, Samarqand Region', '2025-01-30 04:34:50', '2025-01-30 04:34:50', 'bed_breakfast', '5_star', 3, NULL, '55 705 70 40', 'yo\'q', NULL, '[]', NULL),
(84, 'As-salam Hotel', 'Bukhara, Nakshbandi st 116, Bukhara, Bukhara Region', '2025-01-30 05:14:18', '2025-01-30 05:14:18', 'bed_breakfast', '3_star', 4, NULL, '90 710 71 17', 'yo\'q', NULL, '[]', NULL),
(85, 'Harris hotel', 'Rakatboshi Street 2A, 100100, Tashkent', '2025-01-30 05:18:36', '2025-01-30 05:20:01', 'bed_breakfast', '3_star', 2, NULL, '77 141 99 99', 'yo\'q', NULL, '[]', NULL),
(86, 'Abdulla aka uy mehmonxonasi', NULL, '2025-01-30 11:03:17', '2025-01-30 11:03:17', 'bed_breakfast', 'bed_breakfast', 27, NULL, '+998907183060', 'yo\'q', NULL, '[]', NULL),
(87, 'Nurfayz', 'qalqonota navoiy', '2025-01-30 11:07:41', '2025-01-30 11:07:41', 'bed_breakfast', 'bed_breakfast', 28, NULL, '+998934321155', 'yo\'q', NULL, '[]', NULL),
(88, 'Saida opa mehmon uy', 'navoiy nurota', '2025-01-30 11:10:09', '2025-01-30 11:10:09', 'bed_breakfast', 'bed_breakfast', 12, NULL, '+998934322685', 'yo\'q', NULL, '[]', NULL),
(89, 'EuraAsia hotel Khiva', 'P. Mahmud 35, 220900, Khiva, Xorazm Region', '2025-01-30 11:43:28', '2025-01-30 11:43:28', 'bed_breakfast', '3_star', 5, 'Xiva norm hotel', '+998 99 500 46 61', ' hoteleuroasiakhiva@gmail.com', NULL, '[]', NULL),
(90, 'Metan Obid ', 'Samarkand, Samarqand Region', '2025-01-30 11:56:22', '2025-01-30 11:56:22', 'bed_breakfast', 'bed_breakfast', 3, NULL, '+998995076631', 'yo\'q', NULL, '[]', NULL),
(91, 'SIAB HOTEL', 'Абдулла 5, Vohid Abdullo Street, 140100, Samarkand, Samarqand Region', '2025-02-19 08:07:24', '2025-02-19 08:07:24', 'bed_breakfast', '3_star', 3, NULL, '91 488 33 33', 'yo\'q', NULL, '[]', NULL),
(92, 'Emirkhan Hotel', '46А Dagbitskaya str, Samarqand Region', '2025-02-20 04:54:13', '2025-02-20 04:54:13', 'bed_breakfast', '4_star', 3, NULL, '95 410 00 24', 'yo\'q', NULL, '[]', NULL),
(93, 'Boulevard Palace Hotel', 'Orzu, Orzi Makhmudov Str. 22, 140100, Samarqand, Samarqand viloyati', '2025-02-20 04:58:21', '2025-02-20 04:58:21', 'bed_breakfast', '3_star', 3, NULL, '66 233 44 53', 'yo\'q', NULL, '[]', NULL),
(94, 'RADISSON BLU HOTEL TASHKENT', 'Amir Temur Avenue 88, 100084, Tashkent', '2025-02-20 05:01:45', '2025-02-20 05:01:45', 'bed_breakfast', '4_star', 2, NULL, '78 120 49 00', 'yo\'q', NULL, '[]', NULL),
(95, 'Crowne Plaza (Yanis) hotel', ' 17 Zulfiya Khonum Str Tashkent, 100128 Uzbekistan', '2025-03-03 12:40:02', '2025-03-03 12:40:02', 'bed_breakfast', '4_star', 2, NULL, ' 998-55-5115555', ' info@cptashkent.com', NULL, '[]', NULL),
(96, 'Isakhoja Hotel', 'Ulitsa Rakhmanova 70, 220900', '2025-06-08 12:50:01', '2025-06-08 12:50:01', 'bed_breakfast', '3_star', 5, 'Comfortable Accommodations: Isakhoja Hotel in Khiva offers family rooms with air-conditioning, private bathrooms, and city views. Each room includes a work desk, TV, and free WiFi.\n\nDining Experience: Guests can enjoy Asian cuisine at the modern restaurant, featuring halal, vegetarian, gluten-free, and dairy-free options. Breakfast includes local specialities, warm dishes, fresh pastries, and more.\n\nLeisure Facilities: The hotel features a sun terrace, outdoor seating area, and free on-site parking. Additional amenities include a minimarket, hairdre', '91 430 42 46', 'ok@ok.com', NULL, '[\"01JX7SEFTDDP6363E1359XP0T3.jpg\", \"01JX7SEFTQEHCA6D102HGF23V8.jpg\", \"01JX7SEFTZV7B2TAAVGX0545DA.jpg\", \"01JX7SEFV3C2HQYQ19YC1027C8.jpg\", \"01JX7SEFV66EBN3XHS2ZZHDQ06.jpg\", \"01JX7SEFV958F40TGMC367AA1V.jpg\"]', NULL),
(97, 'Volidam Hotel', 'Namozgoh', '2025-06-09 17:56:04', '2025-06-09 17:56:04', 'bed_breakfast', '3_star', 3, 'Comfortable Accommodations: Volidam Hotel in Samarqand offers family rooms with private bathrooms, air-conditioning, and free WiFi. Each room includes a balcony with city views, ensuring a pleasant stay.\n\nExceptional Facilities: Guests can enjoy free bicycles, a terrace, and a lounge. Additional amenities include a coffee shop, child-friendly buffet, and free on-site private parking.\n\nDining Options: A buffet breakfast is available with local specialities, warm dishes, fresh pastries, and more. Vegetarian and halal options cater to diverse dietary n', '88 636 36 36', 'hotelvolidam@gmail.com', NULL, '[\"01JXAXBK6AC5SJPAZD2MYNJ4F3.jpg\", \"01JXAXBK6D987Q60MRD4P04361.jpg\", \"01JXAXBK6E9HKE07YKDWC7BRMX.jpg\", \"01JXAXBK6FHZBJKZZF5FSWVTAM.jpg\"]', NULL),
(98, 'Ayvan plaza Buxoror', 'Бухара, ул. Чобаколи, 184', '2025-06-24 05:37:54', '2025-09-26 03:35:22', 'bed_breakfast', '3_star', 4, 'Buxoror hotel', '+998 65 308 06 66', 'ayvanhotel@gmail.com', NULL, '[]', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

CREATE TABLE `hotel_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_day_hotel_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`id`, `tour_day_hotel_id`, `room_id`, `quantity`, `created_at`, `updated_at`) VALUES
(7, 6, 2, 5, '2025-01-30 05:15:00', '2025-01-30 05:15:00'),
(8, 7, 100, 5, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(9, 8, 2, 5, '2025-01-30 05:15:01', '2025-01-30 05:15:01'),
(11, 10, 2, 8, '2025-01-30 05:53:27', '2025-01-30 05:53:27'),
(12, 11, 2, 8, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(13, 12, 155, 8, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(14, 13, 155, 8, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(15, 14, 2, 8, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(16, 15, 2, 0, '2025-01-30 05:53:28', '2025-01-30 05:53:28'),
(17, 16, 2, 7, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(18, 16, 3, 1, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(19, 17, 155, 7, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(20, 17, 154, 1, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(21, 18, 155, 7, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(22, 18, 154, 1, '2025-01-30 07:25:26', '2025-01-30 07:25:26'),
(23, 19, 58, 7, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(24, 19, 57, 1, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(25, 20, 2, 7, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(26, 20, 3, 1, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(27, 21, 2, 0, '2025-01-30 07:25:27', '2025-01-30 07:25:27'),
(28, 22, 2, 1, '2025-01-30 07:45:40', '2025-01-30 07:45:40'),
(29, 24, 58, 1, '2025-01-30 07:45:41', '2025-01-30 07:45:41'),
(30, 25, 2, 8, '2025-01-30 10:55:10', '2025-01-30 10:55:10'),
(32, 27, 155, 8, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(33, 28, 155, 8, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(34, 29, 58, 8, '2025-01-30 10:55:11', '2025-01-30 10:55:11'),
(36, 31, 104, 8, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(37, 32, 2, 8, '2025-01-30 10:55:12', '2025-01-30 10:55:12'),
(42, 37, 58, 1, '2025-01-30 11:17:36', '2025-01-30 11:17:36'),
(43, 38, 162, 1, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(44, 39, 165, 1, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(45, 40, 167, 1, '2025-01-30 11:17:37', '2025-01-30 11:17:37'),
(51, 46, 151, 1, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(52, 47, 151, 1, '2025-01-30 12:08:53', '2025-01-30 12:08:53'),
(53, 48, 171, 1, '2025-01-30 12:08:54', '2025-01-30 12:08:54'),
(54, 49, 94, 1, '2025-01-30 12:08:54', '2025-01-30 12:08:54'),
(55, 50, 92, 1, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(56, 51, 92, 1, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(57, 52, 2, 1, '2025-01-30 12:31:45', '2025-01-30 12:31:45'),
(58, 53, NULL, 1, '2025-01-30 12:33:55', '2025-01-30 12:33:55'),
(59, 54, 2, 10, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(60, 55, 58, 10, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(61, 56, 155, 10, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(62, 57, 155, 10, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(63, 58, 2, 10, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(64, 59, NULL, 0, '2025-01-31 05:26:38', '2025-01-31 05:26:38'),
(65, 60, 28, 1, '2025-01-31 05:35:46', '2025-01-31 05:35:46'),
(66, 61, NULL, 1, '2025-01-31 05:35:46', '2025-01-31 05:35:46'),
(67, 62, 2, 8, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(68, 63, 155, 8, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(69, 64, 155, 8, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(70, 65, 58, 8, '2025-01-31 06:04:27', '2025-01-31 06:04:27'),
(71, 66, NULL, 1, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(72, 67, 168, 8, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(73, 68, 2, 8, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(74, 69, NULL, 1, '2025-01-31 06:04:28', '2025-01-31 06:04:28'),
(75, 70, 155, 7, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(76, 70, 154, 1, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(77, 71, 58, 7, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(78, 71, 57, 1, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(79, 72, 155, 7, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(80, 72, 154, 1, '2025-01-31 06:34:03', '2025-01-31 06:34:03'),
(81, 73, NULL, 1, '2025-01-31 06:34:04', '2025-01-31 06:34:04'),
(82, 74, 2, 9, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(83, 75, 155, 9, '2025-01-31 07:29:40', '2025-01-31 07:29:40'),
(84, 76, 155, 9, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(85, 77, 58, 9, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(86, 78, 168, 9, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(87, 79, 2, 9, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(88, 80, NULL, 1, '2025-01-31 07:29:41', '2025-01-31 07:29:41'),
(89, 81, 168, 8, '2025-01-31 11:23:40', '2025-01-31 11:23:40'),
(92, 84, NULL, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(93, 85, NULL, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(94, 86, 58, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(95, 87, 58, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(96, 88, 58, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(97, 89, NULL, 1, '2025-01-31 11:46:18', '2025-01-31 11:46:18'),
(103, 94, 168, 8, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(104, 95, 58, 8, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(105, 96, 58, 8, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(106, 97, 155, 8, '2025-02-01 04:49:22', '2025-02-01 04:49:22'),
(107, 98, 155, 8, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(108, 99, 2, 8, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(109, 100, NULL, 1, '2025-02-01 04:49:23', '2025-02-01 04:49:23'),
(110, 101, 2, 8, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(111, 102, 168, 8, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(112, 103, 58, 8, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(113, 104, 155, 8, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(114, 105, 155, 8, '2025-02-01 07:47:28', '2025-02-01 07:47:28'),
(115, 106, 2, 8, '2025-02-01 07:47:29', '2025-02-01 07:47:29'),
(116, 108, NULL, 1, '2025-02-04 12:43:11', '2025-02-04 12:43:11'),
(117, 109, 94, 2, '2025-02-11 10:06:27', '2025-02-11 10:06:27'),
(118, 110, 94, 2, '2025-02-11 10:10:50', '2025-02-11 10:10:50'),
(119, 111, 52, 2, '2025-02-11 10:48:32', '2025-02-11 10:48:32'),
(120, 112, 52, 2, '2025-02-11 10:57:21', '2025-02-11 10:57:21'),
(121, 113, 2, 2, '2025-02-11 10:57:22', '2025-02-11 10:57:22'),
(122, 114, NULL, 1, '2025-02-11 10:57:22', '2025-02-11 10:57:22'),
(123, 115, 2, 9, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(124, 116, 155, 9, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(125, 117, 58, 9, '2025-02-19 07:38:27', '2025-02-19 07:38:27'),
(126, 118, 104, 9, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(127, 119, 2, 9, '2025-02-19 07:38:28', '2025-02-19 07:38:28'),
(128, 120, NULL, 1, '2025-02-19 11:17:00', '2025-02-19 11:17:00'),
(129, 121, 2, 12, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(130, 121, 3, 1, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(131, 122, 173, 12, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(132, 122, 172, 1, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(133, 123, 173, 12, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(134, 123, 172, 1, '2025-02-20 10:45:53', '2025-02-20 10:45:53'),
(135, 124, 58, 12, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(136, 124, 57, 1, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(137, 125, 2, 12, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(138, 125, 3, 1, '2025-02-20 10:45:54', '2025-02-20 10:45:54'),
(139, 126, 7, 1, '2025-02-21 13:35:30', '2025-02-21 13:35:30'),
(140, 126, 7, 1, '2025-02-21 13:35:30', '2025-02-21 13:35:30'),
(141, 126, 6, 1, '2025-02-21 13:35:30', '2025-02-21 13:35:30'),
(142, 127, NULL, 1, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(143, 128, NULL, 1, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(144, 129, NULL, 1, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(145, 130, NULL, 1, '2025-03-11 07:22:54', '2025-03-11 07:22:54'),
(146, 131, NULL, 1, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(147, 132, NULL, 1, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(148, 133, NULL, 1, '2025-03-11 07:22:55', '2025-03-11 07:22:55'),
(155, 136, 7, 5, '2025-06-26 06:53:02', '2025-06-26 06:53:02'),
(156, 137, 92, 5, '2025-06-26 06:54:51', '2025-06-26 06:54:51'),
(157, 138, NULL, 1, '2025-08-22 03:35:31', '2025-08-22 03:35:31'),
(158, 139, 9, 4, '2025-09-26 03:46:09', '2025-09-26 03:46:09'),
(159, 139, 192, 4, '2025-09-26 03:46:09', '2025-09-26 03:46:09'),
(160, 139, 10, 3, '2025-09-26 03:46:09', '2025-09-26 03:46:09'),
(164, 141, 185, 4, '2025-09-26 03:54:52', '2025-09-26 03:54:52'),
(165, 141, 186, 4, '2025-09-26 03:54:52', '2025-09-26 03:54:52'),
(166, 141, 187, 4, '2025-09-26 03:54:52', '2025-09-26 03:54:52'),
(167, 142, 185, 4, '2025-09-26 03:54:53', '2025-09-26 03:54:53'),
(168, 142, 186, 3, '2025-09-26 03:54:53', '2025-09-26 03:54:53'),
(169, 142, 187, 4, '2025-09-26 03:54:53', '2025-09-26 03:54:53'),
(170, 143, 191, 4, '2025-09-26 04:04:15', '2025-09-26 04:04:15'),
(171, 143, 193, 4, '2025-09-26 04:04:15', '2025-09-26 04:04:15'),
(172, 143, 194, 3, '2025-09-26 04:04:15', '2025-09-26 04:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `itineraries`
--

CREATE TABLE `itineraries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `transport_id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `tour_group_code` varchar(255) NOT NULL,
  `km_start` int(11) DEFAULT NULL,
  `km_end` int(11) DEFAULT NULL,
  `fuel_expenditure_factual` int(11) DEFAULT NULL,
  `fuel_expenditure` int(11) DEFAULT NULL,
  `itinerary_number` varchar(255) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `fuel_remaining_liter` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `itineraries`
--

INSERT INTO `itineraries` (`id`, `created_at`, `updated_at`, `transport_id`, `tour_id`, `company_id`, `tour_group_code`, `km_start`, `km_end`, `fuel_expenditure_factual`, `fuel_expenditure`, `itinerary_number`, `number`, `file_name`, `fuel_remaining_liter`) VALUES
(1, '2025-02-15 08:29:39', '2025-02-21 11:47:54', 11, 12, 0, 'LI 2402', 0, NULL, 0, 270, 'EST1022025', 'EST-2025-001', 'itinerary_1.pdf', 270),
(2, '2025-02-15 08:36:26', '2025-02-21 05:16:34', 17, 12, 0, 'Viktoriya ZL2-23-28', 100, NULL, -22, 132, 'EST2022025', 'EST-2025-002', 'itinerary_2.pdf', 154),
(3, '2025-02-15 08:38:36', '2025-02-21 11:49:35', 10, 34, 0, 'Viktoriya SW2-2328', 14, NULL, -4, 156, 'EST3022025', 'EST-2025-003', 'itinerary_3.pdf', 160),
(4, '2025-05-16 03:52:11', '2025-05-16 03:57:22', 2, 20, 1, 'Ut dignissimos aut dolore iste', 75, 48, -8, 102, 'EST4052025', 'EST-2025-004', 'itinerary_4.pdf', 110);

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_items`
--

CREATE TABLE `itinerary_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` date NOT NULL,
  `itinerary_id` bigint(20) UNSIGNED NOT NULL,
  `city_distance_id` bigint(20) UNSIGNED NOT NULL,
  `time` time DEFAULT NULL,
  `program` varchar(255) NOT NULL,
  `accommodation` tinyint(1) DEFAULT NULL,
  `food` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `itinerary_items`
--

INSERT INTO `itinerary_items` (`id`, `created_at`, `updated_at`, `date`, `itinerary_id`, `city_distance_id`, `time`, `program`, `accommodation`, `food`) VALUES
(1, '2025-02-15 08:29:39', '2025-02-21 11:47:54', '2025-02-24', 1, 48, '08:20:00', 'vstrech aeraportu ekskursiya', 1, 0),
(2, '2025-02-15 08:29:39', '2025-02-21 11:47:54', '2025-02-25', 1, 59, '08:00:00', 'ekskursiya', 0, 1),
(3, '2025-02-15 08:29:39', '2025-02-21 11:47:54', '2025-02-26', 1, 3, '08:00:00', 'ekskursiya', 1, 0),
(4, '2025-02-15 08:36:26', '2025-02-21 05:16:34', '2025-02-23', 2, 48, '19:30:00', 'Vstrecha aeraportu ujin', 1, 0),
(5, '2025-02-15 08:36:26', '2025-02-21 05:16:34', '2025-02-24', 2, 48, '08:00:00', 'ekskursiya', 1, 0),
(6, '2025-02-15 08:36:26', '2025-02-21 05:16:34', '2025-02-25', 2, 48, '08:00:00', 'ekskursiya', 1, 0),
(7, '2025-02-15 08:38:36', '2025-02-21 11:49:35', '2025-02-23', 3, 48, '19:30:00', 'Vstrecha aeraportu ujin', 1, 0),
(8, '2025-02-21 05:01:50', '2025-02-21 11:47:54', '2025-02-27', 1, 2, '08:00:00', 'ekskursiya', 1, 0),
(9, '2025-02-21 05:01:50', '2025-02-21 11:47:54', '2025-02-28', 1, 49, '08:00:00', 'ekskursiya transfer vokzale 15:00', 0, 1),
(10, '2025-02-21 05:16:34', '2025-02-21 05:16:34', '2025-02-26', 2, 48, '08:00:00', 'ekskursiya', 1, 0),
(11, '2025-02-21 05:16:34', '2025-02-21 05:16:34', '2025-02-27', 2, 48, '08:00:00', 'ekskursiya', 1, 0),
(12, '2025-02-21 05:16:34', '2025-02-21 05:16:34', '2025-02-28', 2, 48, '08:00:00', 'ekskursiya', 1, 0),
(13, '2025-02-21 05:25:15', '2025-02-21 11:49:35', '2025-02-24', 3, 48, '08:00:00', 'ekskursiya', 1, 0),
(14, '2025-02-21 05:25:15', '2025-02-21 11:49:35', '2025-02-25', 3, 48, '08:00:00', 'ekskursiya', 1, 0),
(15, '2025-02-21 05:25:15', '2025-02-21 11:49:35', '2025-02-26', 3, 48, '08:00:00', 'ekskursiya', 1, 0),
(16, '2025-02-21 05:25:15', '2025-02-21 11:49:35', '2025-02-27', 3, 48, '08:00:00', 'ekskursiya', 1, 0),
(17, '2025-02-21 05:25:15', '2025-02-21 11:49:35', '2025-02-28', 3, 48, '08:00:00', 'ekskursiya', 1, 0),
(18, '2025-05-16 03:52:11', '2025-05-16 03:57:22', '1974-01-27', 4, 16, '06:13:00', 'Vstrecha', 0, 0),
(19, '2025-05-16 03:57:22', '2025-05-16 03:57:22', '2020-09-10', 4, 8, '12:29:00', 'Po gorodu', 0, 1),
(20, '2025-05-16 03:57:22', '2025-05-16 03:57:22', '1996-04-20', 4, 57, '20:34:00', 'Provodi', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal_types`
--

CREATE TABLE `meal_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `name` enum('breakfast','lunch','dinner','coffee_break') NOT NULL,
  `menu_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`menu_images`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meal_types`
--

INSERT INTO `meal_types` (`id`, `created_at`, `updated_at`, `description`, `restaurant_id`, `price`, `name`, `menu_images`) VALUES
(2, '2025-01-11 05:26:29', '2025-01-22 09:04:12', '3 salads , 1st and 2nd meals, drinks', 2, 11.00, 'lunch', NULL),
(3, '2025-01-11 05:26:29', '2025-01-22 09:04:12', '3 salads , meal, drinks', 2, 11.00, 'dinner', NULL),
(4, '2025-01-11 06:20:23', '2025-01-13 11:22:51', '3 salads, 1st meal, 2nd meal', 3, 10.00, 'lunch', NULL),
(5, '2025-01-11 06:22:54', '2025-01-25 04:48:17', '3 salads, 1st and 2nd meals, drinks', 4, 8.00, 'lunch', NULL),
(6, '2025-01-13 10:15:04', '2025-01-25 04:49:59', '3 salads, 1st and 2nd meals', 5, 12.00, 'lunch', NULL),
(7, '2025-01-13 10:19:49', '2025-01-13 10:19:49', '3 salads, 1st and 2nd meals', 6, 10.00, 'dinner', NULL),
(8, '2025-01-13 10:25:36', '2025-01-13 10:25:36', '3 salads, 1st and 2nd meals,drinks', 7, 10.00, 'lunch', NULL),
(9, '2025-01-13 10:43:25', '2025-01-13 10:43:25', '3 salads, 1st and 2nd meal', 8, 10.00, 'lunch', NULL),
(10, '2025-01-13 10:43:27', '2025-01-21 12:00:38', '3 salads, 1st and 2nd meal', 9, 13.00, 'lunch', NULL),
(11, '2025-01-13 10:52:15', '2025-01-22 12:02:35', '3 salads, 1st and 2nd meals', 10, 12.00, 'lunch', NULL),
(12, '2025-01-13 11:04:20', '2025-01-25 04:48:17', '3 salad,  2-meal drinks', 4, 8.00, 'dinner', NULL),
(13, '2025-01-13 12:26:02', '2025-01-22 12:01:52', '3 salads,1st and 2nd meals, drinks', 11, 12.00, 'dinner', NULL),
(14, '2025-01-13 12:46:21', '2025-01-22 12:03:55', '3 salads, 1st and 2nd meals, drinks', 12, 12.00, 'dinner', NULL),
(15, '2025-01-13 13:03:25', '2025-01-22 12:44:38', '3 salads, 1st and 2nd meals, drinks', 13, 12.00, 'lunch', NULL),
(16, '2025-01-13 13:08:39', '2025-01-22 12:45:37', '3 salads, 1st and 2nd meals, drinks', 14, 12.00, 'dinner', NULL),
(17, '2025-01-14 11:31:23', '2025-01-22 12:11:13', '3 salads, 1st and 2nd meals, drinks', 15, 12.00, 'lunch', NULL),
(18, '2025-01-14 11:37:31', '2025-01-14 11:37:31', '3 salads, 1st and 2nd meals, drinks', 16, 10.00, 'lunch', NULL),
(19, '2025-01-14 11:37:31', '2025-01-14 11:37:31', '3 salads, 1st and 2nd meals, drinks', 16, 10.00, 'dinner', NULL),
(20, '2025-01-14 11:41:00', '2025-01-14 11:41:00', '3 salads, 1st and 2nd meals, drinks', 17, 10.00, 'lunch', NULL),
(21, '2025-01-14 11:41:00', '2025-01-14 11:41:00', '3 salads, 1st and 2nd meals, drinks', 17, 10.00, 'dinner', NULL),
(22, '2025-01-14 11:45:11', '2025-01-14 11:45:11', '3 salads, 1st and 2nd meals, drinks', 18, 10.00, 'lunch', NULL),
(23, '2025-01-14 11:45:11', '2025-01-14 11:45:11', '3 salads, 1st and 2nd meals, drinks', 18, 10.00, 'dinner', NULL),
(24, '2025-01-14 11:51:12', '2025-01-14 11:51:12', '3 salads, 1st and 2nd meals, drinks', 19, 10.00, 'lunch', NULL),
(25, '2025-01-14 11:51:12', '2025-01-14 11:51:12', '3 salads, 1st and 2nd meals, drinks', 19, 10.00, 'dinner', NULL),
(26, '2025-01-14 12:05:27', '2025-01-14 12:05:27', '3 salads, 1st and 2nd meals, drinks', 20, 10.00, 'lunch', NULL),
(27, '2025-01-14 12:05:27', '2025-01-14 12:05:27', '3 salads, 1st and 2nd meals, drinks', 20, 10.00, 'dinner', NULL),
(28, '2025-01-14 12:18:39', '2025-01-14 12:18:39', '3 salads, 1st and 2nd meals, drinks', 21, 10.00, 'lunch', NULL),
(29, '2025-01-14 12:18:39', '2025-01-14 12:18:39', '3 salads, 1st and 2nd meals, drinks', 21, 10.00, 'dinner', NULL),
(30, '2025-01-15 11:06:12', '2025-01-15 11:06:12', '3 salads, 1st and 2nd meals, drinks', 8, 10.00, 'dinner', NULL),
(31, '2025-01-15 11:34:24', '2025-01-15 11:34:24', '3 salads, 1st and 2nd meals', 22, 10.00, 'lunch', NULL),
(32, '2025-01-15 11:34:24', '2025-01-15 11:34:24', '3 salads, 1st  and 2nd meals, drinks', 22, 10.00, 'dinner', NULL),
(33, '2025-01-16 11:13:43', '2025-01-16 11:13:43', '3 salads, 1st and 2nd meals, drinks', 23, 10.00, 'lunch', NULL),
(34, '2025-01-16 11:13:43', '2025-01-16 11:13:43', '3 salads, 1st and 2nd meals, drinks', 23, 10.00, 'dinner', NULL),
(35, '2025-01-17 12:28:20', '2025-01-17 12:28:20', '3 salads, 1st and 2nd meals, drinks', 24, 10.00, 'lunch', NULL),
(36, '2025-01-17 12:28:20', '2025-01-17 12:28:20', '3 salads, 1st and 2nd meals, drinks', 24, 10.00, 'dinner', NULL),
(37, '2025-01-17 12:37:00', '2025-01-17 12:37:00', '3 salads, 1st and 2nd meals, drinks', 25, 10.00, 'lunch', NULL),
(38, '2025-01-17 12:37:00', '2025-01-17 12:37:00', '3 salads, 1st and 2nd meals, drinks', 25, 10.00, 'dinner', NULL),
(39, '2025-01-17 12:53:46', '2025-01-17 12:53:46', '3 salads, 1st and 2nd meals, drinks', 26, 10.00, 'lunch', NULL),
(40, '2025-01-17 12:53:46', '2025-01-17 12:53:46', '3 salads, 1st and 2nd meals, drinks', 26, 10.00, 'dinner', NULL),
(41, '2025-01-18 05:05:51', '2025-01-18 05:05:51', '3 salads, 1st and 2nd meals, drinks', 27, 10.00, 'lunch', NULL),
(42, '2025-01-18 05:05:51', '2025-01-18 05:05:51', '3 salads, 1st and 2nd meals, drinks', 27, 10.00, 'dinner', NULL),
(43, '2025-01-18 05:13:12', '2025-01-18 05:13:12', '3 salads, 1st and 2nd meals, drinks', 28, 10.00, 'lunch', NULL),
(44, '2025-01-18 05:13:12', '2025-01-18 05:13:12', '3 salads, 1st and 2nd meals, drinks', 28, 10.00, 'dinner', NULL),
(45, '2025-01-18 05:25:58', '2025-01-18 05:25:58', '3 salads, 1st and 2nd meals, drinks', 29, 10.00, 'lunch', NULL),
(46, '2025-01-18 05:25:58', '2025-01-18 05:25:58', '3 salads, 1st and 2nd meals, drinks', 29, 10.00, 'dinner', NULL),
(47, '2025-01-18 05:39:42', '2025-01-18 05:39:42', '3 salads, 1st and 2nd meals, drinks', 30, 10.00, 'lunch', NULL),
(48, '2025-01-18 05:39:42', '2025-01-18 05:39:42', '3 salads, 1st and 2nd meals, drinks', 30, 10.00, 'dinner', NULL),
(49, '2025-01-18 05:47:04', '2025-01-18 05:47:04', '3 salads, 1st and 2nd meals, drinks', 31, 10.00, 'lunch', NULL),
(50, '2025-01-18 05:47:04', '2025-01-18 05:47:04', '3 salads, 1st and 2nd meals, drinks', 31, 10.00, 'dinner', NULL),
(51, '2025-01-20 07:00:12', '2025-01-20 07:00:12', '3 salads, 1st and 2nd meals, drinks', 32, 10.00, 'lunch', NULL),
(52, '2025-01-20 07:00:12', '2025-01-20 07:00:12', '3 salads, 1st and 2nd meals, drinks', 32, 10.00, 'dinner', NULL),
(53, '2025-01-20 07:04:59', '2025-01-20 07:04:59', '3 salads, 1st and 2nd meals, drinks', 33, 10.00, 'lunch', NULL),
(54, '2025-01-20 07:04:59', '2025-01-20 07:04:59', '3 salads, 1st and 2nd meals, drinks', 33, 10.00, 'dinner', NULL),
(55, '2025-01-20 08:06:25', '2025-01-20 08:06:25', '3 salads, 1st and 2nd melas, drinks', 34, 10.00, 'lunch', NULL),
(56, '2025-01-20 08:06:25', '2025-01-20 08:06:25', '3 salads, 1st and 2nd melas, drinks', 34, 10.00, 'dinner', NULL),
(57, '2025-01-20 08:37:27', '2025-01-20 08:37:27', '3 salads, 1st and 2nd meals, drinks', 35, 10.00, 'lunch', NULL),
(58, '2025-01-20 08:37:27', '2025-01-20 08:37:27', '3 salads, 1st and 2nd meals, drinks', 35, 10.00, 'dinner', NULL),
(59, '2025-01-20 08:42:43', '2025-01-20 08:42:43', '3 salads, 1st and 2nd meals, drinks', 36, 10.00, 'lunch', NULL),
(60, '2025-01-20 08:42:43', '2025-01-20 08:42:43', '3 salads, 1st and 2nd meals, drinks', 36, 10.00, 'dinner', NULL),
(61, '2025-01-20 09:55:37', '2025-01-20 09:55:37', '3 salads, 1st and 2nd meals,drinks', 37, 10.00, 'lunch', NULL),
(62, '2025-01-20 09:55:37', '2025-01-20 09:55:37', '3 salads, 1st and 2nd meals, drinks', 37, 10.00, 'dinner', NULL),
(63, '2025-01-20 11:22:20', '2025-01-20 11:22:20', '3 salads, 1st and 2nd meals, drinks', 38, 10.00, 'lunch', NULL),
(64, '2025-01-20 11:22:20', '2025-01-20 11:22:20', '3 salads, 1st and 2nd meals, drinks', 38, 10.00, 'dinner', NULL),
(65, '2025-01-20 11:34:01', '2025-01-20 11:34:01', '3 salads, 1st and 2nd meals, drinks', 39, 10.00, 'lunch', NULL),
(66, '2025-01-20 11:34:01', '2025-01-20 11:34:01', '3 salads, 1st and 2nd meals, drinks', 39, 10.00, 'dinner', NULL),
(67, '2025-01-20 12:04:46', '2025-01-20 12:04:46', '3 salads, 1st and 2nd meals, drinks', 40, 10.00, 'lunch', NULL),
(68, '2025-01-20 12:04:46', '2025-01-20 12:04:46', '3 salads, 1st and 2nd meals,drinks', 40, 10.00, 'dinner', NULL),
(69, '2025-01-20 12:40:22', '2025-01-20 12:40:22', '3 salads, 1st and 2nd meals, drinks', 41, 10.00, 'lunch', NULL),
(70, '2025-01-20 12:40:22', '2025-01-20 12:40:22', '3 salads, 1st and 2nd meals, drinks', 41, 10.00, 'dinner', NULL),
(71, '2025-01-21 10:27:21', '2025-01-21 10:27:21', '3 salads, 1st and 2nd meals, drinks', 42, 10.00, 'lunch', NULL),
(72, '2025-01-21 10:27:21', '2025-01-21 10:27:21', '3 salads, 1st and 2nd meals, drinks', 42, 10.00, 'dinner', NULL),
(73, '2025-01-21 10:32:06', '2025-01-21 10:32:06', '3 salads, 1st and 2nd meals, drinks', 43, 10.00, 'lunch', NULL),
(74, '2025-01-21 10:32:06', '2025-01-21 10:32:06', '3 salads, 1st and 2nd meals, drinks', 43, 10.00, 'dinner', NULL),
(75, '2025-01-21 10:35:39', '2025-01-21 10:35:39', '3 salads, 1st and 2nd meals,drinks', 7, 10.00, 'dinner', NULL),
(76, '2025-01-21 10:40:12', '2025-01-21 12:00:38', '3 salads, 1st and 2nd meal', 9, 13.00, 'dinner', NULL),
(77, '2025-01-21 10:40:12', '2025-01-21 10:40:12', 'Choy, Cofe, blinchik, Omlet, shirinlik ', 9, 8.00, 'breakfast', NULL),
(78, '2025-01-21 10:57:24', '2025-01-21 10:57:24', '3 salads, 1st and 2nd meals, drinks', 44, 10.00, 'lunch', NULL),
(79, '2025-01-21 11:42:30', '2025-01-21 11:42:30', 'салат, горячая закуска, 1-блюда, 2-блюда, вода, чай', 45, 10.00, 'lunch', NULL),
(80, '2025-01-21 11:42:30', '2025-01-21 11:42:30', 'салат, горячая закуска, 1-блюда, 2-блюда, вода, чай', 45, 10.00, 'dinner', NULL),
(81, '2025-01-21 12:54:44', '2025-01-21 12:54:44', 'салат, горячую закуску, первое блюдо, второе блюдо, десерт, чай и хлеб', 46, 9.00, 'lunch', NULL),
(82, '2025-01-21 12:54:44', '2025-01-21 12:54:44', 'салат, горячую закуску, первое блюдо, второе блюдо, десерт, чай и хлеб', 46, 9.00, 'dinner', NULL),
(83, '2025-01-22 11:19:13', '2025-01-25 04:49:59', '3 salads, 1st and 2nd meals', 5, 12.00, 'dinner', NULL),
(84, '2025-01-22 11:43:55', '2025-01-22 11:43:55', '3 salads, 1st and 2nd meals', 6, 10.00, 'lunch', NULL),
(85, '2025-01-22 12:01:52', '2025-01-22 12:01:52', '3 salads,1st and 2nd meals, drinks', 11, 12.00, 'lunch', NULL),
(86, '2025-01-22 12:02:35', '2025-01-22 12:02:35', '3 salads, 1st and 2nd meals', 10, 12.00, 'dinner', NULL),
(87, '2025-01-22 12:03:55', '2025-01-22 12:03:55', '3 salads, 1st and 2nd meals, drinks', 12, 12.00, 'lunch', NULL),
(88, '2025-01-22 12:11:13', '2025-01-22 12:11:13', '3 salads, 1st and 2nd meals, drinks', 15, 12.00, 'dinner', NULL),
(89, '2025-01-22 12:44:38', '2025-01-22 12:44:38', '3 salads, 1st and 2nd meals, drinks', 13, 12.00, 'dinner', NULL),
(90, '2025-01-22 12:45:37', '2025-01-22 12:45:37', '3 salads, 1st and 2nd meals, drinks', 14, 12.00, 'lunch', NULL),
(91, '2025-01-22 12:52:02', '2025-01-22 12:52:02', 'tea, coffee, pancake, sweets', 47, 8.00, 'breakfast', NULL),
(92, '2025-01-24 09:57:05', '2025-01-24 09:57:05', '3 salads, 1st and 2nd meals, drinks', 48, 12.00, 'lunch', NULL),
(93, '2025-01-24 09:57:05', '2025-01-24 09:57:05', '3 salads, 1st and 2nd meals, drinks', 48, 12.00, 'dinner', NULL),
(94, '2025-01-24 10:14:21', '2025-01-24 10:14:21', '3 salads, 1st and 2nd meals, drinks', 49, 9.00, 'lunch', NULL),
(95, '2025-01-24 10:14:21', '2025-01-24 10:14:21', '3 salads, 1st and 2nd meals, drinks', 49, 9.00, 'dinner', NULL),
(96, '2025-01-24 11:39:02', '2025-01-24 11:39:02', '3 salads, 1st and 2nd meals, drinks', 50, 10.00, 'lunch', NULL),
(97, '2025-01-24 11:39:02', '2025-01-24 11:39:02', '3 salads, 1st and 2nd meals, drinks', 50, 10.00, 'dinner', NULL),
(98, '2025-01-24 12:01:20', '2025-01-24 12:01:20', '3 salads, 1st and 2nd meals, drinks', 51, 10.00, 'lunch', NULL),
(99, '2025-01-24 12:01:20', '2025-01-24 12:01:20', '3 salads, 1st and 2nd meals, drinks', 51, 10.00, 'dinner', NULL),
(100, '2025-01-24 12:41:32', '2025-01-24 12:41:32', '3 salads, 1st and 2nd meals, drinks', 52, 10.00, 'lunch', NULL),
(101, '2025-01-24 12:41:32', '2025-01-24 12:41:32', '3 salads, 1st and 2nd meals, drinks', 52, 10.00, 'dinner', NULL),
(102, '2025-01-25 05:36:12', '2025-01-25 05:36:12', '3 salads, 1st and 2nd meals, drinks', 53, 10.00, 'lunch', NULL),
(103, '2025-01-25 05:36:12', '2025-01-25 05:36:12', '3 salads, 1st and 2nd meals, drinks', 53, 10.00, 'dinner', NULL),
(104, '2025-01-25 06:31:53', '2025-01-25 06:31:53', '3 salads, 1st and 2nd meals, drinks', 54, 10.00, 'lunch', NULL),
(105, '2025-01-25 06:31:53', '2025-01-25 06:31:53', '3 salads, 1st and 2nd meals, drinks', 54, 10.00, 'dinner', NULL),
(106, '2025-01-25 07:09:56', '2025-01-25 07:09:56', '3 salads, 1st and 2nd meals, drinks', 55, 10.00, 'lunch', NULL),
(107, '2025-01-25 07:09:56', '2025-01-25 07:09:56', '3 salads, 1st and 2nd meals, drinks', 55, 10.00, 'dinner', NULL),
(108, '2025-01-25 07:33:20', '2025-01-25 07:33:20', '3 salads, 1st and 2nd meals, drinks', 56, 10.00, 'lunch', NULL),
(109, '2025-01-25 07:33:20', '2025-01-25 07:33:20', '3 salads, 1st and 2nd meals, drinks', 56, 10.00, 'dinner', NULL),
(110, '2025-01-25 07:41:02', '2025-01-25 07:41:02', '3 salads, 1st and 2nd meals, drinks', 57, 10.00, 'lunch', NULL),
(111, '2025-01-25 07:41:02', '2025-01-25 07:41:02', '3 salads, 1st and 2nd meals, drinks', 57, 10.00, 'dinner', NULL),
(112, '2025-01-25 07:49:11', '2025-01-25 07:49:52', '3 salads, 1st and 2nd meals, drinks', 58, 12.00, 'lunch', NULL),
(113, '2025-01-25 07:49:52', '2025-01-25 07:49:52', '3 salads, 1st and 2nd meals, drinks', 58, 12.00, 'dinner', NULL),
(114, '2025-01-25 07:52:51', '2025-01-25 07:52:51', '3 salads, 1st and 2nd meals, drinks', 59, 12.00, 'lunch', NULL),
(115, '2025-01-25 07:52:51', '2025-01-25 07:52:51', '3 salads, 1st and 2nd meals, drinks', 59, 12.00, 'dinner', NULL),
(116, '2025-01-25 07:56:25', '2025-01-25 07:56:25', ' 3 salads, 1st and 2 nd meals, drinks', 60, 12.00, 'lunch', NULL),
(117, '2025-01-25 07:56:25', '2025-01-25 07:56:25', '3 salads, 1st and 2 nd meals, drinks', 60, 12.00, 'dinner', NULL),
(118, '2025-01-27 12:58:48', '2025-01-27 12:58:48', '3 salads, 1st and 2nd meals, drinks', 61, 10.00, 'lunch', NULL),
(119, '2025-01-27 12:58:48', '2025-01-27 12:58:48', '3 salads, 1st and 2nd meals, drinks', 61, 10.00, 'dinner', NULL),
(120, '2025-01-29 13:13:59', '2025-01-29 13:13:59', NULL, 62, 10.00, 'lunch', NULL),
(121, '2025-01-29 13:13:59', '2025-01-29 13:13:59', NULL, 62, 10.00, 'dinner', NULL),
(122, '2025-01-29 13:13:59', '2025-01-29 13:13:59', NULL, 62, 8.00, 'coffee_break', NULL),
(123, '2025-01-29 13:15:40', '2025-01-29 13:15:40', NULL, 63, 10.00, 'lunch', NULL),
(124, '2025-01-29 13:15:40', '2025-01-29 13:15:40', NULL, 63, 10.00, 'dinner', NULL),
(125, '2025-01-29 13:15:40', '2025-01-29 13:15:40', NULL, 63, 10.00, 'coffee_break', NULL),
(126, '2025-01-29 13:22:01', '2025-01-29 13:22:01', NULL, 64, 10.00, 'lunch', NULL),
(127, '2025-01-30 04:50:48', '2025-01-30 04:50:48', NULL, 65, 10.00, 'lunch', NULL),
(128, '2025-01-30 04:50:48', '2025-01-30 04:50:48', NULL, 65, 10.00, 'dinner', NULL),
(129, '2025-01-30 04:54:47', '2025-01-30 04:54:47', NULL, 66, 8.00, 'lunch', NULL),
(130, '2025-01-30 04:58:36', '2025-01-30 04:58:36', NULL, 66, 8.00, 'dinner', NULL),
(131, '2025-01-31 11:21:33', '2025-01-31 11:21:33', '3 salads, 1sta nd 2nd meals, drinks', 67, 10.00, 'lunch', NULL),
(132, '2025-01-31 11:21:33', '2025-01-31 11:21:33', '3 salads, 1sta nd 2nd meals, drinks', 67, 10.00, 'dinner', NULL),
(133, '2025-01-31 11:36:30', '2025-01-31 11:36:30', '3 salads, 1st and 2nd meals, drinks', 68, 10.00, 'lunch', NULL),
(134, '2025-01-31 11:36:30', '2025-01-31 11:36:30', '3 salads, 1st and 2nd meals, drinks', 68, 10.00, 'dinner', NULL),
(136, '2025-02-24 12:51:54', '2025-02-24 12:51:54', 'tea,coffee,sweets,pancake....', 70, 8.00, 'breakfast', NULL),
(137, '2025-02-24 12:51:54', '2025-02-24 12:51:54', '3 salads, 1st and 2nd meals, bread, drinks', 70, 12.00, 'lunch', NULL),
(138, '2025-02-24 12:51:54', '2025-02-24 12:51:54', '3 salads, 1st and 2nd meals, bread, drinks', 70, 12.00, 'dinner', NULL),
(139, '2025-03-07 10:28:26', '2025-03-07 10:28:26', '3 salads, 1st and 2nd meals, bread ', 71, 11.00, 'lunch', NULL),
(140, '2025-03-07 10:28:26', '2025-03-07 10:28:26', '3 salads, 1st and 2nd meals, bread ', 71, 11.00, 'dinner', NULL),
(141, '2025-09-26 03:37:19', '2025-09-26 03:37:19', NULL, 72, 10.00, 'dinner', NULL),
(142, '2025-09-26 03:38:07', '2025-09-26 03:38:07', NULL, 73, 12.00, 'dinner', NULL),
(143, '2025-09-26 03:39:19', '2025-09-26 03:39:19', NULL, 74, 13.00, 'dinner', NULL),
(144, '2025-09-26 03:40:56', '2025-09-26 03:40:56', NULL, 75, 13.00, 'dinner', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meal_type_restaurant_tour_days`
--

CREATE TABLE `meal_type_restaurant_tour_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `meal_type_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tour_day_id` bigint(20) UNSIGNED NOT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meal_type_restaurant_tour_days`
--

INSERT INTO `meal_type_restaurant_tour_days` (`id`, `created_at`, `updated_at`, `meal_type_id`, `restaurant_id`, `tour_day_id`, `is_booked`) VALUES
(24, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 8, 7, 20, 0),
(25, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 83, 5, 20, 0),
(26, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 79, 45, 21, 0),
(27, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 115, 59, 21, 0),
(38, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 5, 4, 23, 0),
(39, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 7, 6, 23, 0),
(40, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 129, 66, 24, 0),
(41, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 128, 65, 24, 0),
(42, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 8, 7, 25, 0),
(43, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 83, 5, 25, 0),
(44, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 94, 49, 26, 0),
(45, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 99, 51, 26, 0),
(46, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 102, 53, 27, 0),
(47, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 114, 59, 27, 0),
(49, '2025-01-30 07:45:40', '2025-01-30 07:45:40', 7, 6, 29, 0),
(50, '2025-01-30 07:45:40', '2025-01-30 07:45:40', 2, 2, 30, 0),
(51, '2025-01-30 10:11:37', '2025-01-30 10:11:37', 24, 19, 31, 0),
(52, '2025-01-30 10:11:37', '2025-01-30 10:11:37', 21, 17, 31, 0),
(53, '2025-01-30 10:55:10', '2025-01-30 10:55:10', 5, 4, 32, 0),
(54, '2025-01-30 10:55:10', '2025-01-30 10:55:10', 7, 6, 32, 0),
(56, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 127, 65, 34, 0),
(57, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 130, 66, 34, 0),
(58, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 8, 7, 35, 0),
(59, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 83, 5, 35, 0),
(60, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 94, 49, 36, 0),
(61, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 99, 51, 36, 0),
(63, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 109, 56, 38, 0),
(64, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 104, 54, 39, 0),
(65, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 111, 57, 39, 0),
(68, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 22, 18, 45, 0),
(69, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 97, 50, 45, 0),
(80, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 120, 62, 54, 0),
(81, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 121, 62, 54, 0),
(82, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 120, 62, 54, 0),
(83, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 120, 62, 55, 0),
(84, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 121, 62, 55, 0),
(85, '2025-01-30 12:08:54', '2025-01-30 12:08:54', 68, 40, 56, 0),
(86, '2025-01-30 12:08:54', '2025-01-30 12:08:54', 66, 38, 57, 0),
(87, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 81, 46, 58, 0),
(88, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 86, 10, 58, 0),
(89, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 79, 45, 59, 0),
(90, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 40, 26, 59, 0),
(91, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 6, 5, 60, 0),
(92, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 75, 7, 60, 0),
(93, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 5, 4, 62, 0),
(94, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 7, 6, 62, 0),
(95, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 94, 49, 63, 0),
(96, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 99, 51, 63, 0),
(97, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 102, 53, 64, 0),
(98, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 130, 66, 64, 0),
(99, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 8, 7, 65, 0),
(100, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 128, 65, 65, 0),
(101, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 6, 5, 66, 0),
(102, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 115, 59, 66, 0),
(103, '2025-01-31 05:54:04', '2025-01-31 05:54:04', 84, 6, 69, 0),
(104, '2025-01-31 05:54:04', '2025-01-31 05:54:04', 113, 58, 69, 0),
(105, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 5, 4, 70, 0),
(106, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 7, 6, 70, 0),
(107, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 129, 66, 71, 0),
(108, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 128, 65, 71, 0),
(109, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 8, 7, 72, 0),
(110, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 83, 5, 72, 0),
(111, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 94, 49, 73, 0),
(112, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 99, 51, 73, 0),
(113, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 109, 56, 75, 0),
(114, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 104, 54, 76, 0),
(115, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 111, 57, 76, 0),
(116, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 67, 40, 78, 0),
(117, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 75, 7, 78, 0),
(118, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 94, 49, 79, 0),
(119, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 99, 51, 79, 0),
(120, '2025-01-31 06:34:04', '2025-01-31 06:34:04', 129, 66, 80, 0),
(121, '2025-01-31 06:34:04', '2025-01-31 06:34:04', 83, 5, 80, 0),
(122, '2025-01-31 07:29:40', '2025-08-22 03:34:15', 5, 4, 82, 0),
(123, '2025-01-31 07:29:40', '2025-08-22 03:34:15', 7, 6, 82, 0),
(124, '2025-01-31 07:29:40', '2025-08-22 03:34:15', 129, 66, 83, 0),
(125, '2025-01-31 07:29:41', '2025-08-22 03:34:15', 75, 7, 83, 0),
(126, '2025-01-31 07:29:41', '2025-08-22 03:34:15', 6, 65, 84, 0),
(127, '2025-01-31 07:29:41', '2025-08-22 03:34:15', 83, 5, 84, 0),
(128, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 94, 49, 85, 0),
(129, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 99, 51, 85, 0),
(130, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 94, 49, 86, 0),
(131, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 109, 56, 86, 0),
(132, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 104, 54, 87, 0),
(133, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 111, 57, 87, 0),
(134, '2025-01-31 11:23:40', '2025-01-31 11:23:40', 109, 56, 91, 0),
(135, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 17, 15, 101, 0),
(136, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 25, 19, 101, 0),
(137, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 96, 50, 102, 0),
(138, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 103, 53, 102, 0),
(143, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 104, 54, 108, 0),
(144, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 107, 55, 108, 0),
(145, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 99, 51, 109, 0),
(146, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 28, 21, 109, 0),
(147, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 95, 49, 110, 0),
(148, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 103, 53, 110, 0),
(149, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 129, 66, 111, 0),
(150, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 75, 7, 111, 0),
(151, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 127, 65, 112, 0),
(152, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 83, 5, 112, 0),
(153, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 5, 4, 113, 0),
(154, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 7, 6, 113, 0),
(155, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 5, 4, 115, 0),
(156, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 7, 6, 115, 0),
(157, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 104, 54, 116, 0),
(158, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 109, 56, 116, 0),
(159, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 106, 55, 117, 0),
(160, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 94, 49, 118, 0),
(161, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 99, 51, 118, 0),
(162, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 102, 53, 119, 0),
(163, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 130, 66, 119, 0),
(164, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 8, 65, 120, 0),
(165, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 75, 7, 120, 0),
(166, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 6, 5, 121, 0),
(167, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 115, 59, 121, 0),
(168, '2025-02-04 11:49:22', '2025-02-04 11:49:22', 34, 23, 123, 0),
(169, '2025-02-04 12:43:11', '2025-02-04 12:43:11', 33, 23, 124, 0),
(170, '2025-02-11 10:06:27', '2025-02-11 10:06:27', 65, 39, 125, 0),
(171, '2025-02-11 10:06:27', '2025-02-11 10:06:27', 80, 45, 125, 0),
(172, '2025-02-11 10:10:50', '2025-02-11 10:10:50', 67, 40, 127, 0),
(173, '2025-02-11 10:10:50', '2025-02-11 10:10:50', 83, 5, 127, 0),
(174, '2025-02-11 10:48:32', '2025-02-11 10:57:21', 24, 16, 128, 0),
(175, '2025-02-11 10:48:32', '2025-02-11 10:48:32', 23, 18, 128, 0),
(176, '2025-02-11 10:57:21', '2025-02-11 10:57:21', 24, 19, 129, 0),
(177, '2025-02-11 10:57:21', '2025-02-11 10:57:21', 101, 52, 129, 0),
(178, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 20, 17, 130, 0),
(179, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 7, 6, 130, 0),
(180, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 5, 4, 132, 0),
(181, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 7, 6, 132, 0),
(182, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 129, 66, 134, 0),
(183, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 75, 7, 134, 0),
(184, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 127, 65, 135, 0),
(185, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 99, 51, 135, 0),
(186, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 94, 49, 136, 0),
(187, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 105, 54, 137, 0),
(188, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 108, 56, 138, 0),
(189, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 107, 55, 138, 0),
(190, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 35, 24, 139, 0),
(191, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 117, 60, 139, 0),
(192, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 5, 4, 142, 0),
(193, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 7, 6, 142, 0),
(194, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 129, 66, 143, 0),
(195, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 75, 7, 143, 0),
(196, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 6, 5, 144, 0),
(197, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 128, 65, 144, 0),
(198, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 94, 49, 145, 0),
(199, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 99, 51, 145, 0),
(200, '2025-02-21 13:35:30', '2025-02-21 13:35:30', 48, 30, 147, 0),
(203, '2025-06-26 06:53:02', '2025-06-26 07:02:22', 117, 60, 158, 1),
(204, '2025-09-26 03:46:09', '2025-09-26 04:04:14', 143, 74, 161, 1),
(206, '2025-09-26 03:54:52', '2025-09-26 04:04:14', 141, 72, 164, 1),
(207, '2025-09-26 03:54:53', '2025-09-26 04:04:14', 141, 72, 165, 1),
(208, '2025-09-26 04:04:15', '2025-09-26 04:04:15', 142, 73, 166, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_01_11_035613_add_cols_to_guides_table', 1),
(2, '2025_01_11_043128_add_cols_to_hotels_table', 1),
(3, '2025_01_11_120520_add_menu_image_to_meal_types_table', 2),
(4, '2025_01_11_122220_create_drivers_table', 2),
(5, '2025_01_11_123043_add_driver_id_to_transports_table', 2),
(6, '2025_01_14_031616_add_image_to_tours_table', 3),
(7, '2025_01_14_031802_add_image_to_tour_days_table', 3),
(8, '2025_01_16_042323_add_images_to_transports_table', 4),
(9, '2025_01_16_044541_create_amenity_transport_table', 5),
(10, '2025_01_21_101826_add_tour_file_to_tours_table', 5),
(11, '2025_01_21_143935_add_cols_to_transports_table', 5),
(12, '2025_01_21_143936_create_oil_changes_table', 5),
(13, '2025_01_22_034043_create_city_tour_day_table', 5),
(14, '2025_01_22_081302_create_tour_day_hotels_table', 6),
(15, '2025_01_22_090742_create_hotel_rooms_table', 7),
(16, '2025_01_29_035150_add_markup_col_to_estimates_table', 8),
(17, '2025_01_31_131139_add_price_types_col_to_guides_table', 9),
(18, '2025_01_31_135239_add_price_type_name_to_tour_days', 9),
(19, '2025_02_06_104630_create_itineraries_table', 10),
(20, '2025_02_06_105943_add_cols_to_itineraries_table', 10),
(21, '2025_02_06_125750_add_col_to_itineraries_table', 10),
(22, '2025_02_06_140101_add_col_to_itineraries_table', 10),
(23, '2025_02_06_140248_add_col_to_itineraries_table', 10),
(24, '2025_02_06_142614_add_col_to_itineraries_table', 10),
(25, '2025_02_08_035650_create_city_distances_table', 10),
(26, '2025_02_08_035709_create_itinerary_items_table', 10),
(27, '2025_02_08_052058_add_fule_rem_to_transports_table', 10),
(28, '2025_02_14_090404_add_fuel_ream_col_to_itineraries_table', 10),
(29, '2025_02_14_112351_add_fuel_ream_col_to_transport_table', 10),
(30, '2025_02_15_100829_create_companies_table', 11),
(31, '2025_06_08_125346_add_room_size_to_rooms_table', 12),
(32, '2025_06_09_083349_add_booking_status_flags_to_tour_days_table', 13),
(33, '2025_06_09_085123_add_is_booked_to_tour_day_related', 13),
(34, '2025_06_15_103849_create_booking_requests_table', 14),
(35, '2025_06_15_141239_add_country_to_tours_table', 14),
(36, '2025_06_16_061745_add_company_id_to_hotels_table', 14),
(37, '2025_06_16_084337_add_company_id_to_monuments_table', 14),
(38, '2025_06_16_084655_add_company_id_to_restaurants_table', 14),
(39, '2025_06_16_090826_add_is_operator_to_companies_table', 14),
(40, '2025_06_16_133218_add_request_number_to_booking_requests_table', 14),
(41, '2025_06_24_080400_add_tour_voucher_file_to_booking_requests_table', 14),
(42, '2025_06_24_143908_add_voucher_to_monuments_table', 14),
(43, '2025_06_24_171415_add_license_number_to_companies_table', 14);

-- --------------------------------------------------------

--
-- Table structure for table `monuments`
--

CREATE TABLE `monuments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `ticket_price` decimal(8,2) NOT NULL,
  `description` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voucher` tinyint(1) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `monuments`
--

INSERT INTO `monuments` (`id`, `created_at`, `updated_at`, `name`, `ticket_price`, `description`, `images`, `city_id`, `company_id`, `voucher`) VALUES
(1, '2025-01-10 16:43:53', '2025-09-26 03:04:37', 'Registan', 6.00, 'The Registan (Uzbek: Регистон, Registon) was the heart of the city of Samarkand of the Timurid Empire, now in Uzbekistan. The name Rēgistan (ریگستان) means \"sandy place\" or \"desert\" in Persian.\n\nThe Registan was a public square, where people gathered to hear royal proclamations, heralded by blasts on enormous copper pipes called dzharchis — and a place of public executions. It is framed by three madrasahs (Islamic schools) of distinctive Persian architecture. The square was regarded as the hub of the Timurid Renaissance.', '[]', 3, 1, 1),
(2, '2025-01-11 05:28:05', '2025-06-24 17:38:40', 'Hasti Imam', 3.00, 'The Hazrati Imam complex (also known as Hastimom or Hastim)  is an architectural monument dating from the 16th to 20th centuries, located in the Olmazor district of Tashkent city, Uzbekistan. The complex consists of the Moʻyi Muborak madrasa, the Qaffol Shoshi mausoleum, the Baroqxon Madrasa, the Hazrati Imam mosque, the Tillashayx mosque, and the Imam al-Bukhari Islamic Institute. The ensemble was built near the grave of Hazrati Imam, the first imam-khatib of Tashkent, a scholar, one of the first Islamic preachers in Tashkent, a poet and an artist.\n\nAccording to historical sources, Hazrati Imam was also a master of making locks and keys, for which he received the nickname \"Qaffol\", meaning \"locksmith\". He also spoke 72 languages and translated the Old Testament (Torah) into Arabic.\n\nToday, the Hazrati Imam complex is located in the \"Old City\" part of Tashkent, and survived the strong earthquake of 1966. In 2007, by the Decree of the President of the Republic of Uzbekistan Islam Karimov, the Hazrati Imam (Hastimom) public association was established, and construction and renovation works were carried out to restore the original historical appearance of the Hazrati Imam complex.', '[\"01JH9YQ7PQV7VBB3EPE73NJ2FN.jpg\"]', 2, 1, 1),
(3, '2025-01-11 05:48:39', '2025-06-24 17:38:47', 'Gur-E-Amir ', 4.00, 'The Gūr-i Amīr or Guri Amir (Uzbek: Amir Temur Maqbarasi, Go\'ri Amir, Persian: is a mausoleum of the Turkic conqueror Timur (also known as Tamerlane) in Samarkand, Uzbekistan. It occupies an important place in the history of Turkestan\'s architecture as the precursor for and had influence on later Mughal architecture tombs, including Gardens of Babur in Kabul, Humayun\'s Tomb in Delhi and the Taj Mahal in Agra, built by Timur\'s Indian descendants, Mongols  that followed Indian culture with Central Asian influences.  Mughals established the ruling Mughal dynasty of the Indian subcontinent. The mausoleum has been heavily restored over the course of its existence.', '[\"01JH9YGHS6GXKHRHQ07H8Q2YJ3.jpg\"]', 3, 1, 1),
(4, '2025-01-11 05:58:19', '2025-06-24 17:40:44', 'Shakhi-zinda', 4.00, 'The Shah-i-Zinda Ensemble includes mausoleums and other ritual buildings of 11th – 15th and 19th centuries. The name Shah-i-Zinda (meaning \"The living king\") is connected with the legend that Qutham ibn Abbas, a cousin of Muhammad, is buried here. He came to Samarkand with the Arab invasion in the 7th century to preach Islam.\n\nThe Shah-i-Zinda complex was formed over eight (from the 11th until the 19th) centuries and now includes more than twenty buildings.\nView inside the necropolis\nTuman Aka complex\nThe ensemble comprises three groups of structures: lower, middle and upper connected by four-arched domed passages locally called chartak. The earliest buildings date back to the 11th – 12th centuries. Mainly their bases and headstones have remained now. The most part dates back to the 14th – 15th centuries. Reconstructions of the 16th – 19th centuries were of no significance and did not change the general composition and appearance.\nThe initial main body - Kusam-ibn-Abbas complex - is situated in the north-eastern part of the ensemble. It consists of several buildings. The most ancient of them, the Kusam-ibn-Abbas mausoleum and mosque (16th century), are among them.\nThe upper group of buildings consists of three mausoleums facing each other. The earliest one is Khodja-Akhmad Mausoleum (1340s), which completes the passage from the north. The Mausoleum of 1361, on the right, restricts the same passage from the east.\nThe middle group consists of the mausoleums of the last quarter of the 14th century - first half of the 15th century and is concerned with the names of Timur\'s relatives, military and clergy aristocracy. On the western side the Turkan Ago Mausoleum, the niece of Timur, stands out. This portal-domed one-premise crypt was built in 1372. Opposite is the Mausoleum of Shirin Bika Aga, Timur\'s sister.Next to Shirin-Bika-Aga Mausoleum is the so-called Octahedron, an unusual crypt of the first half of the 15th century.\nNear the multi-step staircase the most well proportioned buildings of the lower group is situated. It is a double-cupola mausoleum of the beginning of the 15th century. This mausoleum is devoted to Kazi Zade Rumi, who was the scientist and astronomer. Therefore the double-cupola mausoleum which was built by Ulugh Beg above his tomb in 1434 to 1435 has the height comparable with cupolas of the royal family\'s mausoleums. The main entrance gate to the ensemble (Darvazakhana or the first chartak) turned southward was built in 1434 to 1435 under Ulugbek', '[\"01JH9Z28QCCFCFJFKWW9FB0YDR.jpg\"]', 3, 1, 1),
(5, '2025-01-11 06:01:37', '2025-06-24 17:42:21', 'Ulugbek Observatory', 4.00, 'The Ulugh Beg Observatory is an observatory in modern day Samarkand, Uzbekistan, which was built in the 1420s by the Timurid astronomer Ulugh Beg. This school of astronomy was constructed under the Timurid Empire, and was the last of its kind from the Islamic Medieval period. Islamic astronomers who worked at the observatory include Jamshid al-Kashi, Ali Qushji, and Ulugh Beg himself. The observatory was destroyed in 1449 and rediscovered in 1908.', '[\"01JH9Z8AACVP9FSYZH0FY8VH9K.jpg\"]', 3, 1, 1),
(6, '2025-01-11 06:05:21', '2025-06-26 06:56:15', 'Bibi-Khanym mosque', 4.00, 'The Bibi-Khanym Mosque (Uzbek: Bibixonim masjidi; Persian: مسجد بی بی خانم; also variously spelled as Khanum, Khanom, Hanum, Hanim) is one of the most important monuments of Samarkand, Uzbekistan. In the 15th century, it was one of the largest and most magnificent mosques in the Islamic world. It is considered a masterpiece of the Timurid Renaissance. By the mid-20th century, only a grandiose ruin of it still survived, but major parts of the mosque were restored during the Soviet period.', '[\"01JH9ZF4VKMWQTRRATCWAZHHK0.jpg\"]', 3, 1, 1),
(7, '2025-01-11 06:08:16', '2025-06-26 06:56:06', 'Afrasiab Museum', 4.00, 'Afrasiab Museum of Samarkand (Uzbek: Afrosiyob-Samarqand shahar tarixi muzeyi) is a museum located at the historical site of Afrasiyab, one of the largest archaeological sites in the world and the ancient city that was destroyed by the Mongols in the early 13th century. Museum building and the archaeological site are located in the north-eastern part of the city of Samarkand in the Central Asian country of Uzbekistan. It bears the name of Afrasiab, mythical king and hero of Turan. Permanent exhibition of the Afrasiab Museum of Samarkand is focused on the history of the city itself as well as the surrounding region. The museum building was designed by Armenian architect Bagdasar Arzumanyan in 1970, at the time when Uzbek Soviet Socialist Republic was still part of the Soviet Union. The opening of the museum was dedicated to the 2500th anniversary of the founding of the city of Samarkand.Thematically, the museum is divided into five rooms dedicated to different periods of life in the fort of Afrasiyab.', '[\"01JH9ZMFVANYE2H02C54YXN7TQ.jpg\"]', 3, 1, 1),
(8, '2025-01-11 06:13:49', '2025-01-11 06:13:49', 'Konighil paper village ', 2.00, 'Konigil village is located 13 km from Samarkand. In the times of the Great Silk Road, there were many caravanserais here, but with its decline, the area fell into decay. However, thanks to the efforts of the Mukhtarov brothers, a once neglected 800 m territory turned into a country handicrafts center, at the same time providing jobs for suburban residents.\n\nUnder the shade of green trees, there is the tourist village of Konigil. It vividly demonstrates the culture, lifestyle, heritage, and customs of the Uzbek people through the works of local craftspeople. And the Meros Paper Mill in Samarkand is ready to offer its visitors its treasure in full.\n\nSamarkand silk paper was spread worldwide by the caravans. It used to be a precious commodity on the Great Silk Road because it did not spoil if got wet and had a minimum shelf life made 400 years! By comparison, the maximum shelf life of modern manufactured paper is one century. In ancient times, silk paper was ordered for writing manuscripts, and nowadays it is used for their restoration.', '[\"01JH9ZYN8WMHHAD302N1WC0KXZ.jpg\"]', 3, NULL, 0),
(9, '2025-01-13 09:35:50', '2025-01-13 09:35:50', 'Tashkent metro station( Tashkent underground Museum)', 1.00, 'Planning for the Tashkent Metro started in 1968, two years after a major earthquake struck the city in 1966. Construction on the first line began in 1972 and it opened on 6 November 1977 with nine stations. This line was extended in 1980, and the second line was added in 1984. The most recent line is the Circle (Halqa) Line, the first section of which opened in 2020.[4]\n\nA northern extension of the Yunusobod Line for 2 stations Turkiston and Yunusobod was completed and opened on 29 August 2020. The fourth Circle line is currently under construction, first 7 stations for the line have already been built in 2020.', '[\"01JHFG9ZAY0FYXDBDQFDPGSVGX.jpg\"]', 2, NULL, 0),
(10, '2025-01-13 09:50:50', '2025-01-13 09:50:50', 'Minor Mosque ( White mosque)', 0.00, 'The Minor Mosque is called the White Mosque or Ok Machit. The mosque opened in October 2014. The snow-white beauty was built near the Ankhor embankment. The White Mosque can accommodate more than 2,400 people, has two tall minarets and a sky-colored dome. The style of the mosque building has absorbed all the best from centuries-old Uzbek traditions. And at the same time, the builders managed to bring something new to the image of the new mosque. Due to the white marble finish, the Minor Mosque acquired some lightness, airiness. White is the color of purity and innocence, which perfectly matches the Muslim way of thinking.', '[\"01JHFH5ECE641N1T9GYDJM569Q.jpg\"]', 2, NULL, 0),
(11, '2025-01-13 09:57:37', '2025-01-29 12:13:18', 'Ark of Bukhara', 4.00, 'The Ark of Bukhara is a massive fortress located in the city of Bukhara, Uzbekistan, that was initially built and occupied around the 5th century AD. In addition to being a military structure, the Ark encompassed what was essentially a town that, during much of the fortress\'s history, was inhabited by the various royal courts that held sway over the region surrounding Bukhara. The Ark was used as a fortress until it fell to Russia in 1920. Currently, the Ark is a tourist attraction and houses museums covering its history. The museums and other restored areas include an archaeological museum, the throne room, the reception and coronation court, a local history museum, and the court mosque', '[\"01JHFHHWC91AZ0Z69T38FF82P2.jpg\"]', 4, NULL, 0),
(12, '2025-01-13 10:01:41', '2025-01-13 10:01:41', 'Ulugbek Madrasah', 0.00, 'Ulugbek madrasah is an architectural monument (1417) in Bukhara, Uzbekistan. It is the oldest preserved madrasah in Central Asia. It is the oldest of the madrasahs built by Ulugbek. During the reign of Abdullah Khan II, major renovation works were carried out (1586).\nThe building is a monument of the heyday of Central Asian architecture, and madrasahs were built on its model in other cities of Central Asia. Currently, the madrasah is the only building of this size preserved in Bukhara from the Timurid dynasty. The madrasah, as well as the three madrasahs built by Ulugbek, is the oldest surviving building. It is located opposite the Abdulaziz Khan Madrasah and forms a single architectural ensemble with it. In the architecture of Central Asia, the paired ensemble of two buildings facing each other is defined by the term \"double\", and the term \"double madrasah\" refers to two madrasahs.\nIt was included in the UNESCO World Heritage List in 1993 as part of the \"Historic Center of Bukhara\". Currently, the Ulugbek madrasa houses the Museum of the History of the Restoration of Bukhara Monuments.', '[\"01JHFHSA0MS3XAS6732FCS5RXT.jpg\"]', 4, NULL, 0),
(13, '2025-01-13 10:04:39', '2025-01-29 12:13:35', 'Kalon Mosque', 4.00, 'Kalan Mosque (Persian: Big mosque) is an architectural monument located in Bukhara, Uzbekistan. It was considered one of the largest mosques built on the place of Jame\' Mosque. Its current appearance was built in 1514 during the reign of Shaybani Ubaidullah Khan of Bukhara. Currently, the mosque is included in the national list of estate real objects of material and cultural heritage of Uzbekistan.', '[\"01JHFHYR3K59HEN6CWFGFC6N1X.jpg\"]', 4, NULL, 0),
(14, '2025-01-13 10:10:54', '2025-01-13 10:10:54', 'Chor Minar ', 2.00, 'Chor Minor (Char Minar Uzbek: Chor minor), alternatively known as the Madrasah of Khalif Niyaz-kul, is a historic gatehouse for a now-destroyed madrasa in the historic city of Bukhara, Uzbekistan. It is located in a lane northeast of the Lyab-i Hauz complex. It is protected as a cultural heritage monument, and also it is a part of the World Heritage Site Historic Centre of Bukhara.[1] In Persian, the name of the monument means \"four minarets\", referring to the building\'s four towers.', '[\"01JHFJA63RPNX4SK7R0W2Z24EG.jpg\"]', 4, NULL, 0),
(15, '2025-01-13 10:14:36', '2025-01-13 10:14:36', 'Bolo Haouz Mosque', 0.00, 'Bolo Haouz Mosque is a historical mosque in Bukhara, Uzbekistan.[1] Built in 1712, on the opposite side of the citadel of Ark in Registan district, it is inscribed in the UNESCO World Heritage Site list along with other parts of the historic city. It served as a Friday mosque during the time when the emir of Bukhara was being subjugated under the Bolshevik Russian rule in the 1920s. Thin columns made of painted wood were added to the frontal part of the iwan (entrance) in 1917, additionally supporting the bulged roof of summer prayer room. The columns are decorated with colored muqarnas.', '[\"01JHFJGZEMFYCJYP5CHZJQ7BE4.jpg\"]', 4, NULL, 0),
(16, '2025-01-13 10:18:27', '2025-01-13 10:18:27', 'Lyabi- Khauz ensemble ', 0.00, 'Lab-i Hauz (Uzbek: Labihovuz, Tajik: Лаби Ҳавз, romanized: Labi Havz, Persian: لب حوض, romanized: Lab-e Howz, meaning in Persian \"by the pool\"), sometimes also known as Lyab-i Khauz, a Russian approximation, is the name of the area surrounding one of the few remaining hauz pools that have survived in the city of Bukhara, Uzbekistan. Until the Soviet period, there were many such pools, which were the city\'s principal source of water, but they were notorious for spreading disease and were mostly filled in during the 1920s and 1930s.\n\nThe Lab-i Hauz survived because it is the centerpiece of a magnificent architectural ensemble, created during the 16th and 17th centuries, which has not been significantly changed since. The Lab-i Hauz ensemble, surrounding the pool on three sides, consists of the Kukeldash Madrasah (1568–1569, the largest madrasa in the city), on the north side of the pool, and two religious edifices built by Nadir Divan-Beghi: a khanqah (1620; Uzbek: xonaqah, meaning a lodging house for itinerant Sufis) and a madrasa (1622), which stand on the west and east sides of the pool respectively. The small Qāzī-e Kalān Nasreddīn madrasa (now demolished) was formerly located beside the Kukeldash madrasah.', '[\"01JHFJR0B3Z4FQPYGWKNFMW6MF.jpg\"]', 4, NULL, 0),
(17, '2025-01-13 10:23:58', '2025-02-05 06:41:29', 'Bahoutdin Architectural Complex', 4.00, 'The Bahouddin Naqshband Memorial Complex is located approximately 10 kilometers northeast of Bukhara city and has been developed over many centuries. During the time of the Soviets, it was forbidden to visit the grave here. The complex was initially established after the death of Bahouddin Naqshband and has been a place of pilgrimage for many generations. Bahouddin Naqshband\'s full name was Bahouddin Muhammad ibn Burhoniddin Muhammad al-Bukhori, and he lived from 1318 to 1389. He was also known by titles such as \"Shohi Naqshband\" and \"Xojayi Buzruk.\" Bahouddin Naqshband is recognized as the seventh Sufi saint.\nThe Bahouddin Naqshband Memorial Complex begins with a small domed gatehouse. In 2003, the calligrapher Habibulloh Solih inscribed the 28th verse of the Surah Ar-Ra\'d (The Thunder) on the wall near the \"Bobi Islom\" gate, using an Arabic script known as \"Nasta\'liq\".In the muqarnas section of the gate, the names of the master builders and the year of construction are inscribed. A rubai (quatrain) is written in \"Nasta\'liq\" script on the entrance door of the mausoleum.The tombs within the complex have been arranged according to the command of Abdulaziz Khan and are currently well-preserved. The largest building in the complex, the khanqah (Sufi lodge), was constructed between 1544 and 1545.[3] Inside the cells of the khanqah, you can find poetry inscribed in \"Nasta\'liq\" script. The memorial complex also includes a minaret featuring an inscription in \"Nasta\'liq\" script, indicating that it was built in 1885', '[\"01JHFK23S782YT2Y6WEECMT7HF.jpg\"]', 4, NULL, 0),
(18, '2025-01-13 10:34:34', '2025-02-05 06:41:52', 'Samanid Mausoleum', 4.00, 'The Samanid Mausoleum is a mausoleum located in the northwestern part of Bukhara, Uzbekistan, just outside its historic center. It was built in the 10th century CE as the resting place of the powerful and influential Islamic Samanid dynasty that ruled the Samanid Empire from approximately 900 to 1000. It contained three burials, one of whom is known to have been that of Nasr II.\nThe mausoleum is considered one of the iconic examples of early Islamic architecture and is known as the oldest funerary building of Central Asian architecture.The Samanids established their de facto independence from the Abbasid Caliphate in Baghdad and ruled over parts of modern Afghanistan, Iran, Uzbekistan, Tajikistan, and Kazakhstan. It is the only surviving monument from the Samanid era, but American art historian Arthur Upham Pope called it \"one of the finest in Persia\".\nPerfectly symmetrical, compact in its size, yet monumental in its structure, the mausoleum not only combined multi-cultural building and decorative traditions, such as Sogdian, Sassanian, Persian and even classical and Byzantine architecture, but incorporated features customary for Islamic architecture – a circular dome and mini domes, pointed arches, elaborate portals, columns and intricate geometric designs in the brickwork. At each corner, the mausoleum\'s builders employed squinches, an architectural solution to the problem of supporting the circular-plan dome on a square. The building was buried in silt some centuries after its construction and was revealed during the 20th century by archaeological excavation conducted under the USSR.', '[\"01JHFKNH4SQZB5BG6GKXF7JYY8.jpg\"]', 4, NULL, 0),
(19, '2025-01-13 10:37:11', '2025-02-05 06:42:03', 'Chashma-Ayub Mausoleum', 4.00, 'Chashma-Ayub Mausoleum (Uzbek: Chashmai Ayyub, lit. \'Job\'s Well\') is located near the Samani Mausoleum, in Bukhara, Uzbekistan. Its name means Job\'s well, due to the legend in which Job (Ayub) visited this place and made a well by striking the ground with his staff. The water of this well is still pure and is considered healing. The current building was constructed during the reign of Timur and features a Khwarazm-style conical dome uncommon in Bukhara.', '[\"01JHFKTAV4R3NVTHZ3HNZ0WM96.jpg\"]', 4, NULL, 0),
(20, '2025-01-13 10:45:21', '2025-02-05 06:42:13', 'Magok-i-Attari Mosque', 4.00, 'Maghoki Attori Mosque (Uzbek: Magʻoki Attori masjidi, Tajik: Масҷиди Мағокии Атторӣ, romanized: Masjidi Maghokii Attori, Persian: مسجد مغاکی عطاری, romanized: Masjed-e Maghākī-ye Attārī) is a historical mosque in Bukhara, Uzbekistan. It forms a part of the historical religious complex of Lyab-i Hauz. The mosque is located in the historical center of Bukhara, about 300 meters southwest of Po-i-Kalyan, 100 meters southwest of the Toqi Telpak Furushon trading dome and 100 meters east of Lab-i Hauz. It is a part of UNESCO World Heritage Site Historic Centre of Bukhara. Today, the mosque is used as a carpet museum.', '[\"01JHFM98G9YNX9WR169X2R46Z0.jpg\"]', 4, NULL, 0),
(21, '2025-01-14 10:59:50', '2025-01-14 10:59:50', 'State Museum of History of Uzbekistan', 5.00, 'The State Museum of History of Uzbekistan (Uzbek: Oʻzbekiston tarixi davlat muzeyi; Russian: Государственный музей истории Узбекистана, Gosudarstvennyj muzej historii Uzbekistana), previously known as the National Museum of Turkestan, was founded in 1876. It is located in Tashkent.\nFormerly known as the Lenin Museum, the History Museum of Uzbekistan has since been renovated and more exhibits have been added.', '[\"01JHJ7GGMA209QG96X6EAKEQJ3.jpg\"]', 2, NULL, 0),
(22, '2025-01-14 11:04:09', '2025-01-14 11:04:09', 'Amir Timur Square', 0.00, 'The Amir Timur Square (Uzbek: Amir Temur xiyoboni, Амир Тимур Хиёбони) is the main town square in Tashkent, Uzbekistan\nThe predecessor of the square is a park built during the first governor-general of the Russian Turkestan era. The square was at the intersection of two main streets, Moscow Street (now Amir Timur Street) and Kaufmann Street (now Milza Ulugh Beg Street), under the name of Constantinov Square. It was built in 1882 by Nikolai Ulyanov (Ульянов, Николай Фёдорович Ульянов) working under Mikhail Chernyayev.\n\nAfter the 1917 Russian Revolution, the square was renamed the Revolution Square. While Joseph Stalin\'s statue was placed in the square during the late 1940s, it was removed due to the October 1961 resolution that all Stalin\'s statues would be removed. In 1968, a statue of Karl Marx was erected.\n\nAfter the independence of Uzbekistan, the square was renamed the Amir Timur Square in 1994, and Timur\'s statue was placed on the site. Adjacent to the park in the south, the Amir Timur Museum was built in 1996.', '[\"01JHJ7RDN1EG18EZDK3T3N2E25.jpg\"]', 2, NULL, 0),
(23, '2025-01-14 11:14:02', '2025-02-05 06:42:53', 'Oqsaroy (Shahrisabz)', 4.00, 'The construction of the Oqsaroy began in 1380 and was completed in 1386. However, the decoration work lasted until 1404. The palace was built in memory of Amir Temur\'s mother, Takina-Khotun. During the construction of the palace, craftsmen from Khorezm, Iran and many other countries participated. In particular, the participation of the stonecutter Muhammad Yusuf Tabrizi in the construction and decoration of the domed arch is recorded in the dome inscriptions.\n\nThe structure was originally supposed to be 73 meters high. A small water pool was placed on its roof, from which water flowing through pipes formed a waterfall. Golden sand was used to build the foundation of the palace. The foundation of the building was built quite deep', '[\"01JHJ8AG723CW40MNDSQEAEVBT.jpg\"]', 6, NULL, 0),
(24, '2025-01-14 11:20:39', '2025-01-14 11:20:39', 'Karshi Bridge', 0.00, '\nKarshi Bridge — (or Kashkadarya Bridge) is an ancient brick bridge built over the Kashkadarya River, which flows through the city of Karshi, connecting the two parts of the city. It is considered one of the symbols of the city of Karshi. The construction of this bridge, which has survived to this day, was carried out in the second half of the 16th century. The Karshi Bridge is the largest built over the Kashkadarya.\n\nBy the relevant resolution of the Cabinet of Ministers, it was included in the National Register of Immovable Objects of Tangible Cultural Heritage[3]. The bridge was last repaired in 2016.', '[\"01JHJ8PMA4YPHDBPNK2MVWY5C1.jpg\"]', 6, NULL, 0),
(25, '2025-01-18 05:03:15', '2025-01-18 05:03:15', 'Muhammad Aminkhan Madrasah', 4.00, 'Muhammad Aminkhan Madrasah is an architectural monument in Khiva (1852–1855). Built by Muhammad Amin Bahadur Khan. The Madrasah is located in the western part of the Itchan Kala. It was built in 1852–1855 with the funds and decree of the Uzbek ruler Muhammad Aminkhan. Muhammad Aminkhan Madrasah is the largest and most tiled in comparison to other Khiva madrasahs.\nIn 1990 it was included in the list of UNESCO World Heritage Sites as a part of Itchan Kala. Currently, it is used as a tourist service and exhibition space. Khiva tourist complex hotel is located there.', '[\"01JHVWPEQKMDZ4KPN37437B021.jpg\"]', 5, NULL, 0),
(26, '2025-01-18 05:06:25', '2025-02-05 06:42:37', 'Kaltaminor', 4.00, 'Kaltaminor is a memorial minaret in Khiva. It is located on the front side of the Muhammad Amin Khan madrasa and sometimes considered as part of it.', '[\"01JHVWW8DZAX3WXZHPT2MDZ540.jpg\"]', 5, NULL, 0),
(27, '2025-01-18 05:18:43', '2025-02-05 06:39:28', 'Islam Khoja complex', 8.00, 'Islam Khoja Madrasah is located behind the minaret. It consists of 42 hujras and a large domed hall. Skills of builders are shown in contrast combinations of architectural forms that skillfully used in a limited space. Mihrab niche is decorated with majolica and ganch. The facade is decorated with glaze.\nDecorative belts of blue and white ceramics alternating with ochre bricks adorn the minaret. It is topped by arched lantern and golden crown.\n\nThe minaret dominates its part of the city and concentrates around it thousand of domes and vaulted constructions. The different sizes of the buildings as they approach the minaret of Islam Khoja contrast with its mass, showing off the skill of town planning of the Khorezm architects.', '[\"01JHVXJS8SZKBG12VZQR9Y7ZGM.jpg\"]', 5, NULL, 0),
(28, '2025-01-18 05:25:22', '2025-06-10 07:15:36', 'Itchan Kala', 20.00, 'Itchan Kala (Uzbek: Ichan-Qаl’а) is the walled inner town of the city of Khiva, Uzbekistan. Since 1990, it has been protected as a World Heritage Site.\nThe old town retains more than 50 historic monuments and 250 old houses, dating primarily from the eighteenth or nineteenth centuries. Juma Mosque, for instance, was established in the tenth century and rebuilt from 1788 to 1789, although its celebrated hypostyle hall still retains 112 columns taken from ancient structures.\nNotable buildings in Itchan Kala are Konya Ark, Juma Mosque, Ak Mosque, Hasanmurod Qushbegi mosque, madrasahs of Alla-Kulli-Khan, Muhammad Aminkhon, Muhammad Rakhimkhon, Mausoleums of Pahlavon Mahmoud, Sayid Allavuddin, Shergozikhon as well as caravanserais and markets.', '[\"01JHVXYYJGMS66F9BTW9TQ2H0V.jpg\",\"01JHVXYYJJ8GSW8MP1E94RPV1F.jpg\"]', 5, NULL, 0),
(29, '2025-01-18 05:30:52', '2025-02-05 06:43:17', 'Pahlavon Mahmud Complex', 4.00, 'The Pahlavon Mahmud complex, Pahlavon Mahmud mausoleum or Polvon ota mausoleum is a memorial monument in Khiva, Khorezm. The mausoleum complex has a total area of 50x30m, and was originally built in 1664 as a miraculous dome over the grave of Pahlavon Mahmud. Pahlavon Mahmud (1247-1326) was a local poet who emerged from humble craftsmen, and was also famous for his heroic strength as an unbeatable wrestler, and his ability to heal people. His tomb has been and is still considered a sacred place by representatives of Uzbeks, Turkmens, Karakalpaks and other peoples. This complex is also known in Khiva as “Hazrati Pahlavon Pir”.\nAccording to his will, Pahlavon Mahmud was buried in his own leather workshop. Over time, this place became a respected pilgrimage site and later a complex named after him was built.', '[\"01JHVY90S60NZQ8MGYNF0BDD5E.jpg\"]', 5, NULL, 0),
(30, '2025-01-18 05:33:29', '2025-01-18 05:33:29', 'Ota Darvaza', 3.00, 'Ota Gate (uzbek: Ota darvoza) is the western gate of the Itchan Kala, in the walled inner town of the city of Khiva, Uzbekistan. It was built during the reign of Olloqulixon in 1828-29 and is also known as the Shermuhammad Gate. The Ota Gatehas been included in the \"List of Intangible Cultural Heritage of Humanity\" by the Cabinet of Ministers of the Republic of Uzbekistan, recognizing its cultural significance. Additionally, it has been added to UNESCO\'s World Heritage List as part of the historical heritage of the city of Khiva, signifying its historical importance.', '[\"01JHVYDTBX9G8DM5B7WHH50AW9.jpg\"]', 5, NULL, 0),
(31, '2025-01-18 07:07:26', '2025-02-05 06:52:01', 'Sultan Saodat Complex', 4.00, 'Sultan Saodat is a complex of religious structures located on the outskirts of modern Termez, in Uzbekistan.\nThe complex of Sultan Saodat, which was formed between the 10th and 17th centuries, holds the graves of the influential Sayyid dynasty of Termez. The Termez Sayyids claimed direct descendancy of the Islamic prophet Muhammad. The founder of the family is presumed to be Termez Sayyid Hassan al-Amir, descendant of Husayn ibn Ali, the grandson of Muhammad. Another historical tradition mentions that Sultan Saadat (Sodot) is the Sultan of Sayyids and the owner \"Sultan Saodat\" Mausoleum in Termez city – and Sultan Saadat is Sayyid Ali Akbar Termizi, who is also mentioned with the nickname (kunyat) Abu Muhammad, and is presumed to have died at the end of the 9th century or early in the 10th century in Termez.\nSultan Saodat Komplex Seit\nSultan Saodat\nSultan Saodat complex is a series of religious structures – mausoleums, mosques and khanaqa – built around a central passage. The oldest here are two large single-chamber, square, domed mausoleums (10th century). They are united by a 15th-century iwan.\nIn the second half of the 15th century two new buildings were built in front of the two mausoleums. Two parallel rows were built in the 15th–17th centuries and joined with the other buildings. Also, some new mausoleums were also pairwise connected with intermediate iwan; their decorations no longer exist. In the 16th–17th centuries courtyards to the south and the north were built up with mausoleums of different sizes and from different eras. The entrance was set up on the west side of the yard. The majestic ensemble stands out as a group of mausoleums, homogeneous in structure and decoration, though built in different styles', '[\"01JHW3SVNVT8G6G55NWDXJTSD8.jpg\"]', 11, NULL, 0),
(32, '2025-01-18 07:09:57', '2025-02-05 06:52:15', 'Hakim at-Termizi Mausoleum', 4.00, 'The Hakim at-Termizi Mausoleum (Uzbek: Hakim at-Termiziy maqbarasi; other names: Termizi Mausoleum) is a historical funerary monument located in the Sherobod district of Surxondaryo Region, Uzbekistan. It serves as a memorial to Islamic scholar Al-Hakim at-Termizi. The mausoleum was constructed on top of his grave', '[\"01JHW3YEJ4R8J3M5FSEBDEVA6J.jpg\"]', 11, NULL, 0),
(33, '2025-01-18 07:25:57', '2025-01-18 07:25:57', 'Fortress Kirk Kiz', 3.00, 'The unique \"Kirk Kiz\" building (\"forty girls”) which has attracted the attention of researchers for a long time , has been variously considered as a palace, an abbey, a caravansarai, Hanaqoh, or just simply a civil construction. The complex \"Kirk Kiz\" is situated 3 km. from the ancient city of Termez. Local tradition connects it with the well-known national legend in which the princess Gulaim and her forty girls bravely struggled against raiding nomads.\nThe building of \"Kirk Kiz\" is a square of about 54m each side of raw brick construction. At all corners of the building were protected by strong towers. There is an inside arched aperture, and also some large windows cut through each facade. There are two in intersections in the hallways placed on the two axes of the building dividing it into four equal parts. There is a little square courtyard in the centre of the building (11.5x11.5m), covered by a dome (to the mind some scholars, but according to another there was no overhead cover.', '[\"01JHW4VRE2QRZWNM57BBS8JCE7.jpg\"]', 11, NULL, 0),
(34, '2025-01-18 07:29:01', '2025-02-05 06:52:46', 'Fayaz Tepe', 4.00, 'Fayaz Tepe, also Fayoz-Tepe, is a Buddhist archaeological site in the Central Asia region of Bactria, in the Termez oasis near the city of Termez in southern Uzbekistan. Located 15 km west of Termez off the main M39 highway. Bus number 15 runs past the turn-off to Fayaz Tepe, from where it is a 1 km walk without shade. The foundations of the site date to the 1st century CE, with a peak of activity around the 3rd and 4th centuries during the Kushan period, before experiencing a fatal decline around the 5th century CE, probably with the invasion of the Kushano-Sassanian, whose coinage can be found at the nearby site of Kara Tepe.', '[\"01JHW51CF4KA8ASMNFMWB72323.jpg\"]', 11, NULL, 0),
(35, '2025-01-18 07:37:09', '2025-01-18 07:37:09', 'Archaeological Museum of Termez', 4.00, 'The Archaeological Museum of Termez is a museum in the city of Termez, modern Uzbekistan. The artifacts contained in the museum are mainly linked to the Graeco-Bactrian and Kushan periods. Some artifacts, such as the seated Buddha under the Bodhi tree or head of the Kushan prince are actually copies, the original of which are located in the History Museum of Tashkent and in the Hermitage Museum in Saint-Petersburg.[1][2]\n\nThere are also scale models of the archaeological sites of Salalli Tepe, Kampyr Tepe, Khalchayan, Balalyk Tepe and Fayaz Tepe.\nA famous mural, the so-called \"Princess of Tokharistan\", was found at Tavka Kurgan in Shirabad', '[\"01JHW5G88G159MX3CCGATAFBY2.jpg\"]', 11, NULL, 0),
(36, '2025-01-22 11:14:20', '2025-01-22 11:14:20', 'Kutlug Timur Minaret', 0.00, 'Kutlug Timur minaret is a minaret in Konye-Urgench in north Turkmenistan, Central Asia. It was built in 1011 during the Khwarazmian dynasty. The height of the minaret is 60 meters with a diameter of 12 metres at the base and 2 metres at the top. In 2005, the ruins of Old Urgench where the minaret is located were inscribed on the UNESCO List of World Heritage Sites.\nThe Kutlug Timur minaret belongs to a group of around 60 minarets and towers built between the 11th and the 13th centuries in Central Asia, Iran and Afghanistan including the Minaret of Jam, Afghanistan.\nOn the basis of its decorative brickwork, including Kufic inscriptions, the minaret is thought to be an earlier construction but was restored by Kutlug-Timur around 1330.', '[\"01JJ6VGSWH8GGRAC2XEWNMVMFT.jpg\"]', 17, NULL, 0),
(37, '2025-01-22 11:37:14', '2025-01-22 11:37:14', 'Turabeg Khanum Mausoleum', 0.00, 'Nearest to the town, you will find a complex of mausoleums belonging to 14th century, one of them being Turabeg Khanum Mausoleum. It is not far from the 11th ', '[\"01JJ6WTR95GGB81RP59W0GTC6T.jpg\"]', 17, NULL, 0),
(38, '2025-01-22 11:38:58', '2025-01-22 11:38:58', 'Sultan Ali Mausoleum', 0.00, 'To the east lies the museum and Matkerim Ishan mausoleum, and to the west, the Nedjmeddin Kubra mausoleum, Sultan Ali mausoleum and Piryarvali mausoleum. The ...', '[\"01JJ6WXX4ED9QKKA3E48G0B34H.jpg\"]', 17, NULL, 0),
(39, '2025-01-22 11:55:10', '2025-01-22 11:55:10', 'Magic City', 0.00, 'Asia\'s largest all season park entertainment and attractions Magic City. The first Magic City amusement park in Uzbekistan is a place that every day will give real emotions and remember magical sensations. The largest zone of magic and entertainment for the whole family, where neither adults nor children will be bored.', '[\"01JJ6XVJP6YRMYXS2EKNW25ZN3.jpg\"]', 2, NULL, 0),
(40, '2025-01-24 08:50:48', '2025-02-05 06:38:15', 'Savitsky Museum', 4.00, 'In summer 2009, both the Leisure column in the Sunday supplement of The New York Times and the International Herald Tribune listed Savitsky Museum as a must-see site for art enthusiasts before they die. French ambassador J.K. Richard echoed these sentiments when he referred to the museum’s rich collections as the “Caves of Ali Baba”. Today, the treasures of this remote museum have never been more accessible to the public.\n\nSavitsky Museum - also called Nukus Museum of Art, State Museum of Arts of the Republic of Karakalpakstan and even \"Louvre in the Sands\" - was founded by legendary Moscow artist Igor Savitsky (1915-1984), a name known to art connoisseurs the world over. Savitsky’s foresight, taste and courage enabled him to identify new artistic trends and up-and-coming artists, prompting him to amass the works of forgotten masters in the museum which he founded in 1966.\n\nAfter Savtisky’s death in 1984, his student Marinika Babanazarova took over the reins, serving as museum director from 1984 to 2015. Thanks to Babanazarova’s tireless work and ceaseless creativity, the collections at Nukus Museum of Art were expanded and the museum brought to worldwide fame.', '[\"01JJBRHWDGPYFDFYKJAFW99EX0.jpg\"]', 18, NULL, 0),
(41, '2025-01-24 08:51:29', '2025-01-24 08:53:03', 'Muynak Ship Graveyard', 0.00, 'Muynak Ship Graveyard is the site of a once-wealthy fishing town which was founded on the shores of the Aral Sea. Muynak flourished until the Karakum Canal was constructed for the irrigation of cotton fields in the 1950s, a reckless act which led to one of the world’s greatest ecological disasters. The Aral Sea nearly dried up in the ensuing decades, and today its shores are 100 kilometers away from the city. Cargo ships were forced to halt their voyages in 1970, and Karakalpakstan’s Muynak is now little more than a ghost town, with a population of 18,000 people.\n\nMuynak Ship Graveyard is a collection of forlorn, abandoned boats which serve as a glaring reminder of this unnecessary tragedy of nature. The sight of a full lineup of ships, embedded in the desert sands where scarcely a drop of water can now be found, is truly tragic. Visitors are welcome to climb atop the old vessels, take photos and tour the small but informative museum which now sits adjacent to the ships.', '[\"01JJBR7J7SBTCADH1NWWQCCW17.jpg\"]', 18, NULL, 0),
(42, '2025-01-24 08:54:56', '2025-01-24 09:08:08', 'Aral Sea', 0.00, 'The infamous Aral Sea was once the fourth largest in the world before its waters were tragically drained in the Soviet irrigation project which was launched in the mid-20th century. Now just 10 percent of its original size, the Aral Sea is at the heart of one of the world’s worst ecological disasters which has had an irreversible impact on the region’s flora and fauna.\n\nKarakalpakstan was once home to more than 60 percent of the tugai forests of Central Asia, yet today only 10 percent of this wealth remains. Much of the area is caked in a layer of salt, which has naturally led to a significant decrease in its birds, fish and wildlife. Sixty-three local species are now listed in the Red Book, including the Central Asian cheetah and the Turanian tiger, while several endangered bird and fish species are listed in the International Union for Conservation of Nature (IUCN) Book.\n\nNevertheless, glimmers of hope for a more promising future can still be found in the region, particularly along the coast of what remains of Karakalpakstan’s Aral Sea. Ride in a 4WD from Nukus via Muynak and the Ustyurt Plateau to reach these now-remote shores, where you can float in its sparkling, salty waters and sunbathe on its shores. Most guests opt to spend the night in nearby yurt camps, where homecooked food, starry skies and a comfortable bed await you. Aral Sea trips help to support the local economy while promising adventure and an eye-opening, firsthand experience of beleaguered and tenacious Karakalpakstan.', '[\"01JJBS35DDCRDB1CJ8KE23YFAX.jpg\"]', 18, NULL, 0),
(43, '2025-01-24 08:56:37', '2025-01-24 08:56:37', 'Desert Castles of Ancient Khorezm', 0.00, 'Ellik Kala Fortresses (Fifty Fortresses), better known as the Desert Castles of Ancient Khorezm, is a UNESCO-recognized string of 8 citadels which were built around an oasis near Uzbekistan’s Karakum Desert. Counted among the oldest sites in Karakalpakstan Region, the defensive structures were constructed between the 4th century BC- 9th century AD and were inhabited until medieval times, when they were likely abandoned following a 13th-century Mongol invasion.\n\nThe castles were rediscovered in the 1930s by famed Soviet archaeologist Sergei Pavlovich Tolstov, who was accompanied on his expedition by Igor Savitsky, the founder of the Nukus museum which now bears his name. Their research determined that the forts served to protect nearby villages from enemy raids and that at least one (Kyzyl-Kala) was a thriving post along the Great Silk Road. Numerous artifacts, Zoroastrian temples and city ruins unearthed at each site now provide invaluable clues into the region’s history.\n\nAmong the most significant of the 8 fortresses are Toprak-Kala (1st-5th centuries AD), a fortress-settlement valued for its advanced and complex architecture; Ayaz-Kala (c. 2nd century AD), whose remains are divided into 3 distinct time periods; and Kyzyl-Kala (c. 1st century AD), which was partially restored in the early 21st century. The forlorn castles now welcome visitors to climb their walls and explore their every nook and cranny. Seeing their stately grandeur and historical significance with your own eyes will make the desert drive to reach them from Nukus well worth the effort!', '[\"01JJBRE2TN4H9WQ153A3VTKKZA.jpg\"]', 18, NULL, 0),
(44, '2025-01-24 09:28:50', '2025-01-24 09:28:50', 'Siyob Bazaar', 0.00, 'Siyob Bazaar (Uzbek: Siyob bozori,Tajik: Бозори Сиёб), also called Siab Bazaar, is the largest bazaar in Samarkand, Uzbekistan. The building is built in the shape of a dome, under which there are several pavilions with a large number of shopping arcades. The main entrance has a triple arch lined with blue majolica. The area of the market is more than 7 hectares. Siyob Bazaar is one of the most visited places in the city. The Siyob Bazaar consists of seven large covered pavilions with counters, as well as several other large pavilions with shops.\n\nThe name of the bazaar comes from the name of one of the historical and geographical regions of the city - Siyob, and the Siyob River flowing near the bazaar. The word “Siyob” is translated from Persian and Tajik as black water/river.\n\nSiyob Bazaar is located adjacent to the Bibi-Khanym Mosque, and is visited not only by local people but also by domestic and foreign tourists.', '[\"01JJBT922ZPHNYY6CX1JYB1PTC.jpg\"]', 3, NULL, 0),
(45, '2025-01-24 09:31:00', '2025-01-24 09:31:00', 'Chorsu Bazaar', 0.00, 'Chorsu Bazaar (Persian: بازار چارسو, Uzbek: Chorsu bozori), also called Charsu Bazaar, is the traditional bazaar located in the center of the old town of Tashkent, the capital city of Uzbekistan. Under its blue-colored domed building and the adjacent areas, all daily necessities are sold.Chorsu Bazaar is located across the street from Chorsu Station of the Tashkent Metro, near Kukeldosh Madrasah. \"Chorsu\" is a word from the Tajik language, meaning \"crossroads\" or \"four streams\". [1] Kukeldash Madrasah, built around 1570, is located at the edge of the bazaar. The modern building and the characteristic blue dome were designed by Vladimir Azimov, Sabir Adylov et al. in 1980, as a late example of Soviet Modernism style.', '[\"01JJBTD1XM4NYYMP8RFCP6J3HV.jpg\"]', 2, NULL, 0),
(46, '2025-01-24 09:40:36', '2025-01-24 09:40:36', 'Tashkent Tower', 5.00, 'The Tashkent Television Tower (Uzbek: Тошкент Телеминораси, Toshkent Teleminorasi) is a 375-metre-high (1,230 ft) tower, located in Tashkent, Uzbekistan and is the twelfth tallest tower in the world. Construction started in 1978. The tower began operating six years later, on 15 January 1985. It was the fourth tallest tower in the world from 1985 to 1991. The decision to construct the tower was made on 1 September 1971 in order to spread TV and radio signals to all over Uzbekistan. It is a vertical cantilever structure, and is constructed out of steel. Its architectural design is a product of the Terkhiev, Tsarukov & Semashko firm.', '[\"01JJBTYKVKHBN6XGEZ58QCV29Y.jpg\"]', 2, NULL, 0),
(47, '2025-01-30 10:12:56', '2025-01-30 10:12:56', 'Independence square tashkent', 0.00, 'The Independence Square (Mustaqilliq Maidoni) is the main square of the country with the administrative offices of the Cabinet and the Senate. Generally it looks more like a large park than a square, and is packed with monuments and fountains. Surrounded by impressive public buildings and filled with trees and flower beds, the Independence Square in Tashkent is a showcase of modern Uzbekistan.\n\nThe entrance to the square is framed by Arch of Independence with the sculptural images of storks on top. In the center of the square on the granite pedestal is placed the symbol of independence - the bronze ball, symbolizing the globe with a symbolic image of the Republic of Uzbekistan on it. At the foot of the obelisk there is the monument of the Happy Mother - the figure of a young woman with a baby in her arms.', '[\"01JJVB64QWG2R924QZQ0P1B2EQ.jpg\"]', 2, NULL, 0),
(48, '2025-02-01 05:28:32', '2025-02-01 05:28:32', 'Kosmonavtlar (Tashkent Metro)', 0.00, 'Kosmonavtlar (\"Cosmonauts\", formerly known as Проспект Космонавтов, Prospekt Kosmonavtov) is a space-programme-themed station of the Tashkent Metro. It honors Soviet cosmonauts such as Yuri Gagarin and Valentina Tereshkova, the first man and woman in space. The station was opened on 8 December 1984 as part of the inaugural section of the line, between Alisher Navoiy and Toshkent.\nUntil 2018 it was illegal to photograph the Tashkent metro, because it also worked as a nuclear bomb shelter.', '[\"01JJZZPTHFTMAJ9FE20VZ9XN9S.jpg\"]', 2, NULL, 0),
(49, '2025-02-05 06:46:23', '2025-02-05 06:46:23', 'Nurullaboy saroyi Khiva', 3.00, 'Nurullaboy saroyi – Xivadagi meʼmoriy yodgorlik[1]. Asfandiyorxonning sobiq rasmiy qabulxonasi (20-asr). Bu saroy-bogʻni Xiva xoni Muhammad Rahimxon II Feruz oʻgʻli Asfandiyorxon uchun qurdirgan (Nurullaboy degan shaxsning bogʻi oʻrniga qurilgani uchun Nurullaboy saroyi deb ataladi).', '[\"01JKADR7TW8JCF6PM0C6Z0BTTK.jpg\"]', 5, NULL, 0),
(50, '2025-06-10 07:18:02', '2025-06-10 07:18:02', 'Hodja Donyor', 2.30, NULL, '[]', 3, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `monument_tour_days`
--

CREATE TABLE `monument_tour_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `monument_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tour_day_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monument_tour_days`
--

INSERT INTO `monument_tour_days` (`id`, `created_at`, `updated_at`, `monument_id`, `tour_day_id`) VALUES
(22, NULL, NULL, 22, 19),
(23, NULL, NULL, 10, 19),
(24, NULL, NULL, 21, 19),
(25, NULL, NULL, 9, 19),
(26, NULL, NULL, 39, 19),
(27, NULL, NULL, 3, 20),
(28, NULL, NULL, 1, 20),
(29, NULL, NULL, 5, 21),
(30, NULL, NULL, 4, 21),
(31, NULL, NULL, 6, 21),
(32, NULL, NULL, 44, 21),
(47, NULL, NULL, 22, 23),
(48, NULL, NULL, 10, 23),
(49, NULL, NULL, 9, 23),
(50, NULL, NULL, 21, 23),
(51, NULL, NULL, 3, 24),
(52, NULL, NULL, 1, 24),
(53, NULL, NULL, 6, 25),
(54, NULL, NULL, 4, 25),
(55, NULL, NULL, 44, 25),
(56, NULL, NULL, 5, 25),
(57, NULL, NULL, 8, 25),
(58, NULL, NULL, 16, 26),
(59, NULL, NULL, 15, 26),
(60, NULL, NULL, 14, 26),
(61, NULL, NULL, 13, 26),
(62, NULL, NULL, 11, 27),
(63, NULL, NULL, 13, 27),
(64, NULL, NULL, 39, 27),
(65, NULL, NULL, 22, 30),
(66, NULL, NULL, 39, 30),
(67, NULL, NULL, 9, 30),
(68, NULL, NULL, 16, 31),
(69, NULL, NULL, 14, 31),
(70, NULL, NULL, 18, 31),
(71, NULL, NULL, 22, 32),
(72, NULL, NULL, 47, 32),
(73, NULL, NULL, 10, 32),
(74, NULL, NULL, 9, 32),
(75, NULL, NULL, 3, 34),
(76, NULL, NULL, 1, 34),
(77, NULL, NULL, 6, 35),
(78, NULL, NULL, 8, 35),
(79, NULL, NULL, 4, 35),
(80, NULL, NULL, 44, 35),
(81, NULL, NULL, 5, 35),
(82, NULL, NULL, 13, 36),
(83, NULL, NULL, 16, 36),
(84, NULL, NULL, 12, 36),
(85, NULL, NULL, 11, 36),
(86, NULL, NULL, 15, 36),
(87, NULL, NULL, 14, 36),
(88, NULL, NULL, 25, 39),
(89, NULL, NULL, 30, 39),
(90, NULL, NULL, 29, 39),
(91, NULL, NULL, 38, 39),
(92, NULL, NULL, 27, 39),
(93, NULL, NULL, 28, 39),
(94, NULL, NULL, 26, 39),
(95, NULL, NULL, 36, 39),
(96, NULL, NULL, 37, 39),
(98, NULL, NULL, 17, 45),
(99, NULL, NULL, 6, 59),
(100, NULL, NULL, 3, 59),
(101, NULL, NULL, 44, 59),
(102, NULL, NULL, 5, 60),
(103, NULL, NULL, 22, 62),
(104, NULL, NULL, 47, 62),
(105, NULL, NULL, 10, 62),
(106, NULL, NULL, 21, 62),
(107, NULL, NULL, 9, 62),
(108, NULL, NULL, 16, 63),
(109, NULL, NULL, 12, 63),
(110, NULL, NULL, 13, 63),
(111, NULL, NULL, 11, 64),
(112, NULL, NULL, 15, 64),
(113, NULL, NULL, 14, 64),
(114, NULL, NULL, 3, 65),
(115, NULL, NULL, 1, 65),
(116, NULL, NULL, 5, 65),
(117, NULL, NULL, 4, 65),
(118, NULL, NULL, 7, 65),
(119, NULL, NULL, 7, 66),
(120, NULL, NULL, 6, 66),
(121, NULL, NULL, 8, 66),
(122, NULL, NULL, 44, 66),
(123, NULL, NULL, 39, 66),
(124, NULL, NULL, 9, 69),
(125, NULL, NULL, 22, 69),
(126, NULL, NULL, 47, 69),
(127, NULL, NULL, 22, 70),
(128, NULL, NULL, 47, 70),
(129, NULL, NULL, 10, 70),
(130, NULL, NULL, 21, 70),
(131, NULL, NULL, 9, 70),
(132, NULL, NULL, 3, 71),
(133, NULL, NULL, 1, 71),
(134, NULL, NULL, 5, 72),
(135, NULL, NULL, 4, 72),
(136, NULL, NULL, 44, 72),
(137, NULL, NULL, 6, 72),
(138, NULL, NULL, 8, 72),
(139, NULL, NULL, 11, 73),
(140, NULL, NULL, 15, 73),
(141, NULL, NULL, 14, 73),
(142, NULL, NULL, 13, 73),
(143, NULL, NULL, 16, 73),
(144, NULL, NULL, 12, 73),
(145, NULL, NULL, 28, 76),
(146, NULL, NULL, 26, 76),
(147, NULL, NULL, 27, 76),
(148, NULL, NULL, 30, 76),
(149, NULL, NULL, 25, 76),
(150, NULL, NULL, 29, 76),
(151, NULL, NULL, 38, 76),
(152, NULL, NULL, 3, 78),
(153, NULL, NULL, 1, 78),
(154, NULL, NULL, 11, 79),
(155, NULL, NULL, 15, 79),
(156, NULL, NULL, 14, 79),
(157, NULL, NULL, 16, 79),
(158, NULL, NULL, 13, 79),
(159, NULL, NULL, 12, 79),
(160, NULL, NULL, 5, 80),
(161, NULL, NULL, 4, 80),
(162, NULL, NULL, 6, 80),
(163, NULL, NULL, 44, 80),
(164, NULL, NULL, 22, 82),
(165, NULL, NULL, 47, 82),
(166, NULL, NULL, 10, 82),
(167, NULL, NULL, 21, 82),
(168, NULL, NULL, 9, 82),
(169, NULL, NULL, 3, 83),
(170, NULL, NULL, 1, 83),
(171, NULL, NULL, 5, 84),
(172, NULL, NULL, 4, 84),
(173, NULL, NULL, 6, 84),
(174, NULL, NULL, 44, 84),
(175, NULL, NULL, 8, 84),
(176, NULL, NULL, 16, 85),
(177, NULL, NULL, 12, 85),
(178, NULL, NULL, 13, 85),
(179, NULL, NULL, 14, 85),
(180, NULL, NULL, 11, 86),
(181, NULL, NULL, 15, 86),
(182, NULL, NULL, 27, 87),
(183, NULL, NULL, 28, 87),
(184, NULL, NULL, 26, 87),
(185, NULL, NULL, 36, 87),
(186, NULL, NULL, 25, 87),
(187, NULL, NULL, 30, 87),
(188, NULL, NULL, 29, 87),
(189, NULL, NULL, 11, 101),
(190, NULL, NULL, 15, 101),
(191, NULL, NULL, 19, 101),
(192, NULL, NULL, 17, 101),
(198, NULL, NULL, 27, 108),
(199, NULL, NULL, 28, 108),
(200, NULL, NULL, 26, 108),
(201, NULL, NULL, 25, 108),
(202, NULL, NULL, 30, 108),
(203, NULL, NULL, 29, 108),
(204, NULL, NULL, 11, 110),
(205, NULL, NULL, 15, 110),
(206, NULL, NULL, 14, 110),
(207, NULL, NULL, 13, 110),
(208, NULL, NULL, 16, 110),
(209, NULL, NULL, 12, 110),
(210, NULL, NULL, 3, 111),
(211, NULL, NULL, 1, 111),
(212, NULL, NULL, 7, 112),
(213, NULL, NULL, 6, 112),
(214, NULL, NULL, 8, 112),
(215, NULL, NULL, 4, 112),
(216, NULL, NULL, 44, 112),
(217, NULL, NULL, 5, 112),
(218, NULL, NULL, 22, 113),
(219, NULL, NULL, 47, 113),
(220, NULL, NULL, 10, 113),
(221, NULL, NULL, 21, 113),
(222, NULL, NULL, 9, 113),
(223, NULL, NULL, 39, 113),
(224, NULL, NULL, 22, 115),
(225, NULL, NULL, 47, 115),
(226, NULL, NULL, 48, 115),
(227, NULL, NULL, 10, 115),
(228, NULL, NULL, 21, 115),
(229, NULL, NULL, 9, 115),
(230, NULL, NULL, 28, 116),
(231, NULL, NULL, 27, 116),
(232, NULL, NULL, 26, 116),
(233, NULL, NULL, 30, 117),
(234, NULL, NULL, 29, 117),
(235, NULL, NULL, 25, 117),
(236, NULL, NULL, 16, 118),
(237, NULL, NULL, 13, 118),
(238, NULL, NULL, 12, 118),
(239, NULL, NULL, 11, 119),
(240, NULL, NULL, 15, 119),
(241, NULL, NULL, 14, 119),
(242, NULL, NULL, 3, 120),
(243, NULL, NULL, 1, 120),
(244, NULL, NULL, 6, 120),
(245, NULL, NULL, 4, 120),
(246, NULL, NULL, 6, 121),
(247, NULL, NULL, 39, 121),
(248, NULL, NULL, 44, 121),
(249, NULL, NULL, 5, 121),
(250, NULL, NULL, 22, 124),
(251, NULL, NULL, 2, 124),
(252, NULL, NULL, 48, 124),
(253, NULL, NULL, 6, 125),
(254, NULL, NULL, 3, 125),
(255, NULL, NULL, 44, 125),
(256, NULL, NULL, 8, 127),
(257, NULL, NULL, 1, 127),
(258, NULL, NULL, 15, 128),
(259, NULL, NULL, 13, 128),
(260, NULL, NULL, 16, 128),
(261, NULL, NULL, 17, 129),
(262, NULL, NULL, 19, 129),
(263, NULL, NULL, 20, 129),
(264, NULL, NULL, 11, 130),
(265, NULL, NULL, 3, 134),
(266, NULL, NULL, 1, 134),
(267, NULL, NULL, 4, 135),
(268, NULL, NULL, 44, 135),
(269, NULL, NULL, 5, 135),
(270, NULL, NULL, 6, 135),
(271, NULL, NULL, 11, 136),
(272, NULL, NULL, 15, 136),
(273, NULL, NULL, 19, 136),
(274, NULL, NULL, 14, 136),
(275, NULL, NULL, 13, 136),
(276, NULL, NULL, 16, 136),
(277, NULL, NULL, 12, 136),
(278, NULL, NULL, 27, 138),
(279, NULL, NULL, 28, 138),
(280, NULL, NULL, 26, 138),
(281, NULL, NULL, 25, 138),
(282, NULL, NULL, 29, 138),
(283, NULL, NULL, 30, 138),
(284, NULL, NULL, 22, 139),
(285, NULL, NULL, 45, 139),
(286, NULL, NULL, 2, 139),
(287, NULL, NULL, 47, 139),
(288, NULL, NULL, 48, 139),
(289, NULL, NULL, 39, 139),
(290, NULL, NULL, 10, 139),
(291, NULL, NULL, 9, 139),
(292, NULL, NULL, 21, 139),
(293, NULL, NULL, 24, 141),
(294, NULL, NULL, 6, 140),
(295, NULL, NULL, 3, 140),
(296, NULL, NULL, 1, 140),
(297, NULL, NULL, 2, 142),
(298, NULL, NULL, 47, 142),
(299, NULL, NULL, 48, 142),
(300, NULL, NULL, 10, 142),
(301, NULL, NULL, 9, 142),
(302, NULL, NULL, 22, 142),
(303, NULL, NULL, 46, 142),
(304, NULL, NULL, 3, 143),
(305, NULL, NULL, 1, 143),
(306, NULL, NULL, 6, 144),
(307, NULL, NULL, 4, 144),
(308, NULL, NULL, 44, 144),
(309, NULL, NULL, 5, 144),
(310, NULL, NULL, 16, 145),
(311, NULL, NULL, 15, 145),
(312, NULL, NULL, 45, 147),
(313, NULL, NULL, 22, 147),
(324, NULL, NULL, 22, 158),
(325, NULL, NULL, 45, 158),
(326, NULL, NULL, 2, 158),
(327, NULL, NULL, 7, 159),
(328, NULL, NULL, 6, 159),
(329, NULL, NULL, 3, 159),
(330, NULL, NULL, 1, 159),
(331, NULL, NULL, 28, 165),
(332, NULL, NULL, 30, 165),
(333, NULL, NULL, 26, 165),
(334, NULL, NULL, 27, 165),
(335, NULL, NULL, 25, 165),
(336, NULL, NULL, 49, 165),
(337, NULL, NULL, 29, 165),
(338, NULL, NULL, 11, 166),
(339, NULL, NULL, 15, 166),
(340, NULL, NULL, 17, 166),
(341, NULL, NULL, 19, 166),
(342, NULL, NULL, 14, 166),
(343, NULL, NULL, 13, 166),
(344, NULL, NULL, 16, 166),
(345, NULL, NULL, 20, 166),
(346, NULL, NULL, 18, 166),
(347, NULL, NULL, 12, 166);

-- --------------------------------------------------------

--
-- Table structure for table `oil_changes`
--

CREATE TABLE `oil_changes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transport_id` bigint(20) UNSIGNED NOT NULL,
  `oil_change_date` date NOT NULL,
  `mileage_at_change` bigint(20) UNSIGNED NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `oil_type` varchar(255) DEFAULT NULL,
  `service_center` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `other_services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_services`)),
  `next_change_date` date DEFAULT NULL,
  `next_change_mileage` int(11) DEFAULT NULL,
  `oil_cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oil_changes`
--

INSERT INTO `oil_changes` (`id`, `transport_id`, `oil_change_date`, `mileage_at_change`, `cost`, `oil_type`, `service_center`, `notes`, `other_services`, `next_change_date`, `next_change_mileage`, `oil_cost`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-01-03', 10, 100.00, 'кастрол', 'гараж', NULL, '[{\"service_cost\": \"5\", \"service_name\": \"filtr\"}]', '2025-02-03', 10010, NULL, '2025-01-23 17:34:41', '2025-01-31 03:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `created_at`, `updated_at`, `name`, `address`, `phone`, `website`, `email`, `city_id`, `company_id`) VALUES
(2, '2025-01-11 05:26:29', '2025-01-11 05:57:13', 'Sim-Sim restaurant', '15-uy, Mukimi Street, 100115, Tashkent', '+998909439067', 'www.simsim.uz', 'sim@mail.ru', 2, NULL),
(3, '2025-01-11 06:20:23', '2025-07-30 17:22:01', 'Miramandi', 'Furqat ko\'chasi 10, 100027, Тоshkent,', '+998972640000', 'almandi.uz', 'Miramandi@tashkent.com', 2, 1),
(4, '2025-01-11 06:22:54', '2025-01-11 06:22:54', 'Beshqozon', ' Iftihor ko\'chasi 1, Тоshkent, Toshkent', '+998712009444', 'wwwbeshqozon.uz', 'Beshqozon@mail.ru', 2, NULL),
(5, '2025-01-13 10:15:04', '2025-01-13 10:15:04', 'Karimbek', ' Гагарин кўчаси 194, Samarqand, Samarqand viloyati', '+998662377739', 'wwwkarimbek.uz', 'karimbekon@mail.ru', 3, NULL),
(6, '2025-01-13 10:19:49', '2025-01-13 10:20:09', 'Ibrohim Bek', 'Muqimiy ko\'chasi, 100100, Тоshkent, Toshkent', '+998712539665', 'www.ibroximbek.uz', 'bekrestaurants@mail.ru', 2, NULL),
(7, '2025-01-13 10:25:36', '2025-01-13 10:25:36', 'Samarqand', 'MX32+R97, Samarqand, Samarqand viloyati', '+998907430405', 'www.Samarqandrest.uz', 'samarqandrest@mail.ru', 3, NULL),
(8, '2025-01-13 10:43:25', '2025-01-15 11:06:12', 'Terrassa  Cafe & Restaurant', 'Terrassa Cafe Khiva, Khiva 220900 Uzbekistan', '+998 91 993 91 11', 'www.terrassacafe.uz', 'terassacafe&restaurant@mail.ru', 5, NULL),
(9, '2025-01-13 10:43:27', '2025-01-13 10:43:27', 'Emirhan ', 'Махмуджанова 1/18 Самарканд Сиябский, 140100, Samarkand', '+998888916000', 'www.emirhan.uz', 'Emirhan@mail.ru', 3, NULL),
(10, '2025-01-13 10:52:15', '2025-01-13 10:52:15', 'Han Atlas', 'Mahmud Qoshgariy ko\'chasi 92, Samarqand, Samarqand viloyati', '+998662331831', 'www.xanatlas.uz', 'hanatlas@uzmail.ru', 3, NULL),
(11, '2025-01-13 12:26:02', '2025-01-13 12:26:36', 'Novvot', 'Matbuotchilar ko\'chasi 9, 100012, Тоshkent, Toshkent', '+998999686868', 'www.navvattashkent.uz', 'navvatrestauranttash@mail.ru', 2, NULL),
(12, '2025-01-13 12:46:21', '2025-01-13 12:46:21', 'Novvot Samarkand', 'Amir Temur ko\'chasi 145, Samarqand, Samarqand viloyati', '+998335251111', 'www.novvot.uz', 'navvatrestaurantsam@uzmail.ru', 3, NULL),
(13, '2025-01-13 13:03:25', '2025-01-13 13:03:25', 'Almaz', ' улица Катта Олмоз, Тоshkent, Toshkent Viloyati', '+998953410202', 'www.Almaz.uz', 'Almazuz@email.ru', 2, NULL),
(14, '2025-01-13 13:08:39', '2025-01-13 13:08:39', 'Crystal Garden restaurant', 'Амир Темур шоҳ кўчаси 15, 100000, Тоshkent, Toshkent', '+998909330888', 'www.crystalgarden.uz', 'crystal@garden.mail.ru', 2, NULL),
(15, '2025-01-14 11:31:23', '2025-01-14 11:31:23', 'Amulet', 'Bakhouddin Nakshbandi St., 152, Bukhara 200118 Uzbekistan', '+998 88 281 98 88', 'www.Amulet.uz', 'amulet@mail.ru', 4, NULL),
(16, '2025-01-14 11:37:31', '2025-01-14 11:37:31', 'JOY Chaikhana lounge', '2, Sarafon Street, Bukhara 200100 Uzbekistan', '+998881830200', 'www.joychaikhana.uz', 'joychaikhana.lounge@mail.ru', 4, NULL),
(17, '2025-01-14 11:41:00', '2025-01-14 11:41:00', 'Old Bukhara', 'Street Samarkand 3 Near the Hotel Asia, Bukhara 200100 Uzbekistan', '+998 90 185 70 77', 'www.oldbukhara.uz', 'old.bukhara@mail.ru', 4, NULL),
(18, '2025-01-14 11:45:11', '2025-01-14 11:45:11', 'Art Restaurant', 'Uzbekistan St. Opposite the Asia Bukhara Hotel, Bukhara Uzbekistan', '+998 65 224 41 13', 'www.artrestaurant.uz', 'art.restaurant@mail.ru', 4, NULL),
(19, '2025-01-14 11:51:12', '2025-01-14 11:51:12', 'Labi Hovuz', 'Mehtar Ambar St, Bukhara Uzbekistan', '+998 93 383 30 23', 'www.labihovuzrestaurant.uz', 'labihovuz.restaurant@mail.ru', 4, NULL),
(20, '2025-01-14 12:05:27', '2025-01-14 12:05:27', 'The Plov Bukhara', 'Bukhara 200100 Uzbekistan', '+998 93 143 07 77', 'www.plovbukhara.uz', 'theplovbukhara.uz@gmail.com', 4, NULL),
(21, '2025-01-14 12:18:39', '2025-01-14 12:18:39', 'Mavrigi', 'B. Nakshband, Bukhara 200124 Uzbekistan', '+998 90 612 88 08', 'www.mavrigirestbukhara.uz', 'mavrigi@restaurant.gmail.com', 4, NULL),
(22, '2025-01-15 11:34:24', '2025-01-15 11:34:24', 'Khorezm Art Restaurant', 'Madrasa Allah Kulikhan, Khiva 220900 Uzbekistan', '+998 95 333 69 74', 'www.khorezmartrest', 'khorezmart.rest@mail.ru', 5, NULL),
(23, '2025-01-16 11:13:43', '2025-01-16 11:13:43', 'Khan Chapan', 'Usta Olim tupik 5, 100019, Тоshkent, Toshkent', '+998712050020', ' khan-chapan.qr-menu.uz', 'KhanChapan@mail.ru', 2, NULL),
(24, '2025-01-17 12:28:20', '2025-01-17 12:28:20', 'Soy milliy taomlar', 'Avliyoota ko\'chasi 50, Тоshkent, Toshkent', '+998998788888', 'www.soymilliytaomlar.uz', 'soy@milliytaomlar.gmail.com', 2, NULL),
(25, '2025-01-17 12:37:00', '2025-01-17 12:37:00', 'Tandiriy', 'Ukchi ko\'chasi 5, 100011, Тоshkent, Toshkent', '+998881002255', 'wwwtandiriy.uz', 'tandiriyuz@mail.ru', 2, NULL),
(26, '2025-01-17 12:53:46', '2025-01-17 12:53:46', 'Amur', ' Самаркандская область, Самаркандская кольцевая дорога', '+998973943399', 'www.amur.uz', 'amur@mail.ru', 3, NULL),
(27, '2025-01-18 05:05:51', '2025-01-18 05:05:51', 'Cafe 1991', 'Mustakillik Street, Tashkent 100000 Uzbekistan', '+998909199100', 'https://www.facebook.com/1991cafe/', 'cafe1991@mail.ru', 2, NULL),
(28, '2025-01-18 05:13:12', '2025-01-18 05:13:12', 'Afsona ', 'Ul. T. Shevchenko, 30, Tashkent 100021 Uzbekistan', '+998712525681', 'http://www.facebook.com/afsonarestaurant/', 'afsona@mail.ru', 2, NULL),
(29, '2025-01-18 05:25:58', '2025-01-18 05:25:58', 'Caravan', 'Abdulla Kahhar Street 22, Tashkent Uzbekistan', '+998781506606', 'http://caravangroup.uz/', 'caravan@mail.ru', 2, NULL),
(30, '2025-01-18 05:39:42', '2025-01-18 05:39:42', 'Khiva Restaurant', '1 Navoiy Street Hyatt Regency Tashkent, Tashkent 100017 Uzbekistan', '+998712071311', 'http://www.hyattrestaurants.com/en/dining/uzbekistan/tashkent/restaurant-in-hyatt-regency-tashkent-khiva-restaurant', 'khivarestaurant@mail.ru', 2, NULL),
(31, '2025-01-18 05:47:04', '2025-01-18 05:47:04', 'Lali ', 'Massiv Kiyot, 57B, Tashkent 100017 Uzbekistan', '+998503335757', 'https://familygarden.su/lali/', 'lali@restaurant.mail.ru', 2, NULL),
(32, '2025-01-20 07:00:12', '2025-01-20 07:00:12', 'Old City', 'Abdurahman Street, Samarkand Uzbekistan', '+998662338020', 'www.oldcityrest.uz', 'oldcitysamrest@mail.ru', 3, NULL),
(33, '2025-01-20 07:04:59', '2025-01-20 07:04:59', 'Chapon Samarkand', 'Bazarova St., 14, Samarkand Uzbekistan', '+998952817007', 'http://www.instagram.com/chaponuz/', 'chapon.samarkand@mail.ru', 3, NULL),
(34, '2025-01-20 08:06:25', '2025-01-20 08:06:25', 'Florencia', 'Amira Temura St. 116, Samarkand 140100 Uzbekistan', '+998955000144', 'http://samflorencia.uz/', 'florencia@mail.ru', 3, NULL),
(35, '2025-01-20 08:37:25', '2025-01-20 08:37:25', 'Manhattan Restaurant', '32, Mironshox Str, Samarkand 140100 Uzbekistan', '+998992388888', 'http://www.instagram.com/manhattan_samarkand/', 'manhattan@mail.ru', 3, NULL),
(36, '2025-01-20 08:42:43', '2025-01-20 08:42:43', 'Zlata Praha Restaurant', 'Mirzo Mlugbeka 59, Samarkand 140164 Uzbekistan', '+998906561144', 'www.zlatapraharestaurant', 'zlatapraharestsam@mail.ru', 3, NULL),
(37, '2025-01-20 09:55:37', '2025-01-20 09:55:37', 'La Tambur', 'Ташкент, ул. Исмаилата, 11', '+998712089002', 'www.latambur.uz', 'latambur@mail.ru', 2, NULL),
(38, '2025-01-20 11:22:20', '2025-01-20 11:22:20', 'Zargaron', 'Tashkent Street 10, Samarkand 140100 Uzbekistan', '+998938109909', 'www.zargaron.uz', 'zargaron@mail.ru', 3, NULL),
(39, '2025-01-20 11:34:01', '2025-01-20 11:34:01', 'Afrosiyob', 'Самарканд, улица Академика Вохида Абдуллаева', '+998955099999', 'www.afrosiyob.uz', 'afrosiyobrestsam@mail.ru', 3, NULL),
(40, '2025-01-20 12:04:46', '2025-01-20 12:04:46', 'Oshqand', 'Zarafshon shokh St., 16, Samarkand 140100 Uzbekistan', '+998933500011', 'www.oshqand.uz', 'oshqandrestsam@mail.ru', 3, NULL),
(41, '2025-01-20 12:40:22', '2025-01-20 12:40:22', 'Saffron', '206 Samarkand Street, Bukhara 8875555 Uzbekistan', '+998888520500', 'www.saffron.uz', 'saffron@mail.ru', 4, NULL),
(42, '2025-01-21 10:27:21', '2025-01-21 10:27:21', 'Osh Markazi', 'Ipak Yoli st., Shahrisabz Uzbekistan', '+998916419555', 'www.oshmarkazi.uz', 'oshmarkazi@mail.ru', 6, NULL),
(43, '2025-01-21 10:32:06', '2025-01-21 10:32:06', 'Kesh Palace restaurant', 'Shahrisabz 181200 Uzbekistan', '+998955055551', 'www.keshpalace.uz', 'keshpalace@mail.ru', 6, NULL),
(44, '2025-01-21 10:57:24', '2025-01-21 10:57:24', 'Kish Mish', 'Fusunkor St., 11, Shahrisabz Uzbekistan', '+998989990770', 'www.kishmish.uz', 'kishmish@mail.ru', 6, NULL),
(45, '2025-01-21 11:42:30', '2025-01-21 11:42:30', 'Oasis Garden', 'Абдурахмон Жомий 59 A, Samarqand, Samarqand viloyati', '+998979262229', 'www.oasisgarden.uz', 'oasisgarden@mail.ru', 3, NULL),
(46, '2025-01-21 12:54:44', '2025-01-21 12:54:44', 'Ayvan', '1 Ориентир La Minor Karaoke, Samarkand, Samarqand Region', '+998970400444', 'www.ayvan.uz', 'ayvan@mail.ru', 3, NULL),
(47, '2025-01-22 12:52:02', '2025-01-22 12:52:02', 'Cafe Fresco', 'Самарканд, ул. Абу Рейхана Беруни, 144А', '+998902845555', 'www.fresco.uz', 'fresco@mail.ru', 3, NULL),
(48, '2025-01-24 09:57:05', '2025-01-24 09:57:05', 'Kapadokya', 'Q. DODXOX ko\'chasi, 2A-uy, 150100 Fergana kapadokya Fergana Kapadokya restsran, 150100', '+998882080033', 'https://www.instagram.com/kapadokya_uz/', 'kapadokya@mail.ru', 9, NULL),
(49, '2025-01-24 10:14:21', '2025-01-24 10:14:21', 'Terakzor', 'Бухара, ул. Мустакиллик, 40/7', '+998992215050', 'www.terakzor.uz', 'terakzor@mail.ru', 4, NULL),
(50, '2025-01-24 11:39:02', '2025-01-25 06:15:27', 'Timur\'s restaurant', ' Бухара, ул. Бахауддина Накшбанда, 2', '+998934770621', 'www.temurrestaurant.uz', 'temir\'srestaurant@mail.ru', 4, NULL),
(51, '2025-01-24 12:01:20', '2025-01-24 12:01:20', 'Dolon', 'Khakhikat Str. 27, Бухара Узбекистан', '+998902745366', 'http://www.facebook.com/pages/Trattoria-Ai-Colli-Storici/1795322243858833', 'Dolon@mail.ru', 4, NULL),
(52, '2025-01-24 12:41:32', '2025-01-24 12:41:32', 'Bolo Hovuz', ' Ташкент, ул. Лабзак, 12', '+998951770509', 'www.bolohovuzrest.uz', 'bolohovuz@mail.ru', 4, NULL),
(53, '2025-01-25 05:36:12', '2025-01-25 05:36:12', 'Chasmai Mirob', 'Бухара, 24', '+998904130760', 'www.chasmaimirob.uz', 'chasmaimirob@mail.ru', 4, NULL),
(54, '2025-01-25 06:31:53', '2025-01-25 06:31:53', 'Yasovulboshi', 'Хорезмская область, Хива, улица Заргарлар', '+998995969977', 'www.yasovulboshi.uz', 'yasovulboshi@mail.ru', 5, NULL),
(55, '2025-01-25 07:09:56', '2025-01-25 07:09:56', 'Mirzaboshi', 'Хорезмская область, Хива, махалля Ичан-Кала', '+998623752753', 'www.mirzaboshi.uz', 'mirzaboshi@mail.ru', 5, NULL),
(56, '2025-01-25 07:33:20', '2025-01-25 07:33:20', 'Zarafshon', 'Хива, ул. Ислам Ходжа, 44', '+998914349817', 'www.zarafshonrest.uz', 'zarafshonrestkhiva@mail.ru', 5, NULL),
(57, '2025-01-25 07:41:02', '2025-01-25 07:41:02', 'Mir', 'Ургенч, махаллинский сход граждан Гулзор, ул. Ал-Беруний, 71A', '+998907260026', 'www.mir.uz', 'mirrest@mail.ru', 17, NULL),
(58, '2025-01-25 07:49:11', '2025-01-25 07:49:11', 'Kish mish', 'Ташкент, Мирзо-Улугбекский район, ул. Хидирали Эргашева, 124', '+998954757300', 'www.kishmish.uz', 'kishmish@mail.ru', 2, NULL),
(59, '2025-01-25 07:52:51', '2025-01-25 07:52:51', 'Anjir', 'Ташкент, ул. Шота Руставели, 12', '+998712567711', 'www.anjir.uz', 'anjir@mail.ru', 2, NULL),
(60, '2025-01-25 07:56:25', '2025-01-25 07:56:25', 'Kuldja hogo', 'Ташкент, Сергелийский район, махалля Кумарик', '+998998078773', 'https://t.me/Gulja_xogo_uz', 'guljaxogo@mail.ru', 2, NULL),
(61, '2025-01-27 12:58:48', '2025-01-27 12:58:48', 'Belissimo', 'Ургенч, ул. Абульгази Бахадырхана, 80', '+998906480333', 'www.belissimo.urgench', 'belissimourgench@mail.ru', 17, NULL),
(62, '2025-01-29 13:13:59', '2025-01-29 13:13:59', 'Yurta', 'Yangigazgan', '+998885480080', 'camping.uz', 'info@sss-tour.com', 19, NULL),
(63, '2025-01-29 13:15:40', '2025-01-29 13:15:40', 'Aydarkul', 'Kushquduq', '+998885480080', 'camping.uz', 'info@sss-tour.com', 12, NULL),
(64, '2025-01-29 13:22:01', '2025-01-29 13:23:58', 'Nats dom', 'Nurata', '+998885480080', 'camping.uz', 'info@sss-tour.com', 12, NULL),
(65, '2025-01-30 04:50:48', '2025-01-30 04:50:48', 'Shirin Tabaka', ' beruniy kochas, Samarqand, Samarqand viloyati', '90 600 00 40', 'no ', 'shirintabaka@gmail.com', 3, NULL),
(66, '2025-01-30 04:54:47', '2025-01-30 04:54:47', 'Шахноза натциональный дом', 'Самарканд,', '979152444', 'no ', 'shaxnoza@gmail.com', 3, NULL),
(67, '2025-01-31 11:21:33', '2025-01-31 11:21:33', 'Xo\'ja Nasriddin', 'B.Naqshband, Bukhara Region', '+998934783595', 'www.xojanasriddin.uz', 'xojanasriddin@mail.ru', 27, NULL),
(68, '2025-01-31 11:36:30', '2025-01-31 11:36:30', 'Nodir Kafe', 'А.Каххар ул. Дом 60/1, 200500, Gijduvon', '+998912435595', 'www.nodirkafe.uz', 'nodirkafe@mail.ru', 27, NULL),
(70, '2025-02-24 12:51:54', '2025-02-24 12:51:54', 'Platan', 'Pushkin St, 2, Samarkand 140100 Uzbekistan', '+998 91 555 88 88', 'https://www.tripadvisor.com/Restaurant_Review-g298068-d1128274-Reviews-Platan-Samarkand_Samarqand_Province.html', 'platan@mail.ru', 3, NULL),
(71, '2025-03-07 10:28:26', '2025-03-07 10:28:26', 'Uchminor osh markazi', ' Самарканд, махаллинский сход граждан Богимайдон', '+998 91 030 83 83', 'https://www.instagram.com/uchminor?igsh=MW56NnZqZTIxcXp3dg==', 'uchminorosh@gmail.com', 3, NULL),
(72, '2025-09-26 03:37:19', '2025-09-26 03:37:19', 'Mahmudjon', 'khiva', '98 577 89 88', 'ww.ggo.com', 'ok@ok.com', 5, 1),
(73, '2025-09-26 03:38:07', '2025-09-26 03:38:07', 'Marina', 'Bukhara', '99 942 00 22', 'ww.fr.com', 'ok@ok.com', 4, 1),
(74, '2025-09-26 03:39:19', '2025-09-26 03:39:19', 'Marina', 'TAshkent', '99 942 00 22', 'ww.gt.com', 'ok@ok.com', 2, 1),
(75, '2025-09-26 03:40:56', '2025-09-26 03:40:56', 'Zahratun', 'between khiva bukhara', '78 888 77 88', 'ww.fg.com', 'ok@ok.com', 32, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost_per_night` decimal(10,2) NOT NULL DEFAULT 0.00,
  `room_size` int(11) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `created_at`, `updated_at`, `hotel_id`, `room_type_id`, `name`, `description`, `cost_per_night`, `room_size`, `images`) VALUES
(2, '2025-01-11 05:20:40', '2025-01-21 11:31:45', 2, 1, NULL, NULL, 62.00, NULL, '[]'),
(3, '2025-01-11 05:21:45', '2025-01-24 10:08:16', 2, 2, NULL, NULL, 50.00, NULL, '[\"01JJBWH96W17YSWF074H0KHND0.jpg\"]'),
(4, '2025-01-11 06:00:36', '2025-01-11 06:00:36', 3, 5, NULL, NULL, 85.00, NULL, '[\"01JH9Z6DYC7W407YZXP7WQA8H7.jpg\"]'),
(5, '2025-01-11 06:00:36', '2025-01-21 11:32:13', 3, 1, NULL, NULL, 97.00, NULL, '[\"01JH9Z6DYEZQ03YD09DN21TMXN.jpg\"]'),
(6, '2025-01-11 06:16:23', '2025-01-11 06:16:23', 4, 5, NULL, NULL, 45.00, NULL, '[\"01JHA03AT0FFXREPYP0536Z5CS.jpg\"]'),
(7, '2025-01-11 06:16:23', '2025-01-11 06:16:23', 4, 1, NULL, NULL, 50.00, NULL, '[\"01JHA03AT216H6G0Y6MSSK67KQ.jpg\"]'),
(8, '2025-01-13 07:13:24', '2025-01-24 10:21:14', 5, 5, NULL, NULL, 70.00, NULL, '[\"01JJBX912H2W1WFMMSZXA46A7B.jpg\"]'),
(9, '2025-01-13 09:40:48', '2025-09-26 03:33:45', 6, 5, NULL, NULL, 45.00, 20, '[]'),
(10, '2025-01-13 09:40:48', '2025-09-26 03:33:45', 6, 1, NULL, NULL, 55.00, 20, '[]'),
(11, '2025-01-13 09:52:00', '2025-01-13 09:52:00', 7, 5, NULL, NULL, 54.00, NULL, '[\"01JHFH7K8BVG2PW890WBSP032T.jpg\"]'),
(12, '2025-01-13 09:52:00', '2025-01-13 09:52:00', 7, 1, NULL, NULL, 70.00, NULL, '[\"01JHFH7K8CRDYVXFTJJXZND1F8.jpg\"]'),
(13, '2025-01-13 11:03:38', '2025-01-13 11:03:38', 8, 5, NULL, NULL, 70.00, NULL, '[]'),
(14, '2025-01-13 11:03:38', '2025-01-13 11:03:38', 8, 1, NULL, NULL, 65.00, NULL, '[]'),
(15, '2025-01-13 11:09:04', '2025-01-13 11:09:04', 9, 5, NULL, NULL, 61.00, NULL, '[]'),
(16, '2025-01-13 11:09:04', '2025-01-13 11:09:04', 9, 1, NULL, NULL, 77.00, NULL, '[]'),
(17, '2025-01-13 11:17:20', '2025-03-03 10:27:12', 10, 5, NULL, NULL, 65.00, NULL, '[]'),
(18, '2025-01-13 11:17:20', '2025-03-03 10:27:12', 10, 1, NULL, NULL, 85.00, NULL, '[\"01JJBXBEA0ETXVWT2VVY5DGY43.jpg\"]'),
(19, '2025-01-13 11:50:50', '2025-03-03 10:44:38', 11, 5, NULL, NULL, 118.00, NULL, '[\"01JJBXES3XZZZAFHNNJFJ3GCCE.jpeg\"]'),
(20, '2025-01-13 12:05:38', '2025-01-21 11:58:06', 12, 5, NULL, NULL, 135.00, NULL, '[]'),
(21, '2025-01-13 13:09:02', '2025-03-03 10:38:35', 13, 5, NULL, NULL, 50.00, NULL, '[]'),
(22, '2025-01-13 13:09:02', '2025-03-03 10:38:35', 13, 1, NULL, NULL, 78.00, NULL, '[]'),
(23, '2025-01-13 13:13:07', '2025-01-21 12:06:12', 14, 5, NULL, NULL, 28.00, NULL, '[]'),
(24, '2025-01-13 13:13:07', '2025-01-24 10:29:42', 14, 1, NULL, NULL, 47.00, NULL, '[\"01JJBXRGV9VD5BQ7VMX71DX3BN.jpg\"]'),
(25, '2025-01-14 07:22:28', '2025-01-21 12:07:07', 15, 5, NULL, NULL, 66.00, NULL, '[]'),
(26, '2025-01-14 07:22:28', '2025-01-21 12:07:07', 15, 1, NULL, NULL, 78.00, NULL, '[]'),
(27, '2025-01-14 08:12:14', '2025-01-21 12:08:17', 16, 5, NULL, NULL, 66.00, NULL, '[]'),
(28, '2025-01-14 08:13:03', '2025-01-21 12:08:17', 16, 1, NULL, NULL, 77.00, NULL, '[]'),
(29, '2025-01-14 08:16:43', '2025-01-21 12:12:11', 17, 5, NULL, NULL, 62.00, NULL, '[]'),
(30, '2025-01-14 08:16:43', '2025-01-21 12:12:11', 17, 1, NULL, NULL, 85.00, NULL, '[]'),
(31, '2025-01-14 10:04:52', '2025-01-22 05:10:18', 18, 5, NULL, NULL, 85.00, NULL, '[]'),
(32, '2025-01-14 10:04:52', '2025-01-22 05:10:18', 18, 1, NULL, NULL, 100.00, NULL, '[]'),
(33, '2025-01-14 10:14:05', '2025-01-22 04:17:59', 19, 5, NULL, '3$', 23.00, NULL, '[]'),
(34, '2025-01-14 10:14:05', '2025-01-22 04:17:59', 19, 1, NULL, '+3$', 30.00, NULL, '[]'),
(35, '2025-01-14 10:29:27', '2025-01-21 12:20:47', 20, 5, NULL, NULL, 55.00, NULL, '[]'),
(36, '2025-01-14 10:29:27', '2025-01-21 12:20:47', 20, 1, NULL, NULL, 62.00, NULL, '[]'),
(37, '2025-01-14 10:40:03', '2025-01-22 04:19:37', 21, 5, NULL, NULL, 70.00, NULL, '[]'),
(38, '2025-01-14 10:40:03', '2025-01-22 04:19:37', 21, 1, NULL, NULL, 82.00, NULL, '[]'),
(39, '2025-01-14 11:06:55', '2025-01-22 04:23:23', 22, 6, NULL, NULL, 155.00, NULL, '[]'),
(40, '2025-01-14 11:06:55', '2025-01-21 12:19:57', 22, 1, NULL, NULL, 193.00, NULL, '[]'),
(41, '2025-01-14 11:14:28', '2025-01-14 11:14:28', 23, 5, NULL, NULL, 40.00, NULL, '[]'),
(42, '2025-01-14 11:14:28', '2025-01-23 09:56:12', 23, 1, NULL, NULL, 47.00, NULL, '[]'),
(43, '2025-01-14 11:19:38', '2025-01-21 12:38:34', 24, 5, NULL, NULL, 35.00, NULL, '[]'),
(44, '2025-01-14 11:19:38', '2025-01-21 12:38:34', 24, 1, NULL, NULL, 55.00, NULL, '[]'),
(45, '2025-01-14 11:25:27', '2025-01-21 12:45:29', 25, 5, NULL, NULL, 38.00, NULL, '[]'),
(46, '2025-01-14 11:25:27', '2025-01-21 12:45:29', 25, 1, NULL, NULL, 55.00, NULL, '[]'),
(47, '2025-01-14 11:28:38', '2025-01-14 11:28:38', 26, 5, NULL, NULL, 75.00, NULL, '[]'),
(48, '2025-01-14 11:28:38', '2025-01-24 10:40:38', 26, 1, NULL, NULL, 85.00, NULL, '[\"01JJBYCHCFC5HQJ1E35NCF6PN6.jpg\"]'),
(49, '2025-01-14 11:33:35', '2025-01-14 11:33:35', 27, 5, NULL, NULL, 58.00, NULL, '[]'),
(50, '2025-01-14 11:33:35', '2025-01-14 11:33:35', 27, 1, NULL, NULL, 76.00, NULL, '[]'),
(51, '2025-01-14 11:38:30', '2025-01-24 10:52:06', 28, 5, NULL, NULL, 35.00, NULL, '[\"01JJBZ1H0VYPQTQ3K5PRD9Q12R.jpg\"]'),
(52, '2025-01-14 11:38:30', '2025-01-24 10:52:06', 28, 1, NULL, NULL, 60.00, NULL, '[\"01JJBZ1H0X7DRSRG2R5THT9JE5.jpg\"]'),
(53, '2025-01-14 11:41:33', '2025-01-21 12:56:43', 29, 5, NULL, NULL, 60.00, NULL, '[]'),
(54, '2025-01-14 11:41:33', '2025-01-21 12:56:43', 29, 1, NULL, NULL, 70.00, NULL, '[]'),
(55, '2025-01-14 11:44:30', '2025-01-14 11:44:30', 30, 5, NULL, NULL, 45.00, NULL, '[]'),
(56, '2025-01-14 11:44:30', '2025-01-14 11:44:30', 30, 1, NULL, NULL, 50.00, NULL, '[]'),
(57, '2025-01-14 11:52:26', '2025-01-22 04:36:28', 31, 5, NULL, NULL, 77.00, NULL, '[]'),
(58, '2025-01-14 11:52:26', '2025-01-22 04:36:28', 31, 1, NULL, NULL, 85.00, NULL, '[]'),
(59, '2025-01-14 11:56:27', '2025-01-22 04:37:58', 32, 5, NULL, NULL, 65.00, NULL, '[]'),
(60, '2025-01-14 11:56:27', '2025-01-22 04:37:58', 32, 1, NULL, NULL, 85.00, NULL, '[]'),
(61, '2025-01-14 12:06:13', '2025-01-22 04:38:55', 33, 5, NULL, NULL, 43.00, NULL, '[]'),
(62, '2025-01-14 12:06:13', '2025-01-22 04:38:55', 33, 1, NULL, NULL, 58.00, NULL, '[]'),
(63, '2025-01-14 12:20:16', '2025-01-22 04:39:48', 34, 5, NULL, NULL, 55.00, NULL, '[]'),
(64, '2025-01-14 12:20:16', '2025-01-22 04:39:48', 34, 1, NULL, NULL, 70.00, NULL, '[]'),
(65, '2025-01-14 12:29:59', '2025-01-22 04:41:52', 35, 5, NULL, NULL, 81.00, NULL, '[]'),
(66, '2025-01-14 12:29:59', '2025-01-22 04:41:52', 35, 1, NULL, NULL, 91.00, NULL, '[]'),
(67, '2025-01-14 12:34:33', '2025-01-22 04:42:43', 36, 5, NULL, NULL, 58.00, NULL, '[]'),
(68, '2025-01-14 12:34:34', '2025-01-22 04:42:43', 36, 1, NULL, NULL, 70.00, NULL, '[]'),
(69, '2025-01-14 12:37:59', '2025-01-22 04:44:27', 37, 5, NULL, NULL, 132.00, NULL, '[]'),
(70, '2025-01-14 12:37:59', '2025-01-22 04:44:27', 37, 1, NULL, NULL, 147.00, NULL, '[]'),
(71, '2025-01-14 12:40:29', '2025-01-22 04:51:58', 38, 5, NULL, NULL, 50.00, NULL, '[]'),
(72, '2025-01-14 12:40:29', '2025-01-22 04:51:58', 38, 1, NULL, NULL, 64.00, NULL, '[]'),
(73, '2025-01-14 12:42:57', '2025-01-22 04:53:53', 39, 5, NULL, NULL, 44.00, NULL, '[]'),
(74, '2025-01-14 12:42:57', '2025-01-22 04:53:53', 39, 1, NULL, NULL, 78.00, NULL, '[]'),
(75, '2025-01-14 12:47:48', '2025-01-22 04:54:46', 40, 5, NULL, NULL, 39.00, NULL, '[]'),
(76, '2025-01-14 12:47:48', '2025-01-22 04:54:46', 40, 1, NULL, NULL, 55.00, NULL, '[]'),
(77, '2025-01-15 04:42:54', '2025-01-22 04:56:34', 41, 5, NULL, NULL, 47.00, NULL, '[]'),
(78, '2025-01-15 04:42:54', '2025-01-22 04:56:34', 41, 1, NULL, NULL, 58.00, NULL, '[]'),
(79, '2025-01-15 04:47:25', '2025-01-15 04:47:25', 42, 5, NULL, NULL, 400.00, NULL, '[]'),
(80, '2025-01-15 04:47:25', '2025-01-15 04:47:25', 42, 1, NULL, NULL, 600.00, NULL, '[]'),
(81, '2025-01-15 04:51:13', '2025-01-15 04:51:13', 43, 5, NULL, NULL, 700.00, NULL, '[]'),
(82, '2025-01-15 04:51:13', '2025-01-15 04:51:13', 43, 1, NULL, NULL, 970.00, NULL, '[]'),
(83, '2025-01-15 04:54:15', '2025-01-15 04:54:15', 44, 5, NULL, NULL, 570.00, NULL, '[]'),
(84, '2025-01-15 04:54:15', '2025-01-15 04:54:15', 44, 1, NULL, NULL, 665.00, NULL, '[]'),
(85, '2025-01-15 04:57:32', '2025-01-15 04:57:32', 45, 5, NULL, NULL, 700.00, NULL, '[]'),
(86, '2025-01-15 04:57:32', '2025-01-15 04:57:32', 45, 1, NULL, NULL, 800.00, NULL, '[]'),
(87, '2025-01-15 05:01:31', '2025-01-15 05:01:31', 46, 5, NULL, NULL, 580.00, NULL, '[]'),
(88, '2025-01-15 05:01:31', '2025-01-15 05:01:31', 46, 1, NULL, NULL, 850.00, NULL, '[]'),
(89, '2025-01-15 05:04:50', '2025-01-22 04:59:58', 47, 5, NULL, NULL, 31.00, NULL, '[]'),
(90, '2025-01-15 05:04:50', '2025-01-22 04:59:58', 47, 1, NULL, NULL, 47.00, NULL, '[]'),
(91, '2025-01-16 09:56:18', '2025-01-22 05:00:54', 48, 5, NULL, NULL, 35.00, NULL, '[]'),
(92, '2025-01-16 09:56:18', '2025-01-22 05:00:54', 48, 1, NULL, NULL, 50.00, NULL, '[]'),
(93, '2025-01-16 10:06:59', '2025-01-22 05:02:03', 49, 5, NULL, NULL, 75.00, NULL, '[]'),
(94, '2025-01-16 10:06:59', '2025-01-22 05:02:03', 49, 1, NULL, NULL, 100.00, NULL, '[]'),
(95, '2025-01-16 10:09:45', '2025-01-22 05:03:00', 50, 5, NULL, NULL, 57.00, NULL, '[]'),
(96, '2025-01-16 10:09:45', '2025-01-22 05:03:00', 50, 1, NULL, NULL, 77.00, NULL, '[]'),
(97, '2025-01-16 10:12:49', '2025-01-22 05:04:02', 51, 5, NULL, NULL, 47.00, NULL, '[]'),
(98, '2025-01-16 10:12:49', '2025-01-22 05:04:02', 51, 1, NULL, NULL, 70.00, NULL, '[]'),
(99, '2025-01-16 10:16:17', '2025-02-25 12:24:58', 52, 5, NULL, NULL, 95.00, NULL, '[]'),
(100, '2025-01-16 10:16:17', '2025-02-25 12:24:58', 52, 1, NULL, NULL, 112.00, NULL, '[]'),
(101, '2025-01-16 10:22:28', '2025-01-22 05:05:51', 53, 5, NULL, NULL, 110.00, NULL, '[]'),
(102, '2025-01-16 10:22:28', '2025-01-22 05:05:51', 53, 1, NULL, NULL, 124.00, NULL, '[]'),
(103, '2025-01-16 10:28:16', '2025-01-22 05:07:05', 54, 5, NULL, NULL, 30.00, NULL, '[]'),
(104, '2025-01-16 10:28:16', '2025-01-22 05:07:05', 54, 1, NULL, NULL, 52.00, NULL, '[]'),
(105, '2025-01-16 10:32:47', '2025-01-22 05:08:25', 55, 5, NULL, NULL, 47.00, NULL, '[]'),
(106, '2025-01-16 10:32:47', '2025-01-22 05:08:25', 55, 1, NULL, NULL, 62.00, NULL, '[]'),
(107, '2025-01-18 04:48:01', '2025-01-18 04:48:01', 56, 5, NULL, NULL, 0.00, NULL, '[]'),
(108, '2025-01-18 04:52:23', '2025-01-21 11:31:04', 57, 5, NULL, NULL, 40.00, NULL, '[]'),
(109, '2025-01-18 04:55:33', '2025-01-18 06:04:29', 58, 1, NULL, 'zavtrak nds tur sbor kiritilmagan', 150.00, NULL, '[]'),
(111, '2025-01-18 05:04:07', '2025-01-18 05:04:07', 60, 1, NULL, NULL, 0.00, NULL, '[]'),
(112, '2025-01-18 05:15:01', '2025-01-21 04:35:00', 61, 5, NULL, NULL, 280.00, NULL, '[]'),
(113, '2025-01-18 05:23:09', '2025-01-18 05:23:09', 62, 5, NULL, NULL, 0.00, NULL, '[]'),
(114, '2025-01-18 05:27:09', '2025-01-18 05:27:09', 63, 5, NULL, NULL, 0.00, NULL, '[]'),
(115, '2025-01-18 05:30:09', '2025-01-18 05:30:09', 64, 5, NULL, NULL, 0.00, NULL, '[]'),
(116, '2025-01-18 05:53:36', '2025-01-21 11:31:04', 57, 1, NULL, NULL, 55.00, NULL, '[]'),
(117, '2025-01-21 04:35:00', '2025-01-21 04:35:00', 61, 1, NULL, NULL, 400.00, NULL, '[]'),
(118, '2025-01-21 11:58:06', '2025-01-24 10:32:12', 12, 1, NULL, NULL, 170.00, NULL, '[\"01JJBXX3GTWXN593W596EG7MHV.jpg\"]'),
(119, '2025-01-22 10:47:41', '2025-01-22 10:47:41', 65, 5, NULL, NULL, 82.00, NULL, '[]'),
(120, '2025-01-22 10:47:41', '2025-01-22 10:47:41', 65, 1, NULL, NULL, 90.00, NULL, '[]'),
(121, '2025-01-22 11:30:32', '2025-01-22 11:30:32', 66, 5, NULL, NULL, 154.00, NULL, '[]'),
(122, '2025-01-22 11:30:32', '2025-01-22 11:30:32', 66, 1, NULL, NULL, 193.00, NULL, '[]'),
(123, '2025-01-22 11:47:53', '2025-01-22 11:47:53', 67, 5, NULL, NULL, 50.00, NULL, '[]'),
(124, '2025-01-22 11:47:53', '2025-01-22 11:47:53', 67, 1, NULL, NULL, 90.00, NULL, '[]'),
(125, '2025-01-22 11:56:01', '2025-01-22 11:56:01', 68, 5, NULL, NULL, 83.00, NULL, '[]'),
(126, '2025-01-22 11:56:01', '2025-01-22 11:56:01', 68, 1, NULL, NULL, 91.00, NULL, '[]'),
(127, '2025-01-22 12:03:41', '2025-01-22 12:03:41', 69, 5, NULL, NULL, 124.00, NULL, '[]'),
(128, '2025-01-22 12:03:41', '2025-01-22 12:03:41', 69, 1, NULL, NULL, 130.00, NULL, '[]'),
(129, '2025-01-23 11:52:35', '2025-01-23 11:52:35', 70, 5, NULL, NULL, 30.00, NULL, '[]'),
(130, '2025-01-23 11:52:35', '2025-01-23 11:52:35', 70, 1, NULL, NULL, 40.00, NULL, '[]'),
(131, '2025-01-24 04:49:22', '2025-01-24 04:49:22', 71, 5, NULL, NULL, 38.00, NULL, '[]'),
(132, '2025-01-24 04:49:22', '2025-01-24 04:49:22', 71, 1, NULL, NULL, 54.00, NULL, '[]'),
(133, '2025-01-24 04:54:27', '2025-01-24 04:54:27', 72, 5, NULL, NULL, 61.00, NULL, '[]'),
(134, '2025-01-24 04:54:27', '2025-01-24 04:54:27', 72, 1, NULL, NULL, 81.00, NULL, '[]'),
(135, '2025-01-24 05:08:49', '2025-01-24 05:08:49', 73, 5, NULL, NULL, 31.00, NULL, '[]'),
(136, '2025-01-24 05:08:49', '2025-01-24 05:08:49', 73, 1, NULL, NULL, 47.00, NULL, '[]'),
(137, '2025-01-24 05:12:45', '2025-01-24 05:12:45', 74, 5, NULL, NULL, 54.00, NULL, '[]'),
(138, '2025-01-24 05:12:45', '2025-01-24 05:12:45', 74, 1, NULL, NULL, 61.00, NULL, '[]'),
(139, '2025-01-24 05:34:36', '2025-01-24 05:34:36', 75, 5, NULL, NULL, 43.00, NULL, '[]'),
(140, '2025-01-24 05:34:36', '2025-01-24 05:34:36', 75, 1, NULL, NULL, 61.00, NULL, '[]'),
(141, '2025-01-24 05:38:09', '2025-01-24 05:38:09', 76, 5, NULL, NULL, 43.00, NULL, '[]'),
(142, '2025-01-24 05:38:09', '2025-01-24 05:38:09', 76, 1, NULL, NULL, 61.00, NULL, '[]'),
(143, '2025-01-24 05:43:23', '2025-01-24 05:43:23', 77, 5, NULL, NULL, 5.00, NULL, '[]'),
(144, '2025-01-24 05:43:23', '2025-01-24 05:43:23', 77, 1, NULL, NULL, 66.00, NULL, '[]'),
(145, '2025-01-24 05:50:08', '2025-01-24 05:50:08', 78, 5, NULL, NULL, 54.00, NULL, '[]'),
(146, '2025-01-24 05:50:08', '2025-01-24 05:50:08', 78, 1, NULL, NULL, 70.00, NULL, '[]'),
(147, '2025-01-24 07:07:40', '2025-01-24 07:07:40', 79, 5, NULL, NULL, 35.00, NULL, '[]'),
(148, '2025-01-24 07:07:40', '2025-01-24 07:07:40', 79, 1, NULL, NULL, 50.00, NULL, '[]'),
(149, '2025-01-24 09:59:50', '2025-01-24 09:59:50', 80, 5, NULL, NULL, 43.00, NULL, '[]'),
(150, '2025-01-24 09:59:50', '2025-01-24 09:59:50', 80, 1, NULL, NULL, 77.00, NULL, '[]'),
(151, '2025-01-29 13:07:20', '2025-01-29 13:07:20', 81, 7, NULL, NULL, 80.00, NULL, '[]'),
(152, '2025-01-29 13:07:20', '2025-01-29 13:07:20', 81, 8, NULL, NULL, 100.00, NULL, '[]'),
(153, '2025-01-29 13:07:20', '2025-01-29 13:07:20', 81, 9, NULL, NULL, 120.00, NULL, '[]'),
(154, '2025-01-30 04:32:37', '2025-01-30 04:32:37', 82, 5, NULL, NULL, 70.00, NULL, '[]'),
(155, '2025-01-30 04:32:37', '2025-01-30 04:32:37', 82, 1, NULL, NULL, 80.00, NULL, '[]'),
(156, '2025-01-30 04:34:50', '2025-01-30 04:34:50', 83, 5, NULL, NULL, 100.00, NULL, '[]'),
(157, '2025-01-30 04:34:50', '2025-01-30 04:34:50', 83, 6, NULL, NULL, 120.00, NULL, '[]'),
(158, '2025-01-30 05:14:18', '2025-01-30 05:14:18', 84, 5, NULL, NULL, 31.00, NULL, '[]'),
(159, '2025-01-30 05:14:18', '2025-01-30 05:14:18', 84, 1, NULL, NULL, 47.00, NULL, '[]'),
(160, '2025-01-30 05:18:36', '2025-01-30 05:18:36', 85, 5, NULL, NULL, 38.00, NULL, '[]'),
(161, '2025-01-30 05:18:36', '2025-01-30 05:18:36', 85, 1, NULL, NULL, 48.00, NULL, '[]'),
(162, '2025-01-30 11:03:17', '2025-01-30 11:03:17', 86, 1, NULL, NULL, 80.00, NULL, '[]'),
(163, '2025-01-30 11:03:17', '2025-01-30 11:03:17', 86, 5, NULL, NULL, 40.00, NULL, '[]'),
(164, '2025-01-30 11:07:41', '2025-01-30 11:07:41', 87, 5, NULL, NULL, 30.00, NULL, '[]'),
(165, '2025-01-30 11:07:41', '2025-01-30 11:07:41', 87, 1, NULL, NULL, 65.00, NULL, '[]'),
(166, '2025-01-30 11:10:09', '2025-01-30 11:10:09', 88, 5, NULL, NULL, 30.00, NULL, '[]'),
(167, '2025-01-30 11:10:09', '2025-01-30 11:10:09', 88, 1, NULL, NULL, 60.00, NULL, '[]'),
(168, '2025-01-30 11:43:28', '2025-01-30 11:43:28', 89, 1, NULL, NULL, 60.00, NULL, '[]'),
(169, '2025-01-30 11:43:28', '2025-01-30 11:43:28', 89, 5, NULL, NULL, 45.00, NULL, '[]'),
(170, '2025-01-30 11:56:22', '2025-01-30 11:56:22', 90, 5, NULL, NULL, 30.00, NULL, '[]'),
(171, '2025-01-30 11:57:30', '2025-01-30 11:57:30', 90, 1, NULL, NULL, 60.00, NULL, '[]'),
(172, '2025-02-19 08:07:24', '2025-02-20 10:21:05', 91, 5, NULL, 'Single odnomestniy', 60.00, NULL, '[]'),
(173, '2025-02-19 08:07:24', '2025-02-20 10:21:05', 91, 1, NULL, 'Twin dvuxmestniy.', 70.00, NULL, '[]'),
(174, '2025-02-20 04:54:13', '2025-02-20 04:54:13', 92, 5, NULL, NULL, 66.00, NULL, '[]'),
(175, '2025-02-20 04:54:13', '2025-02-20 04:54:13', 92, 1, NULL, NULL, 85.00, NULL, '[]'),
(176, '2025-02-20 04:58:21', '2025-02-20 04:58:21', 93, 5, NULL, NULL, 38.00, NULL, '[]'),
(177, '2025-02-20 04:58:21', '2025-02-20 04:58:21', 93, 1, NULL, NULL, 62.00, NULL, '[]'),
(178, '2025-02-20 05:01:45', '2025-02-20 05:01:45', 94, 5, NULL, 'bez tur sbor', 142.00, NULL, '[]'),
(179, '2025-02-20 05:01:45', '2025-02-20 05:01:45', 94, 1, NULL, NULL, 163.00, NULL, '[]'),
(180, '2025-03-03 10:44:38', '2025-03-03 10:44:38', 11, 1, NULL, NULL, 140.00, NULL, '[]'),
(181, '2025-03-03 12:40:02', '2025-03-03 12:40:02', 95, 5, NULL, NULL, 148.00, NULL, '[]'),
(182, '2025-03-03 12:40:02', '2025-03-03 12:40:02', 95, 1, NULL, NULL, 171.00, NULL, '[]'),
(183, '2025-06-07 16:34:00', '2025-06-07 16:34:00', 7, 6, 'Twin', 'good room', 60.00, NULL, '[]'),
(184, '2025-06-07 16:36:32', '2025-06-07 16:36:47', 45, 6, 'Twin', NULL, 60.00, NULL, '[]'),
(185, '2025-06-08 12:50:01', '2025-06-08 12:55:51', 96, 6, NULL, '23 m²\nCity view\nAir conditioning\nPrivate bathroom\nFlat-screen TV\nFree WiFi\nRoom size 23 m²\n2 single beds\nComfy beds, 8.8 – Based on 2 reviews\nThe twin room provides air conditioning, a wardrobe, as well as a private bathroom featuring a bath and a shower. The unit offers 2 beds.\nIn your private bathroom:\nToilet\nBath or shower\nTowels\nSlippers\nHairdryer\nToilet paper\nView:\nCity view\nFacilities:\nAir conditioning\nLinen\nDesk\nTV\nTelephone\nHeating\nFlat-screen TV\nCarpeted\nElectric kettle\nWake-up service\nWardrobe or closet\nClothes rack\nDrying rack for clothing\nAir purifiers', 700000.00, 23, '[\"01JX7SEFVD5MMDX8JAYCVRCZYR.jpg\",\"01JX7SEFVFGQGWH303PTCFR9CX.jpg\",\"01JX7SEFVHTZ1APGN54B6TF10K.jpg\",\"01JX7SEFVRNP8HSF8BZ5NFWMZV.jpg\"]'),
(186, '2025-06-08 12:50:02', '2025-06-08 12:55:51', 96, 10, NULL, '23 m²City viewAir conditioningPrivate bathroomFlat-screen TVFree WiFi\nToilet Bath or shower Towels Linen Desk TV Slippers Telephone Heating Hairdryer Carpeted Electric kettle Wake-up service Wardrobe or closet Clothes rack Drying rack for clothing Toilet paper Air purifiers', 700000.00, 23, '[\"01JX7SEFVZBADBYR0098HQCRA0.jpg\",\"01JX7SEFW0KF18FB1AJ4HR0AGE.jpg\",\"01JX7SEFW25HDY46SS1QS9RN5Y.jpg\"]'),
(187, '2025-06-08 12:50:02', '2025-06-08 12:55:51', 96, 5, NULL, '17 m²City viewAir conditioningPrivate bathroomFlat-screen TVFree WiFi\nToilet Bath or shower Towels Linen Desk TV Slippers Telephone Heating Hairdryer Carpeted Electric kettle Wake-up service Wardrobe or closet Clothes rack Drying rack for clothing Toilet paper Air purifiers', 500000.00, 17, '[\"01JX7SEFW71NPEWZ0K8W4M8WYM.jpg\",\"01JX7SEFWAJN66E36PFZNAB3GF.jpg\",\"01JX7SEFWD40Y5ZM9SPE2N1DAW.jpg\"]'),
(188, '2025-06-09 17:56:04', '2025-06-09 17:56:04', 97, 6, NULL, 'Offering free toiletries and bathrobes, this twin room includes a private bathroom with a bath, a shower and a hairdryer. The air-conditioned twin room offers a flat-screen TV, a private entrance, soundproof walls, a tea and coffee maker as well as city views. The unit offers 2 beds.', 500000.00, 19, '[\"01JXAXBK6HGG9J3Q0NBPZ029G5.jpg\",\"01JXAXBK6K7449H8H06WRGS70P.jpg\",\"01JXAXBK6M85DAZCD1G0G00Q4Z.jpg\"]'),
(189, '2025-06-09 17:56:04', '2025-06-09 17:56:04', 97, 10, NULL, 'Providing free toiletries and bathrobes, this double room includes a private bathroom with a shower, a hairdryer and slippers. The air-conditioned double room provides a flat-screen TV, a private entrance, soundproof walls, a tea and coffee maker as well as city views. The unit offers 1 bed.', 500000.00, 20, '[\"01JXAXBK6N8NXZCFNT51DFS4RD.jpg\",\"01JXAXBK6PD2P65BDEX4HFJYDR.jpg\",\"01JXAXBK6QGAW1ZW8HD5X08B57.jpg\"]'),
(190, '2025-06-09 17:56:04', '2025-06-09 17:56:04', 97, 5, NULL, 'Offering free toiletries and bathrobes, this single room includes a private bathroom with a bath, a shower and a hairdryer. The air-conditioned single room offers a flat-screen TV, a private entrance, soundproof walls, a tea and coffee maker as well as city views. The unit has 1 bed.', 400000.00, 15, '[\"01JXAXBK6S1C84JXNJW43S160R.jpg\"]'),
(191, '2025-06-24 05:37:54', '2025-09-26 03:35:21', 98, 6, NULL, 'Buxoro hotel', 55.00, 8, '[]'),
(192, '2025-09-26 03:33:45', '2025-09-26 03:33:45', 6, 6, NULL, NULL, 50.00, 20, '[]'),
(193, '2025-09-26 03:35:21', '2025-09-26 03:35:21', 98, 5, NULL, NULL, 40.00, 20, '[]'),
(194, '2025-09-26 03:35:21', '2025-09-26 03:35:21', 98, 1, NULL, NULL, 50.00, 20, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `created_at`, `updated_at`, `type`) VALUES
(1, '2025-01-10 16:43:13', '2025-01-10 16:43:13', 'Double'),
(2, '2025-01-11 05:21:21', '2025-01-11 05:21:21', 'sing'),
(3, '2025-01-11 05:44:54', '2025-01-11 05:44:54', 'Delux room'),
(4, '2025-01-11 05:45:34', '2025-01-11 05:45:34', 'Standard room'),
(5, '2025-01-11 05:55:41', '2025-01-11 05:55:41', 'Single'),
(6, '2025-01-22 04:22:59', '2025-01-22 04:22:59', 'Twin room'),
(7, '2025-01-29 13:03:39', '2025-01-29 13:03:39', 'Yurta 4 mest'),
(8, '2025-01-29 13:04:17', '2025-01-29 13:04:17', 'Yurta 5 mest'),
(9, '2025-01-29 13:04:29', '2025-01-29 13:04:29', 'Yurta 6 mest'),
(10, '2025-06-08 12:46:40', '2025-06-08 12:46:40', 'Large Double Room');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kggfFwZS2NFl9nR1jO4jWWPQYbUPmi8kTKelcOdr', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTUhmT1BTT2dzb3lGWlBycVZTZWN2WnpRMzBiMXBtbVpaQk1mcVpXNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi90b3VycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRQNGR5VFh4cE9WNW92OEZpamVVRVJlYWRHRW1HcXpFU2NOTWd2b3N1c2QxSGc1cVdaQjhpZSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1758878940);

-- --------------------------------------------------------

--
-- Table structure for table `spoken_languages`
--

CREATE TABLE `spoken_languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spoken_languages`
--

INSERT INTO `spoken_languages` (`id`, `created_at`, `updated_at`, `name`) VALUES
(1, '2025-01-10 16:42:16', '2025-01-10 16:42:16', 'EN'),
(2, '2025-01-13 11:38:17', '2025-01-13 11:38:17', 'FR'),
(3, '2025-01-13 11:38:49', '2025-01-13 11:38:49', 'EN'),
(4, '2025-01-13 11:40:30', '2025-01-13 11:40:30', 'RU'),
(5, '2025-01-13 11:44:20', '2025-01-13 11:44:20', 'CN'),
(6, '2025-01-13 11:44:35', '2025-01-13 11:44:35', 'UZ'),
(7, '2025-02-21 13:26:31', '2025-02-21 13:26:31', 'TR'),
(8, '2025-06-24 05:41:54', '2025-06-24 05:41:54', 'it'),
(9, '2025-06-24 05:42:01', '2025-06-24 05:42:01', 'ru');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tour_number` varchar(255) NOT NULL,
  `number_people` int(11) NOT NULL DEFAULT 0,
  `tour_duration` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tour_file` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `name`, `description`, `created_at`, `updated_at`, `tour_number`, `number_people`, `tour_duration`, `start_date`, `end_date`, `image`, `tour_file`, `country`) VALUES
(8, 'CD Toshkent - Samarkand  6 days', 'Toshkent - Samarkand 5 Kunlik tur, 3 kun Toshkent, 2 Samarkand ', '2025-01-21 11:05:21', '2025-01-30 11:25:30', 'cd-toshkent-samarkand-6-days', 16, 6, '2025-02-15', '2025-02-20', NULL, NULL, NULL),
(12, ' CD Tashkent-Samarkand 4 days', 'Xitoyliklar uchun eng qisqa marshrut.', '2025-01-30 05:15:00', '2025-01-30 11:19:25', 'cd-tashkent-samarkand-4-days', 10, 4, '2025-02-10', '2025-02-13', '01JJTT4KRFPTR7B219P0Q7GZ47.jpg', '01JJTT4KRJN6GDEMEX0CYQXEC4.jpg', NULL),
(13, 'CD Tashkent-Samarkand-Bukhara', 'Stella 3 ta shahar 6 kunlik tur.', '2025-01-30 07:25:26', '2025-01-30 11:20:29', 'cd-tashkent-samarkand-bukhara', 15, 6, '2025-02-20', '2025-02-25', NULL, NULL, NULL),
(14, 'Tilo 15 Day Tour Uzbekistan velo', 'velosayohat', '2025-01-30 07:45:40', '2025-01-31 04:42:28', 'tilo-15-day-tour-uzbekistan-velo', 2, 15, '2025-03-10', '2025-03-24', NULL, NULL, NULL),
(15, 'CD Qozoq-Uzbek-Tajik-Turkman-Qozoq', 'Stella 4 ta davlat turi. 12 kunlik tur', '2025-01-30 10:55:10', '2025-01-31 04:27:35', 'cd-qozoq-uzbek-tajik-turkman-qozoq', 16, 12, '2025-03-10', '2025-03-21', NULL, NULL, NULL),
(17, 'CD Toshkent- Buxoro-Samarqand', 'Toshkent-Buxoro-Samarqand 6 kunlik tur', '2025-01-31 05:26:37', '2025-01-31 05:26:37', 'cd-toshkent-buxoro-samarqand', 20, 6, '2025-03-10', '2025-03-15', NULL, NULL, NULL),
(18, 'Tilo Uzbekistan hiking trip', 'hiking trip 15 kun', '2025-01-31 05:35:46', '2025-01-31 05:54:04', 'tilo-uzbekistan-hiking-trip', 2, 15, '2025-03-20', '2025-04-03', NULL, NULL, NULL),
(19, 'СD QOZOQ-UZBEK-TURKMAN', '3 ta davlat 9 kunlik tur', '2025-01-31 06:04:27', '2025-01-31 06:04:27', 'sd-qozoq-uzbek-turkman', 16, 9, '2025-04-10', '2025-04-18', NULL, NULL, NULL),
(20, 'CD Xi\'an-Samarqand-Buxoro', 'Xi\'an -Samarqand - Buxoro-Xi\'an 4 kunlik tur', '2025-01-31 06:34:03', '2025-01-31 06:34:33', 'cd-xian-samarqand-buxoro', 15, 4, '2025-03-20', '2025-03-23', NULL, NULL, NULL),
(21, 'CD Uzbekistan ', 'Uzb 4 ta shahar 7 kunlik tur', '2025-01-31 07:29:40', '2025-01-31 07:29:40', 'cd-uzbekistan', 18, 7, '2025-04-10', '2025-04-16', NULL, NULL, NULL),
(22, 'CD TURKMAN-UZBEK-QOZOQ ', '3 ta davlat 8 kunlik tur.', '2025-01-31 11:23:40', '2025-01-31 11:23:40', 'cd-turkman-uzbek-qozoq', 16, 8, '2025-03-15', '2025-03-22', NULL, NULL, NULL),
(34, 'СD QOZOQ-UZBEK-TURKMAN- UZBEK 8 days', 'Xitoyliklar uchun 3 ta davlat 8 kunlik tur', '2025-02-01 07:47:27', '2025-02-01 07:47:27', 'sd-qozoq-uzbek-turkman-uzbek-8-days', 16, 8, '2025-03-20', '2025-03-27', NULL, NULL, NULL),
(35, 'TAJIKISTAN/UZBEKISTAN bike tour', 'Bike tour with bike hire (26er mountain bikes) or driving wheels on mostly paved roads with mostly low-car transport;\nDaily stages between 32 and 67 km; several hilly stages with some large climbs, good physical condition is necessary;\nLuggage transport in the support vehicle;\nNumber of participants: min. 4 / max. 12\nOrganizer: bite active travel', '2025-02-04 11:49:22', '2025-02-04 11:49:22', 'tajikistanuzbekistan-bike-tour', 2, 22, '2025-06-26', '2025-07-17', NULL, NULL, NULL),
(36, 'СD  4 ta davlat - 11 kunlik tur', 'Ayyub chegara orqali o\'tadigan 4 ta davlat turi.\nTosh-Sam-Bux-Xiva', '2025-02-19 07:38:27', '2025-02-19 07:38:27', 'sd-4-ta-davlat-11-kunlik-tur', 18, 11, '2025-05-21', '2025-05-31', NULL, '01JMEJ9MHG722YNKXP7WWG510T.docx', NULL),
(37, 'sam 4 hour', 'sdsa', '2025-02-19 11:14:57', '2025-02-19 11:14:57', 'sam-4-hour', 1, 2, '2025-02-19', '2025-02-20', NULL, NULL, NULL),
(39, 'Chandler Sampson', 'Explicabo Cillum ex', '2025-02-19 11:17:00', '2025-02-19 11:17:00', 'chandler-sampson', 445, 3820, '2025-02-18', '2025-02-19', NULL, NULL, NULL),
(40, 'CD Xitoylar musulmonlari.', 'Musulmon xitoyliklar uchun tur.', '2025-02-20 10:45:53', '2025-02-20 10:45:53', 'cd-xitoylar-musulmonlari', 25, 6, '2025-04-20', '2025-04-25', NULL, '01JMHFDJ40ASYEP7H9NP7VS0C4.xls', NULL),
(41, 'Uzbekistan 5 days', 'very good tour', '2025-02-21 13:35:30', '2025-02-21 13:35:30', 'uzbekistan-5-days', 5, 6, '2025-02-22', '2025-02-27', NULL, NULL, NULL),
(42, 'KYLE TUR', 'XI\'AN - SAMARQAND- AFG\'ONISTON', '2025-03-11 07:22:54', '2025-03-11 07:22:54', 'kyle-tur', 7, -657430, '0225-04-09', '0225-04-15', NULL, NULL, NULL),
(44, 'italy', 'sdas', '2025-06-26 06:53:02', '2025-06-26 06:53:02', 'IT-01027', 17, 11, '2025-10-02', '2025-10-12', NULL, NULL, 'italy'),
(45, 'Shahr', 'sds', '2025-08-22 03:35:31', '2025-08-22 03:35:31', 'JP-01029', 2, 2, '2025-08-22', '2025-08-23', NULL, NULL, 'jp'),
(49, 'Italy October 2-12', 'first group ', '2025-09-26 03:46:08', '2025-09-26 03:46:08', 'IT-01033', 18, 9, '2025-10-02', '2025-10-10', NULL, NULL, 'Italy');

-- --------------------------------------------------------

--
-- Table structure for table `tour_days`
--

CREATE TABLE `tour_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tour_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `guide_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price_type_name` varchar(255) DEFAULT NULL,
  `is_guide_booked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_days`
--

INSERT INTO `tour_days` (`id`, `created_at`, `updated_at`, `tour_id`, `name`, `description`, `guide_id`, `hotel_id`, `type`, `city_id`, `restaurant_id`, `image`, `price_type_name`, `is_guide_booked`) VALUES
(9, '2025-01-21 11:05:21', '2025-01-30 11:25:30', 8, 'DAY 1', 'Tashkentda yarim kun ekskursiya.', 3, 2, '4_star', 2, NULL, NULL, '', 0),
(10, '2025-01-21 11:05:22', '2025-01-30 11:25:30', 8, 'DAY 2', 'Ekskursiya', 3, 2, '4_star', 2, NULL, NULL, '', 0),
(11, '2025-01-21 11:05:22', '2025-01-30 11:25:30', 8, 'DAY 3', 'Pereezd + ekskursiya', 3, 52, '4_star', 3, NULL, NULL, '', 0),
(12, '2025-01-21 11:05:22', '2025-01-30 11:25:30', 8, 'DAY 4', 'Ekskursiya', 3, 52, '4_star', 3, NULL, NULL, '', 0),
(13, '2025-01-21 11:05:22', '2025-01-30 11:25:30', 8, 'DAY 5', NULL, 3, 2, '4_star', 2, NULL, NULL, '', 0),
(14, '2025-01-21 11:05:22', '2025-01-30 11:25:30', 8, 'DAY 6', 'Provodi', 3, NULL, NULL, 2, NULL, NULL, '', 0),
(19, '2025-01-30 05:15:00', '2025-01-30 05:15:00', 12, 'Day 1', 'Toshkentga yetib kelish va Toshkentda yarim kun ekskursiya.', 6, NULL, NULL, NULL, NULL, '01JJTT4KRMHFBJXHC8JY6AFF09.jpg', '', 0),
(20, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 12, 'Day 2', 'Obedgacha Samarqandga yetib kelish. Tushdan so\'ng ekskursiya qilish.', 6, NULL, NULL, NULL, NULL, '01JJTT4KRRX0R08VFAG702MT8B.jpg', '', 0),
(21, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 12, 'Day 3', 'Obedgacha Samarqandda ekskursiya. Tushdan so\'ng Toshkentga yo\'lga chiqish.', 6, NULL, NULL, NULL, NULL, '01JJTT4KRZ5CBBSX4J054VS5DS.jpg', '', 0),
(22, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 12, 'Day 4', 'Nonushtada so\'ng aeroportga kuzatish. ', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(23, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 13, 'DAY 1', 'Toshkentga yetib kelish va yarim kun ekskursiya.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(24, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 13, 'DAY 2', 'Toshkentdan Samarqandga yo\'lga chiqish va Samarqandga yetib kelgach yarim kun ekskursiya.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(25, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 13, 'DAY 3', 'Butun kun davomida Samarqandda ekskursiya.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(26, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 13, 'DAY 4', 'Ertalab nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(27, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 13, 'DAY 5', 'Buxoroda obedgacha ekskursiya. Tushlikdan so\'ng Afrosiyobda Toshkentga qarab yo\'lga chiqish.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(28, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 13, 'DAY 6', 'Nonushtadan so\'ng aeroportga kuzatish.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(29, '2025-01-30 07:45:40', '2025-01-31 04:42:27', 14, 'Day 1', 'The first day of the trip starts with a flight from Frankfurt to Tashkent the capital of Uzbekistan. The landing takes place in the evening.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(30, '2025-01-30 07:45:40', '2025-01-30 10:11:37', 14, 'Day 2', 'Today, we enjoy a city tour of Tashkent, the modern capital of Uzbekistan. We will visit, among other things, the Madrasseh of Kukeldash in the small, charming old town, the monument to the victims of the earthquake of 1966, as well as some of the most artfully designed subway stations in the city. In the evening the day ends an eventful with the journey to the station, where we the night train to Bukhara climb.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(31, '2025-01-30 07:45:40', '2025-01-30 10:11:37', 14, 'Day 3', 'In the Morning, arrival in Bukhara. Transfer to the Hotel in the old town. Guided Tour Of The City. Bukhara is considered to be the \"Holy city\" of Central Asia. Here awaits us a nearly completely preserved Oriental town with its many historic buildings, including the mighty Kalon mosque or madrasah Mir i Arab, the largest Islamic school in the Region, whose domes of bright turquoise Shine.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(32, '2025-01-30 10:55:10', '2025-01-30 10:55:10', 15, 'DAY 1', 'Toshkentga yetib kelish. Tushdan so\'ng ekskursiya.', 4, NULL, NULL, NULL, NULL, NULL, '', 0),
(33, '2025-01-30 10:55:10', '2025-01-30 10:55:10', 15, 'DAY 2', 'Nonushtadan so\'ng chegaraga kuzatish.', 4, NULL, NULL, NULL, NULL, NULL, '', 0),
(34, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 15, 'DAY 4', 'Chegaradan Samarqandga kirib kelish va tushlik qilish. Tushlikdan so\'ng ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, '', 0),
(35, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 15, 'DAY 5', 'Samarqandda ekskursiya', 3, NULL, NULL, NULL, NULL, NULL, '', 0),
(36, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 15, 'DAY 6', 'Nonushtadan so\'ng Buxoro shahriga yo\'lga chiqish', 3, NULL, NULL, NULL, NULL, NULL, '', 0),
(37, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 15, 'DAY 7', 'Nonushtadan so\'ng  Turkmaniston chegarasiga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, '', 0),
(38, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 15, 'DAY 9', 'Kechki soat 15:00 atrofi Urganch chegarasidan mehmonlarni kutib olish.', 8, NULL, NULL, NULL, NULL, NULL, '', 0),
(39, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 15, 'Day 10', 'Xivada ekskursiya. Kechki ovqatdan so\'ng Urganch aeroportiga kuzatish va Toshkentga yetib kelish.', 8, NULL, NULL, NULL, NULL, NULL, '', 0),
(40, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 15, 'DAY 11', 'Ertalab soat 05:00 da Toshkent xalqaro aeroportiga kuzatish.', 5, NULL, NULL, NULL, NULL, NULL, '', 0),
(45, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 14, 'Day 4', 'Today is time for your own explorations in Bukhara or a trip to the environment, for example, to the Mausoleum of Bahovuddin Naqshband, the founder of the Sufi order of the Naqshbandi is. In the afternoon we will take a small test ride the bikes through the old and new quarters of the city. At the Lyabi-Hauz, the mosques and madrasahs-lined pond in the heart of the old town, you can enjoy in the shade of ancient mulberry trees a Cup of green tea and the rain and Bustle.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(46, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 14, 'Day 5', 'In the fertile oasis of Bukhara, we cycle past orchards, cotton fields and mulberry trees next to streets by many small villages. In the ceramic town of Gijduvan we will spend the night in the house of the Potter, master Abdullah aka. Behind Gijduvan we reach the foothills of the desert Kyzyl Kum (desert of the Red sand). We cross the hill chain of the Karatau at the small Karaqarga-Pass (800 m). On the descent from the Pass, caution is advised, since on this route, many of the desert, the road turtles cross. In this Region, Persian live next to Uzbek-speaking Tajiks. In the small town of Nurata we stay in a Tajik family.\n\nCycling: approx. 78 km / approx 83 km / approx. 58 km', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(47, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 14, 'Day 6', 'Today is time for your own explorations in Bukhara or a trip to the environment, for example, to the Mausoleum of Bahovuddin Naqshband, the founder of the Sufi order of the Naqshbandi is. In the afternoon we will take a small test ride the bikes through the old and new quarters of the city. At the Lyabi-Hauz, the mosques and madrasahs-lined pond in the heart of the old town, you can enjoy in the shade of ancient mulberry trees a Cup of green tea and the rain and Bustle.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(48, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 14, 'Day 7', 'Today is time for your own explorations in Bukhara or a trip to the environment, for example, to the Mausoleum of Bahovuddin Naqshband, the founder of the Sufi order of the Naqshbandi is. In the afternoon we will take a small test ride the bikes through the old and new quarters of the city. At the Lyabi-Hauz, the mosques and madrasahs-lined pond in the heart of the old town, you can enjoy in the shade of ancient mulberry trees a Cup of green tea and the rain and Bustle.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(54, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 14, 'Day 8', 'In Nurata, we will visit the source of Hasrat Ali. The source is considered to be Holy and attract Muslim pilgrims from across Central Asia. Then we cycle through the desert to the Yurt camp of Jangikasghan, where we will spend the night twice.\n\nBike: approx. 68 km', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(55, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 14, 'Day 9', ' The day we do a day trip with the bike to the Aydarkul lake. If you like, you can also stay in the Camp, a day of rest or take a camel ride (optional).\n\nBike: approx. 52 km', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(56, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 14, 'Day 10', 'Day a short bus transfer to the rear Nurata. It continues with the wheel through a long valley between the mountain chains of the Aktau and Nuratau to Qoshrabot. Behind Qoshrabot we cross the next day, the of Aktau and to reach the oasis of Samarkand. After an Overnight stay in the village Obolin we visit on 12. The day of our Uzbekistan-bike tour is the Mausoleum of the scholars of al-Bukhari and reach in the afternoon Samarkand.\n\nCycling: approx. 60 km / approx. 65 km / approx. 35 km', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(57, '2025-01-30 12:08:54', '2025-01-30 12:08:54', 14, 'Day 11', 'Day a short bus transfer to the rear Nurata. It continues with the wheel through a long valley between the mountain chains of the Aktau and Nuratau to Qoshrabot. Behind Qoshrabot we cross the next day, the of Aktau and to reach the oasis of Samarkand. After an Overnight stay in the village Obolin we visit on 12. The day of our Uzbekistan-bike tour is the Mausoleum of the scholars of al-Bukhari and reach in the afternoon Samarkand.\n\nCycling: approx. 60 km / approx. 65 km / approx. 35 km', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(58, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 14, 'Day 12', 'we visit the mausoleum of the scholar al-Bukhari on the 12th day of our Uzbekistan bike tour and reach Samarkand in the afternoon', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(59, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 14, 'Day 13', 'Samarkand is one of the oldest cities in the world. With its masterpieces of Islamic architecture such as the Guri Amir Mausoleum or the Bibi Chanym Mosque, its lively bazaar and low mud houses, but also due to the influence of post-socialist modernity characterized by trade and change, it is today the metropolis of the Central Asian East.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(60, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 14, 'Day 14', 'In the morning there is time for exploring Samarkand on your own. You can also visit a silk carpet factory if you wish. In the late afternoon we take the express train through the Hunger Steppe to Tashkent.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(61, '2025-01-30 12:33:55', '2025-01-30 12:33:55', 14, 'Day 15', 'You will be transferred to the airport early in the morning to check in in time for your return flight to Frankfurt. This is how your cycling trip through Uzbekistan comes to an end with many unforgettable impressions.\n', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(62, '2025-01-31 05:26:37', '2025-01-31 05:26:37', 17, 'DAY 1', 'Tush vaqti Toshkentga yetib kelish va tushdan so\'ng ekskursiya qilish.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(63, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 17, 'DAY 2', 'Nonushtadan so\'ng Poyezdda Buxoroga kelish.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(64, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 17, 'DAY 3', 'Obedgacha ekskursiya. Tushdan so\'ng Samarqandga yo\'lga chiqish.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(65, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 17, 'DAY 4', 'Butun kun ekskursiya.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(66, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 17, 'DAY 5', 'Obedgacha ekskursiya va tushdan so\'ng Toshkentga yo\'lga chiqish.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(67, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 17, 'DAY 6', 'Nonushtadan so\'ng aeroportga kuzatish.', 6, NULL, NULL, NULL, NULL, NULL, '', 0),
(68, '2025-01-31 05:35:46', '2025-01-31 05:35:46', 18, 'Day 1', 'Your Uzbekistan trip starts with the flight from Frankfurt to Tashkent . Arrival in the evening. One overnight stay in a hotel.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(69, '2025-01-31 05:35:46', '2025-01-31 05:35:46', 18, 'Day 2 ', 'Guided city tour through the modern Uzbek capital. We visit the Kukeldash Madrasa in the small old town, the memorial to the victims of the 1966 earthquake and some particularly beautiful metro stations . In the late afternoon, transfer to the Chimgan Mountains (3 nights in a hotel).', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(70, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 19, 'DAY 1', 'Toshkentga yetib kelish va Tushdan so\'ng ekskursiya.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(71, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 19, 'DAY 2', 'Nonushtadan so\'ng avtobusda Samarqandga yo\'lga chiqish.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(72, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 19, 'DAY 3', 'Butun kun ekskursiya.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(73, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 19, 'DAY 4', 'Nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(74, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 19, 'DAY 5', 'Nonushtadan so\'ng Olot chegarasiga kuzatish.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(75, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 19, 'Day 7', 'Kechki soat 15:30 lar atrofida Shovot chegaradan kutibn olish.', 8, NULL, NULL, NULL, NULL, NULL, '', 0),
(76, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 19, 'DAY 8', 'Xivada butun kun ekskursiya. Ujindan so\'ng Urganch aeroportiga kuzatish va Toshkentga uchish.', 8, NULL, NULL, NULL, NULL, NULL, '', 0),
(77, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 19, 'DAY 9', 'Nonushtadan keyin aeroportga kuzatish.', 9, NULL, NULL, NULL, NULL, NULL, '', 0),
(78, '2025-01-31 06:34:03', '2025-02-01 04:51:00', 20, 'DAY 1', 'Samarqandga yetib kelish va tushdan so\'ng Samarqandda ekskursiya qilish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(79, '2025-01-31 06:34:03', '2025-02-01 04:51:00', 20, 'DAY 2', 'Nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(80, '2025-01-31 06:34:03', '2025-02-01 04:51:00', 20, 'DAY 3', 'Nonushtadan so\'ng Samarqandga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(81, '2025-01-31 06:34:04', '2025-02-01 04:51:00', 20, 'DAY 4', 'Nonushtadan so\'ng aeroportga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(82, '2025-01-31 07:29:40', '2025-01-31 07:29:40', 21, 'DAY 1', 'Toshkentga yetib kelish va ekskursiya.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(83, '2025-01-31 07:29:40', '2025-01-31 07:29:40', 21, 'DAY 2', 'Nonushtadan so\'ng Samarqandga yo\'lga chiqish va tushdan so\'ng ekskursiya.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(84, '2025-01-31 07:29:41', '2025-01-31 07:29:41', 21, 'DAY 3', 'Butun kun ekskursiya.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(85, '2025-01-31 07:29:41', '2025-01-31 07:29:41', 21, 'DAY 4', 'Nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(86, '2025-01-31 07:29:41', '2025-01-31 07:29:41', 21, 'DAY 5', 'Ertalab ekskursiya. Keyin Xivaga yo\'lga chiqish. Yo\'lda tushlik qilish uchun to\'xtash.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(87, '2025-01-31 07:29:41', '2025-01-31 07:29:41', 21, 'DAY 6', 'Butun kun Xivada ekskursiya va kechqurun ujindan so\'ng Toshkentga uchish.', 8, NULL, NULL, NULL, NULL, NULL, '', 0),
(88, '2025-01-31 07:29:41', '2025-01-31 07:29:41', 21, 'DAY 7', 'Nonushtadan so\'ng aeroportga kuzatish.', 11, NULL, NULL, NULL, NULL, NULL, '', 0),
(91, '2025-01-31 11:23:40', '2025-02-01 04:49:22', 22, 'DAY 1', 'Turkmanistondan Xivaga kirib kelish.', 8, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(98, '2025-01-31 11:46:17', '2025-01-31 11:46:17', 18, 'Day 3', 'The Chimgan Mountains are an offshoot of the Tien Shan with mountains up to 4,000 m high. From our hotel we go on day hikes into the surrounding mountains : on old shepherds\' paths , across alpine meadows and to a waterfall. In the afternoon of the 5th day we drive back to Tashkent and in the evening we board the night train to Bukhara', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(99, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 18, 'Day 4', 'The Chimgan Mountains are an offshoot of the Tien Shan with mountains up to 4,000 m high. From our hotel we go on day hikes into the surrounding mountains : on old shepherds\' paths , across alpine meadows and to a waterfall. In the afternoon of the 5th day we drive back to Tashkent and in the evening we board the night train to Bukhara', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(100, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 18, 'Day 5', NULL, 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(101, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 18, 'Day 6', 'Arrival in Bukhara in the morning. Transfer to the hotel in the old town. Guided city tour. Bukhara is considered the \"holy city\" of Central Asia . Here we can expect an almost completely preserved oriental city center with many historical buildings such as the mighty Kalon Mosque, the Chor Minor Mosque or the Mir i Arab Madrasa , the largest Koran school in the region, whose domes shine in bright turquoise .', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(102, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 18, 'Day 7', 'Today is time for exploring Bukhara on your own or taking a trip to the surrounding area, for example to the mausoleum of Bahovuddin Naqshband, the founder of the Naqshbandi Sufi order . At Ljabi Chaus , the large pond lined with mosques and madrasas in the heart of the old town , you can enjoy a cup of green tea in the shade of ancient mulberry trees in the afternoon and simply watch the hustle and bustle.', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(103, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 18, 'Day 8', 'Today we drive to the Kyzyl Kum desert - the desert of red sand. On the way we visit the workshop of the master potter Abdullah aka in the town of Gishduwan, which is famous for its ceramics . In Nurata we visit the Hasrat Ali spring . The spring is considered holy and attracts Muslim pilgrims from all over Central Asia. In the desert we spend the night in a camp in traditional yurts (felt tents of the nomads).', 10, NULL, NULL, NULL, NULL, NULL, '', 0),
(108, '2025-01-31 12:10:06', '2025-02-01 04:49:22', 22, 'DAY 2', 'Butun kun Xivada ekskursiya.', 8, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(109, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 22, 'DAY 3', 'Nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(110, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 22, 'DAY 4', 'Butun kun ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(111, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 22, 'DAY 5', 'Nonushtadan so\'ng Samarqandga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(112, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 22, 'DAY 6 ', 'Butun kun ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(113, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 22, 'Day 7', 'Nonushtadan so\'ng Toshkentga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(114, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 22, 'DAY 8', 'Nonushtadan so\'ng aeroportga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(115, '2025-02-01 07:47:27', '2025-02-01 07:47:27', 34, 'DAY 1', 'Toshkentga yetib kelish va tushdan so\'ng ekskursiya.', 11, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(116, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 34, 'DAY 2', 'Ertalab Urganchga uchish va Xivada yarim kun ekskursiya.', 8, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(117, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 34, 'DAY 3', 'Obedgacha ekskursiya va tushlikdan so\'ng Turkmanobod chegarasiga kuzatish.', 8, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(118, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 34, 'DAY 4', 'Chegaradan kutib olish va yarim kun ekskursiya qilish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(119, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 34, 'DAY 5', 'Obedgacha ekskursiya va tushdan so\'ng Samarqandga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(120, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 34, 'DAY 6', 'Butun kun Samarqandda ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(121, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 34, 'DAY 7', 'Obedgacha ekskursiya. Tushdan so\'ng Toshkentga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(122, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 34, 'DAY 8', 'Nonushtada so\'ng aeroportga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(123, '2025-02-04 11:49:22', '2025-02-04 11:49:22', 35, 'Day 1', 'Flight from Frankfurt toTashkent, Transfer to the Hotel.', 10, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(124, '2025-02-04 12:43:11', '2025-02-04 12:43:11', 35, 'Day 2', 'In the morning sightseeing in Tashkent. In the afternoon Transfer to the Tajik border and drive to Chudshand, the second-largest city of Tajikistan. Hotel-NIGHTS.', 10, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(125, '2025-02-11 10:06:27', '2025-02-11 10:06:27', 35, 'Day 17', 'Samarkand is one of the oldest cities in the world. With their masterpieces of Islamic architecture , such as the Mausoleum Guri Amir, or the Bibi Khanum mosque, lively Bazaar, and the low Adobe houses, but also due to the influence of the trade and changing post-socialist Modern this city is today the capital of the Central Asian Orient par excellence. At the time of the turk-Mongol ruler Timur (14./15.Jh.) should Samarkand to the \"center of the universe\". The result of this effort, a magnificent blend of different architectural currents and impressed the viewers even today.', 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(127, '2025-02-11 10:10:50', '2025-02-11 10:10:50', 35, 'Day 18', 'Free day in Samarkand', 10, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(128, '2025-02-11 10:48:32', '2025-02-11 10:48:32', 35, 'Day 19', 'In the morning Transfer to Bukhara. Here in the \"Holy city\" of Central Asia is almost completely preserved Oriental town with its many historic buildings, including the mighty Kalon mosque or madrasah Mir i Arab, the largest Islamic school in the Region, whose domes of bright turquoise Shine.', 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(129, '2025-02-11 10:57:21', '2025-02-11 10:57:21', 35, 'Day 20', 'In the morning, another day of sightseeing in Bukhara. The afternoon is free at leisure. At the Lyabi-Hauz, the mosques and madrasahs-lined large pond in the heart of the old town, you can in the shade of ancient mulberry trees a Cup of green tea to enjoy the rain and Bustle.', 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(130, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 35, 'Day 21', 'Today is time for your own explorations in Bukhara or an optional excursion in the surroundings, such as the Mausoleum of Bahovuddin Naqshband, the founder of the Sufi order of the Naqshbandi. In the afternoon, travel by train to Tashkent. Transfer to the Hotel.', 10, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(131, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 35, 'Day 22', 'Early in the morning Transfer to the airport. Flight back to Frankfurt.', 10, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(132, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 36, 'Day 1', 'Tushdan so\'ng kutib olish va mehmonxonaga joylashish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(133, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 36, 'Day 2', 'Nonushtadan so\'ng chegaraga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(134, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 36, 'DAY 3', 'Tushdan oldin Urgut chegaradan kutib olish. Tushdan so\'ng Samarqandda ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(135, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 36, 'Day 4', 'Obedgacha Samarqandda ekskursiya. Obeddan so\'ng Buxoroga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(136, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 36, 'Day 5', 'Nonushtadan so\'ng Buxoroda ekskursiya. Tushlikdan so\'ng  Olot chegaraga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(137, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 36, 'Day 6', 'Tush vaqti chegaradan kutib olish va mehmonxonaga joylashish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(138, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 36, 'Day 7', 'Butun kun Khivada ekskursiya va Kech payt Toshkentga uchish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(139, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 36, 'Day 8', 'Butun kun ekskursiya va kechqurun aeroportga kuzatish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(140, '2025-02-19 11:14:57', '2025-02-19 11:14:57', 37, 'sam1', NULL, 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(141, '2025-02-19 11:17:00', '2025-02-19 11:17:00', 39, 'Kibo Jacobs', 'Tempora eaque quam a', 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(142, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 40, 'Day 1', 'Obed vaqti aeroportdan kutib olish va tushlik qilish. Obeddan so\'ng Toshkentda ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(143, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 40, 'Day 2', 'Ertalab nonushtadan so\'ng Samarqandga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(144, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 40, 'Day 3', 'Butun kun samarqandda ekskursiya.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(145, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 40, 'Day 4', 'Nonushtadan so\'ng Buxoroga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(146, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 40, 'Day 5', 'Obedgacha Buxoroda ekskursiya va tushdan so\'ng Afrosiyobda Toshkentga yo\'lga chiqish.', 3, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(147, '2025-02-21 13:35:30', '2025-02-21 13:35:30', 41, 'Tashkent', NULL, 9, NULL, NULL, NULL, NULL, NULL, 'pickup_dropoff', 0),
(148, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 42, 'DAY 1  09.04', 'AEROPORTDAN KUTIB OLISH VA SHAHRDA EKSKURSIYA QILISH', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(149, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 42, 'DAY 2 10.04', 'SHAHARDA EKSKURSIYA.', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(150, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 42, 'DAY 3 11.04', 'ERTALAB BUXOROGA YOLGA CHIQISH', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(151, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 42, 'DAY 4 12.04', 'BUXORODA EKSKURSIYA', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(152, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 42, 'DAY 5 13.04', 'BUXORODAN TERMZIGA YOLGA CHIQISH', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(153, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 42, 'DAY 6 14.04', 'TERMIZDA EKSKURSIYA', 12, NULL, NULL, NULL, NULL, NULL, 'per_daily', 0),
(154, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 42, 'DAY 7 15.04', 'CHEGARAG KUZATISH', 12, NULL, NULL, NULL, NULL, NULL, 'halfday', 0),
(158, '2025-06-26 06:53:02', '2025-06-26 07:02:22', 44, 'day1', NULL, 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 1),
(159, '2025-06-26 06:54:51', '2025-06-26 07:02:22', 44, 'day 2', NULL, 9, NULL, NULL, NULL, NULL, NULL, 'per_daily', 1),
(160, '2025-08-22 03:35:31', '2025-08-22 03:35:31', 45, 'sha', NULL, 10, NULL, NULL, NULL, NULL, NULL, 'per_daily', 1),
(161, '2025-09-26 03:46:09', '2025-09-26 04:04:15', 49, 'Tashkent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(164, '2025-09-26 03:54:52', '2025-09-26 04:04:15', 49, 'Oct 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(165, '2025-09-26 03:54:52', '2025-09-26 04:04:15', 49, 'Oct 4', NULL, 17, NULL, NULL, NULL, NULL, NULL, 'per_daily', 1),
(166, '2025-09-26 04:04:15', '2025-09-26 04:04:15', 49, 'Oct 5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tour_day_hotels`
--

CREATE TABLE `tour_day_hotels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `tour_day_id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_day_hotels`
--

INSERT INTO `tour_day_hotels` (`id`, `created_at`, `updated_at`, `type`, `tour_day_id`, `hotel_id`, `is_booked`) VALUES
(6, '2025-01-30 05:15:00', '2025-01-30 05:15:00', '4_star', 19, 2, 0),
(7, '2025-01-30 05:15:01', '2025-01-30 05:15:01', '4_star', 20, 52, 0),
(8, '2025-01-30 05:15:01', '2025-01-30 05:15:01', '4_star', 21, 2, 0),
(10, '2025-01-30 05:53:27', '2025-01-30 05:53:27', '4_star', 9, 2, 0),
(11, '2025-01-30 05:53:28', '2025-01-30 05:53:28', '4_star', 10, 2, 0),
(12, '2025-01-30 05:53:28', '2025-01-30 05:53:28', '4_star', 11, 82, 0),
(13, '2025-01-30 05:53:28', '2025-01-30 05:53:28', '4_star', 12, 82, 0),
(14, '2025-01-30 05:53:28', '2025-01-30 05:53:28', '4_star', 13, 2, 0),
(15, '2025-01-30 05:53:28', '2025-01-30 05:53:28', '4_star', 14, 2, 0),
(16, '2025-01-30 07:25:26', '2025-01-30 07:25:26', '4_star', 23, 2, 0),
(17, '2025-01-30 07:25:26', '2025-01-30 07:25:26', '4_star', 24, 82, 0),
(18, '2025-01-30 07:25:26', '2025-01-30 07:25:26', '4_star', 25, 82, 0),
(19, '2025-01-30 07:25:27', '2025-01-30 07:25:27', '4_star', 26, 31, 0),
(20, '2025-01-30 07:25:27', '2025-01-30 07:25:27', '4_star', 27, 2, 0),
(21, '2025-01-30 07:25:27', '2025-01-30 07:25:27', '4_star', 28, 2, 0),
(22, '2025-01-30 07:45:40', '2025-01-30 07:45:40', '4_star', 29, 2, 0),
(23, '2025-01-30 07:45:40', '2025-01-30 07:45:40', '4_star', 30, 31, 0),
(24, '2025-01-30 07:45:40', '2025-01-30 07:45:40', '4_star', 31, 31, 0),
(25, '2025-01-30 10:55:10', '2025-01-30 10:55:10', '4_star', 32, 2, 0),
(27, '2025-01-30 10:55:11', '2025-01-30 10:55:11', '4_star', 34, 82, 0),
(28, '2025-01-30 10:55:11', '2025-01-30 10:55:11', '4_star', 35, 82, 0),
(29, '2025-01-30 10:55:11', '2025-01-30 10:55:11', '4_star', 36, 31, 0),
(31, '2025-01-30 10:55:12', '2025-01-30 10:55:12', '3_star', 38, 54, 0),
(32, '2025-01-30 10:55:12', '2025-01-30 10:55:12', '4_star', 39, 2, 0),
(37, '2025-01-30 11:17:36', '2025-01-30 11:17:36', '4_star', 45, 31, 0),
(38, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 'bed_breakfast', 46, 86, 0),
(39, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 'bed_breakfast', 47, 87, 0),
(40, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 'bed_breakfast', 48, 88, 0),
(46, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 'bed_breakfast', 54, 81, 0),
(47, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 'bed_breakfast', 55, 81, 0),
(48, '2025-01-30 12:08:54', '2025-01-30 12:08:54', 'bed_breakfast', 56, 90, 0),
(49, '2025-01-30 12:08:54', '2025-01-30 12:08:54', '4_star', 57, 49, 0),
(50, '2025-01-30 12:31:45', '2025-01-30 12:31:45', '3_star', 58, 48, 0),
(51, '2025-01-30 12:31:45', '2025-01-30 12:31:45', '3_star', 59, 48, 0),
(52, '2025-01-30 12:31:45', '2025-01-30 12:31:45', '4_star', 60, 2, 0),
(53, '2025-01-30 12:33:55', '2025-01-30 12:33:55', NULL, 61, NULL, 0),
(54, '2025-01-31 05:26:37', '2025-01-31 05:26:37', '4_star', 62, 2, 0),
(55, '2025-01-31 05:26:38', '2025-01-31 05:26:38', '4_star', 63, 31, 0),
(56, '2025-01-31 05:26:38', '2025-01-31 05:26:38', '4_star', 64, 82, 0),
(57, '2025-01-31 05:26:38', '2025-01-31 05:26:38', '4_star', 65, 82, 0),
(58, '2025-01-31 05:26:38', '2025-01-31 05:26:38', '4_star', 66, 2, 0),
(59, '2025-01-31 05:26:38', '2025-01-31 05:26:38', NULL, 67, NULL, 0),
(60, '2025-01-31 05:35:46', '2025-01-31 05:35:46', '4_star', 68, 16, 0),
(61, '2025-01-31 05:35:46', '2025-01-31 05:35:46', NULL, 69, NULL, 0),
(62, '2025-01-31 06:04:27', '2025-01-31 06:04:27', '4_star', 70, 2, 0),
(63, '2025-01-31 06:04:27', '2025-01-31 06:04:27', '4_star', 71, 82, 0),
(64, '2025-01-31 06:04:27', '2025-01-31 06:04:27', '4_star', 72, 82, 0),
(65, '2025-01-31 06:04:27', '2025-01-31 06:04:27', '4_star', 73, 31, 0),
(66, '2025-01-31 06:04:28', '2025-01-31 06:04:28', NULL, 74, NULL, 0),
(67, '2025-01-31 06:04:28', '2025-01-31 06:04:28', '3_star', 75, 89, 0),
(68, '2025-01-31 06:04:28', '2025-01-31 06:04:28', '4_star', 76, 2, 0),
(69, '2025-01-31 06:04:28', '2025-01-31 06:04:28', NULL, 77, NULL, 0),
(70, '2025-01-31 06:34:03', '2025-01-31 06:34:03', '4_star', 78, 82, 0),
(71, '2025-01-31 06:34:03', '2025-01-31 06:34:03', '4_star', 79, 31, 0),
(72, '2025-01-31 06:34:03', '2025-01-31 06:34:03', '4_star', 80, 82, 0),
(73, '2025-01-31 06:34:04', '2025-01-31 06:34:04', NULL, 81, NULL, 0),
(74, '2025-01-31 07:29:40', '2025-08-22 03:34:15', '4_star', 82, 2, 0),
(75, '2025-01-31 07:29:40', '2025-08-22 03:34:15', '4_star', 83, 82, 0),
(76, '2025-01-31 07:29:41', '2025-08-22 03:34:15', '4_star', 84, 82, 0),
(77, '2025-01-31 07:29:41', '2025-08-22 03:34:16', '4_star', 85, 31, 0),
(78, '2025-01-31 07:29:41', '2025-08-22 03:34:16', '3_star', 86, 89, 0),
(79, '2025-01-31 07:29:41', '2025-08-22 03:34:16', '4_star', 87, 2, 0),
(80, '2025-01-31 07:29:41', '2025-08-22 03:34:16', NULL, 88, NULL, 0),
(81, '2025-01-31 11:23:40', '2025-01-31 11:23:40', '3_star', 91, 89, 0),
(84, '2025-01-31 11:46:18', '2025-01-31 11:46:18', '4_star', 98, NULL, 0),
(85, '2025-01-31 11:46:18', '2025-01-31 11:46:18', NULL, 99, NULL, 0),
(86, '2025-01-31 11:46:18', '2025-01-31 11:46:18', '4_star', 100, 31, 0),
(87, '2025-01-31 11:46:18', '2025-01-31 11:46:18', '4_star', 101, 31, 0),
(88, '2025-01-31 11:46:18', '2025-01-31 11:46:18', '4_star', 102, 31, 0),
(89, '2025-01-31 11:46:18', '2025-01-31 11:46:18', NULL, 103, NULL, 0),
(94, '2025-01-31 12:10:06', '2025-02-01 04:49:22', '3_star', 108, 89, 0),
(95, '2025-02-01 04:49:22', '2025-02-01 04:49:22', '4_star', 109, 31, 0),
(96, '2025-02-01 04:49:22', '2025-02-01 04:49:22', '4_star', 110, 31, 0),
(97, '2025-02-01 04:49:22', '2025-02-01 04:49:22', '4_star', 111, 82, 0),
(98, '2025-02-01 04:49:22', '2025-02-01 04:49:22', '4_star', 112, 82, 0),
(99, '2025-02-01 04:49:23', '2025-02-01 04:49:23', '4_star', 113, 2, 0),
(100, '2025-02-01 04:49:23', '2025-02-01 04:49:23', NULL, 114, NULL, 0),
(101, '2025-02-01 07:47:28', '2025-02-01 07:47:28', '4_star', 115, 2, 0),
(102, '2025-02-01 07:47:28', '2025-02-01 07:47:28', '3_star', 116, 89, 0),
(103, '2025-02-01 07:47:28', '2025-02-01 07:47:28', '4_star', 118, 31, 0),
(104, '2025-02-01 07:47:28', '2025-02-01 07:47:28', '4_star', 119, 82, 0),
(105, '2025-02-01 07:47:28', '2025-02-01 07:47:28', '4_star', 120, 82, 0),
(106, '2025-02-01 07:47:29', '2025-02-01 07:47:29', '4_star', 121, 2, 0),
(107, '2025-02-04 11:49:22', '2025-02-04 11:49:22', '4_star', 123, 2, 0),
(108, '2025-02-04 12:43:11', '2025-02-04 12:43:11', NULL, 124, NULL, 0),
(109, '2025-02-11 10:06:27', '2025-02-11 10:06:27', '4_star', 125, 49, 0),
(110, '2025-02-11 10:10:50', '2025-02-11 10:10:50', '4_star', 127, 49, 0),
(111, '2025-02-11 10:48:32', '2025-02-11 10:48:32', '3_star', 128, 28, 0),
(112, '2025-02-11 10:57:21', '2025-02-11 10:57:21', '3_star', 129, 28, 0),
(113, '2025-02-11 10:57:22', '2025-02-11 10:57:22', '4_star', 130, 2, 0),
(114, '2025-02-11 10:57:22', '2025-02-11 10:57:22', NULL, 131, NULL, 0),
(115, '2025-02-19 07:38:27', '2025-02-19 07:38:27', '4_star', 132, 2, 0),
(116, '2025-02-19 07:38:27', '2025-02-19 07:38:27', '4_star', 134, 82, 0),
(117, '2025-02-19 07:38:27', '2025-02-19 07:38:27', '4_star', 135, 31, 0),
(118, '2025-02-19 07:38:28', '2025-02-19 07:38:28', '3_star', 137, 54, 0),
(119, '2025-02-19 07:38:28', '2025-02-19 07:38:28', '4_star', 138, 2, 0),
(120, '2025-02-19 11:17:00', '2025-02-19 11:17:00', NULL, 141, NULL, 0),
(121, '2025-02-20 10:45:53', '2025-02-20 10:45:53', '4_star', 142, 2, 0),
(122, '2025-02-20 10:45:53', '2025-02-20 10:45:53', '3_star', 143, 91, 0),
(123, '2025-02-20 10:45:53', '2025-02-20 10:45:53', '3_star', 144, 91, 0),
(124, '2025-02-20 10:45:54', '2025-02-20 10:45:54', '4_star', 145, 31, 0),
(125, '2025-02-20 10:45:54', '2025-02-20 10:45:54', '4_star', 146, 2, 0),
(126, '2025-02-21 13:35:30', '2025-02-21 13:35:30', '3_star', 147, 4, 0),
(127, '2025-03-11 07:22:54', '2025-03-11 07:22:54', NULL, 148, NULL, 0),
(128, '2025-03-11 07:22:54', '2025-03-11 07:22:54', NULL, 149, NULL, 0),
(129, '2025-03-11 07:22:54', '2025-03-11 07:22:54', NULL, 150, NULL, 0),
(130, '2025-03-11 07:22:54', '2025-03-11 07:22:54', NULL, 151, NULL, 0),
(131, '2025-03-11 07:22:55', '2025-03-11 07:22:55', NULL, 152, NULL, 0),
(132, '2025-03-11 07:22:55', '2025-03-11 07:22:55', NULL, 153, NULL, 0),
(133, '2025-03-11 07:22:55', '2025-03-11 07:22:55', NULL, 154, NULL, 0),
(136, '2025-06-26 06:53:02', '2025-06-26 07:02:22', '3_star', 158, 4, 1),
(137, '2025-06-26 06:54:51', '2025-06-26 07:02:22', '3_star', 159, 48, 1),
(138, '2025-08-22 03:35:31', '2025-08-22 03:35:31', NULL, 160, NULL, 0),
(139, '2025-09-26 03:46:09', '2025-09-26 04:04:14', '4_star', 161, 6, 1),
(141, '2025-09-26 03:54:52', '2025-09-26 04:04:14', '3_star', 164, 96, 1),
(142, '2025-09-26 03:54:52', '2025-09-26 04:04:14', '3_star', 165, 96, 1),
(143, '2025-09-26 04:04:15', '2025-09-26 04:04:15', '3_star', 166, 98, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tour_day_hotel_room`
--

CREATE TABLE `tour_day_hotel_room` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_day_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_day_hotel_room`
--

INSERT INTO `tour_day_hotel_room` (`id`, `tour_day_id`, `hotel_id`, `room_id`, `quantity`, `created_at`, `updated_at`) VALUES
(7, 9, 2, 2, 8, '2025-01-21 11:05:22', '2025-01-21 11:05:22'),
(8, 10, 2, 2, 8, '2025-01-21 11:05:22', '2025-01-21 11:05:22'),
(12, 11, 52, 100, 8, '2025-01-21 11:19:55', '2025-01-21 11:19:55'),
(13, 12, 52, 100, 8, '2025-01-21 11:19:55', '2025-01-21 11:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `tour_day_transport`
--

CREATE TABLE `tour_day_transport` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tour_day_id` bigint(20) UNSIGNED NOT NULL,
  `transport_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price_type` varchar(255) DEFAULT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_day_transport`
--

INSERT INTO `tour_day_transport` (`id`, `created_at`, `updated_at`, `tour_day_id`, `transport_type_id`, `price_type`, `is_booked`) VALUES
(13, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 9, 7, 'per_day', 0),
(14, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 10, 7, 'per_day', 0),
(15, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 11, 7, 'per_day', 0),
(16, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 12, 7, 'per_day', 0),
(17, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 13, 7, 'per_day', 0),
(18, '2025-01-21 11:05:22', '2025-01-21 11:05:22', 14, 7, 'per_pickup_dropoff', 0),
(25, '2025-01-30 05:15:00', '2025-01-30 05:15:00', 19, 5, 'per_day', 0),
(26, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 20, 5, 'per_day', 0),
(27, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 21, 5, 'per_day', 0),
(28, '2025-01-30 05:15:01', '2025-01-30 05:15:01', 22, 5, 'per_day', 0),
(29, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 23, 7, 'per_day', 0),
(30, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 24, 7, 'per_day', 0),
(31, '2025-01-30 07:25:26', '2025-01-30 07:25:26', 25, 7, 'per_day', 0),
(32, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 26, 7, 'per_day', 0),
(33, '2025-01-30 07:25:27', '2025-01-31 04:26:52', 27, 7, 'po_gorodu', 0),
(34, '2025-01-30 07:25:27', '2025-01-30 07:25:27', 28, 7, 'per_pickup_dropoff', 0),
(35, '2025-01-30 07:45:40', '2025-01-30 07:45:40', 29, 2, 'per_pickup_dropoff', 0),
(36, '2025-01-30 07:45:40', '2025-01-30 07:45:40', 30, 2, 'per_day', 0),
(37, '2025-01-30 07:45:40', '2025-01-31 04:39:47', 31, 2, 'per_pickup_dropoff', 0),
(38, '2025-01-30 10:55:10', '2025-01-30 10:55:10', 32, 7, 'per_day', 0),
(39, '2025-01-30 10:55:10', '2025-01-30 10:59:01', 33, 7, 'per_day', 0),
(40, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 34, 7, 'per_day', 0),
(41, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 35, 7, 'per_day', 0),
(42, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 36, 7, 'per_day', 0),
(43, '2025-01-30 10:55:11', '2025-01-30 10:55:11', 37, 7, 'per_day', 0),
(44, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 38, 7, 'per_day', 0),
(45, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 39, 7, 'per_day', 0),
(46, '2025-01-30 10:55:12', '2025-01-30 10:55:12', 40, 7, 'per_pickup_dropoff', 0),
(49, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 45, 2, 'per_day', 0),
(50, '2025-01-30 11:17:36', '2025-01-30 11:17:36', 46, 2, 'per_day', 0),
(51, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 47, 2, 'per_day', 0),
(52, '2025-01-30 11:17:37', '2025-01-30 11:17:37', 48, 2, 'per_day', 0),
(58, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 54, 2, 'per_day', 0),
(59, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 55, 2, 'per_day', 0),
(60, '2025-01-30 12:08:53', '2025-01-30 12:08:53', 56, 2, 'per_day', 0),
(61, '2025-01-30 12:08:54', '2025-01-30 12:08:54', 57, 2, 'per_day', 0),
(62, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 58, 2, 'per_day', 0),
(63, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 59, 2, 'per_day', 0),
(64, '2025-01-30 12:31:45', '2025-01-30 12:31:45', 60, 15, 'economy', 0),
(65, '2025-01-30 12:33:55', '2025-01-30 12:33:55', 61, 2, 'per_pickup_dropoff', 0),
(66, '2025-01-31 04:26:52', '2025-01-31 04:26:52', 27, 16, 'economy', 0),
(67, '2025-01-31 04:26:52', '2025-01-31 04:26:52', 27, 7, 'per_pickup_dropoff', 0),
(72, '2025-01-31 04:42:26', '2025-01-31 04:42:26', 29, 13, 'vip', 0),
(73, '2025-01-31 04:42:26', '2025-01-31 04:42:26', 29, 2, 'per_day', 0),
(74, '2025-01-31 04:42:26', '2025-01-31 04:42:26', 30, 13, 'vip', 0),
(75, '2025-01-31 04:42:26', '2025-01-31 04:42:26', 31, 2, 'per_day', 0),
(76, '2025-01-31 05:26:37', '2025-01-31 05:26:37', 62, 7, 'po_gorodu', 0),
(77, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 63, 7, 'per_pickup_dropoff', 0),
(78, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 63, 13, 'economy', 0),
(79, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 63, 7, 'po_gorodu', 0),
(80, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 64, 7, 'per_day', 0),
(81, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 65, 7, 'per_day', 0),
(82, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 66, 7, 'per_day', 0),
(83, '2025-01-31 05:26:38', '2025-01-31 05:26:38', 67, 7, 'per_pickup_dropoff', 0),
(84, '2025-01-31 05:35:46', '2025-01-31 05:35:46', 68, 2, 'per_pickup_dropoff', 0),
(85, '2025-01-31 05:35:46', '2025-01-31 05:35:46', 69, 2, 'per_day', 0),
(86, '2025-01-31 05:54:04', '2025-01-31 05:54:04', 69, 2, 'per_day', 0),
(87, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 70, 7, 'per_day', 0),
(88, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 71, 7, 'per_day', 0),
(89, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 72, 7, 'per_day', 0),
(90, '2025-01-31 06:04:27', '2025-01-31 06:04:27', 73, 7, 'per_day', 0),
(91, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 74, 7, 'per_day', 0),
(92, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 75, 7, 'per_day', 0),
(93, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 76, 7, 'po_gorodu', 0),
(94, '2025-01-31 06:04:28', '2025-01-31 06:04:28', 77, 7, 'per_pickup_dropoff', 0),
(95, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 78, 7, 'per_day', 0),
(96, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 79, 7, 'per_day', 0),
(97, '2025-01-31 06:34:03', '2025-01-31 06:34:03', 80, 7, 'per_day', 0),
(98, '2025-01-31 06:34:04', '2025-01-31 06:34:04', 81, 7, 'per_pickup_dropoff', 0),
(99, '2025-01-31 07:29:40', '2025-08-22 03:34:15', 82, 7, 'per_day', 0),
(100, '2025-01-31 07:29:40', '2025-08-22 03:34:15', 83, 7, 'per_day', 0),
(101, '2025-01-31 07:29:41', '2025-08-22 03:34:15', 84, 7, 'per_day', 0),
(102, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 85, 7, 'per_day', 0),
(103, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 86, 7, 'per_day', 0),
(104, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 87, 7, 'per_day', 0),
(105, '2025-01-31 07:29:41', '2025-08-22 03:34:16', 88, 7, 'per_pickup_dropoff', 0),
(106, '2025-01-31 11:23:40', '2025-01-31 11:23:40', 91, 7, 'po_gorodu', 0),
(109, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 98, 2, 'per_day', 0),
(110, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 99, 2, 'per_day', 0),
(111, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 100, 13, 'vip', 0),
(112, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 100, 2, 'per_pickup_dropoff', 0),
(113, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 101, 2, 'per_day', 0),
(114, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 102, 2, 'per_day', 0),
(115, '2025-01-31 11:46:18', '2025-01-31 11:46:18', 103, 2, 'per_day', 0),
(120, '2025-01-31 12:10:06', '2025-02-01 04:49:22', 108, 7, 'per_day', 0),
(121, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 109, 7, 'per_day', 0),
(122, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 110, 7, 'per_day', 0),
(123, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 111, 7, 'per_day', 0),
(124, '2025-02-01 04:49:22', '2025-02-01 04:49:22', 112, 7, 'per_day', 0),
(125, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 113, 7, 'per_day', 0),
(126, '2025-02-01 04:49:23', '2025-02-01 04:49:23', 114, 7, 'per_pickup_dropoff', 0),
(127, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 115, 7, 'per_day', 0),
(128, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 116, 7, 'per_day', 0),
(129, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 117, 7, 'per_day', 0),
(130, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 118, 7, 'per_day', 0),
(131, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 119, 7, 'per_day', 0),
(132, '2025-02-01 07:47:28', '2025-02-01 07:47:28', 120, 7, 'per_day', 0),
(133, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 121, 7, 'per_day', 0),
(134, '2025-02-01 07:47:29', '2025-02-01 07:47:29', 122, 7, 'per_pickup_dropoff', 0),
(135, '2025-02-04 11:49:22', '2025-02-04 11:49:22', 123, 2, 'per_pickup_dropoff', 0),
(136, '2025-02-04 12:43:11', '2025-02-04 12:43:11', 124, 2, 'po_gorodu', 0),
(137, '2025-02-11 10:06:27', '2025-02-11 10:06:27', 125, 2, 'per_day', 0),
(138, '2025-02-11 10:10:50', '2025-02-11 10:10:50', 127, 2, 'po_gorodu', 0),
(139, '2025-02-11 10:48:32', '2025-02-11 10:48:32', 128, 2, 'per_day', 0),
(140, '2025-02-11 10:57:21', '2025-02-11 10:57:21', 129, 2, 'per_day', 0),
(141, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 130, 16, 'economy', 0),
(142, '2025-02-11 10:57:22', '2025-02-11 10:57:22', 131, 2, 'per_pickup_dropoff', 0),
(143, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 132, 8, 'per_day', 0),
(144, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 133, 8, 'per_day', 0),
(145, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 134, 8, 'per_day', 0),
(146, '2025-02-19 07:38:27', '2025-02-19 07:38:27', 135, 8, 'per_day', 0),
(147, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 136, 8, 'per_day', 0),
(148, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 137, 8, 'per_day', 0),
(149, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 138, 8, 'per_day', 0),
(150, '2025-02-19 07:38:28', '2025-02-19 07:38:28', 139, 8, 'per_day', 0),
(151, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 142, 7, 'per_day', 0),
(152, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 143, 7, 'per_day', 0),
(153, '2025-02-20 10:45:53', '2025-02-20 10:45:53', 144, 7, 'per_day', 0),
(154, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 145, 7, 'per_day', 0),
(155, '2025-02-20 10:45:54', '2025-02-20 10:45:54', 146, 7, 'per_day', 0),
(156, '2025-02-21 13:35:30', '2025-02-21 13:35:30', 147, 6, 'per_pickup_dropoff', 0),
(157, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 148, 4, 'per_day', 0),
(158, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 149, 4, 'per_day', 0),
(159, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 150, 4, 'per_day', 0),
(160, '2025-03-11 07:22:54', '2025-03-11 07:22:54', 151, 4, 'per_day', 0),
(161, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 152, 4, 'per_day', 0),
(162, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 153, 4, 'per_day', 0),
(163, '2025-03-11 07:22:55', '2025-03-11 07:22:55', 154, 4, 'per_pickup_dropoff', 0),
(166, '2025-06-26 06:53:02', '2025-06-26 07:02:22', 158, 7, 'per_day', 1),
(167, '2025-06-26 06:54:51', '2025-06-26 07:02:22', 159, 7, 'po_gorodu', 0),
(168, '2025-08-22 03:35:31', '2025-08-22 03:35:31', 160, 2, NULL, 1),
(169, '2025-09-26 03:46:09', '2025-09-26 04:04:14', 161, 3, 'per_pickup_dropoff', 0),
(172, '2025-09-26 03:54:52', '2025-09-26 04:04:14', 164, 7, 'per_pickup_dropoff', 1),
(173, '2025-09-26 03:54:52', '2025-09-26 04:04:14', 164, 7, 'per_pickup_dropoff', 1),
(174, '2025-09-26 03:54:52', '2025-09-26 04:04:14', 165, NULL, NULL, 0),
(175, '2025-09-26 04:04:15', '2025-09-26 04:04:15', 166, 7, 'per_pickup_dropoff', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE `transports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `number_of_seat` int(11) DEFAULT NULL,
  `transport_type_id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('bus','car','mikro_bus','mini_van','air','rail') NOT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `oil_change_interval_months` int(11) NOT NULL,
  `oil_change_interval_km` int(11) NOT NULL,
  `fuel_consumption` decimal(8,2) NOT NULL,
  `fuel_type` enum('diesel','benzin/propane','natural gas') NOT NULL,
  `fuel_remaining_liters` int(11) DEFAULT NULL,
  `fuel_remaining_liter` int(11) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transports`
--

INSERT INTO `transports` (`id`, `created_at`, `updated_at`, `plate_number`, `model`, `number_of_seat`, `transport_type_id`, `category`, `departure_time`, `arrival_time`, `driver_id`, `images`, `oil_change_interval_months`, `oil_change_interval_km`, `fuel_consumption`, `fuel_type`, `fuel_remaining_liters`, `fuel_remaining_liter`, `company_id`) VALUES
(2, '2025-01-11 05:04:24', '2025-05-16 03:57:22', '30 355 VAA', 'ZHONGTONG', 53, 10, 'bus', NULL, NULL, 1, '[]', 1, 12000, 30.00, 'diesel', NULL, 110, 0),
(3, '2025-01-11 05:05:48', '2025-02-21 05:25:15', '30 081 YAA', 'ZHONGTONG', 53, 10, 'bus', NULL, NULL, 2, '[]', 1, 12000, 30.00, 'diesel', NULL, 234, 0),
(4, '2025-01-11 05:06:45', '2025-01-29 13:22:39', '30 706 FBA', 'ZONGTONG', 51, 9, 'bus', NULL, NULL, 3, '[]', 1, 12000, 33.00, 'diesel', NULL, NULL, 0),
(5, '2025-01-11 05:58:00', '2025-01-29 13:21:35', '30 745 FBA', 'ZONGTONG', 51, 9, 'bus', NULL, NULL, 5, '[]', 1, 12000, 33.00, 'diesel', NULL, NULL, 0),
(6, '2025-01-11 05:58:24', '2025-01-13 12:06:20', '30 780 FBA', 'ZONGTONG', 51, 9, 'bus', NULL, NULL, 4, NULL, 0, 0, 0.00, 'diesel', NULL, NULL, 0),
(7, '2025-01-11 05:59:01', '2025-01-29 13:23:39', '30 322 ABA', 'YUTONG', 49, 9, 'bus', NULL, NULL, 6, '[]', 1, 12000, 30.00, 'diesel', NULL, NULL, 0),
(8, '2025-01-11 06:00:14', '2025-01-29 13:25:34', '30 299 ABA', 'YUTONG', 43, 8, 'bus', NULL, NULL, 9, '[]', 1, 12000, 26.00, 'diesel', NULL, NULL, 0),
(9, '2025-01-11 06:00:39', '2025-01-29 13:25:57', '30 308 YAA', 'YUTONG', 43, 8, 'bus', NULL, NULL, 8, '[]', 1, 12000, 0.00, 'diesel', NULL, NULL, 0),
(10, '2025-01-11 06:02:15', '2025-02-21 11:49:35', '30 976 QAA', 'YUTONG', 45, 8, 'bus', NULL, NULL, 7, '[]', 1, 12000, 26.00, 'diesel', NULL, 673, 0),
(11, '2025-01-11 06:04:06', '2025-02-21 11:47:54', '30 175 VBA', 'ZONGTONG', 43, 8, 'bus', NULL, NULL, 10, '[]', 1, 12000, 30.00, 'benzin/propane', NULL, 270, 0),
(12, '2025-01-11 06:05:37', '2025-01-29 13:29:10', '85 409 HBA', 'ZONGTONG', 43, 8, 'bus', NULL, NULL, 11, '[]', 1, 12000, 30.00, 'benzin/propane', NULL, NULL, 0),
(13, '2025-01-11 06:06:56', '2025-01-29 13:29:47', '85 689 HBA', 'ZONGTONG', 35, 7, 'bus', NULL, NULL, 12, '[]', 1, 12000, 22.00, 'diesel', NULL, NULL, 0),
(14, '2025-01-11 06:07:46', '2025-02-21 05:25:37', '85 651 HBA', 'YUTONG', 33, 7, 'bus', NULL, NULL, 13, '[]', 1, 12000, 22.00, 'diesel', NULL, 135, 0),
(15, '2025-01-11 06:13:32', '2025-01-29 13:30:44', '30 637 RAA', 'YUTONG', 33, 7, 'bus', NULL, NULL, 14, '[]', 1, 12000, 22.00, 'diesel', NULL, NULL, 0),
(16, '2025-01-11 06:13:57', '2025-01-29 13:31:53', '30 517 SAA', 'YUTONG', 33, 7, 'bus', NULL, NULL, 15, '[]', 1, 12000, 22.00, 'diesel', NULL, NULL, 0),
(17, '2025-01-11 06:14:47', '2025-02-21 05:16:34', '30 422 RAA', 'YUTONG', 33, 7, 'bus', NULL, NULL, 16, '[]', 1, 12000, 22.00, 'diesel', NULL, 154, 0),
(18, '2025-01-11 06:15:49', '2025-01-29 13:33:46', '30 887 EBA', 'JOYLONG', 16, 4, 'mikro_bus', NULL, NULL, 17, '[]', 1, 8000, 17.00, 'benzin/propane', NULL, NULL, 0),
(19, '2025-01-11 06:16:29', '2025-01-29 13:34:37', '30 247 FBA', 'JOYLONG', 16, 4, 'mikro_bus', NULL, NULL, 18, '[]', 1, 8000, 17.00, 'diesel', NULL, NULL, 0),
(20, '2025-01-11 06:30:09', '2025-01-29 13:35:03', '30M128CB', 'NEXIA', 3, 2, 'car', NULL, NULL, 19, '[]', 1, 5000, 10.00, 'diesel', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `transport_prices`
--

CREATE TABLE `transport_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cost` decimal(8,2) NOT NULL DEFAULT 0.00,
  `transport_type_id` bigint(20) UNSIGNED NOT NULL,
  `price_type` enum('per_day','per_pickup_dropoff','vip','economy','business','po_gorodu') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_prices`
--

INSERT INTO `transport_prices` (`id`, `created_at`, `updated_at`, `cost`, `transport_type_id`, `price_type`) VALUES
(3, '2025-01-11 04:45:36', '2025-01-11 04:45:36', 50.00, 2, 'per_day'),
(4, '2025-01-11 04:46:05', '2025-01-11 04:46:05', 15.00, 2, 'per_pickup_dropoff'),
(5, '2025-01-11 04:46:52', '2025-01-11 04:46:52', 80.00, 3, 'per_day'),
(6, '2025-01-11 04:46:52', '2025-01-11 04:46:52', 20.00, 3, 'per_pickup_dropoff'),
(7, '2025-01-11 04:47:30', '2025-01-11 04:47:30', 100.00, 4, 'per_day'),
(8, '2025-01-11 04:47:30', '2025-01-11 04:47:30', 25.00, 4, 'per_pickup_dropoff'),
(9, '2025-01-11 04:48:27', '2025-01-11 04:48:27', 120.00, 5, 'per_day'),
(10, '2025-01-11 04:48:27', '2025-01-11 04:48:27', 40.00, 5, 'per_pickup_dropoff'),
(11, '2025-01-11 04:49:12', '2025-01-11 04:49:12', 150.00, 6, 'per_day'),
(12, '2025-01-11 04:49:12', '2025-01-11 04:49:12', 40.00, 6, 'per_pickup_dropoff'),
(13, '2025-01-11 04:50:35', '2025-01-11 04:50:35', 190.00, 7, 'per_day'),
(14, '2025-01-11 04:50:35', '2025-01-11 04:50:35', 50.00, 7, 'per_pickup_dropoff'),
(15, '2025-01-11 04:53:12', '2025-01-11 04:53:12', 200.00, 8, 'per_day'),
(16, '2025-01-11 04:53:12', '2025-01-11 04:53:12', 50.00, 8, 'per_pickup_dropoff'),
(17, '2025-01-11 04:53:12', '2025-01-11 04:53:12', 150.00, 8, 'economy'),
(18, '2025-01-11 04:53:44', '2025-01-11 04:53:44', 220.00, 9, 'per_day'),
(19, '2025-01-11 04:54:08', '2025-01-11 04:54:08', 220.00, 10, 'per_day'),
(20, '2025-01-11 04:56:58', '2025-01-11 04:56:58', 27.00, 11, 'economy'),
(21, '2025-01-11 04:56:58', '2025-01-11 04:56:58', 40.00, 11, 'business'),
(22, '2025-01-11 04:56:58', '2025-01-11 04:56:58', 52.00, 11, 'vip'),
(23, '2025-01-11 04:58:55', '2025-01-11 04:58:55', 27.00, 12, 'economy'),
(24, '2025-01-11 05:00:04', '2025-01-11 05:35:58', 35.00, 13, 'economy'),
(25, '2025-01-11 05:00:46', '2025-01-11 05:00:46', 27.00, 14, 'economy'),
(26, '2025-01-11 05:01:45', '2025-01-11 05:01:45', 27.00, 15, 'economy'),
(27, '2025-01-11 05:21:40', '2025-01-11 05:21:40', 40.00, 12, 'business'),
(28, '2025-01-11 05:21:40', '2025-01-11 05:21:40', 52.00, 12, 'vip'),
(29, '2025-01-11 05:23:20', '2025-01-11 05:23:20', 40.00, 15, 'business'),
(30, '2025-01-11 05:23:20', '2025-01-11 05:23:20', 52.00, 15, 'vip'),
(31, '2025-01-11 05:24:21', '2025-01-11 05:24:21', 40.00, 14, 'business'),
(32, '2025-01-11 05:24:21', '2025-01-11 05:24:21', 52.00, 14, 'vip'),
(33, '2025-01-11 05:35:58', '2025-01-11 05:35:58', 51.00, 13, 'business'),
(34, '2025-01-11 05:37:44', '2025-01-11 05:37:44', 35.00, 16, 'economy'),
(35, '2025-01-11 05:37:44', '2025-01-11 05:37:44', 51.00, 16, 'business'),
(36, '2025-01-11 05:44:13', '2025-01-11 05:44:13', 15.00, 17, 'economy'),
(37, '2025-01-11 05:44:13', '2025-01-11 05:44:13', 19.00, 17, 'business'),
(38, '2025-01-11 05:45:11', '2025-01-11 05:45:11', 15.00, 18, 'economy'),
(39, '2025-01-11 05:45:11', '2025-01-11 05:45:11', 19.00, 18, 'business'),
(40, '2025-01-11 05:46:06', '2025-01-11 05:46:06', 50.00, 10, 'per_pickup_dropoff'),
(41, '2025-01-11 05:59:36', '2025-01-11 05:59:36', 50.00, 9, 'per_pickup_dropoff'),
(42, '2025-01-11 14:22:46', '2025-01-11 14:22:46', 45.00, 2, 'po_gorodu'),
(43, '2025-02-20 09:44:47', '2025-02-21 10:02:21', 48.00, 19, 'economy'),
(44, '2025-02-20 09:44:47', '2025-02-21 10:02:21', 144.00, 19, 'business'),
(45, '2025-02-20 09:44:47', '2025-02-21 10:02:21', 159.00, 19, 'vip'),
(46, '2025-02-20 09:49:35', '2025-02-21 10:06:11', 41.00, 20, 'economy'),
(47, '2025-02-20 09:49:35', '2025-02-21 10:06:11', 102000.00, 20, 'business'),
(48, '2025-02-20 09:49:35', '2025-02-21 10:06:11', 118.00, 20, 'vip'),
(49, '2025-02-20 11:51:11', '2025-02-20 11:51:11', 85.00, 21, 'economy'),
(50, '2025-02-20 11:51:11', '2025-02-20 11:51:11', 146.00, 21, 'business'),
(51, '2025-02-20 11:51:11', '2025-02-20 11:51:11', 162.00, 21, 'vip'),
(52, '2025-02-20 11:55:48', '2025-02-20 11:55:48', 43.00, 22, 'economy'),
(53, '2025-02-20 11:55:48', '2025-02-20 11:55:48', 104.00, 22, 'business'),
(54, '2025-02-20 11:55:48', '2025-02-20 11:55:48', 120.00, 22, 'vip'),
(55, '2025-02-20 12:16:06', '2025-02-20 12:16:06', 30.00, 23, 'economy'),
(56, '2025-02-20 12:16:06', '2025-02-20 12:16:06', 104.00, 23, 'business'),
(57, '2025-02-20 12:16:06', '2025-02-20 12:16:06', 120.00, 23, 'vip'),
(58, '2025-02-20 12:28:40', '2025-02-20 12:28:40', 30.00, 24, 'economy'),
(59, '2025-02-20 12:28:40', '2025-02-20 12:28:40', 77.00, 24, 'business'),
(60, '2025-02-20 12:28:40', '2025-02-20 12:28:40', 85.00, 24, 'vip'),
(61, '2025-02-20 12:32:26', '2025-02-20 12:32:26', 31.00, 25, 'economy'),
(62, '2025-02-20 12:32:26', '2025-02-20 12:32:26', 100.00, 25, 'business'),
(63, '2025-02-20 12:32:26', '2025-02-20 12:32:26', 115.00, 25, 'vip'),
(64, '2025-02-20 12:47:52', '2025-02-20 12:47:52', 66.00, 26, 'economy'),
(65, '2025-02-20 12:47:52', '2025-02-20 12:47:52', 123.00, 26, 'business'),
(66, '2025-02-20 12:47:52', '2025-02-20 12:47:52', 138.00, 26, 'vip'),
(67, '2025-02-20 12:54:51', '2025-02-20 12:54:51', 55.30, 27, 'economy'),
(68, '2025-02-20 12:54:51', '2025-02-20 12:54:51', 110.00, 27, 'business'),
(69, '2025-02-20 12:54:51', '2025-02-20 12:54:51', 115.00, 27, 'vip'),
(70, '2025-02-21 10:23:54', '2025-02-21 10:23:54', 62.00, 28, 'economy'),
(71, '2025-02-21 10:23:54', '2025-02-21 10:23:54', 104.00, 28, 'business'),
(72, '2025-02-21 10:23:54', '2025-02-21 10:23:54', 115.00, 28, 'vip'),
(73, '2025-02-21 10:27:54', '2025-02-21 10:27:54', 41.00, 29, 'economy'),
(74, '2025-02-21 10:27:54', '2025-02-21 10:27:54', 76.00, 29, 'business'),
(75, '2025-02-21 10:27:54', '2025-02-21 10:27:54', 92.00, 29, 'vip'),
(76, '2025-02-21 10:27:56', '2025-02-21 10:27:56', 41.00, 30, 'economy'),
(77, '2025-02-21 10:27:56', '2025-02-21 10:27:56', 76.00, 30, 'business'),
(78, '2025-02-21 10:27:56', '2025-02-21 10:27:56', 92.00, 30, 'vip'),
(79, '2025-02-21 10:40:19', '2025-02-21 10:40:19', 34.00, 31, 'economy'),
(80, '2025-02-21 10:40:20', '2025-02-21 10:40:20', 41.00, 31, 'business'),
(81, '2025-02-21 10:40:20', '2025-02-21 10:40:20', 87.00, 31, 'vip'),
(82, '2025-02-24 09:30:20', '2025-02-24 09:30:20', 53.00, 32, 'economy'),
(83, '2025-02-24 09:30:20', '2025-02-24 09:30:20', 104.00, 32, 'business'),
(84, '2025-02-24 09:30:20', '2025-02-24 09:30:20', 119.00, 32, 'vip');

-- --------------------------------------------------------

--
-- Table structure for table `transport_types`
--

CREATE TABLE `transport_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `cost` decimal(8,2) NOT NULL DEFAULT 0.00,
  `price_type` enum('per_day','per_pickup_dropoff','po_gorodu') NOT NULL,
  `category` enum('bus','car','mikro_bus','mini_van','air','rail') NOT NULL,
  `running_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Dumping data for table `transport_types`
--

INSERT INTO `transport_types` (`id`, `created_at`, `updated_at`, `type`, `cost`, `price_type`, `category`, `running_days`) VALUES
(2, '2025-01-11 04:45:36', '2025-01-11 04:45:36', 'sedn', 0.00, 'per_day', 'car', NULL),
(3, '2025-01-11 04:46:52', '2025-01-11 05:51:22', 'Hyundai H1 3-5', 0.00, 'per_day', 'mikro_bus', NULL),
(4, '2025-01-11 04:47:30', '2025-01-11 05:51:49', 'Joylong 6-8', 0.00, 'per_day', 'mikro_bus', NULL),
(5, '2025-01-11 04:48:27', '2025-01-11 04:48:27', 'Sprintor', 0.00, 'per_day', 'mikro_bus', NULL),
(6, '2025-01-11 04:49:12', '2025-01-11 04:49:12', 'Coster', 0.00, 'per_day', 'mikro_bus', NULL),
(7, '2025-01-11 04:50:35', '2025-01-11 04:50:35', '33 seat', 0.00, 'per_day', 'bus', NULL),
(8, '2025-01-11 04:53:12', '2025-01-11 04:53:12', '43', 0.00, 'per_day', 'bus', NULL),
(9, '2025-01-11 04:53:44', '2025-01-11 04:53:44', '50', 0.00, 'per_day', 'bus', NULL),
(10, '2025-01-11 04:54:08', '2025-01-11 04:54:08', '53', 0.00, 'per_day', 'bus', NULL),
(11, '2025-01-11 04:56:58', '2025-01-11 04:56:58', 'AFRASIYAB  Tosh-Sam', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(12, '2025-01-11 04:58:55', '2025-01-11 04:58:55', 'AFRASIYOB  Sam-Bux', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(13, '2025-01-11 05:00:03', '2025-01-11 05:00:03', 'AFRASITAB Tosh-Bux', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(14, '2025-01-11 05:00:46', '2025-01-11 05:00:46', 'AFRASIYOB  Bux-Sam', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(15, '2025-01-11 05:01:45', '2025-01-11 05:01:45', 'ASFRASIYAB  Sam-Tash', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(16, '2025-01-11 05:37:44', '2025-01-11 05:38:19', 'AFRASIYAB  Bux-Tosh', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(17, '2025-01-11 05:44:13', '2025-01-11 05:44:13', 'AFRASIYAB  Tosh-Marg', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(18, '2025-01-11 05:45:11', '2025-01-11 05:45:11', 'AFRASIYAB  Marg-Tosh', 0.00, 'per_day', 'rail', '[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]'),
(19, '2025-02-20 09:44:47', '2025-02-21 10:02:21', 'Uz airways Nukus-Toshkent', 0.00, 'per_day', 'air', NULL),
(20, '2025-02-20 09:49:35', '2025-02-21 10:06:11', 'Uz airways Namangan-Toshkent', 0.00, 'per_day', 'air', NULL),
(21, '2025-02-20 11:51:11', '2025-02-20 11:51:11', 'Uz airways Toshkent-Urganch', 0.00, 'per_day', 'air', NULL),
(22, '2025-02-20 11:55:48', '2025-02-20 11:55:48', 'Uz airways Toshkent-Namangan Hy 91', 0.00, 'per_day', 'air', NULL),
(23, '2025-02-20 12:16:06', '2025-02-20 12:16:06', 'Uz airways Toshkent-Farg\'ona ', 0.00, 'per_day', 'air', NULL),
(24, '2025-02-20 12:28:40', '2025-02-20 12:28:40', 'Uz airways Toshkent-Qarshi', 0.00, 'per_day', 'air', NULL),
(25, '2025-02-20 12:32:26', '2025-02-20 12:32:26', 'Uz airways Toshkent-Samarqand', 0.00, 'per_day', 'air', NULL),
(26, '2025-02-20 12:47:52', '2025-02-20 12:47:52', 'Uz airways Toshkent-Buxoro', 0.00, 'per_day', 'air', NULL),
(27, '2025-02-20 12:54:51', '2025-02-20 12:54:51', 'Uz airways Toshkent-Nukus', 0.00, 'per_day', 'air', NULL),
(28, '2025-02-21 10:23:54', '2025-02-21 10:23:54', 'Uz airways Urganch-Toshkent', 0.00, 'per_day', 'air', NULL),
(29, '2025-02-21 10:27:54', '2025-02-21 10:27:54', 'Uz airways Farg\'ona-Toshkent', 0.00, 'per_day', 'air', NULL),
(30, '2025-02-21 10:27:56', '2025-02-21 10:27:56', 'Uz airways Farg\'ona-Toshkent', 0.00, 'per_day', 'air', NULL),
(31, '2025-02-21 10:40:19', '2025-02-21 10:40:19', 'Uz airways Qarshi-Toshkent', 0.00, 'per_day', 'air', NULL),
(32, '2025-02-24 09:30:20', '2025-02-24 09:30:20', 'Uz airways Samarqand-Toshkent', 0.00, 'per_day', 'air', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'Tolib', 'tolib71@mail.ru', NULL, '$2y$12$UPsLsbvomMz2Jn8BzNgel.9Fn5Jy0dvdqb6ud8TaYBu/r5XPdTn1y', '9925NwtVTz4e7gpQUtdM173Tlv7JtlGLy48pAYPddgcSflpJz94wd6srFLEh', '2025-01-11 04:43:41', '2025-01-11 04:43:41'),
(3, 'Odil', 'odilorg@gmail.com', NULL, '$2y$12$P4dyTXxpOV5ov8FijeUEReadGEmGqzEScNMgvosusd1Hg5qWZB8ie', 'nQoKKCfg1S537IA7KrGdvWUOOlmVZ1wsmBgvatzw1mEeThneTkgNbJyRlk3i', '2025-09-01 03:14:04', '2025-09-01 03:14:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amenity_room`
--
ALTER TABLE `amenity_room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `amenity_room_amenity_id_foreign` (`amenity_id`),
  ADD KEY `amenity_room_room_id_foreign` (`room_id`);

--
-- Indexes for table `amenity_transport`
--
ALTER TABLE `amenity_transport`
  ADD PRIMARY KEY (`id`),
  ADD KEY `amenity_transport_transport_id_foreign` (`transport_id`),
  ADD KEY `amenity_transport_amenity_id_foreign` (`amenity_id`);

--
-- Indexes for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_requests_tour_id_foreign` (`tour_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `city_distances`
--
ALTER TABLE `city_distances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `city_tour_day`
--
ALTER TABLE `city_tour_day`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_tour_day_tour_day_id_foreign` (`tour_day_id`),
  ADD KEY `city_tour_day_city_id_foreign` (`city_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_phone_unique` (`phone`),
  ADD UNIQUE KEY `drivers_email_unique` (`email`),
  ADD UNIQUE KEY `drivers_license_number_unique` (`license_number`);

--
-- Indexes for table `estimates`
--
ALTER TABLE `estimates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estimates_estimate_number_unique` (`estimate_number`),
  ADD KEY `fk_estimates_customer_id` (`customer_id`),
  ADD KEY `fk_estimates_tour_id` (`tour_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guide_spoken_language`
--
ALTER TABLE `guide_spoken_language`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_spoken_language_guide_id_foreign` (`guide_id`),
  ADD KEY `guide_spoken_language_spoken_language_id_foreign` (`spoken_language_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `hotels_company_id_foreign` (`company_id`);

--
-- Indexes for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_rooms_tour_day_hotel_id_foreign` (`tour_day_hotel_id`),
  ADD KEY `hotel_rooms_room_id_foreign` (`room_id`);

--
-- Indexes for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itineraries_transport_id_foreign` (`transport_id`),
  ADD KEY `itineraries_tour_id_foreign` (`tour_id`);

--
-- Indexes for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itinerary_items_itinerary_id_foreign` (`itinerary_id`),
  ADD KEY `itinerary_items_city_distance_id_foreign` (`city_distance_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meal_types`
--
ALTER TABLE `meal_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_meal_types_restaurant_id` (`restaurant_id`);

--
-- Indexes for table `meal_type_restaurant_tour_days`
--
ALTER TABLE `meal_type_restaurant_tour_days`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_meal_type_restaurant_tour_days_meal_type_id` (`meal_type_id`),
  ADD KEY `fk_meal_type_restaurant_tour_days_restaurant_id` (`restaurant_id`),
  ADD KEY `fk_meal_type_restaurant_tour_days_tour_day_id` (`tour_day_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monuments`
--
ALTER TABLE `monuments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_monuments_city_id` (`city_id`),
  ADD KEY `monuments_company_id_foreign` (`company_id`);

--
-- Indexes for table `monument_tour_days`
--
ALTER TABLE `monument_tour_days`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monument_tour_days_monument_id_foreign` (`monument_id`),
  ADD KEY `monument_tour_days_tour_day_id_foreign` (`tour_day_id`);

--
-- Indexes for table `oil_changes`
--
ALTER TABLE `oil_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oil_changes_transport_id_foreign` (`transport_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurants_city_id_foreign` (`city_id`),
  ADD KEY `restaurants_company_id_foreign` (`company_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_hotel_id_foreign` (`hotel_id`),
  ADD KEY `rooms_room_type_id_foreign` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `spoken_languages`
--
ALTER TABLE `spoken_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tours_tour_number_unique` (`tour_number`);

--
-- Indexes for table `tour_days`
--
ALTER TABLE `tour_days`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `fk_tour_days_city_id` (`city_id`),
  ADD KEY `fk_tour_days_restaurant_id` (`restaurant_id`);

--
-- Indexes for table `tour_day_hotels`
--
ALTER TABLE `tour_day_hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_day_hotels_tour_day_id_foreign` (`tour_day_id`),
  ADD KEY `tour_day_hotels_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `tour_day_hotel_room`
--
ALTER TABLE `tour_day_hotel_room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_day_id` (`tour_day_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `tour_day_transport`
--
ALTER TABLE `tour_day_transport`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_day_transport_tour_day_id_foreign` (`tour_day_id`),
  ADD KEY `tour_day_transport_transport_id_foreign` (`transport_type_id`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transports_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `transport_prices`
--
ALTER TABLE `transport_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transport_prices_transport_type_id` (`transport_type_id`);

--
-- Indexes for table `transport_types`
--
ALTER TABLE `transport_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `amenity_room`
--
ALTER TABLE `amenity_room`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=634;

--
-- AUTO_INCREMENT for table `amenity_transport`
--
ALTER TABLE `amenity_transport`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `booking_requests`
--
ALTER TABLE `booking_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `city_distances`
--
ALTER TABLE `city_distances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `city_tour_day`
--
ALTER TABLE `city_tour_day`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `estimates`
--
ALTER TABLE `estimates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `guide_spoken_language`
--
ALTER TABLE `guide_spoken_language`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `itineraries`
--
ALTER TABLE `itineraries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meal_types`
--
ALTER TABLE `meal_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `meal_type_restaurant_tour_days`
--
ALTER TABLE `meal_type_restaurant_tour_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `monuments`
--
ALTER TABLE `monuments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monument_tour_days`
--
ALTER TABLE `monument_tour_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `oil_changes`
--
ALTER TABLE `oil_changes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `spoken_languages`
--
ALTER TABLE `spoken_languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tour_days`
--
ALTER TABLE `tour_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `tour_day_hotels`
--
ALTER TABLE `tour_day_hotels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `tour_day_hotel_room`
--
ALTER TABLE `tour_day_hotel_room`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tour_day_transport`
--
ALTER TABLE `tour_day_transport`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transport_prices`
--
ALTER TABLE `transport_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `transport_types`
--
ALTER TABLE `transport_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amenity_room`
--
ALTER TABLE `amenity_room`
  ADD CONSTRAINT `amenity_room_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `amenity_room_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `amenity_transport`
--
ALTER TABLE `amenity_transport`
  ADD CONSTRAINT `amenity_transport_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `amenity_transport_transport_id_foreign` FOREIGN KEY (`transport_id`) REFERENCES `transports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD CONSTRAINT `booking_requests_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `city_tour_day`
--
ALTER TABLE `city_tour_day`
  ADD CONSTRAINT `city_tour_day_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `city_tour_day_tour_day_id_foreign` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `estimates`
--
ALTER TABLE `estimates`
  ADD CONSTRAINT `fk_estimates_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_estimates_tour_id` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guide_spoken_language`
--
ALTER TABLE `guide_spoken_language`
  ADD CONSTRAINT `guide_spoken_language_guide_id_foreign` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_spoken_language_spoken_language_id_foreign` FOREIGN KEY (`spoken_language_id`) REFERENCES `spoken_languages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD CONSTRAINT `hotel_rooms_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_rooms_tour_day_hotel_id_foreign` FOREIGN KEY (`tour_day_hotel_id`) REFERENCES `tour_day_hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD CONSTRAINT `itineraries_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itineraries_transport_id_foreign` FOREIGN KEY (`transport_id`) REFERENCES `transports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD CONSTRAINT `itinerary_items_city_distance_id_foreign` FOREIGN KEY (`city_distance_id`) REFERENCES `city_distances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerary_items_itinerary_id_foreign` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_types`
--
ALTER TABLE `meal_types`
  ADD CONSTRAINT `fk_meal_types_restaurant_id` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_type_restaurant_tour_days`
--
ALTER TABLE `meal_type_restaurant_tour_days`
  ADD CONSTRAINT `fk_meal_type_restaurant_tour_days_meal_type_id` FOREIGN KEY (`meal_type_id`) REFERENCES `meal_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_meal_type_restaurant_tour_days_restaurant_id` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_meal_type_restaurant_tour_days_tour_day_id` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monuments`
--
ALTER TABLE `monuments`
  ADD CONSTRAINT `fk_monuments_city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `monuments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `monument_tour_days`
--
ALTER TABLE `monument_tour_days`
  ADD CONSTRAINT `monument_tour_days_monument_id_foreign` FOREIGN KEY (`monument_id`) REFERENCES `monuments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monument_tour_days_tour_day_id_foreign` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oil_changes`
--
ALTER TABLE `oil_changes`
  ADD CONSTRAINT `oil_changes_transport_id_foreign` FOREIGN KEY (`transport_id`) REFERENCES `transports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_days`
--
ALTER TABLE `tour_days`
  ADD CONSTRAINT `tour_days_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_days_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_days_ibfk_3` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_day_hotels`
--
ALTER TABLE `tour_day_hotels`
  ADD CONSTRAINT `tour_day_hotels_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_day_hotels_tour_day_id_foreign` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_day_hotel_room`
--
ALTER TABLE `tour_day_hotel_room`
  ADD CONSTRAINT `tour_day_hotel_room_ibfk_1` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_day_hotel_room_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_day_hotel_room_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_day_transport`
--
ALTER TABLE `tour_day_transport`
  ADD CONSTRAINT `tour_day_transport_ibfk_1` FOREIGN KEY (`transport_type_id`) REFERENCES `transport_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_day_transport_tour_day_id_foreign` FOREIGN KEY (`tour_day_id`) REFERENCES `tour_days` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transports`
--
ALTER TABLE `transports`
  ADD CONSTRAINT `transports_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transport_prices`
--
ALTER TABLE `transport_prices`
  ADD CONSTRAINT `fk_transport_prices_transport_type_id` FOREIGN KEY (`transport_type_id`) REFERENCES `transport_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
