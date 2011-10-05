--
-- Table structure for table `currency_rates`
--

CREATE TABLE IF NOT EXISTS `currency_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `base` varchar(3) NOT NULL,
  `to` varchar(3) NOT NULL,
  `rate` float(6,4) NOT NULL,
  `modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `base` (`base`,`to`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(128) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(10) NOT NULL,
  `fee` float(7,2) NOT NULL,
  `fixed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `currency` varchar(3) NOT NULL,
  `currency_rate` float(6,4) DEFAULT NULL,
  `started` int(10) unsigned NOT NULL DEFAULT '0',
  `completed` int(10) NOT NULL DEFAULT '0',
  `due` int(10) unsigned NOT NULL,
  `timezone` varchar(32) NOT NULL,
  `created` int(10) NOT NULL,
  `modified` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `job_logs`
--

CREATE TABLE IF NOT EXISTS `job_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`job_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
  `detail` tinytext,
  `currency` varchar(3) NOT NULL,
  `timezone` varchar(32) NOT NULL,
  `settings` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `token`, `active`, `admin`, `username`, `password`, `email`, `detail`, `currency`, `timezone`, `settings`) VALUES
(1, 'Admin', 'User', 'dcb19ff84086fedbc4e01881080cb7d58c40', 1, 1, 'admin', 'password', 'admin@example.com', NULL, 'NZD', 'UTC', '{"currencies":[],"timezones":[]}');
