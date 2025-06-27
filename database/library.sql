-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 04:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Cell` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Name`, `Email`, `Username`, `Password`, `Cell`, `Address`) VALUES
('Ashish Tamang', 'ashish@gmail.com', 'ashish', 'qwerty', '9840362754', 'Sano thimi, Bhaktapur'),
('Tarjan Thapa', 'tarjan@gmail.com', 'tarjan', 'qwerty', '9840362754', 'Sano thimi, Bhaktapur'),
('Babish Chaudhary', 'babish@gmail.com', 'babish', 'qwerty', '9840362754', 'Sano thimi, Bhaktapur'),
('[sonam]', '[sonam@gmail.com]', '[sonam]', '[qwerty]', '[9841233445]', '[samakhusi]');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(255) NOT NULL,
  `author_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(255) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `available_quantity` int(11) NOT NULL,
  `book_num` varchar(255) NOT NULL,
  `book_edition` varchar(255) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `publication` varchar(100) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `semester` varchar(11) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `total_quantity`, `available_quantity`, `book_num`, `book_edition`, `author_name`, `publication`, `faculty`, `semester`, `picture`, `description`) VALUES
(1, 'Python', 50, 37, '9786233121118', '2nd', 'Babish', 'Sagarmatha', 'BIM', '7', '683c199306715_1748769171.png', 'Data Mining by Abraham Silberschatz for semester 8 of Bsc.Csit faculty.'),
(2, 'Object Oriented Programming', 8, 5, '9783631420167', '3th', 'E. Balagurusamy', 'SchaumOutlines', 'BCA', '2', '683ff1d7eff7f_1749021143.png', 'Object Oriented Programming by E. Balagurusamy for semester 2 of BCA faculty.'),
(3, 'Project Management', 10, 7, '9785386505125', '4th', 'Kenneth Laudon', 'Wiley', 'BIM', '2', '683ff24a97329_1749021258.png', 'Project Management by Kenneth Laudon for semester 2 of BIM faculty.'),
(4, 'Project Management', 10, 2, '9781302591112', '3th', 'David A. Patterson', 'Oxford', 'BIM', '1', '683ff5feca209_1749022206.png', 'Project Management by David A. Patterson for semester 1 of BIM faculty.'),
(5, 'Operating System Concepts', 20, 9, '9789840472409', '3th', 'E. Balagurusamy', 'Oxford', 'BIM', '5', '683ff30d9efff_1749021453.png', 'Operating System Concepts by E. Balagurusamy for semester 5 of BIM faculty.'),
(6, 'Object Oriented Programming', 9, 2, '9782236952826', '5th', 'Seymour Lipschutz', 'Oxford', 'BCA', '5', '685cb111c5f1a_1750905105.png', 'Object Oriented Programming by Seymour Lipschutz for semester 5 of BCA faculty.'),
(7, 'Computer Architecture', 11, 2, '9782840292216', '2th', 'Stephen P. Robbins', 'Cambridge', 'BIM', '2', '683ff53ebde81_1749022014.png', 'Computer Architecture by Stephen P. Robbins for semester 2 of BIM faculty.'),
(8, 'Mobile Application Development', 9, 3, '9786093462704', '3th', 'Stuart Russell', 'Pearson', 'Bsc.Csit', '4', '685cb152b09e7_1750905170.png', 'Mobile Application Development by Stuart Russell for semester 4 of Bsc.Csit faculty.'),
(9, 'System Analysis and Design', 12, 4, '9781450995121', '3th', 'Korth &amp; Sudarshan', 'McGraw-Hill', 'Bsc.Csit', '2', '685cb1822794b_1750905218.png', 'System Analysis and Design by Korth &amp; Sudarshan for semester 2 of Bsc.Csit faculty.'),
(10, 'Computer Architecture', 8, 5, '9782738915825', '2th', 'Korth &amp; Sudarshan', 'Schaum&#039;s Outlines', 'Bsc.Csit', '1', '685cb1c6d78e6_1750905286.png', 'Computer Architecture by Korth &amp; Sudarshan for semester 1 of Bsc.Csit faculty.'),
(11, 'Data Structures &amp; Algorithms', 13, 7, '9784811202864', '2th', 'E. Balagurusamy', 'Schaum&#039;s Outlines', 'Bsc.Csit', '7', '683ff3c9a50fa_1749021641.png', 'Data Structures &amp; Algorithms by E. Balagurusamy for semester 7 of Bsc.Csit faculty.'),
(12, 'Financial Accounting', 6, 0, '9782853101041', '5th', 'Kenneth Laudon', 'Oxford', 'Bsc.Csit', '2', '683ff2b829033_1749021368.png', 'Financial Accounting by Kenneth Laudon for semester 2 of Bsc.Csit faculty.'),
(13, 'Digital Logic', 9, 2, '9783120124853', '2th', 'Ralph M. Stair', 'Oxford', 'BCA', '1', '685cb1f1850b5_1750905329.png', 'Digital Logic by Ralph M. Stair for semester 1 of BCA faculty.'),
(14, 'Database Management Systems', 11, 11, '9781077917694', '4th', 'Stephen P. Robbins', 'Schaum&#039;s Outlines', 'BCA', '1', '683ff3f86c788_1749021688.png', 'Database Management Systems by Stephen P. Robbins for semester 1 of BCA faculty.'),
(15, 'Business Communication', 7, 7, '9785997211312', '5th', 'Ralph M. Stair', 'Wiley', 'BIM', '3', '685cb22e716ba_1750905390.png', 'Business Communication by Ralph M. Stair for semester 3 of BIM faculty.'),
(16, 'E-Governance', 16, 16, '9781346612406', '1th', 'Kenneth Laudon', 'McGraw-Hill', 'BBM', '6', '685cb24f9c0e7_1750905423.png', 'E-Governance by Kenneth Laudon for semester 6 of Bsc.Csit faculty.'),
(17, 'Distributed Systems', 7, 2, '9781928087678', '1th', 'Seymour Lipschutz', 'Pearson', 'BIM', '1', '685cb27205e2b_1750905458.png', 'Distributed Systems by Seymour Lipschutz for semester 1 of BIM faculty.'),
(18, 'Data Mining', 11, 0, '9789303209161', '2th', 'Kenneth Laudon', 'Cambridge', 'BCA', '2', '685cb29c74b88_1750905500.png', 'Data Mining by Kenneth Laudon for semester 2 of BCA faculty.'),
(19, 'Business Communication', 13, 5, '9789234657908', '1th', 'Ian Sommerville', 'Wiley', 'BBM', '7', '685cb2be7bd38_1750905534.png', 'Business Communication by Ian Sommerville for semester 7 of BCA faculty.'),
(20, 'Data Mining', 13, 3, '9785904240354', '2th', 'Seymour Lipschutz', 'Wiley', 'BIM', '7', '685cb2e3035c4_1750905571.png', 'Data Mining by Seymour Lipschutz for semester 7 of BIM faculty.'),
(21, 'Human Computer Interaction', 10, 0, '9782511962648', '5th', 'Philip Kotler', 'McGraw-Hill', 'BBM', '5', '685cb8f4f3208_1750907124.png', 'Human Computer Interaction by Philip Kotler for semester 5 of BIM faculty.'),
(22, 'E-Governance', 17, 17, '9783330524034', '5th', 'Abraham Silberschatz', 'Schaum&#039;s Outlines', 'BIM', '8', '685cb915d5224_1750907157.png', 'E-Governance by Abraham Silberschatz for semester 8 of BIM faculty.'),
(23, 'Artificial Intelligence', 10, 1, '9784837428827', '3th', 'Philip Kotler', 'Tata McGraw-Hill', 'BCA', '3', '683ff33244e59_1749021490.png', 'Artificial Intelligence by Philip Kotler for semester 3 of BCA faculty.'),
(24, 'Information Security', 12, 8, '9784907049707', '5th', 'Ralph M. Stair', 'Cambridge', 'Bsc.Csit', '8', '685cb94811166_1750907208.png', 'Information Security by Ralph M. Stair for semester 8 of Bsc.Csit faculty.'),
(25, 'Financial Accounting', 14, 8, '9782968336288', '2th', 'Seymour Lipschutz', 'Tata McGraw-Hill', 'BBM', '4', '685cb969a41a1_1750907241.png', 'Financial Accounting by Seymour Lipschutz for semester 4 of Bsc.Csit faculty.'),
(26, 'E-Commerce Essentials', 19, 3, '9788469127427', '1th', 'Stephen P. Robbins', 'Schaum&#039;s Outlines', 'Bsc.Csit', '2', '685cb99180916_1750907281.png', 'E-Commerce Essentials by Stephen P. Robbins for semester 2 of Bsc.Csit faculty.'),
(27, 'E-Governance', 10, 8, '9784377081759', '2th', 'Seymour Lipschutz', 'Tata McGraw-Hill', 'Bsc.Csit', '3', '685cba1a0e79c_1750907418.png', 'E-Governance by Seymour Lipschutz for semester 3 of Bsc.Csit faculty.'),
(28, 'E-Commerce Essentials', 5, 5, '9788066203172', '5th', 'Stuart Russell', 'Pearson', 'BCA', '1', '685cba3bb4575_1750907451.png', 'E-Commerce Essentials by Stuart Russell for semester 1 of BCA faculty.'),
(29, 'Artificial Intelligence', 8, 0, '9783457845566', '5th', 'Stephen P. Robbins', 'Schaum&#039;s Outlines', 'Bsc.Csit', '6', '685cba657f544_1750907493.png', 'Artificial Intelligence by Stephen P. Robbins for semester 6 of Bsc.Csit faculty.'),
(30, 'Business Communication', 8, 1, '9784138010801', '1th', 'David A. Patterson', 'Pearson', 'BBM', '3', '685cba89dc9f8_1750907529.png', 'Business Communication by David A. Patterson for semester 3 of BCA faculty.'),
(31, 'Distributed Systems', 7, 5, '9781888024913', '1th', 'Ralph M. Stair', 'McGraw-Hill', 'Bsc.Csit', '8', '685cbac3d469c_1750907587.png', 'Distributed Systems by Ralph M. Stair for semester 8 of Bsc.Csit faculty.'),
(32, 'Introduction to C Programming', 7, 4, '9786734967789', '4th', 'Ian Sommerville', 'Pearson', 'BIM', '5', '683ff3b30e853_1749021619.png', 'Introduction to C Programming by Ian Sommerville for semester 5 of BIM faculty.'),
(33, 'Introduction to C Programming', 12, 1, '9786730753370', '4th', 'E. Balagurusamy', 'Tata McGraw-Hill', 'BCA', '8', '685cbae82d726_1750907624.png', 'Introduction to C Programming by E. Balagurusamy for semester 8 of BCA faculty.'),
(34, 'E-Governance', 12, 2, '9783780548579', '3th', 'Ian Sommerville', 'McGraw-Hill', 'BIM', '6', '685cbb1bed2c8_1750907675.png', 'E-Governance by Ian Sommerville for semester 6 of BIM faculty.'),
(35, 'Financial Accounting', 13, 0, '9783748660623', '2th', 'George Reynolds', 'McGraw-Hill', 'BBM', '3', '683ff2dee72b9_1749021406.png', 'Financial Accounting by George Reynolds for semester 3 of Bsc.Csit faculty.'),
(36, 'Data Mining', 20, 9, '9786747156844', '4th', 'Seymour Lipschutz', 'Cambridge', 'BCA', '7', '685cbb3df0682_1750907709.png', 'Data Mining by Seymour Lipschutz for semester 7 of BCA faculty.'),
(37, 'E-Governance', 17, 10, '9787394451965', '5th', 'E. Balagurusamy', 'Schaum&#039;s Outlines', 'Bsc.Csit', '3', '685cbb660aca4_1750907750.png', 'E-Governance by E. Balagurusamy for semester 3 of Bsc.Csit faculty.'),
(38, 'Data Structures &amp; Algorithms', 13, 11, '9789845116029', '5th', 'Kenneth Laudon', 'McGraw-Hill', 'Bsc.Csit', '1', '685cbb898ad3f_1750907785.png', 'Data Structures &amp; Algorithms by Kenneth Laudon for semester 1 of Bsc.Csit faculty.'),
(39, 'Data Structures &amp; Algorithms', 6, 5, '9785945913452', '1th', 'Ian Sommerville', 'Oxford', 'Bsc.Csit', '1', '685cbbacc4e65_1750907820.png', 'Data Structures &amp; Algorithms by Ian Sommerville for semester 1 of Bsc.Csit faculty.'),
(40, 'Artificial Intelligence', 20, 18, '9783127759274', '2th', 'Abraham Silberschatz', 'McGraw-Hill', 'BCA', '2', '685cbbcd554fd_1750907853.png', 'Artificial Intelligence by Abraham Silberschatz for semester 2 of BCA faculty.'),
(41, 'Web Technologies', 7, 2, '9784973666913', '2th', 'Ian Sommerville', 'Wiley', 'BIM', '6', '685cbc0115842_1750907905.png', 'Web Technologies by Ian Sommerville for semester 6 of BIM faculty.'),
(42, 'Data Structures &amp; Algorithms', 14, 14, '9784576255155', '1th', 'E. Balagurusamy', 'Schaum&#039;s Outlines', 'BCA', '6', '685cc981ab79b_1750911361.png', 'Data Structures &amp; Algorithms by E. Balagurusamy for semester 6 of BCA faculty.'),
(43, 'Computer Architecture', 5, 2, '9789724428314', '3th', 'E. Balagurusamy', 'Tata McGraw-Hill', 'BIM', '1', '683ff54d76cb7_1749022029.png', 'Computer Architecture by E. Balagurusamy for semester 1 of BIM faculty.'),
(44, 'Database Management Systems', 18, 8, '9783168104745', '4th', 'Andrew S. Tanenbaum', 'Oxford', 'BCA', '4', '685ccc62b2144_1750912098.png', 'Database Management Systems by Andrew S. Tanenbaum for semester 4 of BCA faculty.'),
(46, 'Data Structures &amp; Algorithms', 6, 0, '9781403099398', '5th', 'Rajiv Sabherwal', 'Pearson', 'Bsc.Csit', '1', '685ccb1b38e3e_1750911771.png', 'Data Structures &amp; Algorithms by Rajiv Sabherwal for semester 1 of Bsc.Csit faculty.'),
(48, 'Digital Logic', 15, 9, '9782274219154', '1th', 'Philip Kotler', 'Wiley', 'BIM', '1', '685d160912833_1750930953.png', 'Digital Logic by Philip Kotler for semester 1 of BIM faculty.'),
(52, 'Cloud Computing', 6, 2, '9785478846340', '5th', 'Ian Sommerville', 'McGraw-Hill', 'BCA', '7', '685d76ef36b56_1750955759.png', 'Cloud Computing by Ian Sommerville for semester 7 of BCA faculty.'),
(54, 'Mobile Application Development', 12, 1, '9781215861326', '5th', 'David A. Patterson', 'Wiley', 'Bsc.Csit', '5', '', 'Mobile Application Development by David A. Patterson for semester 5 of Bsc.Csit faculty.'),
(56, 'Marketing Management', 7, 7, '9788325615861', '5th', 'Korth & Sudarshan', 'Tata McGraw-Hill', 'BIM', '4', '', 'Marketing Management by Korth & Sudarshan for semester 4 of BIM faculty.'),
(57, 'Financial Accounting', 7, 5, '9782608843360', '3th', 'Philip Kotler', 'Cambridge', 'BIM', '7', '685d7b84a8f0e_1750956932.png', 'Financial Accounting by Philip Kotler for semester 7 of BIM faculty.'),
(58, 'Object Oriented Programming', 19, 1, '9785679468275', '3th', 'George Reynolds', 'Oxford', 'BCA', '4', '', 'Object Oriented Programming by George Reynolds for semester 4 of BCA faculty.'),
(59, 'Operating System Concepts', 15, 8, '9789933831318', '2th', 'Robert Lafore', 'Schaum\'s Outlines', 'BIM', '6', '', 'Operating System Concepts by Robert Lafore for semester 6 of BIM faculty.'),
(60, 'Marketing Management', 10, 2, '9786767186145', '3th', 'Ralph M. Stair', 'Wiley', 'BIM', '3', '', 'Marketing Management by Ralph M. Stair for semester 3 of BIM faculty.'),
(61, 'Data Structures &amp; Algorithms', 15, 5, '9781435133695', '3th', 'Robert Lafore', 'Wiley', 'BIM', '1', '685ccb2ca6d27_1750911788.png', 'Data Structures &amp; Algorithms by Robert Lafore for semester 1 of BIM faculty.'),
(62, 'Distributed Systems', 5, 0, '9789297176476', '3th', 'Andrew S. Tanenbaum', 'Schaum\'s Outlines', 'BIM', '1', '', 'Distributed Systems by Andrew S. Tanenbaum for semester 1 of BIM faculty.'),
(63, 'Database Management Systems', 15, 1, '9784181843730', '4th', 'David A. Patterson', 'Oxford', 'BIM', '2', '683ff4287262d_1749021736.png', 'Database Management Systems by David A. Patterson for semester 2 of BIM faculty.'),
(65, 'Computer Architecture', 17, 17, '9788520085300', '1th', 'George Reynolds', 'Schaum&#039;s Outlines', 'BCA', '3', '685d1cc91636a_1750932681.png', 'Computer Architecture by George Reynolds for semester 3 of BCA faculty.'),
(66, 'Business Communication', 20, 7, '9782114869951', '5th', 'Seymour Lipschutz', 'Schaum\'s Outlines', 'Bsc.Csit', '6', '', 'Business Communication by Seymour Lipschutz for semester 6 of Bsc.Csit faculty.'),
(67, 'Database Management Systems', 18, 2, '9782920587827', '4th', 'Robert Lafore', 'Cambridge', 'Bsc.Csit', '1', '685ccc72afdb0_1750912114.png', 'Database Management Systems by Robert Lafore for semester 1 of Bsc.Csit faculty.'),
(68, 'Information Security', 12, 12, '9784190607879', '1th', 'Stuart Russell', 'Schaum\'s Outlines', 'BCA', '8', '', 'Information Security by Stuart Russell for semester 8 of BCA faculty.'),
(69, 'Mobile Application Development', 20, 13, '9787011558147', '3th', 'Andrew S. Tanenbaum', 'Tata McGraw-Hill', 'BIM', '6', '', 'Mobile Application Development by Andrew S. Tanenbaum for semester 6 of BIM faculty.'),
(70, 'Organizational Behavior', 16, 14, '9785612734900', '4th', 'Philip Kotler', 'Schaum\'s Outlines', 'Bsc.Csit', '7', '', 'Organizational Behavior by Philip Kotler for semester 7 of Bsc.Csit faculty.'),
(71, 'Organizational Behavior', 19, 9, '9783810794942', '2th', 'Philip Kotler', 'Tata McGraw-Hill', 'BIM', '1', '', 'Organizational Behavior by Philip Kotler for semester 1 of BIM faculty.'),
(73, 'Organizational Behavior', 9, 4, '9782094833278', '3th', 'David A. Patterson', 'Oxford', 'Bsc.Csit', '7', '', 'Organizational Behavior by David A. Patterson for semester 7 of Bsc.Csit faculty.'),
(74, 'E-Commerce Essentials', 12, 4, '9783469138189', '4th', 'Robert Lafore', 'Pearson', 'Bsc.Csit', '6', '', 'E-Commerce Essentials by Robert Lafore for semester 6 of Bsc.Csit faculty.'),
(75, 'E-Commerce Essentials', 10, 1, '9786410414776', '5th', 'Korth & Sudarshan', 'Wiley', 'Bsc.Csit', '7', '', 'E-Commerce Essentials by Korth & Sudarshan for semester 7 of Bsc.Csit faculty.'),
(77, 'Mobile Application Development', 5, 3, '9785765447891', '2th', 'Ian Sommerville', 'Pearson', 'BCA', '7', '', 'Mobile Application Development by Ian Sommerville for semester 7 of BCA faculty.'),
(78, 'Data Mining', 12, 4, '9785280102191', '2th', 'E. Balagurusamy', 'Tata McGraw-Hill', 'BIM', '1', '685ccce28e7be_1750912226.png', 'Data Mining by E. Balagurusamy for semester 1 of BIM faculty.'),
(79, 'Mobile Application Development', 8, 2, '9785001657767', '3th', 'Philip Kotler', 'Tata McGraw-Hill', 'Bsc.Csit', '8', '', 'Mobile Application Development by Philip Kotler for semester 8 of Bsc.Csit faculty.'),
(80, 'Project Management', 19, 4, '9785936698008', '1th', 'Korth &amp; Sudarshan', 'Schaum&#039;s Outlines', 'BCA', '7', '683ff609edeae_1749022217.png', 'Project Management by Korth &amp; Sudarshan for semester 7 of BCA faculty.'),
(82, 'E-Commerce Essentials', 11, 1, '9786030201229', '3th', 'Stephen P. Robbins', 'Cambridge', 'BIM', '1', '', 'E-Commerce Essentials by Stephen P. Robbins for semester 1 of BIM faculty.'),
(83, 'Distributed Systems', 18, 11, '9786146029165', '4th', 'Ian Sommerville', 'Schaum\'s Outlines', 'Bsc.Csit', '3', '', 'Distributed Systems by Ian Sommerville for semester 3 of Bsc.Csit faculty.'),
(84, 'Computer Networks', 18, 3, '9783181042333', '4th', 'David A. Patterson', 'Tata McGraw-Hill', 'BIM', '1', '685d79933947c_1750956435.png', 'Computer Networks by David A. Patterson for semester 1 of BIM faculty.'),
(85, 'Data Mining', 11, 5, '9788110351848', '2th', 'Stuart Russell', 'Tata McGraw-Hill', 'BCA', '4', '685cccf0a8aa9_1750912240.png', 'Data Mining by Stuart Russell for semester 4 of BCA faculty.'),
(87, 'Web Technologies', 5, 1, '9789571817393', '3th', 'Rajiv Sabherwal', 'Oxford', 'BCA', '5', '685cbc2c9dc2b_1750907948.png', 'Web Technologies by Rajiv Sabherwal for semester 5 of BCA faculty.'),
(88, 'Digital Logic', 16, 13, '9788857285940', '2th', 'Philip Kotler', 'Tata McGraw-Hill', 'BIM', '6', '685d1625b5542_1750930981.png', 'Digital Logic by Philip Kotler for semester 6 of BIM faculty.'),
(89, 'Information Security', 5, 3, '9783472665299', '5th', 'Seymour Lipschutz', 'Oxford', 'BIM', '5', '', 'Information Security by Seymour Lipschutz for semester 5 of BIM faculty.'),
(90, 'Data Mining', 15, 4, '9788621645814', '4th', 'Philip Kotler', 'McGraw-Hill', 'Bsc.Csit', '2', '685ccd070f8e8_1750912263.png', 'Data Mining by Philip Kotler for semester 2 of Bsc.Csit faculty.'),
(91, 'Information Security', 12, 8, '9781356751589', '5th', 'Korth & Sudarshan', 'Pearson', 'BCA', '6', '', 'Information Security by Korth & Sudarshan for semester 6 of BCA faculty.'),
(92, 'Data Mining', 17, 13, '9789091270352', '3th', 'Kenneth Laudon', 'Cambridge', 'BIM', '7', '685ccd18a8bd4_1750912280.png', 'Data Mining by Kenneth Laudon for semester 7 of BIM faculty.'),
(93, 'Business Communication', 8, 7, '9784454858356', '1th', 'Robert Lafore', 'Oxford', 'BBM', '6', '', 'Business Communication by Robert Lafore for semester 6 of BCA faculty.'),
(94, 'Mobile Application Development', 11, 6, '9786438139015', '1th', 'Andrew S. Tanenbaum', 'McGraw-Hill', 'BCA', '8', '', 'Mobile Application Development by Andrew S. Tanenbaum for semester 8 of BCA faculty.'),
(95, 'Software Engineering', 7, 2, '9789513660757', '4th', 'George Reynolds', 'Pearson', 'BIM', '6', '683ff45fd7d33_1749021791.png', 'Software Engineering by George Reynolds for semester 6 of BIM faculty.'),
(96, 'E-Governance', 19, 2, '9784120307871', '2th', 'Kenneth Laudon', 'Pearson', 'BIM', '2', '', 'E-Governance by Kenneth Laudon for semester 2 of BIM faculty.'),
(97, 'E-Governance', 10, 9, '9787060398904', '1th', 'Andrew S. Tanenbaum', 'Schaum\'s Outlines', 'BIM', '6', '', 'E-Governance by Andrew S. Tanenbaum for semester 6 of BIM faculty.'),
(98, 'Database Management Systems', 12, 11, '9784759744764', '3th', 'Seymour Lipschutz', 'Schaum&#039;s Outlines', 'Bsc.Csit', '3', '685ccc82702d8_1750912130.png', 'Database Management Systems by Seymour Lipschutz for semester 3 of Bsc.Csit faculty.'),
(99, 'Digital Logic', 20, 1, '9787395876637', '4th', 'Ralph M. Stair', 'McGraw-Hill', 'BIM', '7', '685d1635633e7_1750930997.png', 'Digital Logic by Ralph M. Stair for semester 7 of BIM faculty.'),
(100, 'Organizational Behavior', 17, 7, '9786097957758', '1th', 'E. Balagurusamy', 'McGraw-Hill', 'BCA', '3', '', 'Organizational Behavior by E. Balagurusamy for semester 3 of BCA faculty.'),
(101, 'E-Governance', 15, 9, '9781534151706', '4th', 'Stephen P. Robbins', 'Cambridge', 'Bsc.Csit', '7', '', 'E-Governance by Stephen P. Robbins for semester 7 of Bsc.Csit faculty.'),
(102, 'Distributed Systems', 9, 7, '9786245581079', '3th', 'Andrew S. Tanenbaum', 'Schaum\'s Outlines', 'BIM', '6', '', 'Distributed Systems by Andrew S. Tanenbaum for semester 6 of BIM faculty.'),
(103, 'Computer Architecture', 18, 0, '9784598574799', '3th', 'George Reynolds', 'Schaum&#039;s Outlines', 'BIM', '6', '683ff55b7bbbe_1749022043.png', 'Computer Architecture by George Reynolds for semester 6 of BIM faculty.'),
(104, 'Information Security', 19, 11, '9785909097215', '4th', 'Stuart Russell', 'Pearson', 'Bsc.Csit', '6', '', 'Information Security by Stuart Russell for semester 6 of Bsc.Csit faculty.'),
(105, 'Object Oriented Programming', 20, 0, '9782466396505', '3th', 'Stuart Russell', 'Cambridge', 'BCA', '8', '', 'Object Oriented Programming by Stuart Russell for semester 8 of BCA faculty.'),
(106, 'Project Management', 7, 5, '9789919003815', '4th', 'George Reynolds', 'Oxford', 'BIM', '2', '683ff6161b513_1749022230.png', 'Project Management by George Reynolds for semester 2 of BIM faculty.'),
(107, 'System Analysis and Design', 7, 1, '9786307747729', '1th', 'Ian Sommerville', 'Wiley', 'BIM', '8', '', 'System Analysis and Design by Ian Sommerville for semester 8 of BIM faculty.'),
(108, 'Software Engineering', 17, 8, '9788290001046', '5th', 'Ian Sommerville', 'Cambridge', 'BCA', '3', '', 'Software Engineering by Ian Sommerville for semester 3 of BCA faculty.'),
(109, 'Marketing Management', 11, 10, '9785638214105', '4th', 'Kenneth Laudon', 'Oxford', 'BBM', '8', '', 'Marketing Management by Kenneth Laudon for semester 8 of Bsc.Csit faculty.'),
(110, 'Cloud Computing', 13, 11, '9784325913243', '1th', 'Ian Sommerville', 'Pearson', 'BCA', '3', '685d7706b6af9_1750955782.png', 'Cloud Computing by Ian Sommerville for semester 3 of BCA faculty.'),
(111, 'Operating System Concepts', 6, 4, '9788110657366', '2th', 'Kenneth Laudon', 'McGraw-Hill', 'BIM', '6', '', 'Operating System Concepts by Kenneth Laudon for semester 6 of BIM faculty.'),
(112, 'Marketing Management', 18, 7, '9785167958054', '5th', 'David A. Patterson', 'Tata McGraw-Hill', 'BIM', '6', '', 'Marketing Management by David A. Patterson for semester 6 of BIM faculty.'),
(114, 'Distributed Systems', 7, 7, '9784982901739', '4th', 'David A. Patterson', 'Tata McGraw-Hill', 'BCA', '2', '', 'Distributed Systems by David A. Patterson for semester 2 of BCA faculty.'),
(115, 'Database Management Systems', 17, 16, '9781699472520', '3th', 'Ian Sommerville', 'Oxford', 'BIM', '5', '685ccc90c038f_1750912144.png', 'Database Management Systems by Ian Sommerville for semester 5 of BIM faculty.'),
(116, 'Financial Accounting', 9, 8, '9785902873218', '3th', 'Abraham Silberschatz', 'Oxford', 'BCA', '7', '685d7b91e8d30_1750956945.png', 'Financial Accounting by Abraham Silberschatz for semester 7 of BCA faculty.'),
(117, 'E-Governance', 7, 1, '9784523682949', '4th', 'Philip Kotler', 'Oxford', 'Bsc.Csit', '7', '', 'E-Governance by Philip Kotler for semester 7 of Bsc.Csit faculty.'),
(118, 'Distributed Systems', 7, 3, '9781545029215', '4th', 'Korth & Sudarshan', 'Wiley', 'BIM', '3', '', 'Distributed Systems by Korth & Sudarshan for semester 3 of BIM faculty.'),
(119, 'Computer Architecture', 17, 3, '9787353311341', '2th', 'Philip Kotler', 'Wiley', 'Bsc.Csit', '1', '685d1f8ad0104_1750933386.png', 'Computer Architecture by Philip Kotler for semester 1 of Bsc.Csit faculty.'),
(120, 'Artificial Intelligence', 14, 13, '9783302947990', '5th', 'George Reynolds', 'Cambridge', 'Bsc.Csit', '5', '683ff358d6df4_1749021528.png', 'Artificial Intelligence by George Reynolds for semester 5 of Bsc.Csit faculty.'),
(121, 'Project Management', 9, 8, '9788244642436', '5th', 'Robert Lafore', 'Pearson', 'BIM', '6', '683ff62159b23_1749022241.png', 'Project Management by Robert Lafore for semester 6 of BIM faculty.'),
(122, 'Software Engineering', 15, 15, '9786482020431', '2th', 'Stephen P. Robbins', 'Schaum\'s Outlines', 'BIM', '6', '', 'Software Engineering by Stephen P. Robbins for semester 6 of BIM faculty.'),
(123, 'Financial Accounting', 13, 0, '9789028361100', '2th', 'Kenneth Laudon', 'Oxford', 'BIM', '1', '685d7bad9f30f_1750956973.png', 'Financial Accounting by Kenneth Laudon for semester 1 of BIM faculty.'),
(124, 'Organizational Behavior', 6, 6, '9787859725875', '2th', 'Abraham Silberschatz', 'Cambridge', 'BCA', '2', '', 'Organizational Behavior by Abraham Silberschatz for semester 2 of BCA faculty.'),
(125, 'Computer Networks', 12, 2, '9787107957060', '2th', 'Rajiv Sabherwal', 'Pearson', 'Bsc.Csit', '4', '685d79aae04d5_1750956458.png', 'Computer Networks by Rajiv Sabherwal for semester 4 of Bsc.Csit faculty.'),
(126, 'E-Commerce Essentials', 8, 2, '9789632904043', '1th', 'Abraham Silberschatz', 'Pearson', 'Bsc.Csit', '5', '', 'E-Commerce Essentials by Abraham Silberschatz for semester 5 of Bsc.Csit faculty.'),
(127, 'Financial Accounting', 13, 5, '9787663012905', '3th', 'Ian Sommerville', 'Schaum&#039;s Outlines', 'BIM', '3', '685d7b9ebb74f_1750956958.png', 'Financial Accounting by Ian Sommerville for semester 3 of BIM faculty.'),
(128, 'Business Communication', 7, 3, '9785860982679', '2th', 'Ian Sommerville', 'Oxford', 'Bsc.Csit', '7', '', 'Business Communication by Ian Sommerville for semester 7 of Bsc.Csit faculty.'),
(131, 'Business Communication', 15, 12, '9784053083842', '1th', 'Philip Kotler', 'Schaum\'s Outlines', 'BIM', '6', '', 'Business Communication by Philip Kotler for semester 6 of BIM faculty.'),
(132, 'Distributed Systems', 16, 4, '9789825161508', '4th', 'Andrew S. Tanenbaum', 'McGraw-Hill', 'BIM', '1', '', 'Distributed Systems by Andrew S. Tanenbaum for semester 1 of BIM faculty.'),
(133, 'Introduction to C Programming', 15, 9, '9783332758202', '3th', 'Ian Sommerville', 'Tata McGraw-Hill', 'BIM', '2', '', 'Introduction to C Programming by Ian Sommerville for semester 2 of BIM faculty.'),
(134, 'Organizational Behavior', 17, 14, '9783433166202', '1th', 'Philip Kotler', 'Wiley', 'BIM', '6', '', 'Organizational Behavior by Philip Kotler for semester 6 of BIM faculty.'),
(135, 'Artificial Intelligence', 12, 1, '9785116283703', '4th', 'Ralph M. Stair', 'Wiley', 'BCA', '7', '', 'Artificial Intelligence by Ralph M. Stair for semester 7 of BCA faculty.'),
(136, 'Object Oriented Programming', 9, 1, '9783045288503', '5th', 'Kenneth Laudon', 'Wiley', 'BIM', '7', '', 'Object Oriented Programming by Kenneth Laudon for semester 7 of BIM faculty.'),
(138, 'Data Structures &amp; Algorithms', 17, 8, '9786411971407', '3th', 'Ralph M. Stair', 'Wiley', 'BIM', '7', '685ccb4106a77_1750911809.png', 'Data Structures &amp; Algorithms by Ralph M. Stair for semester 7 of BIM faculty.'),
(139, 'Digital Logic', 8, 5, '9786648231838', '1th', 'Ian Sommerville', 'Cambridge', 'BCA', '2', '685d16451dea2_1750931013.png', 'Digital Logic by Ian Sommerville for semester 2 of BCA faculty.'),
(140, 'Project Management', 11, 5, '9789810260119', '5th', 'E. Balagurusamy', 'Oxford', 'BIM', '1', '683ff63147ea6_1749022257.png', 'Project Management by E. Balagurusamy for semester 1 of BIM faculty.'),
(141, 'Digital Logic', 12, 2, '9782069254659', '5th', 'Andrew S. Tanenbaum', 'McGraw-Hill', 'BIM', '5', '685d1655289e6_1750931029.png', 'Digital Logic by Andrew S. Tanenbaum for semester 5 of BIM faculty.'),
(142, 'Artificial Intelligence', 11, 0, '9788621176198', '5th', 'David A. Patterson', 'Wiley', 'Bsc.Csit', '6', '', 'Artificial Intelligence by David A. Patterson for semester 6 of Bsc.Csit faculty.'),
(143, 'Computer Architecture', 6, 1, '9783925863625', '5th', 'Stuart Russell', 'Pearson', 'BIM', '3', '683ff56b4d89f_1749022059.png', 'Computer Architecture by Stuart Russell for semester 3 of BIM faculty.'),
(144, 'System Analysis and Design', 7, 4, '9787865396460', '5th', 'Robert Lafore', 'McGraw-Hill', 'BCA', '4', '', 'System Analysis and Design by Robert Lafore for semester 4 of BCA faculty.'),
(145, 'Cloud Computing', 8, 7, '9788147580773', '3th', 'Robert Lafore', 'Cambridge', 'BCA', '7', '', 'Cloud Computing by Robert Lafore for semester 7 of BCA faculty.'),
(147, 'Software Engineering', 14, 10, '9781005420021', '3th', 'Ian Sommerville', 'Oxford', 'BIM', '6', '683ff491d5bc7_1749021841.png', 'Software Engineering by Ian Sommerville for semester 6 of BIM faculty.'),
(148, 'Distributed Systems', 15, 15, '9784312319031', '1th', 'Abraham Silberschatz', 'Pearson', 'BIM', '6', '', 'Distributed Systems by Abraham Silberschatz for semester 6 of BIM faculty.'),
(149, 'Artificial Intelligence', 8, 4, '9788765170161', '2th', 'Philip Kotler', 'Schaum&#039;s Outlines', 'BIM', '8', '683ff3959a223_1749021589.png', 'Artificial Intelligence by Philip Kotler for semester 8 of BIM faculty.'),
(150, 'Marketing Management', 9, 1, '9788086344523', '3th', 'Seymour Lipschutz', 'McGraw-Hill', 'BBM', '8', '', 'Marketing Management by Seymour Lipschutz for semester 8 of BIM faculty.'),
(151, 'Computer Architecture', 17, 14, '9786789903463', '2th', 'Abraham Silberschatz', 'Wiley', 'Bsc.Csit', '2', '685d2039e75df_1750933561.png', 'Computer Architecture by Abraham Silberschatz for semester 2 of Bsc.Csit faculty.'),
(152, 'Web Technologies', 16, 1, '9789527165070', '3th', 'Korth &amp; Sudarshan', 'Oxford', 'Bsc.Csit', '5', '685cbc4f3e219_1750907983.png', 'Web Technologies by Korth &amp; Sudarshan for semester 5 of Bsc.Csit faculty.'),
(153, 'System Analysis and Design', 18, 18, '9785630346594', '2th', 'Andrew S. Tanenbaum', 'Schaum\'s Outlines', 'BIM', '4', '', 'System Analysis and Design by Andrew S. Tanenbaum for semester 4 of BIM faculty.'),
(154, 'Data Structures &amp;amp; Algorithms', 16, 16, '9783793656419', '5th', 'Stephen P. Robbins', 'Oxford', 'BCA', '3', '685ccd543812f_1750912340.png', 'Data Structures &amp;amp; Algorithms by Stephen P. Robbins for semester 3 of BCA faculty.'),
(156, 'Introduction to C Programming', 9, 3, '9784061322224', '5th', 'Stuart Russell', 'Pearson', 'BIM', '5', '', 'Introduction to C Programming by Stuart Russell for semester 5 of BIM faculty.'),
(157, 'Human Computer Interaction', 5, 5, '9783482974688', '1th', 'Robert Lafore', 'Wiley', 'Bsc.Csit', '5', '685d7a95cbb4f_1750956693.png', 'Human Computer Interaction by Robert Lafore for semester 5 of Bsc.Csit faculty.'),
(159, 'Introduction to C Programming', 7, 3, '9785224357734', '4th', 'E. Balagurusamy', 'Tata McGraw-Hill', 'BIM', '8', '', 'Introduction to C Programming by E. Balagurusamy for semester 8 of BIM faculty.'),
(160, 'Data Mining', 18, 12, '9783317363977', '1th', 'Korth &amp; Sudarshan', 'Schaum&#039;s Outlines', 'BIM', '1', '685cce6167746_1750912609.png', 'Data Mining by Korth &amp; Sudarshan for semester 1 of BIM faculty.'),
(161, 'Project Management', 13, 12, '9789321076163', '3th', 'Kenneth Laudon', 'McGraw-Hill', 'BCA', '3', '', 'Project Management by Kenneth Laudon for semester 3 of BCA faculty.'),
(162, 'Data Structures &amp; Algorithms', 15, 0, '9786876656365', '4th', 'Ralph M. Stair', 'Cambridge', 'BCA', '7', '685ccdaa803a7_1750912426.png', 'Data Structures &amp; Algorithms by Ralph M. Stair for semester 7 of BCA faculty.'),
(163, 'Operating System Concepts', 16, 0, '9787794054749', '1th', 'Philip Kotler', 'Oxford', 'BIM', '3', '', 'Operating System Concepts by Philip Kotler for semester 3 of BIM faculty.'),
(164, 'Mobile Application Development', 6, 0, '9787649481626', '2th', 'Andrew S. Tanenbaum', 'Tata McGraw-Hill', 'Bsc.Csit', '2', '', 'Mobile Application Development by Andrew S. Tanenbaum for semester 2 of Bsc.Csit faculty.'),
(165, 'Distributed Systems', 13, 9, '9784208991256', '2th', 'Rajiv Sabherwal', 'Oxford', 'BCA', '3', '', 'Distributed Systems by Rajiv Sabherwal for semester 3 of BCA faculty.'),
(166, 'Operating System Concepts', 10, 9, '9783670073069', '4th', 'Ian Sommerville', 'Wiley', 'Bsc.Csit', '2', '', 'Operating System Concepts by Ian Sommerville for semester 2 of Bsc.Csit faculty.'),
(167, 'Human Computer Interaction', 10, 6, '9788292602287', '2th', 'Robert Lafore', 'McGraw-Hill', 'BCA', '5', '685d7aaba2b5e_1750956715.png', 'Human Computer Interaction by Robert Lafore for semester 5 of BCA faculty.'),
(168, 'Operating System Concepts', 10, 7, '9787138177938', '5th', 'Stuart Russell', 'Wiley', 'BCA', '8', '', 'Operating System Concepts by Stuart Russell for semester 8 of BCA faculty.'),
(170, 'Web Technologies', 7, 4, '9788455581096', '5th', 'Korth &amp; Sudarshan', 'Wiley', 'BIM', '5', '685cbc62eaf19_1750908002.png', 'Web Technologies by Korth &amp; Sudarshan for semester 5 of BIM faculty.'),
(171, 'Operating System Concepts', 12, 9, '9789480137404', '1th', 'E. Balagurusamy', 'Schaum\'s Outlines', 'BCA', '4', '', 'Operating System Concepts by E. Balagurusamy for semester 4 of BCA faculty.'),
(172, 'Computer Networks', 9, 6, '9784732202443', '2th', 'Robert Lafore', 'Oxford', 'BCA', '4', '685d79bbd418c_1750956475.png', 'Computer Networks by Robert Lafore for semester 4 of BCA faculty.'),
(173, 'Data Mining', 14, 8, '9782214796536', '3th', 'George Reynolds', 'Tata McGraw-Hill', 'BCA', '3', '685cce7ea1bfc_1750912638.png', 'Data Mining by George Reynolds for semester 3 of BCA faculty.'),
(174, 'Software Engineering', 15, 4, '9789866993099', '3th', 'Philip Kotler', 'Oxford', 'BIM', '4', '', 'Software Engineering by Philip Kotler for semester 4 of BIM faculty.'),
(175, 'E-Governance', 9, 5, '9781832014347', '4th', 'Ian Sommerville', 'Pearson', 'Bsc.Csit', '1', '', 'E-Governance by Ian Sommerville for semester 1 of Bsc.Csit faculty.'),
(176, 'Distributed Systems', 6, 0, '9786297564481', '3th', 'Ian Sommerville', 'Cambridge', 'BCA', '8', '', 'Distributed Systems by Ian Sommerville for semester 8 of BCA faculty.'),
(177, 'Organizational Behavior', 6, 0, '9789812728084', '1th', 'Ralph M. Stair', 'Cambridge', 'BIM', '4', '', 'Organizational Behavior by Ralph M. Stair for semester 4 of BIM faculty.'),
(178, 'Data Structures &amp; Algorithms', 19, 18, '9789600496275', '5th', 'Seymour Lipschutz', 'Schaum&#039;s Outlines', 'BIM', '3', '685cce16d8208_1750912534.png', 'Data Structures &amp; Algorithms by Seymour Lipschutz for semester 3 of BIM faculty.'),
(179, 'Database Management Systems', 7, 6, '9782749478969', '4th', 'Kenneth Laudon', 'Pearson', 'BCA', '6', '685ccead75192_1750912685.png', 'Database Management Systems by Kenneth Laudon for semester 6 of BCA faculty.'),
(180, 'Operating System Concepts', 10, 9, '9783034067729', '4th', 'E. Balagurusamy', 'McGraw-Hill', 'BIM', '8', '', 'Operating System Concepts by E. Balagurusamy for semester 8 of BIM faculty.'),
(181, 'Computer Networks', 15, 3, '9781292896548', '2th', 'Philip Kotler', 'Tata McGraw-Hill', 'Bsc.Csit', '3', '685d79c9e316d_1750956489.png', 'Computer Networks by Philip Kotler for semester 3 of Bsc.Csit faculty.'),
(182, 'Web Technologies', 13, 11, '9784199822119', '2th', 'Korth &amp; Sudarshan', 'Pearson', 'BCA', '2', '685cbc78a0c3c_1750908024.png', 'Web Technologies by Korth &amp; Sudarshan for semester 2 of BCA faculty.'),
(183, 'Introduction to C Programming', 8, 0, '9785184959117', '3th', 'Stuart Russell', 'Oxford', 'Bsc.Csit', '1', '', 'Introduction to C Programming by Stuart Russell for semester 1 of Bsc.Csit faculty.'),
(184, 'E-Governance', 18, 9, '9788452197935', '1th', 'Stuart Russell', 'Pearson', 'Bsc.Csit', '4', '', 'E-Governance by Stuart Russell for semester 4 of Bsc.Csit faculty.'),
(186, 'Mobile Application Development', 13, 5, '9783900504696', '1th', 'Korth & Sudarshan', 'Wiley', 'BCA', '2', '', 'Mobile Application Development by Korth & Sudarshan for semester 2 of BCA faculty.'),
(187, 'Organizational Behavior', 7, 2, '9783636440432', '1th', 'Ralph M. Stair', 'McGraw-Hill', 'BCA', '1', '', 'Organizational Behavior by Ralph M. Stair for semester 1 of BCA faculty.'),
(188, 'Organizational Behavior', 11, 6, '9782336463558', '2th', 'Stephen P. Robbins', 'Oxford', 'BIM', '3', '', 'Organizational Behavior by Stephen P. Robbins for semester 3 of BIM faculty.'),
(189, 'Computer Architecture', 18, 5, '9783861135540', '4th', 'Stuart Russell', 'Schaum&#039;s Outlines', 'BIM', '5', '683ff578e371a_1749022072.png', 'Computer Architecture by Stuart Russell for semester 5 of BIM faculty.'),
(190, 'Organizational Behavior', 14, 12, '9787728157192', '5th', 'Robert Lafore', 'McGraw-Hill', 'Bsc.Csit', '3', '', 'Organizational Behavior by Robert Lafore for semester 3 of Bsc.Csit faculty.'),
(192, 'Mobile Application Development', 20, 1, '9788745211666', '1th', 'Andrew S. Tanenbaum', 'Cambridge', 'Bsc.Csit', '5', '', 'Mobile Application Development by Andrew S. Tanenbaum for semester 5 of Bsc.Csit faculty.'),
(193, 'Marketing Management', 17, 4, '9784691013769', '5th', 'Robert Lafore', 'Tata McGraw-Hill', 'Bsc.Csit', '3', '', 'Marketing Management by Robert Lafore for semester 3 of Bsc.Csit faculty.'),
(194, 'Artificial Intelligence', 20, 11, '9782565802121', '1th', 'Stuart Russell', 'Oxford', 'BIM', '5', '', 'Artificial Intelligence by Stuart Russell for semester 5 of BIM faculty.'),
(195, 'Database Management Systems', 14, 6, '9789379714178', '5th', 'Ralph M. Stair', 'Pearson', 'BIM', '2', '683ff436ed301_1749021750.png', 'Database Management Systems by Ralph M. Stair for semester 2 of BIM faculty.'),
(196, 'Introduction to C Programming', 7, 0, '9788447370545', '5th', 'Andrew S. Tanenbaum', 'Wiley', 'BCA', '8', '', 'Introduction to C Programming by Andrew S. Tanenbaum for semester 8 of BCA faculty.'),
(197, 'Data Mining', 18, 2, '9787237638450', '5th', 'Seymour Lipschutz', 'Schaum&#039;s Outlines', 'BCA', '5', '685ccec9dda21_1750912713.png', 'Data Mining by Seymour Lipschutz for semester 5 of BCA faculty.'),
(198, 'Business Communication', 13, 1, '9789321104179', '3th', 'Andrew S. Tanenbaum', 'McGraw-Hill', 'BCA', '5', '', 'Business Communication by Andrew S. Tanenbaum for semester 5 of BCA faculty.'),
(199, 'Mobile Application Development', 8, 0, '9787726623077', '3th', 'Abraham Silberschatz', 'Schaum\'s Outlines', 'BIM', '2', '', 'Mobile Application Development by Abraham Silberschatz for semester 2 of BIM faculty.'),
(200, 'Data Structures &amp; Algorithms', 9, 6, '9789951147840', '1th', 'Philip Kotler', 'Schaum&#039;s Outlines', 'BIM', '4', '685cce2e2bbc5_1750912558.png', 'Data Structures &amp; Algorithms by Philip Kotler for semester 4 of BIM faculty.'),
(201, 'Information Security', 8, 3, '9781458780508', '3th', 'Ralph M. Stair', 'Tata McGraw-Hill', 'BCA', '6', '', 'Information Security by Ralph M. Stair for semester 6 of BCA faculty.'),
(202, 'Object Oriented Programming', 16, 1, '9784724278740', '3th', 'Seymour Lipschutz', 'Pearson', 'BIM', '7', '', 'Object Oriented Programming by Seymour Lipschutz for semester 7 of BIM faculty.'),
(203, 'Object Oriented Programming', 5, 4, '9785544581591', '2th', 'Philip Kotler', 'Cambridge', 'BIM', '4', '', 'Object Oriented Programming by Philip Kotler for semester 4 of BIM faculty.'),
(204, 'Project Management', 16, 13, '9781480879117', '5th', 'Robert Lafore', 'Schaum\'s Outlines', 'BCA', '3', '', 'Project Management by Robert Lafore for semester 3 of BCA faculty.'),
(205, 'Software Engineering', 14, 14, '9782693968157', '3th', 'Philip Kotler', 'Schaum&#039;s Outlines', 'Bsc.Csit', '8', '683ff49f8378e_1749021855.png', 'Software Engineering by Philip Kotler for semester 8 of Bsc.Csit faculty.'),
(206, 'Business Communication', 10, 3, '9786629185785', '4th', 'Ralph M. Stair', 'Cambridge', 'BIM', '8', '', 'Business Communication by Ralph M. Stair for semester 8 of BIM faculty.'),
(207, 'Information Security', 10, 8, '9789295424599', '2th', 'Philip Kotler', 'McGraw-Hill', 'BCA', '2', '', 'Information Security by Philip Kotler for semester 2 of BCA faculty.'),
(208, 'Computer Networks', 10, 7, '9786573795168', '2th', 'Abraham Silberschatz', 'Tata McGraw-Hill', 'BCA', '8', '685d79da02420_1750956506.png', 'Computer Networks by Abraham Silberschatz for semester 8 of BCA faculty.'),
(209, 'Mobile Application Development', 10, 7, '9787576016076', '3th', 'Seymour Lipschutz', 'Wiley', 'BIM', '2', '', 'Mobile Application Development by Seymour Lipschutz for semester 2 of BIM faculty.'),
(210, 'Information Security', 10, 8, '9788162589595', '4th', 'George Reynolds', 'Wiley', 'BCA', '5', '', 'Information Security by George Reynolds for semester 5 of BCA faculty.'),
(211, 'Project Management', 8, 0, '9785127462504', '5th', 'Ian Sommerville', 'Pearson', 'BCA', '4', '', 'Project Management by Ian Sommerville for semester 4 of BCA faculty.'),
(212, 'Database Management Systems', 20, 20, '9787580256840', '3th', 'Stuart Russell', 'Schaum&#039;s Outlines', 'BCA', '3', '685ccf0a5156c_1750912778.png', 'Database Management Systems by Stuart Russell for semester 3 of BCA faculty.'),
(213, 'Computer Architecture', 9, 5, '9785095506956', '4th', 'Rajiv Sabherwal', 'Tata McGraw-Hill', 'BIM', '7', '', 'Computer Architecture by Rajiv Sabherwal for semester 7 of BIM faculty.'),
(214, 'Marketing Management', 11, 7, '9788822108276', '4th', 'Rajiv Sabherwal', 'Pearson', 'BIM', '4', '', 'Marketing Management by Rajiv Sabherwal for semester 4 of BIM faculty.'),
(215, 'Organizational Behavior', 16, 14, '9783200374570', '1th', 'David A. Patterson', 'Wiley', 'BCA', '5', '', 'Organizational Behavior by David A. Patterson for semester 5 of BCA faculty.'),
(216, 'Project Management', 20, 2, '9789423477114', '4th', 'Stephen P. Robbins', 'Oxford', 'BIM', '3', '', 'Project Management by Stephen P. Robbins for semester 3 of BIM faculty.'),
(217, 'Database Management Systems', 12, 7, '9789346369836', '1th', 'Andrew S. Tanenbaum', 'Pearson', 'Bsc.Csit', '8', '685ccf323be3b_1750912818.png', 'Database Management Systems by Andrew S. Tanenbaum for semester 8 of Bsc.Csit faculty.'),
(218, 'Operating System Concepts', 18, 7, '9789993134045', '1th', 'Seymour Lipschutz', 'Oxford', 'BCA', '2', '', 'Operating System Concepts by Seymour Lipschutz for semester 2 of BCA faculty.'),
(220, 'Project Management', 7, 6, '9782882050631', '5th', 'Stephen P. Robbins', 'Cambridge', 'BCA', '1', '', 'Project Management by Stephen P. Robbins for semester 1 of BCA faculty.'),
(221, 'E-Commerce Essentials', 5, 2, '9787758825354', '2th', 'Stephen P. Robbins', 'McGraw-Hill', 'BIM', '5', '', 'E-Commerce Essentials by Stephen P. Robbins for semester 5 of BIM faculty.'),
(222, 'Business Communication', 11, 8, '9782194549399', '4th', 'Stuart Russell', 'Tata McGraw-Hill', 'BIM', '4', '', 'Business Communication by Stuart Russell for semester 4 of BIM faculty.'),
(223, 'Cloud Computing', 11, 9, '9786416283503', '1th', 'Abraham Silberschatz', 'Tata McGraw-Hill', 'BCA', '4', '685d7715c4563_1750955797.png', 'Cloud Computing by Abraham Silberschatz for semester 4 of BCA faculty.'),
(224, 'Human Computer Interaction', 7, 6, '9782969126651', '2th', 'Kenneth Laudon', 'Oxford', 'BIM', '7', '685d7ab946bf2_1750956729.png', 'Human Computer Interaction by Kenneth Laudon for semester 7 of BIM faculty.'),
(225, 'E-Commerce Essentials', 9, 8, '9785944683803', '5th', 'David A. Patterson', 'Pearson', 'BCA', '5', '', 'E-Commerce Essentials by David A. Patterson for semester 5 of BCA faculty.'),
(226, 'Marketing Management', 20, 2, '9783314916785', '2th', 'Korth & Sudarshan', 'Wiley', 'BIM', '6', '', 'Marketing Management by Korth & Sudarshan for semester 6 of BIM faculty.'),
(227, 'Computer Architecture', 12, 3, '9789214453197', '4th', 'Stuart Russell', 'Cambridge', 'Bsc.Csit', '3', '', 'Computer Architecture by Stuart Russell for semester 3 of Bsc.Csit faculty.'),
(228, 'E-Governance', 19, 9, '9781339986245', '3th', 'Ralph M. Stair', 'Schaum\'s Outlines', 'Bsc.Csit', '6', '', 'E-Governance by Ralph M. Stair for semester 6 of Bsc.Csit faculty.'),
(229, 'Software Engineering', 5, 1, '9789811186162', '2th', 'Robert Lafore', 'Pearson', 'BCA', '8', '', 'Software Engineering by Robert Lafore for semester 8 of BCA faculty.'),
(230, 'Artificial Intelligence', 18, 13, '9783612719633', '2th', 'Kenneth Laudon', 'Pearson', 'BIM', '6', '', 'Artificial Intelligence by Kenneth Laudon for semester 6 of BIM faculty.'),
(231, 'Digital Logic', 9, 4, '9783246839040', '5th', 'Stuart Russell', 'McGraw-Hill', 'BCA', '7', '685d1665268dd_1750931045.png', 'Digital Logic by Stuart Russell for semester 7 of BCA faculty.'),
(232, 'Computer Networks', 19, 19, '9781444603193', '5th', 'David A. Patterson', 'McGraw-Hill', 'Bsc.Csit', '8', '685d79ebb993f_1750956523.png', 'Computer Networks by David A. Patterson for semester 8 of Bsc.Csit faculty.'),
(233, 'Distributed Systems', 16, 3, '9789110950240', '3th', 'Seymour Lipschutz', 'Wiley', 'BCA', '4', '', 'Distributed Systems by Seymour Lipschutz for semester 4 of BCA faculty.'),
(234, 'Operating System Concepts', 14, 8, '9786764270169', '4th', 'Philip Kotler', 'Pearson', 'Bsc.Csit', '8', '', 'Operating System Concepts by Philip Kotler for semester 8 of Bsc.Csit faculty.'),
(235, 'E-Governance', 12, 8, '9786817601062', '3th', 'George Reynolds', 'Wiley', 'BCA', '8', '', 'E-Governance by George Reynolds for semester 8 of BCA faculty.'),
(236, 'Business Communication', 17, 3, '9785398583024', '1th', 'Stephen P. Robbins', 'Cambridge', 'BCA', '4', '', 'Business Communication by Stephen P. Robbins for semester 4 of BCA faculty.'),
(237, 'Object Oriented Programming', 6, 2, '9784813765475', '5th', 'Kenneth Laudon', 'Schaum\'s Outlines', 'Bsc.Csit', '1', '', 'Object Oriented Programming by Kenneth Laudon for semester 1 of Bsc.Csit faculty.'),
(239, 'Organizational Behavior', 12, 10, '9781530000625', '4th', 'Robert Lafore', 'Pearson', 'BIM', '7', '', 'Organizational Behavior by Robert Lafore for semester 7 of BIM faculty.'),
(240, 'Database Management Systems', 17, 4, '9784730041795', '2th', 'Andrew S. Tanenbaum', 'Pearson', 'BIM', '6', '685ccf70ac8dd_1750912880.png', 'Database Management Systems by Andrew S. Tanenbaum for semester 6 of BIM faculty.'),
(241, 'Mobile Application Development', 14, 10, '9781887262173', '5th', 'Stephen P. Robbins', 'Cambridge', 'Bsc.Csit', '5', '', 'Mobile Application Development by Stephen P. Robbins for semester 5 of Bsc.Csit faculty.'),
(242, 'Operating System Concepts', 7, 4, '9783516556764', '4th', 'Korth & Sudarshan', 'Cambridge', 'BIM', '8', '', 'Operating System Concepts by Korth & Sudarshan for semester 8 of BIM faculty.'),
(243, 'E-Governance', 7, 2, '9783841429173', '2th', 'Philip Kotler', 'Schaum\'s Outlines', 'BCA', '1', '', 'E-Governance by Philip Kotler for semester 1 of BCA faculty.'),
(244, 'Mobile Application Development', 11, 3, '9789931388639', '4th', 'Stephen P. Robbins', 'McGraw-Hill', 'BIM', '5', '', 'Mobile Application Development by Stephen P. Robbins for semester 5 of BIM faculty.'),
(245, 'Database Management Systems', 15, 0, '9781986907178', '2th', 'George Reynolds', 'McGraw-Hill', 'Bsc.Csit', '5', '', 'Database Management Systems by George Reynolds for semester 5 of Bsc.Csit faculty.'),
(246, 'Artificial Intelligence', 9, 8, '9784631640863', '2th', 'Rajiv Sabherwal', 'Wiley', 'BCA', '2', '', 'Artificial Intelligence by Rajiv Sabherwal for semester 2 of BCA faculty.'),
(247, 'Data Structures &amp; Algorithms', 12, 3, '9784087464594', '2th', 'E. Balagurusamy', 'Oxford', 'Bsc.Csit', '2', '685cce46eb977_1750912582.png', 'Data Structures &amp; Algorithms by E. Balagurusamy for semester 2 of Bsc.Csit faculty.'),
(248, 'Human Computer Interaction', 16, 3, '9782586809336', '4th', 'Andrew S. Tanenbaum', 'Tata McGraw-Hill', 'BCA', '6', '685d7ad72a248_1750956759.png', 'Human Computer Interaction by Andrew S. Tanenbaum for semester 6 of BCA faculty.'),
(249, 'Software Engineering', 12, 3, '9782949274395', '2th', 'Stuart Russell', 'Oxford', 'BIM', '1', '', 'Software Engineering by Stuart Russell for semester 1 of BIM faculty.'),
(250, 'Distributed Systems', 8, 0, '9783417996720', '5th', 'Andrew S. Tanenbaum', 'Tata McGraw-Hill', 'BIM', '5', '', 'Distributed Systems by Andrew S. Tanenbaum for semester 5 of BIM faculty.'),
(251, 'Web Technologies', 12, 11, '9788398451676', '5th', 'Andrew S. Tanenbaum', 'Schaum&#039;s Outlines', 'BCA', '5', '685cbc905ec2e_1750908048.png', 'Web Technologies by Andrew S. Tanenbaum for semester 5 of BCA faculty.'),
(252, 'E-Commerce Essentials', 10, 1, '9783326752770', '2th', 'Ralph M. Stair', 'Oxford', 'Bsc.Csit', '7', '', 'E-Commerce Essentials by Ralph M. Stair for semester 7 of Bsc.Csit faculty.'),
(253, 'Artificial Intelligence', 12, 2, '9783433253557', '4th', 'E. Balagurusamy', 'Oxford', 'BCA', '5', '', 'Artificial Intelligence by E. Balagurusamy for semester 5 of BCA faculty.'),
(254, 'Data Mining', 12, 5, '9786461247093', '5th', 'Seymour Lipschutz', 'Schaum&#039;s Outlines', 'Bsc.Csit', '6', '685ccee6e5be6_1750912742.png', 'Data Mining by Seymour Lipschutz for semester 6 of Bsc.Csit faculty.'),
(256, 'Mobile Application Development', 12, 10, '9784312270679', '4th', 'David A. Patterson', 'Pearson', 'BCA', '8', '', 'Mobile Application Development by David A. Patterson for semester 8 of BCA faculty.'),
(257, 'Digital Logic', 18, 14, '9785885450150', '2th', 'Kenneth Laudon', 'McGraw-Hill', 'BIM', '8', '685d167554e1f_1750931061.png', 'Digital Logic by Kenneth Laudon for semester 8 of BIM faculty.'),
(258, 'Organizational Behavior', 5, 5, '9784691538645', '5th', 'Kenneth Laudon', 'McGraw-Hill', 'Bsc.Csit', '3', '', 'Organizational Behavior by Kenneth Laudon for semester 3 of Bsc.Csit faculty.'),
(259, 'Object Oriented Programming', 13, 11, '9782684207460', '3th', 'Abraham Silberschatz', 'Oxford', 'BIM', '1', '', 'Object Oriented Programming by Abraham Silberschatz for semester 1 of BIM faculty.'),
(260, 'Marketing Management', 7, 4, '9785030172262', '5th', 'Seymour Lipschutz', 'Cambridge', 'BIM', '8', '', 'Marketing Management by Seymour Lipschutz for semester 8 of BIM faculty.'),
(261, 'Computer Architecture', 11, 4, '9787094993240', '4th', 'George Reynolds', 'McGraw-Hill', 'Bsc.Csit', '5', '', 'Computer Architecture by George Reynolds for semester 5 of Bsc.Csit faculty.'),
(262, 'Project Management', 6, 3, '9788102936697', '5th', 'George Reynolds', 'Wiley', 'BCA', '5', '', 'Project Management by George Reynolds for semester 5 of BCA faculty.'),
(263, 'Human Computer Interaction', 17, 17, '9786883214391', '4th', 'Stephen P. Robbins', 'Cambridge', 'Bsc.Csit', '4', '685d7ac8419b3_1750956744.png', 'Human Computer Interaction by Stephen P. Robbins for semester 4 of Bsc.Csit faculty.'),
(264, 'Data Mining', 5, 1, '9783860044625', '1th', 'Korth &amp; Sudarshan', 'Oxford', 'BIM', '7', '685ccfadd9ecb_1750912941.png', 'Data Mining by Korth &amp; Sudarshan for semester 7 of BIM faculty.'),
(265, 'Information Security', 12, 3, '9782407789062', '5th', 'E. Balagurusamy', 'Wiley', 'BIM', '8', '', 'Information Security by E. Balagurusamy for semester 8 of BIM faculty.'),
(266, 'Web Technologies', 20, 17, '9787233602160', '3th', 'Kenneth Laudon', 'Wiley', 'BCA', '5', '685cbcabdcd79_1750908075.png', 'Web Technologies by Kenneth Laudon for semester 5 of BCA faculty.'),
(267, 'Digital Logic', 8, 2, '9782499446815', '2th', 'Seymour Lipschutz', 'McGraw-Hill', 'Bsc.Csit', '6', '', 'Digital Logic by Seymour Lipschutz for semester 6 of Bsc.Csit faculty.'),
(268, 'Business Communication', 20, 10, '9785438847208', '2th', 'George Reynolds', 'Wiley', 'BIM', '1', '', 'Business Communication by George Reynolds for semester 1 of BIM faculty.'),
(269, 'Organizational Behavior', 17, 10, '9782717672131', '4th', 'David A. Patterson', 'Schaum\'s Outlines', 'Bsc.Csit', '4', '', 'Organizational Behavior by David A. Patterson for semester 4 of Bsc.Csit faculty.');
INSERT INTO `books` (`book_id`, `book_name`, `total_quantity`, `available_quantity`, `book_num`, `book_edition`, `author_name`, `publication`, `faculty`, `semester`, `picture`, `description`) VALUES
(270, 'Data Mining', 18, 0, '9781311144894', '2th', 'Kenneth Laudon', 'Schaum&#039;s Outlines', 'BIM', '5', '685ccfc1a78db_1750912961.png', 'Data Mining by Kenneth Laudon for semester 5 of BIM faculty.'),
(271, 'Web Technologies', 17, 1, '9787665429133', '1th', 'Seymour Lipschutz', 'Wiley', 'Bsc.Csit', '2', '685cbcc1a3ae7_1750908097.png', 'Web Technologies by Seymour Lipschutz for semester 2 of Bsc.Csit faculty.'),
(272, 'E-Commerce Essentials', 5, 1, '9787240078382', '2th', 'Kenneth Laudon', 'Oxford', 'Bsc.Csit', '6', '', 'E-Commerce Essentials by Kenneth Laudon for semester 6 of Bsc.Csit faculty.'),
(273, 'Introduction to C Programming', 15, 3, '9785677174154', '5th', 'Stuart Russell', 'Tata McGraw-Hill', 'BIM', '6', '', 'Introduction to C Programming by Stuart Russell for semester 6 of BIM faculty.'),
(274, 'E-Governance', 13, 12, '9787259816489', '1th', 'Philip Kotler', 'McGraw-Hill', 'BCA', '2', '', 'E-Governance by Philip Kotler for semester 2 of BCA faculty.'),
(275, 'Marketing Management', 12, 4, '9784627544027', '1th', 'Robert Lafore', 'Pearson', 'BCA', '1', '', 'Marketing Management by Robert Lafore for semester 1 of BCA faculty.'),
(276, 'Cloud Computing', 19, 7, '9785199199210', '5th', 'Seymour Lipschutz', 'Tata McGraw-Hill', 'Bsc.Csit', '6', '685d772293788_1750955810.png', 'Cloud Computing by Seymour Lipschutz for semester 6 of Bsc.Csit faculty.'),
(277, 'Introduction to C Programming', 13, 1, '9786610169073', '5th', 'Rajiv Sabherwal', 'Pearson', 'Bsc.Csit', '3', '', 'Introduction to C Programming by Rajiv Sabherwal for semester 3 of Bsc.Csit faculty.'),
(278, 'Distributed Systems', 10, 10, '9789877970809', '1th', 'David A. Patterson', 'McGraw-Hill', 'BCA', '4', '', 'Distributed Systems by David A. Patterson for semester 4 of BCA faculty.'),
(281, 'E-Commerce Essentials', 7, 6, '9788933444710', '4th', 'Rajiv Sabherwal', 'Wiley', 'BCA', '1', '', 'E-Commerce Essentials by Rajiv Sabherwal for semester 1 of BCA faculty.'),
(283, 'Web Technologies', 15, 7, '9785480387639', '4th', 'Korth &amp; Sudarshan', 'Wiley', 'BCA', '4', '685cbcdb49d0a_1750908123.png', 'Web Technologies by Korth &amp; Sudarshan for semester 4 of BCA faculty.'),
(284, 'Introduction to C Programming', 14, 5, '9785657920667', '3th', 'Stephen P. Robbins', 'Tata McGraw-Hill', 'Bsc.Csit', '5', '', 'Introduction to C Programming by Stephen P. Robbins for semester 5 of Bsc.Csit faculty.'),
(287, 'Software Engineering', 17, 7, '9788467730869', '3th', 'E. Balagurusamy', 'Cambridge', 'Bsc.Csit', '7', '', 'Software Engineering by E. Balagurusamy for semester 7 of Bsc.Csit faculty.'),
(289, 'Information Security', 16, 7, '9787664189336', '5th', 'Stephen P. Robbins', 'Cambridge', 'BCA', '6', '', 'Information Security by Stephen P. Robbins for semester 6 of BCA faculty.'),
(290, 'E-Commerce Essentials', 8, 4, '9782722228293', '3th', 'Ian Sommerville', 'Cambridge', 'Bsc.Csit', '5', '', 'E-Commerce Essentials by Ian Sommerville for semester 5 of Bsc.Csit faculty.'),
(291, 'Mobile Application Development', 8, 7, '9787994500730', '4th', 'George Reynolds', 'Pearson', 'BIM', '8', '', 'Mobile Application Development by George Reynolds for semester 8 of BIM faculty.'),
(293, 'Digital Logic', 14, 6, '9786567595263', '1th', 'Seymour Lipschutz', 'McGraw-Hill', 'BIM', '7', '685d16858cafc_1750931077.png', 'Digital Logic by Seymour Lipschutz for semester 7 of BIM faculty.'),
(294, 'E-Commerce Essentials', 19, 7, '9781606694954', '3th', 'Andrew S. Tanenbaum', 'McGraw-Hill', 'BIM', '7', '685c2668e1119_1750869608.png', 'E-Commerce Essentials by Andrew S. Tanenbaum for semester 7 of BIM faculty.'),
(295, 'Digital Logic', 17, 6, '9783173771063', '1th', 'Ralph M. Stair', 'Tata McGraw-Hill', 'BBM', '3', '685d169896454_1750931096.png', 'Digital Logic by Ralph M. Stair for semester 3 of BIM faculty.'),
(296, 'Project Management', 11, 6, '9784693467653', '4th', 'Korth & Sudarshan', 'Cambridge', 'Bsc.Csit', '3', '', 'Project Management by Korth & Sudarshan for semester 3 of Bsc.Csit faculty.'),
(298, 'E-Governance', 9, 4, '9781254863050', '2th', 'Ian Sommerville', 'Pearson', 'BBM', '8', '', 'E-Governance by Ian Sommerville for semester 8 of BIM faculty.'),
(302, 'Principle of Accounting', 50, 50, '9876545364098', '1st', 'Ratna Shakya', 'Gorkhali', 'BBM', '1', '683ec71ae206f_1748944666.png', 'The book is for Accounting students.'),
(303, 'Operation Management', 50, 50, '3546736453456', '1st', 'Ram Krishna Tiwari', 'Nepalgunj', 'BBM', '4', '', 'The book provide the insight on the operation level of business.'),
(304, 'Networking and system administration', 50, 49, '1111222233333', '4th', 'peter parker', 'Bhaktapuria', 'BIM', '7', '685d7a14e5081_1750956564.png', 'The book provide the insight on networking and systems.');

-- --------------------------------------------------------

--
-- Table structure for table `book_request`
--

CREATE TABLE `book_request` (
  `request_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `book_edition` varchar(100) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `student_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `book_num` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_request`
