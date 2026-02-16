-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 03:59 AM
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
-- Database: `employee_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_username`, `password`) VALUES
('root', 'root');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `birthday` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `nid` int(20) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `degree` varchar(100) NOT NULL,
  `pic` text NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `firstName`, `lastName`, `email`, `password`, `birthday`, `gender`, `contact`, `nid`, `address`, `department`, `degree`, `pic`, `cv`, `created_at`) VALUES
(1, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-02-08', 'Male', '', 1235, NULL, 'management', 'CA', 'uploads/1733744165_IMG_20221005_112650.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(2, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-10-16', 'Male', '', 1234, NULL, 'IT', 'bca', 'uploads/kiran.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(3, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-10-16', 'Male', '', 1234, NULL, 'IT', 'bca', 'uploads/kiran.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(4, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-10-16', 'Male', '', 1234, NULL, 'IT', 'bca', 'uploads/1733745923_IMG_20241024_102122~2.jpg', 'uploads/1733745956_Linux-Tutorial-for-Beginners-Youtube-2.pdf', '2024-12-25 08:20:45'),
(5, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-10-16', 'Male', '', 1234, NULL, 'IT', 'bca', 'uploads/kiran.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(6, 'gaurav', 'Adhikari', 'kiranadhikari863@gmail.com', '1234', '2001-06-06', 'Male', '9802145687', 1, 'Kathmandu', 'Finance', 'bca', 'uploads/kiran.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(7, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '1234', '2001-02-06', 'Male', '', 12145, NULL, 'Finance', 'aaa', 'uploads/prem.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(8, 'kk', 'kk', 'kiranadhikari924@gmail.com', '1234', '1990-10-24', 'Male', '', 122, NULL, 'Sales', 'Civil Engineering (Sub engineer)', 'uploads/IMG_20241024_102608.jpg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(9, 'Kiran', 'Adhikari', 'kiranadhikari924@gmail.com', '9823596371', '1998-05-04', 'Male', '', 2147483647, NULL, 'Finance', 'bca', 'uploads/received_794784109416047.jpeg', 'uploads/1 Assignment for 4th Semester.pdf', '2024-12-25 08:20:45'),
(10, 'Deepak', 'Khanal', 'deepak113@gmail.com', '12345678', '2001-06-12', 'Male', '9866437014', 2147483647, 'Buddhabhumi 02 gorusinge kapilvastu', 'IT', 'Civil Engineering (Sub engineer)', 'uploads/dpk.jpg', 'uploads/scripting language.pdf', '2024-12-25 08:20:45'),
(11, 'deepak', 'khanal', 'root@gmail.com', '$2y$10$aqc3X1r7uLLD4Jhofiuai.g5cFPA4uQSYfrZltvbckL3RGLiv/QWi', '2001-01-05', 'male', '9823564589', 2147483647, 'Kathmandu', 'management', 'bca', 'uploads/1740655591_Screenshot_20250221-131728.png', 'uploads/1740655591_cover page (1).pdf', '2025-02-27 06:41:31');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Cancelled') DEFAULT 'Pending',
  `leave_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `employee_id`, `name`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `leave_type`) VALUES
(1, 1, 'kisor', '2024-11-01', '2024-11-05', 0, 'sick leave', 'Cancelled', ''),
(2, 2, 'kisor Adhikari', '2024-10-31', '2024-11-10', 11, 'sick', 'Approved', ''),
(3, 2, 'kisor Adhikari', '2024-11-01', '2024-11-06', 6, 'sick ', 'Approved', ''),
(4, 2, 'kisor Adhikari', '2024-11-01', '2024-11-06', 6, 'sick ', 'Approved', ''),
(5, 2, 'kisor Adhikari', '2024-11-01', '2024-11-06', 6, 'sick ', 'Approved', ''),
(6, 2, 'kisor Adhikari', '2024-11-20', '2024-11-30', 11, 'sss', 'Approved', ''),
(7, 2, 'kisor Adhikari', '2024-11-20', '2024-11-30', 11, 'sss', 'Approved', ''),
(8, 2, 'kisor Adhikari', '2024-11-20', '2024-11-30', 11, 'sss', 'Approved', ''),
(9, 2, 'kisor Adhikari', '2024-11-22', '2024-11-30', 9, 'ss', 'Approved', ''),
(10, 2, 'kisor Adhikari', '2024-11-22', '2024-11-30', 9, 'ss', 'Cancelled', ''),
(11, 2, 'kisor Adhikari', '2024-11-02', '2024-11-30', 29, 'cxxz', 'Approved', ''),
(12, 2, 'kisor Adhikari', '2024-11-02', '2024-11-30', 29, 'ddc', 'Cancelled', ''),
(13, 2, 'kisor Adhikari', '2024-11-02', '2024-11-20', 19, 'holiday', 'Approved', ''),
(14, 2, 'kisor Adhikari', '2024-11-20', '2024-11-22', 3, 'sick', 'Approved', ''),
(15, 101, 'kiran Adhikari ', '2024-11-01', '2024-11-21', 21, 'holiday', 'Approved', ''),
(16, 12, 'Prem shah', '2024-11-20', '2024-11-21', 2, 'family tour', 'Approved', ''),
(17, 12, 'Prem shah', '2024-11-20', '2024-11-19', 0, 'fj', 'Approved', ''),
(18, 1, 'Kishor Adhikari', '2024-12-27', '2024-12-28', 2, 'sick', 'Cancelled', '');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `employee_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `submission_date` date DEFAULT NULL,
  `mark` int(11) DEFAULT NULL,
  `status` enum('Pending','Submitted','Reviewed') DEFAULT 'Pending',
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`employee_id`, `project_name`, `due_date`, `submission_date`, `mark`, `status`, `file_path`) VALUES
(1, 'billing system', '2024-12-13', '2025-03-06', 10, 'Submitted', 'uploads/675c5d410ea9b_Linux-Tutorial-for-Beginners-Youtube-2.pdf'),
(10, 'marketing', '2024-12-10', NULL, 9, 'Reviewed', 'uploads/project/675434ea19be9-scripting language.pdf'),
(10, 'marketing', '2024-12-08', NULL, 9, 'Reviewed', ''),
(1, 'sales', '2024-12-17', '2025-03-06', 9, 'Reviewed', ''),
(1, 'project1 ', '2024-12-20', '2025-03-06', 8, 'Submitted', ''),
(1, 'project2', '2024-12-18', '2025-03-06', 9, 'Submitted', ''),
(1, 'marketing', '2025-03-20', '2025-03-06', 10, 'Submitted', ''),
(1, 'marketing', '2025-03-13', '2025-03-06', 10, 'Submitted', ''),
(1, 'sales', '2025-03-22', '2025-03-06', 9, 'Reviewed', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD UNIQUE KEY `username` (`admin_username`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD KEY `projects_ibfk_1` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
