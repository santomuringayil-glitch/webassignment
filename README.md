# CampusVote — Secure & Transparent Online Voting System

> **A modern, responsive, and cryptographically verifiable web application engineered with PHP, MySQL, HTML5, and CSS3 for student government elections, class representatives, and university community polls.**

---

## 📌 Project Overview

**CampusVote** solves the core challenges of conventional campus elections (long queues, high logistics and paper costs, manual counting errors, and lack of mathematical auditability). 

It delivers an end-to-end electronic voting platform that guarantees:
1. **Absolute Secret Ballot Privacy:** Voters' identities are decoupled from their candidate selections using one-way salted SHA-256 hashes.
2. **Strict Single-Vote Integrity:** Composite unique database constraints and ACID transactions eliminate duplicate voting.
3. **Verifiable Audit Receipts:** Each student receives an authentic cryptographic receipt token that can be independently audited.
4. **Instant Real-Time Results:** Vote tallies compile automatically in real time with visual animated progress bars and winner indicators.
5. **Role-Based Administration:** Election commissioners can schedule voting windows, configure contested offices, nominate candidates, and monitor immutable security audit logs.

---

## 🗂️ Project Directory Structure

```text
webassi/
├── config/
│   └── db.php                  # PDO database connection & session configuration
├── includes/
│   ├── auth.php                # Authentication, CSRF guard, and audit helpers
│   ├── header.php              # Shared HTML head, meta tags, stylesheet links
│   ├── navbar.php              # Responsive navigation bar (Voter / Admin / Guest)
│   └── footer.php              # Shared HTML footer & scripts
├── assets/
│   ├── css/
│   │   └── style.css           # Modern, custom responsive CSS3 stylesheet
│   ├── js/
│   │   └── script.js           # Client-side validation, card selection, search
│   └── images/                 # Generated diagrams and UI screenshots
│       ├── architecture_diagram.png
│       ├── mysql_screenshot.png
│       ├── ui_ballot_screenshot.png
│       └── ui_results_screenshot.png
├── admin/
│   ├── index.php               # Admin overview dashboard & audit stream
│   ├── elections.php           # CRUD election scheduling & status management
│   ├── candidates.php          # Candidate nominations & office positions
│   └── voters.php              # Student voter registry & status toggling
├── index.php                   # Public landing portal & active election cards
├── login.php                   # Secure authentication portal (Voter & Admin)
├── register.php                # Student voter registration with ID verification
├── logout.php                  # Secure session termination
├── dashboard.php               # Student voter dashboard & voting history
├── vote.php                    # Official digital secret ballot voting engine
├── receipt.php                 # Cryptographic verification slip & printable receipt
├── results.php                 # Live real-time election results & candidate analytics
├── database.sql                # Complete MySQL schema & initial seed demo data
├── presentation/
│   └── index.html              # Interactive browser-based presentation deck
├── presentation.pptx           # 12-slide Widescreen PowerPoint presentation
├── generate_presentation.py    # Python script to build presentation.pptx
├── generate_assets.py          # Python script to generate diagrams & mockups
├── VIDEO_RECORDING_GUIDE.md    # Script & instructions for Google Classroom video
└── README.md                   # Project documentation & setup manual
```

---

## 🚀 Setup & Installation Instructions

### 1. Prerequisites
- **Web Server:** Apache (via XAMPP, WAMP, Laragon, or standalone PHP server)
- **PHP Version:** PHP 7.4+ or PHP 8.x (with `pdo_mysql` and `session` extensions enabled)
- **Database:** MySQL Server 5.7+ or 8.0+ / MariaDB 10.x+

### 2. Database Setup
1. Open your MySQL client (**phpMyAdmin** or **MySQL Workbench**).
2. Create a new database named `online_voting_db` (or import directly):
   ```sql
   CREATE DATABASE online_voting_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the file [`database.sql`](file:///c:/Users/Lenovo/Downloads/webassi/database.sql):
   - In phpMyAdmin: Select `online_voting_db` &rarr; click **Import** &rarr; choose `database.sql` &rarr; click **Go**.
   - Or via MySQL command line:
     ```bash
     mysql -u root -p online_voting_db < database.sql
     ```

### 3. Database Connection Configuration
Open [`config/db.php`](file:///c:/Users/Lenovo/Downloads/webassi/config/db.php) and adjust credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password if configured
define('DB_NAME', 'online_voting_db');
```

