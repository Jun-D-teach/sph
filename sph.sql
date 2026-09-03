-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2026 at 04:36 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sph`
--

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `mapel` varchar(60) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `smt` varchar(10) DEFAULT NULL,
  `tanggal` varchar(20) DEFAULT NULL,
  `bentuk` varchar(20) DEFAULT NULL,
  `jumlah_soal` int(11) DEFAULT NULL,
  `kkm` int(11) DEFAULT NULL,
  `skor_per_soal` decimal(6,2) DEFAULT NULL,
  `kunci` varchar(120) DEFAULT NULL,
  `guru` varchar(120) DEFAULT NULL,
  `nip_guru` varchar(60) DEFAULT NULL,
  `wali` varchar(120) DEFAULT NULL,
  `nip_wali` varchar(60) DEFAULT NULL,
  `mode` varchar(10) DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `mapel`, `kelas`, `smt`, `tanggal`, `bentuk`, `jumlah_soal`, `kkm`, `skor_per_soal`, `kunci`, `guru`, `nip_guru`, `wali`, `nip_wali`, `mode`, `dibuat_oleh`) VALUES
(6, 'Informatika', 'XI.1', 'Ganjil', '2026-08-18', 'Pilihan Ganda', 10, 85, '10.00', 'DDDDDDDDDD', 'Junaidi', '198107252009011013', 'Junaidi', '198107252009011013', 'CBT', 2);

-- --------------------------------------------------------

--
-- Table structure for table `indikator`
--

CREATE TABLE `indikator` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `no_soal` int(11) DEFAULT NULL,
  `indikator` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `indikator`
--

INSERT INTO `indikator` (`id`, `exam_id`, `no_soal`, `indikator`) VALUES
(4, 6, 1, 'menari'),
(5, 6, 2, 'cuci piring'),
(6, 6, 3, 'solat lima waktu');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(20) DEFAULT NULL,
  `wali` varchar(120) DEFAULT NULL,
  `nip_wali` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `wali`, `nip_wali`) VALUES
(1, 'XI.1', 'Junaidi', '198107252009011013');

-- --------------------------------------------------------

--
-- Table structure for table `keterampilan`
--

CREATE TABLE `keterampilan` (
  `exam_id` int(11) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `p1` int(11) DEFAULT NULL,
  `p2` int(11) DEFAULT NULL,
  `p3` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `kkm_range`
--

CREATE TABLE `kkm_range` (
  `id` int(11) NOT NULL,
  `min` int(11) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `predikat` varchar(5) DEFAULT NULL,
  `keterangan` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kkm_range`
--

