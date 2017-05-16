-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2017 at 08:12 AM
-- Server version: 5.5.25a
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `glistformadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `additionalfields`
--

CREATE TABLE IF NOT EXISTS `additionalfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `html` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` varchar(6) NOT NULL,
  `user` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) unsigned zerofill NOT NULL,
  `day` int(2) unsigned zerofill NOT NULL,
  `headliner` varchar(255) NOT NULL,
  `pricing_21` varchar(255) NOT NULL,
  `pricing_18` varchar(255) NOT NULL,
  `maxsub` int(11) NOT NULL,
  `maxsub_21` int(11) NOT NULL,
  `maxsub_18` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events_archive`
--

CREATE TABLE IF NOT EXISTS `events_archive` (
  `id` varchar(6) NOT NULL,
  `user` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) unsigned zerofill NOT NULL,
  `day` int(2) unsigned zerofill NOT NULL,
  `headliner` varchar(255) NOT NULL,
  `pricing_21` varchar(255) NOT NULL,
  `pricing_18` varchar(255) NOT NULL,
  `maxsub` int(11) NOT NULL,
  `maxsub_21` int(11) NOT NULL,
  `maxsub_18` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `signups`
--

CREATE TABLE IF NOT EXISTS `signups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(6) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `age` varchar(2) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `custom1` text NOT NULL,
  `custom2` text NOT NULL,
  `custom3` text NOT NULL,
  `custom4` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69285 ;

-- --------------------------------------------------------

--
-- Table structure for table `signups_archive`
--

CREATE TABLE IF NOT EXISTS `signups_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(6) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `age` varchar(2) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `custom1` text NOT NULL,
  `custom2` text NOT NULL,
  `custom3` text NOT NULL,
  `custom4` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69285 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(16) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `artistlinks` text NOT NULL,
  `promoterlinks` varchar(255) NOT NULL,
  `customfields` int(1) NOT NULL,
  `association` int(11) NOT NULL,
  `status` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=192 ;
