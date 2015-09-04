-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2015 at 04:48 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `lalaws2015`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `civ_pro_view`
--
CREATE TABLE IF NOT EXISTS `civ_pro_view` (
`id` int(11)
,`sortcode` varchar(100)
,`title` varchar(100)
,`description` varchar(100)
,`law_text` text
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `civ_view`
--
CREATE TABLE IF NOT EXISTS `civ_view` (
`id` int(11)
,`sortcode` varchar(100)
,`title` varchar(100)
,`description` varchar(100)
,`law_text` text
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `crim_code_view`
--
CREATE TABLE IF NOT EXISTS `crim_code_view` (
`id` int(11)
,`sortcode` varchar(100)
,`title` varchar(100)
,`description` varchar(100)
,`law_text` text
);
-- --------------------------------------------------------

--
-- Table structure for table `laws`
--

CREATE TABLE IF NOT EXISTS `laws` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `docid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `sortcode` varchar(100) NOT NULL,
  `law_text` text NOT NULL,
  `last_scraped` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)

) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `view_as_table`
--

CREATE TABLE IF NOT EXISTS `view_as_table` (
  `id` int(11) NOT NULL DEFAULT '0',
  `sortcode` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `law_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure for view `civ_pro_view`
--
DROP TABLE IF EXISTS `civ_pro_view`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `civ_pro_view` AS select `laws`.`id` AS `id`,`laws`.`sortcode` AS `sortcode`,`laws`.`title` AS `title`,`laws`.`description` AS `description`,`laws`.`law_text` AS `law_text` from `laws` where ((`laws`.`sortcode` like '%CCP %') or (`laws`.`sortcode` like '%RS 000001 000055%') or (`laws`.`sortcode` like 'RS 000009 000224%') or (`laws`.`sortcode` like 'RS 000009 000225%') or (`laws`.`sortcode` like 'RS 000009 000234%') or (`laws`.`sortcode` like 'RS 000009 000245%') or ((`laws`.`sortcode` >= 'RS 000009 000272') and (`laws`.`sortcode` <= 'RS 000009 000276')) or (`laws`.`sortcode` like 'RS 000009 000301') or (`laws`.`sortcode` like 'RS 000009 000302') or ((`laws`.`sortcode` >= 'RS 000009 000307') and (`laws`.`sortcode` <= 'RS 000009 000309')) or ((`laws`.`sortcode` >= 'RS 000009 000331') and (`laws`.`sortcode` <= 'RS 000009 000336')) or ((`laws`.`sortcode` >= 'RS 000009 000341') and (`laws`.`sortcode` <= 'RS 000009 000348')) or (`laws`.`sortcode` like 'RS 000009 000351') or (`laws`.`sortcode` like 'RS 000009 002781') or (`laws`.`sortcode` like 'RS 000009 002801%') or (`laws`.`sortcode` like 'RS 000009 003150') or (`laws`.`sortcode` like 'RS 000009 003221') or (`laws`.`sortcode` like 'RS 000009 003500') or ((`laws`.`sortcode` >= 'RS 000009 004241') and (`laws`.`sortcode` <= 'RS 000009 004276')) or ((`laws`.`sortcode` >= 'RS 000009 005011') and (`laws`.`sortcode` <= 'RS 000009 005016')) or ((`laws`.`sortcode` >= 'RS 000009 005550') and (`laws`.`sortcode` <= 'RS 000009 005557')) or ((`laws`.`sortcode` >= 'RS 000009 005604') and (`laws`.`sortcode` <= 'RS 000009 005605%')) or (`laws`.`sortcode` like 'RS 000009 005622') or (`laws`.`sortcode` like 'RS 000009 005628') or (`laws`.`sortcode` like 'RS 000009 005630') or (`laws`.`sortcode` like 'RS 000009 005642') or (`laws`.`sortcode` like 'RS 000009 005801') or (`laws`.`sortcode` like 'RS 000012 000308') or ((`laws`.`sortcode` >= 'RS 000013 001702') and (`laws`.`sortcode` <= 'RS 000013 001707')) or ((`laws`.`sortcode` >= 'RS 000013 001801') and (`laws`.`sortcode` <= 'RS 000013 001842')) or (`laws`.`sortcode` like 'RS 000013 003041') or ((`laws`.`sortcode` >= 'RS 000013 003201') and (`laws`.`sortcode` <= 'RS 000013 003207')) or (`laws`.`sortcode` like 'RS 000013 003471') or (`laws`.`sortcode` like 'RS 000013 003472') or (`laws`.`sortcode` like 'RS 000013 003474') or (`laws`.`sortcode` like 'RS 000013 003475') or (`laws`.`sortcode` like 'RS 000013 003479') or (`laws`.`sortcode` like 'RS 000013 003661%') or ((`laws`.`sortcode` >= 'RS 000013 003721') and (`laws`.`sortcode` <= 'RS 000013 003722')) or ((`laws`.`sortcode` >= 'RS 000013 003821') and (`laws`.`sortcode` <= 'RS 000013 003824')) or (`laws`.`sortcode` like 'RS 000013 003881') or (`laws`.`sortcode` like 'RS 000013 003886%') or ((`laws`.`sortcode` >= 'RS 000013 003921') and (`laws`.`sortcode` <= 'RS 000013 003928')) or ((`laws`.`sortcode` >= 'RS 000013 004101') and (`laws`.`sortcode` <= 'RS 000013 004112')) or (`laws`.`sortcode` like 'RS 000013 004165') or (`laws`.`sortcode` like 'RS 000013 004202') or ((`laws`.`sortcode` >= 'RS 000013 004207') and (`laws`.`sortcode` <= 'RS 000013 004210')) or (`laws`.`sortcode` like 'RS 000013 004231') or (`laws`.`sortcode` like 'RS 000013 004232') or ((`laws`.`sortcode` >= 'RS 000013 004241') and (`laws`.`sortcode` <= 'RS 000013 004248')) or (`laws`.`sortcode` like 'RS 000013 004359') or (`laws`.`sortcode` like 'RS 000013 004363') or (`laws`.`sortcode` like 'RS 000013 004441') or (`laws`.`sortcode` like 'RS 000013 004611') or ((`laws`.`sortcode` >= 'RS 000013 005101') and (`laws`.`sortcode` <= 'RS 000013 005108')) or ((`laws`.`sortcode` >= 'RS 000013 005200') and (`laws`.`sortcode` <= 'RS 000013 005212')) or (`laws`.`sortcode` like 'RS 000020 000001') or (`laws`.`sortcode` like 'RS 000022 000656') or (`laws`.`sortcode` like 'RS 000022 000985') or (`laws`.`sortcode` like 'RS 000022 001253') or (`laws`.`sortcode` like 'RS 000023 000921') or (`laws`.`sortcode` like 'RS 000035 000200') or (`laws`.`sortcode` like 'RS 000039 001538') or ((`laws`.`sortcode` >= 'RS 000013 005101') and (`laws`.`sortcode` <= 'RS 000013 005108')) or ((`laws`.`sortcode` >= 'RS 000013 005200') and (`laws`.`sortcode` <= 'RS 000013 005212')) or (`laws`.`sortcode` like 'RS 000020 000001') or (`laws`.`sortcode` like 'RS 000022 000656') or (`laws`.`sortcode` like 'RS 000022 000985') or (`laws`.`sortcode` like 'RS 000022 001253') or (`laws`.`sortcode` like 'RS 000023 000921') or (`laws`.`sortcode` like 'RS 000035 000200') or (`laws`.`sortcode` like 'RS 000039 001538') or (`laws`.`sortcode` like 'RS 000040 001299 000041') or (`laws`.`sortcode` like 'RS 000040 001299 000047') or (`laws`.`sortcode` like 'RS 000040 002010 000008') or (`laws`.`sortcode` like 'RS 000040 002010 000009') or ((`laws`.`sortcode` >= 'CC 000101') and (`laws`.`sortcode` <= 'CC 000105')) or (`laws`.`sortcode` like 'CC 000197') or (`laws`.`sortcode` like 'CC 002000') or (`laws`.`sortcode` like 'CC 002315%') or ((`laws`.`sortcode` >= 'CC 002316') and (`laws`.`sortcode` <= 'CC 002317%')) or ((`laws`.`sortcode` >= 'CC 002318') and (`laws`.`sortcode` <= 'CC 002324%')) or (`laws`.`sortcode` like 'CC 002924') or ((`laws`.`sortcode` >= 'CC 003445') and (`laws`.`sortcode` <= 'CC 003503')) or (`laws`.`sortcode` like 'CE %') or ((`laws`.`sortcode` >= 'CHC 001301 000001') and (`laws`.`sortcode` <= 'CHC 001301 000002'))) order by `laws`.`sortcode`;

-- --------------------------------------------------------

--
-- Structure for view `civ_view`
--
DROP TABLE IF EXISTS `civ_view`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `civ_view` AS select `laws`.`id` AS `id`,`laws`.`sortcode` AS `sortcode`,`laws`.`title` AS `title`,`laws`.`description` AS `description`,`laws`.`law_text` AS `law_text` from `laws` where ((`laws`.`sortcode` like '%CC %') or (`laws`.`sortcode` like '%RS 000009 %')) order by `laws`.`sortcode`;

-- --------------------------------------------------------

--
-- Structure for view `crim_code_view`
--
DROP TABLE IF EXISTS `crim_code_view`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `crim_code_view` AS select `laws`.`id` AS `id`,`laws`.`sortcode` AS `sortcode`,`laws`.`title` AS `title`,`laws`.`description` AS `description`,`laws`.`law_text` AS `law_text` from `laws` where ((`laws`.`sortcode` like 'CONST 000001 %') or (`laws`.`sortcode` like 'RS 000014 %') or (`laws`.`sortcode` like 'RS 000015 %') or ((`laws`.`sortcode` >= 'RS 000032 000001') and (`laws`.`sortcode` <= 'RS 000032 000432')) or ((`laws`.`sortcode` >= 'RS 000032 000661') and (`laws`.`sortcode` <= 'RS     000032 000681')) or ((`laws`.`sortcode` >= 'RS 000032 000851') and (`laws`.`sortcode` <= 'RS 000032 001043')) or ((`laws`.`sortcode` >= 'RS 000032 001471') and (`laws`.`sortcode` <= 'RS        000032 001481')) or ((`laws`.`sortcode` >= 'RS 000040 000961') and (`laws`.`sortcode` <= 'RS 000040 001049 000011')) or ((`laws`.`sortcode` >= 'RS 000040 001237') and (`laws`.`sortcode` <= 'RS 000040 001238 000004')) or ((`laws`.`sortcode` >= 'RS 000040 001742') and (`laws`.`sortcode` <= 'RS 000040 001742 000001')) or ((`laws`.`sortcode` >= 'RS 000040 001750') and (`laws`.`sortcode` <= 'RS 000040 001812')) or ((`laws`.`sortcode` >= 'RS 000040 002601') and (`laws`.`sortcode` <= 'RS 000040 002622')) or ((`laws`.`sortcode` >= 'RS 000044 000003') and (`laws`.`sortcode` <= 'RS 000044 000003 000002')) or ((`laws`.`sortcode` >= 'RS 000044 000051') and (`laws`.`sortcode` <= 'RS 000044 000057')) or ((`laws`.`sortcode` >= 'RS 000046 001841') and (`laws`.`sortcode` <= 'RS 000046 001846')) or ((`laws`.`sortcode` >= 'RS 000046 002131') and (`laws`.`sortcode` <= 'RS 000046 002151')) or ((`laws`.`sortcode` >= 'RS 000056 000031') and (`laws`.`sortcode` <= 'RS 000056 000070 000004')) or (`laws`.`sortcode` like 'CCRP %') or (`laws`.`sortcode` like 'CE %') or ((`laws`.`sortcode` >= 'CHC 000100') and (`laws`.`sortcode` <= 'CHC 000116')) or ((`laws`.`sortcode` >= 'CHC 000301') and (`laws`.`sortcode` <= 'CHC 000960')) or ((`laws`.`sortcode` >= 'CHC 001351') and (`laws`.`sortcode` <= 'CHC 001355')) or ((`laws`.`sortcode` >= 'CHC 001564') and (`laws`.`sortcode` <= 'CHC 001575')) or ((`laws`.`sortcode` >= 'CHC 001661') and (`laws`.`sortcode` <= 'CHC 001673'))) order by `laws`.`sortcode`;

