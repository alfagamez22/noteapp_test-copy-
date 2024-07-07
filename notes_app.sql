-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2024 at 01:43 PM
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
-- Database: `notes_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `created_at`) VALUES
(26, 8, 10, '2024-07-07 11:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`request_id`, `sender_id`, `receiver_id`, `status`, `created_at`) VALUES
(7, 8, 10, 'accepted', '2024-07-07 11:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `status`) VALUES
(16, 8, 10, 'Hello\r\n', '2024-07-07 11:36:49', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `content`, `created_at`, `user_id`, `image_url`) VALUES
(13, 'Testing', 'Test', '2024-07-03 14:13:57', 6, NULL),
(14, 'Test 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed et suscipit nunc. Sed nec pharetra justo. Nulla facilisi. Praesent non orci nec nisi feugiat scelerisque. Quisque et purus eros. Nullam id neque ultricies, fermentum quam eget, ultrices justo. Mauris tempus nec est non aliquam. Fusce viverra quam ac ligula euismod, ut convallis risus suscipit. Nulla sed lectus felis. Maecenas vehicula rutrum odio, nec faucibus ipsum tincidunt id. Curabitur auctor vitae metus ac vehicula. Vivamus vel posuere eros, vel eleifend quam. Ut feugiat, quam a auctor ultricies, nibh eros gravida erat.', '2024-07-07 10:27:13', 10, 'uploads/kiminonawa.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `lastname`, `email`, `birthdate`, `password`, `created_at`, `profile_image`) VALUES
(6, 'Harvey', 'Buan', 'test123@gmail.com', '2003-01-03', '$2y$10$8xus18CCpPDmzKA2pbdpDeWVto02HcVBu/nec61LC6IkGghlh8B9G', '2024-07-03 13:45:02', 0x70726f66696c655f75706c6f6164732f3334313138353935375f3931313631383533363733303037355f343730343235343036373639353038363435375f6e2e6a7067),
(7, 'test', 'testing', 'test2@gmail.com', '2024-07-03', '$2y$10$Wu2Jp7T.fwziE7J9/beDFu2GzBghu6ImJs1d9K0hHGoejwcUR0i.e', '2024-07-03 13:55:02', 0x70726f66696c655f75706c6f6164732f6d652e6a7067),
(8, 'TesT3', 'testing', 'test3@gmail.com', '2003-01-03', '$2y$10$UxmEy2Euk9FLD1ph.Gs3IOis5uea7BJFZLo2CJRpUsE1XB.4PK.FS', '2024-07-03 13:57:22', 0x70726f66696c655f75706c6f6164732f39333044383035432d363234302d343643422d384245432d3532383444313432454541322e6a706567),
(9, 'BUANTEST', 'BUAN', 'test4@gmail.com', '2024-07-18', '$2y$10$3WXrDCKiykk0SzoDjH8f.uKTAQ7I3/cU3MfP72a6EIix3/LaLqfyC', '2024-07-03 16:05:30', 0x70726f66696c655f75706c6f6164732f31313039393036382e706e67),
(10, 'Dhanniel Harvey', 'Pogi', 'test1@gmail.com', '2003-03-01', '$2y$10$weefPmtyabcRoK09XV2CQO7fLuEEzMD5KS4syYqSduehKkjwtQqB.', '2024-07-07 09:11:23', 0x70726f66696c655f75706c6f6164732f39333044383035432d363234302d343643422d384245432d3532383444313432454541322e6a706567);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `fk_sender` (`sender_id`),
  ADD KEY `fk_receiver` (`receiver_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