INSERT INTO `kkm_range` (`id`, `min`, `max`, `predikat`, `keterangan`) VALUES
(1, 93, 100, 'A', 'sangat baik'),
(2, 85, 92, 'B', 'baik'),
(3, 78, 84, 'C', 'cukup'),
(4, 0, 77, 'D', 'kurang');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama` varchar(120) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `jawaban` varchar(120) DEFAULT NULL,
  `detail` varchar(120) DEFAULT NULL,
  `benar` int(11) DEFAULT NULL,
  `skor` int(11) DEFAULT NULL,
  `status` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `exam_id`, `nisn`, `nama`, `kelas`, `jawaban`, `detail`, `benar`, `skor`, `status`) VALUES
(145, 6, '3109820038', 'ABQARY ALZHAFIR', 'XI.1', 'DBAABCCBAC', '1000000000', 1, 10, 'REMEDIAL'),
(146, 6, '3100257495', 'AIMEE PUTRI NAZILA', 'XI.1', 'DAACBCAACE', '1000000000', 1, 10, 'REMEDIAL'),
(147, 6, '0109672923', 'ALANNA MAYYASAH KAMILAH', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(148, 6, '0102188281', 'ANISAH MAHYA ZULFA', 'XI.1', 'DCAABCCECE', '1000000000', 1, 10, 'REMEDIAL'),
(149, 6, '0106141743', 'ARDELIA ARIQAH', 'XI.1', 'DAACBCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(150, 6, '0108404553', 'ATHIRAH QUEEN DALKUCI', 'XI.1', 'DCACACCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(151, 6, '3108764013', 'ATHIRAH ZAHRA IZZATI', 'XI.1', 'DEAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(152, 6, '3102131830', 'AZ ZAHRA MUTIARA', 'XI.1', 'DAACBCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(153, 6, '3103395530', 'DZAKI MUHAMMAD AL\'FATHIR', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(154, 6, '3109800627', 'FAIHA SEPTALITA IRANTI', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(155, 6, '0119632763', 'FARIZA SYAKIRA', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(156, 6, '0103737038', 'FATHEEYA HANIN DITA', 'XI.1', 'DBAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(157, 6, '0105533319', 'GHANIYAH KAYYISAH AULIA', 'XI.1', 'DAAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(158, 6, '3104006974', 'KAIZEN MUHAMMAD UMAR', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(159, 6, '0109517412', 'KAYLA RABBEECA FAHMI', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(160, 6, '3103809115', 'KAYYASAH PUTRI QANITAH', 'XI.1', 'DCAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(161, 6, '0104419142', 'KEYSHA NAJWA', 'XI.1', 'DEBAACCACE', '1000000000', 1, 10, 'REMEDIAL'),
(162, 6, '0106199752', 'KHANSA AUDREY SYIFA', 'XI.1', 'DBAABCCDCE', '1000000100', 2, 20, 'REMEDIAL'),
(163, 6, '0098325893', 'M. AL FAUZAN', 'XI.1', 'DAABBCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(164, 6, '0104116097', 'M. ALMER ARRAFA', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(165, 6, '3105872564', 'M. ARKAN AFALLA', 'XI.1', 'DCAABBCDCE', '1000000100', 2, 20, 'REMEDIAL'),
(166, 6, '0108432262', 'M. FARIDS ALFARIZI', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(167, 6, '0103400371', 'MASAYU ASTIAISYAH', 'XI.1', 'DAAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(168, 6, '3102838113', 'MUHAMMAD BAGAS SEPTARIZA', 'XI.1', 'DAAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(169, 6, '0117985468', 'MUHAMMAD FADLI PRATAMA', 'XI.1', 'DEAABCCDCE', '1000000100', 2, 20, 'REMEDIAL'),
(170, 6, '3108861446', 'NABILA PUTRI PRASETYO', 'XI.1', 'DCAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(171, 6, '0104094802', 'NAYLA ASYIFA', 'XI.1', 'DBAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(172, 6, '3107573675', 'NYS. ARIBAH KHALIDAH', 'XI.1', 'DAAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(173, 6, '3102858090', 'RAISYA TSAQIEF ROSYADA', 'XI.1', 'DBAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(174, 6, '0107488737', 'RASYA ANANDYA ALMAJID', 'XI.1', 'DCAABCCDCE', '1000000100', 2, 20, 'REMEDIAL'),
(175, 6, '0107996884', 'RIFDAH SHAFANA', 'XI.1', 'DAAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(176, 6, '3100532488', 'SABRINA SALSABILAH', 'XI.1', 'DCAABCCBCE', '1000000000', 1, 10, 'REMEDIAL'),
(177, 6, '0109374094', 'VITO DZAKWAN WINATA SUWARDANA', 'XI.1', 'DBAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(178, 6, '3106598104', 'ZAHRA AFIFAH ROMZAL', 'XI.1', 'DCAABCCACE', '1000000000', 1, 10, 'REMEDIAL'),
(179, 6, '3100182406', 'ZAHRA KHAIRUNNISA HASYIMIYYAH SIREGAR', 'XI.1', 'DEAABBCDCE', '1000000100', 2, 20, 'REMEDIAL'),
(180, 6, '3108201844', 'ZALIKHA ARIEF', 'XI.1', 'DAAABCCBCE', '1000000000', 1, 10, 'REMEDIAL');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `sekolah` varchar(120) DEFAULT NULL,
  `ta` varchar(20) DEFAULT NULL,
  `smt` varchar(10) DEFAULT NULL,
  `kkm` int(11) DEFAULT 75,
  `kepala` varchar(120) DEFAULT NULL,
  `nip_kepala` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `sekolah`, `ta`, `smt`, `kkm`, `kepala`, `nip_kepala`) VALUES
(1, 'MAN 2 PALEMBANG', '2026/2027', 'Ganjil', 85, 'Yusri Erlini, M.Pd', '197302031998032002');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(120) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`nisn`, `nama`, `kelas`) VALUES
('0098325893', 'M. AL FAUZAN', 'XI.1'),
('0102188281', 'ANISAH MAHYA ZULFA', 'XI.1'),
('0103400371', 'MASAYU ASTIAISYAH', 'XI.1'),
('0103737038', 'FATHEEYA HANIN DITA', 'XI.1'),
('0104094802', 'NAYLA ASYIFA', 'XI.1'),
('0104116097', 'M. ALMER ARRAFA', 'XI.1'),
('0104419142', 'KEYSHA NAJWA', 'XI.1'),
('0105533319', 'GHANIYAH KAYYISAH AULIA', 'XI.1'),
('0106141743', 'ARDELIA ARIQAH', 'XI.1'),
('0106199752', 'KHANSA AUDREY SYIFA', 'XI.1'),
('0107488737', 'RASYA ANANDYA ALMAJID', 'XI.1'),
('0107996884', 'RIFDAH SHAFANA', 'XI.1'),
('0108404553', 'ATHIRAH QUEEN DALKUCI', 'XI.1'),
('0108432262', 'M. FARIDS ALFARIZI', 'XI.1'),
('0109374094', 'VITO DZAKWAN WINATA SUWARDANA', 'XI.1'),
('0109517412', 'KAYLA RABBEECA FAHMI', 'XI.1'),
('0109672923', 'ALANNA MAYYASAH KAMILAH', 'XI.1'),
('0117985468', 'MUHAMMAD FADLI PRATAMA', 'XI.1'),
('0119632763', 'FARIZA SYAKIRA', 'XI.1'),
('3100182406', 'ZAHRA KHAIRUNNISA HASYIMIYYAH SIREGAR', 'XI.1'),
('3100257495', 'AIMEE PUTRI NAZILA', 'XI.1'),
('3100532488', 'SABRINA SALSABILAH', 'XI.1'),
('3102131830', 'AZ ZAHRA MUTIARA', 'XI.1'),
('3102838113', 'MUHAMMAD BAGAS SEPTARIZA', 'XI.1'),
('3102858090', 'RAISYA TSAQIEF ROSYADA', 'XI.1'),
('3103395530', 'DZAKI MUHAMMAD AL\'FATHIR', 'XI.1'),
('3103809115', 'KAYYASAH PUTRI QANITAH', 'XI.1'),
('3104006974', 'KAIZEN MUHAMMAD UMAR', 'XI.1'),
('3105872564', 'M. ARKAN AFALLA', 'XI.1'),
('3106598104', 'ZAHRA AFIFAH ROMZAL', 'XI.1'),
('3107573675', 'NYS. ARIBAH KHALIDAH', 'XI.1'),
('3108201844', 'ZALIKHA ARIEF', 'XI.1'),
('3108764013', 'ATHIRAH ZAHRA IZZATI', 'XI.1'),
('3108861446', 'NABILA PUTRI PRASETYO', 'XI.1'),
('3109800627', 'FAIHA SEPTALITA IRANTI', 'XI.1'),
('3109820038', 'ABQARY ALZHAFIR', 'XI.1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  `pass_plain` varchar(60) DEFAULT NULL,
  `nama` varchar(120) DEFAULT NULL,
  `nip` varchar(60) DEFAULT NULL,
  `mapel` varchar(60) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'guru'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass_hash`, `pass_plain`, `nama`, `nip`, `mapel`, `role`) VALUES
(1, 'admin', '$2y$10$Zbwq9bCeG8Fs0E2aEHR6ZelW/E3QWuM9OHA3QFqa0N/t.AmRxYdDO', 'admin123', 'Administrator', NULL, NULL, 'admin'),
(2, '198107252009011013', '$2y$10$XLvDeREwNmSF86G6if3K4.nria0AgsjKJ5BGwOnDVIkk8RsK02YX2', 'guru123', 'Junaidi', '198107252009011013', 'Informatika', 'guru');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `indikator`
--
ALTER TABLE `indikator`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq` (`exam_id`,`no_soal`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`);

--
-- Indexes for table `keterampilan`
--
ALTER TABLE `keterampilan`
  ADD PRIMARY KEY (`exam_id`,`nisn`);

--
-- Indexes for table `kkm_range`
--
ALTER TABLE `kkm_range`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq` (`exam_id`,`nisn`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`nisn`),
  ADD KEY `kelas` (`kelas`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `indikator`
--
ALTER TABLE `indikator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kkm_range`
--
ALTER TABLE `kkm_range`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
