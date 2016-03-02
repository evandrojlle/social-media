/*
SQLyog Community v12.12 (64 bit)
MySQL - 5.6.24 : Database - social-media
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`social-media` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `social-media`;

/*Table structure for table `sm_feeds` */

DROP TABLE IF EXISTS `sm_feeds`;

CREATE TABLE `sm_feeds` (
  `idFeed` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `ds_feed` varchar(140) NOT NULL,
  `st_feed` tinyint(1) NOT NULL DEFAULT '1',
  `dt_insert` datetime NOT NULL,
  `dt_update` datetime DEFAULT NULL,
  PRIMARY KEY (`idFeed`),
  KEY `fk_feed_user` (`idUser`),
  CONSTRAINT `fk_feed_user` FOREIGN KEY (`idUser`) REFERENCES `sm_users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Table structure for table `sm_friends` */

DROP TABLE IF EXISTS `sm_friends`;

CREATE TABLE `sm_friends` (
  `idFriend` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `dt_insert` datetime NOT NULL,
  PRIMARY KEY (`idFriend`,`idUser`),
  KEY `fk_friend_user` (`idUser`),
  CONSTRAINT `fk_friend_friend` FOREIGN KEY (`idFriend`) REFERENCES `sm_users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_friend_user` FOREIGN KEY (`idUser`) REFERENCES `sm_users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `sm_users` */

DROP TABLE IF EXISTS `sm_users`;

CREATE TABLE `sm_users` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `ds_user` varchar(180) NOT NULL,
  `ds_login` varchar(120) NOT NULL,
  `ds_pass` varchar(32) NOT NULL,
  `st_user` tinyint(1) NOT NULL DEFAULT '1',
  `dt_insert` datetime NOT NULL,
  `dt_update` datetime DEFAULT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