### 4. Running the Web Application
- If using **XAMPP / WAMP**:
  Place this `webassi` folder in your `htdocs` (or `www`) directory and open:
  ```text
  http://localhost/webassi/
  ```
- Or using PHP's built-in development server (if PHP is installed in your terminal):
  ```bash
  cd c:\Users\Lenovo\Downloads\webassi
  php -S localhost:8000
  ```
  Then browse to `http://localhost:8000`.

---

## 🔑 Default Demo User Accounts

| Role | Username / Student ID | Email | Password |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `ADMIN001` | `admin@campusvote.org` | `password123` |
| **Student Voter 1** | `STU202601` | `alex.morgan@campus.edu` | `password123` |
| **Student Voter 2** | `STU202602` | `sarah.chen@campus.edu` | `password123` |
| **Student Voter 3** | `STU202603` | `marcus.j@campus.edu` | `password123` |
| **Student Voter 4** | `STU202604` | `emily.davis@campus.edu` | `password123` |

---

## 🛡️ Security Architecture & Highlights

1. **SQL Injection Defense:** All queries utilize PHP PDO prepared statements with strict parameter binding. No raw user input is ever concatenated into SQL strings.
2. **Cross-Site Request Forgery (CSRF):** Every form includes a randomized 256-bit cryptographically secure token checked on submission.
3. **Password Security:** All voter and admin passwords are encrypted with `password_hash()` using the **BCRYPT** algorithm with adaptive salt.
4. **Secret Ballot Anonymization:**
   ```php
   $voterHash = hash('sha256', $user['voter_id'] . '_' . $electionId . '_' . VOTE_SALT);
   ```
   Votes are linked only to `$voterHash`, completely detaching the voter's real identity from their candidate selection.
5. **Duplicate Voting Prevention:**
   A database-level composite unique constraint `UNIQUE KEY (voter_hash, position_id)` guarantees each voter can only submit one vote per position.
6. **ACID Transactions:**
   Ballot submission is wrapped in `$db->beginTransaction()`, `$db->commit()`, and `$db->rollBack()` to prevent half-saved states during network interruptions.
7. **Tamper-Evident Audit Logging:**
   Every login, registration, ballot sealing, and administrative change is permanently recorded in the `audit_logs` table with client IP addresses.

---

## 📊 Presentation Deliverables

This project includes both digital and Microsoft PowerPoint presentation formats:

1. **PowerPoint Presentation File:**  
   [`presentation.pptx`](file:///c:/Users/Lenovo/Downloads/webassi/presentation.pptx)  
   12 widescreen 16:9 slides complete with diagrams, code snippets, database screenshots, and slide speaker notes.

2. **Interactive Browser Slide Deck:**  
   [`presentation/index.html`](file:///c:/Users/Lenovo/Downloads/webassi/presentation/index.html)  
   Open directly in any modern browser. Supports keyboard navigation (`Left` / `Right` arrows), presenter speaker notes drawer (`S` key), and fullscreen mode (`F` key).

3. **Slide Structure:**
   - **Slide 1:** Title of the Project
   - **Slide 2:** Introduction & Problem Statement
   - **Slide 3:** Objectives of the Project
   - **Slide 4:** Key Features & Capabilities
   - **Slide 5:** System Design: 3-Tier Architecture & Diagram
   - **Slide 6:** Database Design & Relational Schema
   - **Slide 7:** HTML5 & CSS3 Front-End Code Implementation
   - **Slide 8:** PHP Back-End & ACID Transaction Security
   - **Slide 9:** PHP with MySQL Implementation & Database Evidence
   - **Slide 10:** Application Screenshots & Live Demonstration
   - **Slide 11:** Conclusion & Future Roadmap
   - **Slide 12:** Thank You & Q&A

---

## 📹 Video Recording & Google Classroom Submission

Refer to [`VIDEO_RECORDING_GUIDE.md`](file:///c:/Users/Lenovo/Downloads/webassi/VIDEO_RECORDING_GUIDE.md) for:
- Free recording tools for Windows (`Win + G` Game Bar, Clipchamp, OBS Studio).
- Word-for-word presentation script with recommended timestamps.
- Live web app demonstration script.
- Step-by-step instructions on attaching and submitting your video in Google Classroom.
