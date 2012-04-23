-- `content` table
CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `comment` text,
  `ip` varchar(255) NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- `content` content
INSERT INTO `content` (`id`, `content`, `author`, `comment`, `ip`, `modified_date`) VALUES (1, '<h2>Initial page content</h2><p>Lorem ipsum</p>', 'Pete', 'initial commit', '192.168.0.1', '2008-02-16 11:26:08');

