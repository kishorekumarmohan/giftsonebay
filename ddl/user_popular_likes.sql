-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 20, 2011 at 09:52 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `aafe8qb7_gift`
--
-- --------------------------------------------------------
--
-- Table structure for table `user_popular_likes`
--

CREATE TABLE IF NOT EXISTS `user_popular_likes` (
  `fb_user_id` bigint(20) NOT NULL,
  `category` varchar(75) NOT NULL,
  `name` varchar(75) NOT NULL,
  `fb_user_ids` varchar(512) NOT NULL,
  `count` smallint(6) NOT NULL,
  KEY `fb_user_id` (`fb_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

truncate table user_popular_likes;

insert into user_popular_likes (fb_user_id,category,name,fb_user_ids,count)
(
SELECT u.fb_user_id AS fb_user_id, category AS category, name AS name, GROUP_CONCAT( u.fb_friend_user_id,  '' ) AS fb_user_ids, COUNT( ul.fb_user_id ) AS count
FROM user_relation u
JOIN user_likes ul ON u.fb_friend_user_id = ul.fb_user_id
GROUP BY u.fb_user_id, category, name
HAVING COUNT( ul.fb_user_id ) >1
);



SELECT category, name 
FROM  `user_popular_likes` 
WHERE fb_user_id =601264908 and category in ('Movie', 'Book', 'Music', 'Tv show') order by count desc;

