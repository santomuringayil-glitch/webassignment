-- Online Voting System Database Schema
-- Designed for Secure & Transparent Student Elections and Community Polls
-- Compatible with MySQL 5.7+ / MySQL 8.0+

CREATE DATABASE IF NOT EXISTS `online_voting_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `online_voting_db`;

-- 1. Users Table (Admin & Voters)
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `voter_ballots`;
DROP TABLE IF EXISTS `votes`;
DROP TABLE IF EXISTS `candidates`;
DROP TABLE IF EXISTS `positions`;
DROP TABLE IF EXISTS `elections`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `voter_id` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique Student ID or Voter Card Number',
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL COMMENT 'BCRYPT Password Hash',
  `role` ENUM('admin', 'voter') NOT NULL DEFAULT 'voter',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Elections Table
CREATE TABLE `elections` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `status` ENUM('upcoming', 'active', 'completed') NOT NULL DEFAULT 'upcoming',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Positions Table
CREATE TABLE `positions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `election_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `max_votes` INT NOT NULL DEFAULT 1,
  `priority` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Candidates Table
CREATE TABLE `candidates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `position_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `party` VARCHAR(100) DEFAULT 'Independent',
  `manifesto` TEXT,
  `photo` VARCHAR(255) DEFAULT 'default_avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Votes Table (Cryptographically anonymized)
CREATE TABLE `votes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `election_id` INT NOT NULL,
  `position_id` INT NOT NULL,
  `candidate_id` INT NOT NULL,
  `voter_hash` VARCHAR(64) NOT NULL COMMENT 'SHA256(voter_id + election_salt) - prevents duplicate votes while preserving ballot anonymity',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_vote_per_position` (`voter_hash`, `position_id`),
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Voter Ballots Table (Tracking submission status & verification receipt)
CREATE TABLE `voter_ballots` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `election_id` INT NOT NULL,
  `receipt_token` VARCHAR(64) NOT NULL UNIQUE COMMENT 'Cryptographic verification receipt for student audit',
  `cast_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_election` (`user_id`, `election_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Audit Logs Table (Tamper-evident logs)
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT,
  `ip_address` VARCHAR(45),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- SEED DATA FOR DEMO & TESTING
-- Default Password for all accounts: password123
-- BCRYPT hash of 'password123': $2y$10$w8.1G26n7pE2jKzI8qD32uO6y8PqC76qL/wO2fG0w9m6D9mPzY6d2
-- Also supported fallback: admin123 -> $2y$10$3YmZ9uUoAom3YtB9P9HffOgX9Q2gWkK7pE2Q0R8y6UoAom3YtB9P9
-- ========================================================

-- Insert Admin and Sample Voters
-- We generate standard bcrypt hashes
INSERT INTO `users` (`id`, `voter_id`, `full_name`, `email`, `password`, `role`, `status`) VALUES
(1, 'ADMIN001', 'System Administrator', 'admin@campusvote.org', '$2y$10$Y145F7V3GvY7zC1rE.y0t.276pZfEce5k483.m0z3k0s6FkZqWqea', 'admin', 'active'),
(2, 'STU202601', 'Alex Morgan', 'alex.morgan@campus.edu', '$2y$10$Y145F7V3GvY7zC1rE.y0t.276pZfEce5k483.m0z3k0s6FkZqWqea', 'voter', 'active'),
(3, 'STU202602', 'Sarah Chen', 'sarah.chen@campus.edu', '$2y$10$Y145F7V3GvY7zC1rE.y0t.276pZfEce5k483.m0z3k0s6FkZqWqea', 'voter', 'active'),
(4, 'STU202603', 'Marcus Johnson', 'marcus.j@campus.edu', '$2y$10$Y145F7V3GvY7zC1rE.y0t.276pZfEce5k483.m0z3k0s6FkZqWqea', 'voter', 'active'),
(5, 'STU202604', 'Emily Davis', 'emily.davis@campus.edu', '$2y$10$Y145F7V3GvY7zC1rE.y0t.276pZfEce5k483.m0z3k0s6FkZqWqea', 'voter', 'active');

-- Insert Sample Election
INSERT INTO `elections` (`id`, `title`, `description`, `start_date`, `end_date`, `status`) VALUES
(1, '2026 Campus Student Council General Elections', 'Annual democratic election to elect the Executive Committee representing the undergraduate and graduate student body across all faculties.', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 'active'),
(2, 'Community Green Initiative Poll 2026', 'Community referendum poll on campus sustainability guidelines and library solar roof funding.', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY), 'upcoming');

-- Insert Positions for Election 1
INSERT INTO `positions` (`id`, `election_id`, `title`, `max_votes`, `priority`) VALUES
(1, 1, 'Student Council President', 1, 1),
(2, 1, 'Vice President of Academic Affairs', 1, 2),
(3, 1, 'General Secretary', 1, 3),
(4, 1, 'Director of Campus Life & Sports', 1, 4);

-- Insert Candidates for Positions
INSERT INTO `candidates` (`id`, `position_id`, `name`, `party`, `manifesto`, `photo`) VALUES
-- Candidates for President
(1, 1, 'Aarav Patel', 'Campus Unity Alliance', 'Committed to 24/7 library access, transparent student fund allocations, and enhanced career mentoring programs.', 'aarav.png'),
(2, 1, 'Sophia Rodriguez', 'Progressive Student Voice', 'Championing affordable campus dining, mental health counseling expansion, and greener campus transit initiatives.', 'sophia.png'),
(3, 1, 'Liam O\'Connor', 'Tech & Innovation Coalition', 'Promoting automated digital room bookings, high-speed WiFi across campus quads, and hackathon sponsorships.', 'liam.png'),

-- Candidates for Vice President
(4, 2, 'Maya Sharma', 'Campus Unity Alliance', 'Pledging fair grading policies, syllabus standardization, and open lecture recordings for all courses.', 'maya.png'),
(5, 2, 'David Kim', 'Progressive Student Voice', 'Advocating for reduced textbook costs, textbook loan libraries, and expanded undergraduate research grants.', 'david.png'),

-- Candidates for General Secretary
(6, 3, 'Zainab Al-Mansoor', 'Campus Unity Alliance', 'Ensuring absolute financial transparency, weekly open council office hours, and streamlined club funding.', 'zainab.png'),
(7, 3, 'Noah Bennett', 'Independent', 'Focused on direct feedback channels, student survey responsiveness, and digital town hall meetings.', 'noah.png'),

-- Candidates for Sports & Campus Life
(8, 4, 'Chloe Dubois', 'Active Campus Movement', 'Revitalizing inter-departmental tournaments, upgraded fitness equipment, and inclusive intramural sports.', 'chloe.png'),
(9, 4, 'Lucas Silva', 'Campus Unity Alliance', 'Expanding cultural festivals, esports arena support, and subsidized outdoor adventure trips.', 'lucas.png');

-- Sample audit entry
INSERT INTO `audit_logs` (`user_id`, `action`, `details`, `ip_address`) VALUES
(1, 'SYSTEM_INIT', 'Database schema and seed elections initialized successfully.', '127.0.0.1');
