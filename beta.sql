-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 20 Août 2009 à 20:56
-- Version du serveur: 5.1.36
-- Version de PHP: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE IF NOT EXISTS `site_inscrits` (
  `regis_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id de l inscrit',
  `regis_mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Adresse email de l inscrit',
  `regis_validation` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Pour savoir si l inscrit est valide',
  `regis_site` int(5) NOT NULL COMMENT 'Site sur lequel est inscrit l user',
  `regis_nb_gagne` int(5) NOT NULL DEFAULT '0' COMMENT 'Nombre de fois que l inscrit gagne',
  `regis_nb_mail` int(5) NOT NULL DEFAULT '0' COMMENT 'Nombre de mails que l inscrit reçoit',
  `regis_date_inscrit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Timestamp de l inscription',
  PRIMARY KEY (`regis_id`),
  UNIQUE KEY `regis_mail` (`regis_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `site_publicites` (
  `pub_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id de la publicité',
  `pub_contact` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Adresse email de l annonceur',
  `pub_taille` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Url de l annonceur',
  `pub_url_site` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Url de l annonceur',
  `pub_url_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Url de l image de publicité',
  `pub_nbr_affiche` int(11) NOT NULL COMMENT 'Nombre(s) d affichage(s) restant(s)',
  `pub_valide` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Validation de la publicité',
  `pub_date_ajout` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Timestamp de l inscription',
  PRIMARY KEY (`pub_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `site_participations` (
  `parti_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id du participant',
  `parti_id_inscrit` int(11) NOT NULL COMMENT 'Id de l inscrit',
  PRIMARY KEY (`parti_id`),
  UNIQUE KEY `parti_id_inscrit` (`parti_id_inscrit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;