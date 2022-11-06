-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2022 at 04:59 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anime_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `anime`
--

CREATE TABLE `anime` (
  `anime_id` int(9) NOT NULL,
  `production_id` int(9) NOT NULL,
  `genre_list_id` int(9) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `year` int(4) NOT NULL DEFAULT 2022,
  `nb_releases` int(4) NOT NULL DEFAULT 1,
  `cover_picture` varchar(50) NOT NULL DEFAULT 'blank.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int(9) NOT NULL,
  `genre_list_id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `genre_list`
--

CREATE TABLE `genre_list` (
  `genre_list_id` int(9) NOT NULL,
  `genre_id` int(9) NOT NULL,
  `manga_id` int(9) DEFAULT NULL,
  `anime_id` int(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

CREATE TABLE `list` (
  `list_id` int(9) NOT NULL,
  `user_id` int(9) NOT NULL,
  `anime_id` int(9) NOT NULL,
  `manga_id` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `manga`
--

CREATE TABLE `manga` (
  `manga_id` int(9) NOT NULL,
  `genre_list_id` int(9) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `year` int(4) NOT NULL DEFAULT 2022,
  `mangaka` varchar(100) NOT NULL,
  `num_of_volumes` int(4) NOT NULL DEFAULT 1,
  `cover_picture` varchar(50) NOT NULL DEFAULT 'blank.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `production_id` int(9) NOT NULL,
  `studio_id` int(9) NOT NULL,
  `anime_id` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(9) NOT NULL,
  `anime_id` int(9) DEFAULT NULL,
  `manga_id` int(9) DEFAULT NULL,
  `user_id` int(9) NOT NULL,
  `title` varchar(100) NOT NULL,
  `star_rating` decimal(1,0) NOT NULL DEFAULT 0 COMMENT 'must be between 0 and 5',
  `content` varchar(500) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `studio`
--

CREATE TABLE `studio` (
  `studio_id` int(9) NOT NULL,
  `production_id` int(9) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(9) NOT NULL,
  `watched_id` int(9) NOT NULL,
  `towatch_id` int(9) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password_hash` varchar(63) NOT NULL,
  `phone` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anime`
--
ALTER TABLE `anime`
  ADD PRIMARY KEY (`anime_id`),
  ADD UNIQUE KEY `ANIME_NAME_UNIQUE` (`name`),
  ADD KEY `ANIME_PRODUCTION_FK` (`production_id`),
  ADD KEY `ANIME_GENRELIST_FK` (`genre_list_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`),
  ADD KEY `GENRE_GENRELIST_FK` (`genre_list_id`);

--
-- Indexes for table `genre_list`
--
ALTER TABLE `genre_list`
  ADD PRIMARY KEY (`genre_list_id`),
  ADD KEY `GENRELIST_GENRE_FK` (`genre_id`),
  ADD KEY `GENRELIST_ANIME_FK` (`anime_id`),
  ADD KEY `GENRELIST_MANGA_FK` (`manga_id`);

--
-- Indexes for table `list`
--
ALTER TABLE `list`
  ADD PRIMARY KEY (`list_id`),
  ADD KEY `LIST_USER_FK` (`user_id`),
  ADD KEY `LIST_ANIME_FK` (`anime_id`),
  ADD KEY `LIST_MANGA_FK` (`manga_id`);

--
-- Indexes for table `manga`
--
ALTER TABLE `manga`
  ADD PRIMARY KEY (`manga_id`),
  ADD UNIQUE KEY `MANGA_NAME_UNIQUE` (`name`),
  ADD KEY `MANGA_GENRELIST_FK` (`genre_list_id`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`production_id`),
  ADD KEY `PRODUCTION_STUDIO_FK` (`studio_id`),
  ADD KEY `PRODUCTION_ANIME_FK` (`anime_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `REVIEW_USER_FK` (`user_id`),
  ADD KEY `REVIEW_ANIME_FK` (`anime_id`),
  ADD KEY `REVIEW_MANGA_FK` (`manga_id`);

--
-- Indexes for table `studio`
--
ALTER TABLE `studio`
  ADD PRIMARY KEY (`studio_id`),
  ADD KEY `STUDIO_PRODUCTION_FK` (`production_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `USERNAME_UNIQUE` (`username`),
  ADD UNIQUE KEY `USER_EMAIL_UNIQUE` (`email`),
  ADD KEY `USER_TOWACH_FK` (`towatch_id`),
  ADD KEY `USER_WATCHED_FK` (`watched_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anime`
--
ALTER TABLE `anime`
  MODIFY `anime_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `genre_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genre_list`
--
ALTER TABLE `genre_list`
  MODIFY `genre_list_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list`
--
ALTER TABLE `list`
  MODIFY `list_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manga`
--
ALTER TABLE `manga`
  MODIFY `manga_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `production_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studio`
--
ALTER TABLE `studio`
  MODIFY `studio_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(9) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anime`
--
ALTER TABLE `anime`
  ADD CONSTRAINT `ANIME_GENRELIST_FK` FOREIGN KEY (`genre_list_id`) REFERENCES `genre_list` (`genre_list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ANIME_PRODUCTION_FK` FOREIGN KEY (`production_id`) REFERENCES `production` (`production_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `genre`
--
ALTER TABLE `genre`
  ADD CONSTRAINT `GENRE_GENRELIST_FK` FOREIGN KEY (`genre_list_id`) REFERENCES `genre_list` (`genre_list_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `genre_list`
--
ALTER TABLE `genre_list`
  ADD CONSTRAINT `GENRELIST_ANIME_FK` FOREIGN KEY (`anime_id`) REFERENCES `anime` (`anime_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `GENRELIST_GENRE_FK` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `GENRELIST_MANGA_FK` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`manga_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `list`
--
ALTER TABLE `list`
  ADD CONSTRAINT `LIST_ANIME_FK` FOREIGN KEY (`anime_id`) REFERENCES `anime` (`anime_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `LIST_MANGA_FK` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`manga_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `LIST_USER_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `manga`
--
ALTER TABLE `manga`
  ADD CONSTRAINT `MANGA_GENRELIST_FK` FOREIGN KEY (`genre_list_id`) REFERENCES `genre_list` (`genre_list_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `PRODUCTION_ANIME_FK` FOREIGN KEY (`anime_id`) REFERENCES `anime` (`anime_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PRODUCTION_STUDIO_FK` FOREIGN KEY (`studio_id`) REFERENCES `studio` (`studio_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `REVIEW_ANIME_FK` FOREIGN KEY (`anime_id`) REFERENCES `anime` (`anime_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `REVIEW_MANGA_FK` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`manga_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `REVIEW_USER_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studio`
--
ALTER TABLE `studio`
  ADD CONSTRAINT `STUDIO_PRODUCTION_FK` FOREIGN KEY (`production_id`) REFERENCES `production` (`production_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `USER_TOWACH_FK` FOREIGN KEY (`towatch_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `USER_WATCHED_FK` FOREIGN KEY (`watched_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
