# Complete Video Recording & Google Classroom Submission Guide
## Project: Online Voting System (PHP & MySQL)

This guide provides everything you need to record your project presentation, demo the live web application, and submit the video to **Google Classroom**.

---

## ⏱️ Video Structure & Recommended Timing (5 – 8 Minutes)

| Segment | Content Covered | Recommended Duration |
| :--- | :--- | :--- |
| **Part 1** | **Project Presentation Slides** (Title, Intro, Objective, Features, Design, Code, DB Screenshot) | ~3 - 4 Minutes |
| **Part 2** | **Live Web Application Demo** (Login, Digital Ballot, Sealed Receipt, Live Results, Admin Panel) | ~2 - 3 Minutes |
| **Part 3** | **Conclusion & Q&A Wrap-Up** | ~30 - 45 Seconds |
| **Total** | | **~6 - 8 Minutes** |

---

## 🛠️ Step 1: Recommended Screen Recording Tools (100% Free on Windows)

You can record your screen and microphone using any of these easy options:

### Option A: Windows Game Bar (Built right into Windows 10 & 11 - No installation needed)
1. Open your presentation (either `presentation.pptx` in PowerPoint or `presentation/index.html` in your browser).
2. Press **`Win + G`** on your keyboard (or press **`Win + Alt + R`** to start recording immediately).
3. Ensure the microphone toggle is **ON** (so your voice is recorded).
4. Present the slides, then switch to your browser to show the web application.
5. Press **`Win + Alt + R`** to stop recording.
6. The video MP4 will be saved automatically in:  
   `C:\Users\<YourUsername>\Videos\Captures\`

### Option B: Microsoft Clipchamp (Built into Windows 11)
1. Open the Start menu and type **Clipchamp**.
2. Click **"Record something"** &rarr; **"Screen & camera"** or **"Screen only"**.
3. Select your browser or full screen.
4. Record and export the video as **1080p MP4**.

### Option C: OBS Studio (Open-Source, High Quality)
1. Download from [obsproject.com](https://obsproject.com).
2. Add a **"Display Capture"** source and your **"Audio Input Capture"** (Microphone).
3. Click **Start Recording**, present your slides and demo, then click **Stop Recording**.

---

## 🎙️ Step 2: Slide-by-Slide Word-for-Word Speaking Script

Open `presentation/index.html` in your web browser (or open `presentation.pptx` in PowerPoint). Read or adapt this script:

### Slide 1: Title Slide (0:00 – 0:30)
> *"Hello everyone and welcome. Today, I am presenting our project: **Online Voting System** — a secure and transparent electronic voting platform designed for student elections and community polls.  
> This system is developed using **PHP and MySQL** on the backend, with a modern, responsive **HTML5 and CSS3** frontend. It ensures strict ballot secrecy, eliminates duplicate voting, and provides cryptographic audit receipts."*

### Slide 2: Introduction & Problem Statement (0:30 – 1:00)
> *"In student council elections and community polling, traditional paper-based voting suffers from major drawbacks: long queues, low voter participation among commuter students, printing costs, and human counting errors during manual tallies. Moreover, physical ballots can be damaged or lost.  
> Our solution, **CampusVote**, provides a 24/7 web-based portal where registered students can vote from anywhere in seconds, while results are tabulated automatically and transparently."*

### Slide 3: Objectives of the Project (1:00 – 1:40)
> *"The project was designed with five core objectives:  
> 1. **Robust Security:** Preventing SQL injection using PDO prepared statements and stopping CSRF attacks with token validation.  
> 2. **Secret Ballot Anonymity:** Decoupling the student's identity from their chosen candidates using one-way cryptographic SHA-256 hashes.  
> 3. **Single-Vote Integrity:** Guaranteeing at the database level that each student votes exactly once.  
> 4. **Verifiable Audit Receipts:** Giving each voter a digital confirmation token as proof of participation.  
> 5. **Clean Responsive UI:** Providing an intuitive experience across mobile devices and laptops without any voter training."*

### Slide 4: Key Features (1:40 – 2:20)
> *"Key highlights of our platform include:  
> • A dedicated **Student Voter Portal** with registration and ballot tracking.  
> • An interactive **Digital Ballot** where offices are neatly grouped, complete with candidate photos and manifestos.  
> • Instant **Verifiable Cryptographic Receipts** issued upon ballot submission.  
> • **Real-time Live Tallying** with animated progress bars and winner indicators.  
> • A comprehensive **Admin Control Center** to schedule elections, configure positions, and manage candidates.  
> • A **Tamper-Evident Security Audit Log** capturing all critical actions with IP addresses and timestamps."*

### Slide 5: System Design & Architecture (2:20 – 3:00)
> *"Here is the 3-Tier Architecture of our system.  
> In Tier 1, the **Presentation Layer** is built with semantic HTML5, responsive CSS3 Grid, and vanilla JavaScript for client-side validation.  
> In Tier 2, the **Application Logic Layer** uses PHP 8.x to handle authentication, enforce role-based access control, and manage the secret ballot anonymizer.  
> In Tier 3, the **Persistence Layer** is powered by MySQL 8.0 with InnoDB, utilizing ACID transactions and composite unique keys."*

### Slide 6: Database Design & Schema (3:00 – 3:40)
> *"Our database, `online_voting_db`, is normalized into seven key tables: `users`, `elections`, `positions`, `candidates`, `votes`, `voter_ballots`, and `audit_logs`.  
> A critical design innovation is in the `votes` table: we store candidate choices under a `voter_hash` rather than the student's direct ID. A composite UNIQUE constraint on `(voter_hash, position_id)` guarantees mathematical single-vote enforcement while preserving ballot secrecy."*

### Slide 7: HTML & CSS Code Implementation (3:40 – 4:20)
> *"On the frontend, we use clean HTML5 markup where candidate cards serve as interactive labels for custom styled radio buttons.  
> In CSS3, we use CSS Custom Properties (`:root` variables) for consistent branding, auto-fitting CSS Grid layouts for multi-device responsiveness, and dynamic border/shadow transitions when a candidate is clicked."*

### Slide 8: PHP Back-End & Transaction Logic (4:20 – 5:00)
> *"Looking at the PHP backend code, security is enforced at every layer:  
> We check CSRF tokens on every form submission. When casting a ballot, PHP initiates a database transaction via `$db->beginTransaction()`. We compute the one-way voter hash and insert the votes. We then record the ballot and issue a unique SHA-256 receipt token. If any error or race condition occurs, `$db->rollBack()` triggers automatically."*

### Slide 9: PHP with MySQL Screenshot & Database Evidence (5:00 – 5:30)
> *"Here is the screenshot of our live MySQL database environment. You can see all seven InnoDB tables, active foreign key constraints with `ON DELETE CASCADE`, and query execution times under 3 milliseconds."*

### Slide 10: Application Screenshots & Live Interface (5:30 – 6:00)
> *"Here are screenshots of the running platform: on the left, the candidate ballot card view where students review manifestos; on the right, the live election results displaying candidate tallies and percentage progress bars."*

---

## 💻 Step 3: Live Application Demonstration Script (6:00 – 7:30)

Switch your screen recording from the slides to your web browser (`http://localhost/webassi` or your local development server).

