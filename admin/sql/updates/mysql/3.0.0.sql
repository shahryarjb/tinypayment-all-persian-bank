CREATE TABLE IF NOT EXISTS `#__tinypayment_banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_id` tinyint(2) DEFAULT '0',
  `bank_name` varchar(50) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `terminal_code` varchar(50) DEFAULT NULL,
  `test_mode` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `#__tinypayment_banks` (`id`, `bank_id`, `bank_name`, `active`, `username`, `password`, `terminal_code`, `test_mode`) VALUES
(1, 1, 'mellat', 0, 'username', 'password', 'terminalcode', 0),
(2, 3, 'zarinpal', 0, 'username', 'password', 'terminalcode', 1),
(3, 9, 'saman', 0, 'username', 'password', 'terminalcode', 0);


CREATE TABLE IF NOT EXISTS `#__tinypayment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_back` int(2) NOT NULL,
  `show_pdf` tinyint(1) NOT NULL,
  `captcha` tinyint(1) NOT NULL,
  `public_key` text NOT NULL,
  `private_key` text NOT NULL,
  `show_email` tinyint(1) NOT NULL,
  `bootstrap` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `#__tinypayment_settings` (`id`, `time_back`, `show_pdf`, `captcha`, `public_key`, `private_key`, `show_email`, `bootstrap`) VALUES
(1, 10, 1, 0, 'publickey', 'privatekey', 1, 1);
