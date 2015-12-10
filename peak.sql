-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 09 Ara 2015, 11:56:08
-- Sunucu sürümü: 5.6.17
-- PHP Sürümü: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Veritabanı: `peak`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gifts`
--

CREATE TABLE IF NOT EXISTS `gifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` int(11) NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Tablo döküm verisi `gifts`
--

INSERT INTO `gifts` (`id`, `name`, `value`, `picture`, `status`) VALUES
(1, '1 Coins', 1, 'images/gifts/coins_1.png', 0),
(2, '5 Coins', 5, 'images/gifts/coins_5.png', 0),
(3, '10 Coins', 10, 'images/gifts/coins_10.png', 1),
(4, '20 Coins', 20, 'images/gifts/coins_20.png', 0),
(5, '50 Coins', 50, 'images/gifts/coins_50.png', 1),
(6, '100 Coins', 100, 'images/gifts/coins_100.png', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faceId` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `coins` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faceId_2` (`faceId`),
  KEY `id` (`id`),
  KEY `faceId` (`faceId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;


-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_gift`
--

CREATE TABLE IF NOT EXISTS `user_gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gift_id` int(11) NOT NULL,
  `sent_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

