-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 16, 2021 at 06:32 PM
-- Server version: 8.0.23-0ubuntu0.20.04.1
-- PHP Version: 7.4.8

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint NOT NULL,
  `cdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int NOT NULL,
  `lang` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `avatar_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_post` int DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `banned` tinyint(1) DEFAULT NULL,
  `lastReplyDate` datetime DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_file`
--

CREATE TABLE `workingforum_file` (
  `id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint NOT NULL,
  `cdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_forum`
--

CREATE TABLE `workingforum_forum` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_post`
--

CREATE TABLE `workingforum_post` (
  `id` int NOT NULL,
  `thread_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `cdate` datetime NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `moderateReason` longtext COLLATE utf8_unicode_ci,
  `voteUp` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_post_report`
--

CREATE TABLE `workingforum_post_report` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `cdate` datetime NOT NULL,
  `processed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_post_vote`
--

CREATE TABLE `workingforum_post_vote` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `thread_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `voteType` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_rules`
--

CREATE TABLE `workingforum_rules` (
  `id` int NOT NULL,
  `lang` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_setting`
--

CREATE TABLE `workingforum_setting` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_subforum`
--

CREATE TABLE `workingforum_subforum` (
  `id` int NOT NULL,
  `forum_id` int DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_thread` int DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_post` int DEFAULT NULL,
  `last_reply_date` datetime DEFAULT NULL,
  `allowed_roles` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `lastReplyUser` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_subscription`
--

CREATE TABLE `workingforum_subscription` (
  `id` int NOT NULL,
  `thread_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workingforum_thread`
--

CREATE TABLE `workingforum_thread` (
  `id` int NOT NULL,
  `subforum_id` int NOT NULL,
  `author_id` int NOT NULL,
  `cdate` datetime NOT NULL,
  `nbReplies` int NOT NULL,
  `lastReplyDate` datetime NOT NULL,
  `resolved` tinyint(1) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sublabel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pin` tinyint(1) DEFAULT NULL,
  `lastReplyUser` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8C9F3610B548B0F` (`path`),
  ADD KEY `IDX_8C9F36104B89032C` (`post_id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`);

--
-- Indexes for table `workingforum_file`
--
ALTER TABLE `workingforum_file`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_CA43646BB548B0F` (`path`),
  ADD KEY `IDX_CA43646B4B89032C` (`post_id`);

--
-- Indexes for table `workingforum_forum`
--
ALTER TABLE `workingforum_forum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workingforum_post`
--
ALTER TABLE `workingforum_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1C563EF6E2904019` (`thread_id`),
  ADD KEY `IDX_1C563EF6A76ED395` (`user_id`);

--
-- Indexes for table `workingforum_post_report`
--
ALTER TABLE `workingforum_post_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A95E2B754B89032C` (`post_id`),
  ADD KEY `IDX_A95E2B75A76ED395` (`user_id`);

--
-- Indexes for table `workingforum_post_vote`
--
ALTER TABLE `workingforum_post_vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5EF3D04F4B89032C` (`post_id`),
  ADD KEY `IDX_5EF3D04FE2904019` (`thread_id`),
  ADD KEY `IDX_5EF3D04FA76ED395` (`user_id`);

--
-- Indexes for table `workingforum_rules`
--
ALTER TABLE `workingforum_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workingforum_setting`
--
ALTER TABLE `workingforum_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workingforum_subforum`
--
ALTER TABLE `workingforum_subforum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9EACE2E229CCBAD0` (`forum_id`),
  ADD KEY `IDX_9EACE2E21F7EE8A0` (`lastReplyUser`);

--
-- Indexes for table `workingforum_subscription`
--
ALTER TABLE `workingforum_subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_54F96036E2904019` (`thread_id`),
  ADD KEY `IDX_54F96036A76ED395` (`user_id`);

--
-- Indexes for table `workingforum_thread`
--
ALTER TABLE `workingforum_thread`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_788E9ABA225C0759` (`subforum_id`),
  ADD KEY `IDX_788E9ABAF675F31B` (`author_id`),
  ADD KEY `IDX_788E9ABA1F7EE8A0` (`lastReplyUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_file`
--
ALTER TABLE `workingforum_file`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_forum`
--
ALTER TABLE `workingforum_forum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_post`
--
ALTER TABLE `workingforum_post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_post_report`
--
ALTER TABLE `workingforum_post_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_post_vote`
--
ALTER TABLE `workingforum_post_vote`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_rules`
--
ALTER TABLE `workingforum_rules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_setting`
--
ALTER TABLE `workingforum_setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_subforum`
--
ALTER TABLE `workingforum_subforum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_subscription`
--
ALTER TABLE `workingforum_subscription`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workingforum_thread`
--
ALTER TABLE `workingforum_thread`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `FK_8C9F36104B89032C` FOREIGN KEY (`post_id`) REFERENCES `workingforum_post` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_file`
--
ALTER TABLE `workingforum_file`
  ADD CONSTRAINT `FK_CA43646B4B89032C` FOREIGN KEY (`post_id`) REFERENCES `workingforum_post` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_post`
--
ALTER TABLE `workingforum_post`
  ADD CONSTRAINT `FK_1C563EF6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_1C563EF6E2904019` FOREIGN KEY (`thread_id`) REFERENCES `workingforum_thread` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_post_report`
--
ALTER TABLE `workingforum_post_report`
  ADD CONSTRAINT `FK_A95E2B754B89032C` FOREIGN KEY (`post_id`) REFERENCES `workingforum_post` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_A95E2B75A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_post_vote`
--
ALTER TABLE `workingforum_post_vote`
  ADD CONSTRAINT `FK_5EF3D04F4B89032C` FOREIGN KEY (`post_id`) REFERENCES `workingforum_post` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_5EF3D04FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_5EF3D04FE2904019` FOREIGN KEY (`thread_id`) REFERENCES `workingforum_thread` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_subforum`
--
ALTER TABLE `workingforum_subforum`
  ADD CONSTRAINT `FK_9EACE2E21F7EE8A0` FOREIGN KEY (`lastReplyUser`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_9EACE2E229CCBAD0` FOREIGN KEY (`forum_id`) REFERENCES `workingforum_forum` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_subscription`
--
ALTER TABLE `workingforum_subscription`
  ADD CONSTRAINT `FK_54F96036A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_54F96036E2904019` FOREIGN KEY (`thread_id`) REFERENCES `workingforum_thread` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `workingforum_thread`
--
ALTER TABLE `workingforum_thread`
  ADD CONSTRAINT `FK_788E9ABA1F7EE8A0` FOREIGN KEY (`lastReplyUser`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_788E9ABA225C0759` FOREIGN KEY (`subforum_id`) REFERENCES `workingforum_subforum` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_788E9ABAF675F31B` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