--

INSERT INTO `book_request` (`request_id`, `user_email`, `book_name`, `book_edition`, `author_name`, `request_date`, `status`, `student_id`, `student_name`, `book_num`) VALUES
(119, 'remesh@gmail.com', 'Computer Architecture', '2th', 'Korth &amp; Sudarshan', '2025-06-26 07:29:05', '', 44, 'Ramesh Timalsina', '9782738915825'),
(120, 'remesh@gmail.com', 'E-Commerce Essentials', '3th', 'Andrew S. Tanenbaum', '2025-06-26 07:29:15', '', 44, 'Ramesh Timalsina', '9781606694954'),
(121, 'remesh@gmail.com', 'Organizational Behavior', '3th', 'David A. Patterson', '2025-06-26 07:29:20', '', 44, 'Ramesh Timalsina', '9782094833278'),
(122, 'remesh@gmail.com', 'Project Management', '4th', 'Kenneth Laudon', '2025-06-26 07:29:25', '', 44, 'Ramesh Timalsina', '9785386505125'),
(123, 'remesh@gmail.com', 'Data Mining', '2th', 'Kenneth Laudon', '2025-06-26 07:29:32', '', 44, 'Ramesh Timalsina', '9789303209161'),
(124, 'remesh@gmail.com', 'Mobile Application Development', '3th', 'Stuart Russell', '2025-06-26 07:31:38', '', 44, 'Ramesh Timalsina', '9786093462704'),
(127, 'remesh@gmail.com', 'Human Computer Interaction', '2th', 'Kenneth Laudon', '2025-06-26 07:32:02', '', 44, 'Ramesh Timalsina', '9782969126651'),
(128, 'remesh@gmail.com', 'Financial Accounting', '5th', 'Kenneth Laudon', '2025-06-26 07:32:07', '', 44, 'Ramesh Timalsina', '9782853101041'),
(129, 'remesh@gmail.com', 'Data Mining', '2th', 'Stuart Russell', '2025-06-26 08:06:29', '', 44, 'Ramesh Timalsina', '9788110351848'),
(131, 'remesh@gmail.com', 'Data Mining', '1th', 'Korth &amp; Sudarshan', '2025-06-26 08:06:55', '', 44, 'Ramesh Timalsina', '9783860044625'),
(134, 'remesh@gmail.com', 'Principle of Accounting', '1st', 'Ratna Shakya', '2025-06-26 15:18:17', '', 44, 'Ramesh Lama', '9876545364098'),
(137, 'remesh@gmail.com', 'Computer Architecture', '3th', 'George Reynolds', '2025-06-23 15:39:11', '', 44, 'Ramesh Lama', '9784598574799'),
(138, 'remesh@gmail.com', 'Database Management Systems', '1th', 'Andrew S. Tanenbaum', '2025-06-26 15:54:14', '', 44, 'Ramesh Lama', '9789346369836'),
(141, 'ashtmg69@gmail.com', 'Financial Accounting', '3th', 'Philip Kotler', '2025-06-27 02:34:51', '', 45, 'Ashish Tamang', '9782608843360'),
(142, 'ashtmg69@gmail.com', 'Organizational Behavior', '1th', 'David A. Patterson', '2025-06-27 02:34:56', '', 45, 'Ashish Tamang', '9783200374570'),
(143, 'ashtmg69@gmail.com', 'Data Mining', '5th', 'Seymour Lipschutz', '2025-06-27 02:45:54', '', 45, 'Ashish Tamang', '9787237638450'),
(144, 'ashtmg69@gmail.com', 'Financial Accounting', '2th', 'George Reynolds', '2025-06-27 02:45:59', '', 45, 'Ashish Tamang', '9783748660623'),
(145, 'ashtmg69@gmail.com', 'Computer Architecture', '3th', 'George Reynolds', '2025-06-27 02:46:04', '', 45, 'Ashish Tamang', '9784598574799');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(255) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issued`
--

CREATE TABLE `issued` (
  `student_id` int(255) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `book_num` varchar(255) NOT NULL,
  `book_author` varchar(255) NOT NULL,
  `issue_date` varchar(255) NOT NULL,
  `semester` int(11) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `publication` varchar(100) NOT NULL,
  `returned` tinyint(1) DEFAULT NULL,
  `returned_date` varchar(100) NOT NULL,
  `picture` text NOT NULL,
  `due_date` varchar(100) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issued`
