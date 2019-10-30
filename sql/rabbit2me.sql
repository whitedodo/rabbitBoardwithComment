-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- ?앹꽦 ?쒓컙: 18-08-06 09:01
-- ?쒕쾭 踰꾩쟾: 5.5.59-log
-- PHP 踰꾩쟾: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- ?곗씠?곕쿋?댁뒪: `rabbit2me`
--

-- --------------------------------------------------------

--
-- ?뚯씠釉?援ъ“ `board_story`
--

CREATE TABLE `board_story` (
  `id` int(11) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `author` varchar(25) NOT NULL,
  `password` varchar(128) NOT NULL,
  `memo` text NOT NULL,
  `reply` int(5) NOT NULL,
  `mode` varchar(10) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `regidate` datetime NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- ?뚯씠釉붿쓽 ?ㅽ봽 ?곗씠??`board_story`
--

--
-- ?뚯씠釉?援ъ“ `board_story_comment`
--

CREATE TABLE `board_story_comment` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `memo` text NOT NULL,
  `author` varchar(25) NOT NULL,
  `password` varchar(128) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `regidate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- ?뚯씠釉붿쓽 ?ㅽ봽 ?곗씠??`board_story_comment`
--
--
-- ?ㅽ봽???뚯씠釉붿쓽 ?몃뜳??--

--
-- ?뚯씠釉붿쓽 ?몃뜳??`board_story`
--
ALTER TABLE `board_story`
  ADD PRIMARY KEY (`id`);

--
-- ?뚯씠釉붿쓽 ?몃뜳??`board_story_comment`
--
ALTER TABLE `board_story_comment`
  ADD PRIMARY KEY (`id`);

--
-- ?ㅽ봽???뚯씠釉붿쓽 AUTO_INCREMENT
--

--
-- ?뚯씠釉붿쓽 AUTO_INCREMENT `board_story`
--
ALTER TABLE `board_story`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- ?뚯씠釉붿쓽 AUTO_INCREMENT `board_story_comment`
--
ALTER TABLE `board_story_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
