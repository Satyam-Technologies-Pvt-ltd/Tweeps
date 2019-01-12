-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2016 at 08:38 PM
-- Server version: 5.7.11
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dripfeed`
--

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

CREATE TABLE `feeds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `page_from` int(5) NOT NULL,
  `page_to` int(5) NOT NULL,
  `current_page` int(5) NOT NULL,
  `current_guid` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `current_tweet` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `current_tweet_count` int(3) DEFAULT NULL,
  `post_interval` int(5) NOT NULL,
  `post_offset` int(5) NOT NULL,
  `post_sequence` int(5) NOT NULL,
  `post_rotation` int(3) DEFAULT '0',
  `tweet_per_post` int(2) NOT NULL DEFAULT '1',
  `tweet_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_text_filters` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_url_prefix` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_url_post_fix` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_url_filters` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `tweet_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `search_image` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `only_if_image` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `shorten_bit` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `posts_ignore_words` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `autopaused` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `feed_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(254) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `current_page` int(5) NOT NULL,
  `current_tweet_count` int(5) DEFAULT NULL,
  `log_status` varchar(254) NOT NULL,
  `log` varchar(254) NOT NULL,
  `status_message` varchar(254) NOT NULL,
  `image_url` varchar(254) NOT NULL,
  `link` varchar(254) NOT NULL,
  `feedurl` varchar(254) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `payment_status` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `account_status` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `oauth_provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `oauth_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `oauth_token` text COLLATE utf8_unicode_ci NOT NULL,
  `oauth_secret` text COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `yourname` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `youremail` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `bitlyusername` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `bitlyapikey` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `sendnewsletter` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `autofollow` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `autofollowmessage` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feeds`
--
ALTER TABLE `feeds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feeds`
--
ALTER TABLE `feeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259361;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
