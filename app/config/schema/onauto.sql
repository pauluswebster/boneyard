-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2012 at 09:18 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `onauto`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(10) unsigned NOT NULL,
  `year` smallint(4) unsigned DEFAULT NULL,
  `kilometers` int(10) unsigned DEFAULT NULL,
  `engine_size` smallint(5) unsigned DEFAULT NULL,
  `transmission` tinyint(2) unsigned DEFAULT NULL,
  `body` tinyint(3) unsigned DEFAULT NULL,
  `doors` tinyint(2) unsigned DEFAULT NULL,
  `stereo` varchar(255) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `category_left` int(10) unsigned NOT NULL,
  `category_right` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE IF NOT EXISTS `listings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(32) NOT NULL DEFAULT 'Listings',
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `listed` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned DEFAULT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class` (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `token` varchar(36) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` varchar(24) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `timezone` varchar(32) NOT NULL,
  `settings` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