1. **Show the Homepage (`index.php`):**
   - *"Here is the landing page displaying total registered voters, verified ballots cast, and active student council elections."*
2. **Student Login (`login.php`):**
   - *"Now, I'll log in as a student voter using Student ID `STU202601` and password `password123`."*
3. **Voter Dashboard (`dashboard.php`):**
   - *"The dashboard shows our active elections. I can click 'Proceed to Ballot'."*
4. **Casting a Ballot (`vote.php`):**
   - *"Here is the digital ballot. For President, I will select 'Aarav Patel'; for Vice President, 'Maya Sharma'; and for General Secretary, 'Zainab Al-Mansoor'. Notice the smooth card selection highlighting. I will click 'Submit & Seal My Vote' and confirm the prompt."*
5. **Viewing the Verification Receipt (`receipt.php`):**
   - *"Immediately upon submission, the system generates an official cryptographic verification receipt with an authentic SHA-256 token and timestamp. This can be printed or saved."*
6. **Live Results (`results.php`):**
   - *"Navigating to the Live Results page, we see our votes tabulated immediately, updating the candidate progress bars and leading indicators in real time."*
7. **Admin Console (`admin/index.php`):**
   - *"Lastly, logging into the Admin panel, the election committee can view real-time voter turnout, audit logs, manage candidate nominations, and close or open elections."*

### Conclusion Wrap-Up (7:30 – 8:00)
> *"In conclusion, this project demonstrates a complete, secure, and production-ready online voting platform built with PHP and MySQL. Thank you for watching!"*

---

## 📤 Step 4: How to Upload to Google Classroom

1. **Locate Your Recorded Video File:**
   - Make sure your video is saved in **MP4** or **WebM** format.
   - Name your file professionally: `Online_Voting_System_Presentation_YourName_RollNo.mp4`.

2. **Open Google Classroom:**
   - Go to [classroom.google.com](https://classroom.google.com) and sign in with your student account.
   - Click on your class and navigate to the **"Classwork"** tab.
   - Click on the designated assignment (e.g., *"Project Submission: Online Voting System"*).

3. **Attach Your Submission:**
   - Under the **"Your work"** box on the right side, click **"+ Add or create"**.
   - **Method A (Direct File Upload):** Select **"File"**, browse for your recorded MP4 video and your `presentation.pptx` file, and click **Upload**.
   - **Method B (Google Drive - Recommended for larger videos over 100MB):**  
     1. Upload your video to [drive.google.com](https://drive.google.com).  
     2. In Google Classroom, click **"+ Add or create"** &rarr; **"Google Drive"** &rarr; Select the video.  
     3. Make sure the file sharing in Google Drive is set to *"Anyone with the link can view"* so your teacher/evaluator can watch it without permission errors!

4. **Attach Project Files (Recommended for Extra Credit):**
   - You can also attach:
     - `presentation.pptx` (PowerPoint slides)
     - `database.sql` (MySQL schema)
     - A ZIP of the project files.

5. **Turn In:**
   - Click the blue **"Turn In"** or **"Hand In"** button and confirm.

---

## ✅ Submission Checklist Before Submitting

- [x] Clear voice audio with no heavy background noise.
- [x] Title of the project clearly stated at the beginning.
- [x] Introduction and Objectives clearly explained.
- [x] Key features described with clarity.
- [x] 3-Tier Design and Database Schema explained.
- [x] HTML and CSS code snippets highlighted.
- [x] PHP transaction code and MySQL screenshots displayed.
- [x] Live working demo recorded and verified.
- [x] Video file properly named and turned in on Google Classroom.
