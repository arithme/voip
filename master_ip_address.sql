-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2023 at 02:20 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ravi`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_ip_address`
--

CREATE TABLE `master_ip_address` (
  `ID` int(11) NOT NULL,
  `SSA` varchar(10) NOT NULL,
  `DISTRICT` varchar(50) NOT NULL,
  `LOCATION` varchar(10) NOT NULL,
  `PS_HO` varchar(100) NOT NULL,
  `WAN_ID` varchar(100) NOT NULL,
  `NOC_End` varchar(50) NOT NULL,
  `Office_End` varchar(11) NOT NULL,
  `LAN_ID` varchar(11) NOT NULL,
  `LAN_GATEWAY` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `master_ip_address`
--

INSERT INTO `master_ip_address` (`ID`, `SSA`, `DISTRICT`, `LOCATION`, `PS_HO`, `WAN_ID`, `NOC_End`, `Office_End`, `LAN_ID`, `LAN_GATEWAY`) VALUES
(210, 'DMK', 'Dumka', 'Dumka Naga', 'PS', '172.16.36.0', '172.16.36.1', '172.16.36.2', '172.16.48.0', '172.16.48.1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_ip_address`
--
ALTER TABLE `master_ip_address`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_ip_address`
--
ALTER TABLE `master_ip_address`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