--

INSERT INTO `issued` (`student_id`, `book_name`, `book_num`, `book_author`, `issue_date`, `semester`, `faculty`, `publication`, `returned`, `returned_date`, `picture`, `due_date`, `createdAt`) VALUES
(44, 'Project Management', '9785386505125', 'Kenneth Laudon', '2025-06-26', 2, 'BIM', 'Wiley', NULL, '', '683ff24a97329_1749021258.png', '2025-07-02', '2025-06-26 13:15:36'),
(44, 'Organizational Behavior', '9782094833278', 'David A. Patterson', '2025-06-26', 7, 'Bsc.Csit', 'Oxford', NULL, '', '', '2025-06-28', '2025-06-26 13:15:50'),
(44, 'E-Commerce Essentials', '9781606694954', 'Andrew S. Tanenbaum', '2025-06-26', 7, 'BIM', 'McGraw-Hill', NULL, '', '685c2668e1119_1750869608.png', '2025-06-28', '2025-06-26 13:16:01'),
(44, 'Computer Architecture', '9782738915825', 'Korth &amp; Sudarshan', '2025-06-26', 1, 'Bsc.Csit', 'Schaum&#039;s Outlines', NULL, '', '685cb1c6d78e6_1750905286.png', '2025-07-01', '2025-06-26 13:16:13'),
(44, 'Financial Accounting', '9782853101041', 'Kenneth Laudon', '2025-06-26', 2, 'Bsc.Csit', 'Oxford', NULL, '', '683ff2b829033_1749021368.png', '2025-12-23', '2025-06-26 13:18:53'),
(44, 'Human Computer Interaction', '9782969126651', 'Kenneth Laudon', '2025-06-26', 7, 'BIM', 'Oxford', 1, '2025-06-26', '', '2025-12-23', '2025-06-26 13:18:57'),
(44, 'Database Management Systems', '9782920587827', 'Robert Lafore', '2025-06-26', 1, 'Bsc.Csit', 'Cambridge', 1, '2025-06-26', '685ccc72afdb0_1750912114.png', '2025-07-10', '2025-06-26 13:46:42'),
(44, 'Project Management', '9789810260119', 'E. Balagurusamy', '2025-06-26', 1, 'BIM', 'Oxford', 1, '2025-06-26', '683ff63147ea6_1749022257.png', '2025-07-03', '2025-06-26 13:47:46'),
(44, 'Data Mining', '9783860044625', 'Korth &amp; Sudarshan', '2025-06-26', 7, 'BIM', 'Oxford', 1, '2025-06-26', '685ccfadd9ecb_1750912941.png', '2025-12-23', '2025-06-26 13:52:02'),
(44, 'Principle of Accounting', '9876545364098', 'Ratna Shakya', '2025-06-26', 1, 'BBM', 'Gorkhali', 1, '2025-06-26', '683ec71ae206f_1748944666.png', '2025-07-11', '2025-06-26 21:04:27'),
(44, 'Computer Architecture', '9784598574799', 'George Reynolds', '2025-06-26', 6, 'BIM', 'Schaum&#039;s Outlines', 1, '2025-06-26', '683ff55b7bbbe_1749022043.png', '2025-12-23', '2025-06-26 21:28:53'),
(44, 'Database Management Systems', '9789346369836', 'Andrew S. Tanenbaum', '2025-06-26', 8, 'Bsc.Csit', 'Pearson', 1, '2025-06-26', '685ccf323be3b_1750912818.png', '2025-12-23', '2025-06-26 21:40:01'),
(44, 'Data Mining', '9788110351848', 'Stuart Russell', '2025-06-27', 4, 'BCA', 'Tata McGraw-Hill', NULL, '', '685cccf0a8aa9_1750912240.png', '2025-12-24', '2025-06-27 08:19:42'),
(45, 'Organizational Behavior', '9783200374570', 'David A. Patterson', '2025-06-27', 5, 'BCA', 'Wiley', NULL, '', '', '2025-12-24', '2025-06-27 08:20:05'),
(45, 'Financial Accounting', '9782608843360', 'Philip Kotler', '2025-06-27', 7, 'BIM', 'Cambridge', NULL, '', '685d7b84a8f0e_1750956932.png', '2025-12-24', '2025-06-27 08:23:08'),
(45, 'Computer Architecture', '9784598574799', 'George Reynolds', '2025-06-27', 6, 'BIM', 'Schaum&#039;s Outlines', NULL, '', '683ff55b7bbbe_1749022043.png', '2025-12-24', '2025-06-27 08:31:12'),
(45, 'Financial Accounting', '9783748660623', 'George Reynolds', '2025-06-27', 3, 'BBM', 'McGraw-Hill', NULL, '', '683ff2dee72b9_1749021406.png', '2025-12-24', '2025-06-27 08:33:35'),
(45, 'Data Mining', '9787237638450', 'Seymour Lipschutz', '2025-06-27', 5, 'BCA', 'Schaum&#039;s Outlines', NULL, '', '685ccec9dda21_1750912713.png', '2025-12-24', '2025-06-27 08:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `issue_records`
--

CREATE TABLE `issue_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` date NOT NULL DEFAULT curdate(),
  `return_date` date DEFAULT NULL,
  `due_date` date NOT NULL,
  `status` enum('issued','returned','overdue') DEFAULT 'issued',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Cell` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Name`, `Email`, `faculty`, `Username`, `Password`, `Cell`, `Address`) VALUES
