-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2020 at 12:57 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `muse`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstract` longtext COLLATE utf8mb4_unicode_ci,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_category`
--

CREATE TABLE `post_category` (
  `id` int(11) NOT NULL,
  `supercategory` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_category`
--

INSERT INTO `post_category` (`id`, `supercategory`, `name`, `icon`) VALUES
(1, 'meta', 'Muse', 'meta/muse.png'),
(2, 'philosophy', 'epistemology', 'philosophy/epistemology.png'),
(3, 'philosophy', 'ethics', 'philosophy/ethics.png'),
(4, 'philosophy', 'existentialism', 'philosophy/existentialism.png'),
(5, 'philosophy', 'hedonism', 'philosophy/hedonism.png'),
(6, 'philosophy', 'law', 'philosophy/law.png'),
(7, 'philosophy', 'logic', 'philosophy/logic.png'),
(8, 'philosophy', 'nihilism', 'philosophy/nihilism.png'),
(9, 'philosophy', 'stoicism', 'philosophy/stoicism.png'),
(10, 'science', 'astronomy', 'science/astronomy.png'),
(11, 'science', 'biology', 'science/biology.png'),
(12, 'science', 'chemistry', 'science/chemistry.png'),
(13, 'science', 'computer science', 'science/computer_science.png'),
(14, 'science', 'criminology', 'science/criminology.png'),
(15, 'science', 'earth science', 'science/earth_science.png'),
(16, 'science', 'mathematics', 'science/mathematics.png'),
(17, 'science', 'medical science', 'science/medical_science.png'),
(18, 'science', 'physics', 'science/physics.png'),
(19, 'science', 'psychology', 'science/psychology.png'),
(20, 'technology', 'aerospace technology', 'technology/aerospace_technology.png'),
(21, 'technology', 'agriculture', 'technology/agriculture.png'),
(22, 'technology', 'artificial intelligence', 'technology/artificial_intelligence.png'),
(23, 'technology', 'computing', 'technology/computing.png'),
(24, 'technology', 'electronics', 'technology/electronics.png'),
(25, 'technology', 'energy', 'technology/energy.png'),
(26, 'technology', 'engineering', 'technology/engineering.png'),
(27, 'technology', 'medical technology', 'technology/medical_technology.png'),
(28, 'technology', 'robotics', 'technology/robotics.png'),
(29, 'innovation', 'entrepreneurship', 'innovation/entrepreneurship.png'),
(30, 'innovation', 'futurism', 'innovation/futurism.png'),
(31, 'innovation', 'humanitarianism', 'innovation/humanitarianism.png'),
(32, 'innovation', 'idea', 'innovation/idea.png'),
(33, 'innovation', 'proposal', 'innovation/proposal.png'),
(34, 'strategy', 'game theory', 'strategy/game_theory.png'),
(35, 'strategy', 'interpersonal influence', 'strategy/interpersonal_influence.png'),
(36, 'strategy', 'marketing', 'strategy/marketing.png'),
(37, 'strategy', 'military strategy', 'strategy/military_strategy.png'),
(38, 'strategy', 'propaganda', 'strategy/propaganda.png'),
(39, 'politics', 'anarchism', 'politics/anarchism.png'),
(40, 'politics', 'communism', 'politics/communism.png'),
(41, 'politics', 'conservatism', 'politics/conservatism.png'),
(42, 'politics', 'fascism', 'politics/fascism.png'),
(43, 'politics', 'liberalism', 'politics/liberalism.png'),
(44, 'politics', 'libertarianism', 'politics/libertarianism.png'),
(45, 'politics', 'miscellaneous politics', 'politics/miscellaneous_politics.png'),
(46, 'politics', 'monarchy', 'politics/monarchy.png'),
(47, 'politics', 'socialism', 'politics/socialism.png'),
(48, 'religion', 'buddhism', 'religion/buddhism.png'),
(49, 'religion', 'christianity', 'religion/christianity.png'),
(50, 'religion', 'hinduism', 'religion/hinduism.png'),
(51, 'religion', 'islam', 'religion/islam.png'),
(52, 'religion', 'judaism', 'religion/judaism.png'),
(53, 'religion', 'miscellaneous religion', 'religion/miscellaneous_religion.png'),
(54, 'religion', 'mormonism', 'religion/mormonism.png'),
(55, 'religion', 'sikhism', 'religion/sikhism.png'),
(56, 'religion', 'taoism', 'religion/taoism.png'),
(57, 'society', 'culture', 'society/culture.png'),
(58, 'society', 'film', 'society/film.png'),
(59, 'society', 'fine arts', 'society/fine_arts.png'),
(60, 'society', 'infrastructure', 'society/infrastructure.png'),
(61, 'society', 'liberal arts', 'society/liberal_arts.png'),
(62, 'society', 'music', 'society/music.png'),
(63, 'society', 'sports', 'society/sports.png'),
(64, 'society', 'theater', 'society/theater.png'),
(65, 'society', 'video games', 'society/video_games.png');

-- --------------------------------------------------------

--
-- Table structure for table `post|post_category`
--

CREATE TABLE `post|post_category` (
  `post_id` int(11) NOT NULL,
  `post_category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526C4B89032C` (`post_id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`);

--
-- Indexes for table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8DA76ED395` (`user_id`);

--
-- Indexes for table `post_category`
--
ALTER TABLE `post_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post|post_category`
--
ALTER TABLE `post|post_category`
  ADD PRIMARY KEY (`post_id`,`post_category_id`),
  ADD KEY `IDX_17D573704B89032C` (`post_id`),
  ADD KEY `IDX_17D57370FE0617CD` (`post_category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `post_category`
--
ALTER TABLE `post_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `post|post_category`
--
ALTER TABLE `post|post_category`
  ADD CONSTRAINT `FK_A6D02E734B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A6D02E73FE0617CD` FOREIGN KEY (`post_category_id`) REFERENCES `post_category` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;