(36, 'Joseph Tamang', 'joseph@gmail.com', 'BIM', '', '$2y$10$UyOHqm4w4QYvsICP2qtxoerm/KYcu6wm7uV0to05grGKa9a.4ehCG', '9810101010', 'kalimati'),
(37, 'Nima Tamang', 'nima@gmail.com', 'BBM', '', '$2y$10$2yaF9DOX/dJWUqmWDfcfK.Hqg5223wWeRVfqwvXOoxjxovmz8tKEC', '9864646432', 'Kalimati'),
(40, 'Khem Shrestha', 'khem@gmail.com', 'Bsc.Csit', '', '$2y$10$b1aHHferbuVZ5AVDGHUQWOVkyeIgG/ly1u5RN3PNniEa2OxQ1EIrO', '9811232323', 'Kapan'),
(41, 'Krish Magar', 'Krish@gmail.com', 'BBM', '', '$2y$10$OvjILspr17s3XXd881FSne7TlBmhEFLDqHfoUriiWgnYbi.zaUiMy', '9835352423', 'Freak Street'),
(44, 'Ramesh Lama', 'ashtmg69@gmail.com', 'Bsc.Csit', '', '$2y$10$dGH1YhUE4Sgat0v8J7b1XeUlJ3pga47sW6pF2Z.ozdlCkglmvT/zS', '9834344343', 'Macchapokhari'),
(45, 'Ashish Tamang', 'ashtmg69@gmail.com', 'BBM', '', '$2y$10$mYQhVyst41NcGKBAOOAeTe2yzzrF9elKtXmF3FBS6W3cmuvDNfw1i', '9835354635', 'Bhimsensthan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `book_request`
--
ALTER TABLE `book_request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `issue_records`
--
ALTER TABLE `issue_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_book_id` (`book_id`),
  ADD KEY `idx_issue_date` (`issue_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `book_request`
--
ALTER TABLE `book_request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_records`
--
ALTER TABLE `issue_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issue_records`
--
ALTER TABLE `issue_records`
  ADD CONSTRAINT `issue_records_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
