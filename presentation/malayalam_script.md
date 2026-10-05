# 🗳️ CampusVote - Online Voting System
## Presentation Script in Malayalam (മലയാളം)

---

## സ്ലൈഡ് 1: Title Slide — പ്രോജക്ട് ടൈറ്റിൽ

> **"സുപ്രഭാതം / ശുഭദിനം. ഞാൻ ഇന്ന് അവതരിപ്പിക്കാൻ പോകുന്നത് ഞങ്ങളുടെ ടീം തയ്യാറാക്കിയ ഒരു വെബ് ആപ്ലിക്കേഷൻ പ്രോജക്ടാണ്.**
>
> **പ്രോജക്ടിന്റെ പേര്: Online Voting System — CampusVote.**
>
> **ഇത് ഒരു സുരക്ഷിതവും, സുതാര്യവുമായ ഇലക്ട്രോണിക് വോട്ടിംഗ് പ്ലാറ്റ്ഫോമാണ്. ഇത് Student Council Election, Class Representative Election, Community Polls എന്നിവയ്ക്കായി ഡിസൈൻ ചെയ്തിരിക്കുന്നു.**
>
> **ഈ പ്രോജക്ടിൽ ഉപയോഗിച്ചിരിക്കുന്ന Technology Stack ഇവയാണ്:**
> - **PHP 8.x** — Back-End Server Logic
> - **MySQL Server 8.0** — Database Management
> - **HTML5 & CSS3** — Responsive Front-End UI
> - **JavaScript** — Client-Side Validation
>
> **ഈ System ഉറപ്പ് നൽകുന്നത്: Secret Ballot Anonymity (രഹസ്യ ബാലറ്റ്), Zero Duplicate Voting (ഇരട്ട വോട്ട് ഇല്ലായ്മ), Cryptographic Audit Receipts (ക്രിപ്റ്റോഗ്രാഫിക് രസീത്) ഇവ ആണ്."**

---

## സ്ലൈഡ് 2: Introduction — ആമുഖം

> **"ഈ Slide-ൽ ഞാൻ explain ചെയ്യുന്നത്, ഒരു Traditional Paper-Based Election System-ന്റെ പ്രശ്നങ്ങളും, നമ്മളുടെ Digital Solution-ഉം ആണ്.**
>
> **Traditional Voting-ന്റെ Problems:**
> - Physical Ballot Paper Printing-ന് വലിയ ചെലവ് ആകും.
> - Polling Booth-ലേക്ക് വരാൻ കഴിയാത്ത Students vote ചെയ്യാൻ സാധിക്കില്ല.
> - Manual Counting ൽ Errors ഉണ്ടാകും, Time കൂടുതൽ ആകും.
> - Paper Ballot-കൾ Tamper ആകാനോ, Lose ആകാനോ Chance ഉണ്ട്.
> - Voter-ക്ക് തന്റെ Vote Count ആയോ എന്ന് verify ചെയ്യാൻ ഒരു Mechanism ഇല്ല.
>
> **ഇതിന്റെ Solution ആണ് CampusVote. ഇത് ഉറപ്പ് നൽകുന്നത്:**
> - Student-കൾക്ക് Mobile, Laptop ഉപയോഗിച്ച് 24/7 vote ചെയ്യാൻ കഴിയും.
> - SHA-256 Cryptographic Hash ഉപയോഗിച്ച് Voter-ന്റെ Identity-യും Vote-ഉം Separate ആക്കി Ballot Secrecy ഉറപ്പ് വരുത്തുന്നു.
> - Vote കഴിഞ്ഞ ഉടനെ Results Automatically Update ആകും.
> - ഓരോ Voter-ക്കും ഒരു Unique Cryptographic Receipt Token ലഭിക്കും."**

---

## സ്ലൈഡ് 3: Objectives — ലക്ഷ്യങ്ങൾ

> **"ഈ Project-ന്റെ പ്രധാന ലക്ഷ്യങ്ങൾ 5 ആണ്:**
>
> **ഒന്ന്: Security ഉറപ്പ് വരുത്തൽ.**
> SQL Injection Attack-കൾ തടയാൻ PDO Prepared Statements ഉപയോഗിക്കുന്നു. CSRF Token Protection കൊണ്ട് Cross-Site Request Forgery Attack-കൾ Prevent ചെയ്യുന്നു. Password-കൾ BCRYPT Algorithm ഉപയോഗിച്ച് Securely Hash ചെയ്ത് Store ചെയ്യുന്നു.
>
> **രണ്ട്: Secret Ballot Anonymity.**
> Student-ന്റെ Voter ID-യും, അദ്ദേഹം Select ചെയ്ത Candidate-ഉം തമ്മിൽ Direct Link ഉണ്ടാകില്ല. ഇതിന് SHA-256 One-Way Cryptographic Hash ഉപയോഗിക്കുന്നു.
>
> **മൂന്ന്: Single Vote Enforcement.**
> ഒരു Student-ന് ഒരു Election-ൽ ഒരു Position-ലേക്ക് ഒരേ ഒരു Vote മാത്രമേ Cast ചെയ്യാൻ കഴിയൂ. ഇത് Database-Level Unique Constraint ഉം ACID Transaction ഉം കൊണ്ട് Mathematically Guarantee ചെയ്യുന്നു.
>
> **നാല്: Verifiable Receipt.**
> Vote ചെയ്ത ഓരോ Student-ഉം ഒരു Unique 64-Character SHA-256 Receipt Token ലഭിക്കും. ഇത് Print ചെയ്ത് Proof ആയി സൂക്ഷിക്കാം.
>
> **അഞ്ച്: User-Friendly Responsive Interface.**
> Mobile Phone, Tablet, Laptop എന്നിവ ഉപയോഗിച്ച് ഏതൊരു Student-ഉം ആദ്യമായി Use ചെയ്ത് Vote ചെയ്യാൻ കഴിയും."**

---

## സ്ലൈഡ് 4: Key Features — പ്രധാന Feature-കൾ

> **"CampusVote-ന്റെ 6 Key Features ഇവയാണ്:**
>
> **ഒന്ന് — Student Voter Portal:**
> Students-ന് Self-Registration ചെയ്ത്, Election Dashboard-ൽ Login ചെയ്ത്, Active Elections കാണാൻ കഴിയും. Vote ചെയ്ത Elections-ൽ 'Ballot Cast' Status കാണിക്കും.
>
> **രണ്ട് — Interactive Digital Ballot:**
> ഓരോ Position-ഉം Separate Section ആയി Display ആകും. Candidate-ന്റെ Photo, Party, Manifesto കാണാം. Card Select ചെയ്യുമ്പോൾ Blue Highlight ആകും.
>
> **മൂന്ന് — Cryptographic Ballot Receipt:**
> Vote Submit ചെയ്ത ഉടൻ ഒരു Official Verification Slip Generate ആകും. ഇതിൽ ഒരു Unique SHA-256 Token ഉം Timestamp ഉം ഉണ്ടായിരിക്കും. ഇത് Print ചെയ്ത് Audit ചെയ്യാം.
>
> **നാല് — Real-Time Live Results:**
> Vote-കൾ Automatically Count ആകുന്നു. Live Progress Bar, Percentage, Leader Badge ഇവ Display ആകും.
>
> **അഞ്ച് — Admin Control Panel:**
> Election Commission Administrator-ക്ക് Elections Create ചെയ്യാം, Positions Add ചെയ്യാം, Candidates Nominate ചെയ്യാം, Voter Roll Manage ചെയ്യാം.
>
> **ആറ് — Tamper-Evident Audit Log:**
> Login, Registration, Vote Casting, Admin Actions — ഇവ ഒക്കെ IP Address-ഉം Timestamp-ഉം ഉൾക്കൊള്ളിച്ച് Permanently Record ആകും. ഇത് Tamper ചെയ്യാൻ Possible അല്ല."**

---

## സ്ലൈഡ് 5: System Design — 3-Tier Architecture

> **"ഇവിടെ നമ്മൾ കാണുന്നത് ഈ System-ന്റെ Architecture ആണ്. ഇത് ഒരു Standard 3-Tier Client-Server Architecture ആണ്.**
>
> **Tier 1 — Presentation Layer (Front-End):**
> Student-ന്റെ Browser-ൽ Run ആകുന്ന HTML5, CSS3, JavaScript Code ആണ് ഇത്. Responsive Design കൊണ്ട് Mobile, Tablet, Desktop-ൽ Perfectly Display ആകും.
>
> **Tier 2 — Application Logic Layer (PHP Backend):**
> ഇതാണ് System-ന്റെ Brain. PHP 8.x ഉപയോഗിച്ച് Authentication, CSRF Validation, Role-Based Access Control, Secret Ballot Anonymizer, Vote Processing ഇവ Handle ചെയ്യുന്നു.
>
> **Tier 3 — Data Persistence Layer (MySQL Database):**
> MySQL Server 8.0, InnoDB Engine, ACID Transactions ഉപയോഗിച്ച് Data Securely Store ചെയ്യുന്നു. Composite Unique Key ഉപയോഗിച്ച് Duplicate Voting Mathematically Impossible ആക്കുന്നു.
>
> **ഈ Three Tiers ഒരുമിച്ച് പ്രവർത്തിക്കുമ്പോൾ ഒരു Secure, Scalable, Maintainable Voting Platform ലഭിക്കുന്നു."**

---

## സ്ലൈഡ് 6: Database Design — ഡേറ്റാബേസ് Design

> **"ഞങ്ങൾ 'online_voting_db' Database-ൽ 7 Tables Design ചെയ്തിട്ടുണ്ട്:**
>
> **'users' Table:**
> Voter ID, Full Name, Email, BCRYPT Password Hash, Role (voter/admin), Status Store ചെയ്യുന്നു.
>
> **'elections' Table:**
> Election Title, Description, Start Date, End Date, Status (upcoming/active/completed) Store ചെയ്യുന്നു.
>
> **'positions' Table:**
> Election-ൽ Contest ചെയ്യുന്ന Offices — President, Vice President, Secretary — ഇവ Store ചെയ്യുന്നു. Election ID Foreign Key ആയി Connect ആണ്.
>
> **'candidates' Table:**
> ഓരോ Position-ലേക്കും Nominated ആകുന്ന Candidates-ന്റെ Name, Party, Manifesto, Photo Store ചെയ്യുന്നു.
>
> **'votes' Table — ഇതാണ് ഏറ്റവും Important:**
> ഇവിടെ candidate_id-ക്കൊപ്പം voter_id Direct ആയി Store ചെയ്യുന്നില്ല. പകരം SHA-256 Hash ആയ 'voter_hash' Store ചെയ്യുന്നു. ഇത് UNIQUE KEY (voter_hash, position_id) ആക്കി Database Level-ൽ ഒരു Voter ഒരു Position-ൽ ഒരിക്കൽ മാത്രം Vote ചെയ്യാൻ Guarantee ചെയ്യുന്നു. Identity Secure ആണ്.
>
> **'voter_ballots' Table:**
> Vote ചെയ്ത ഓരോ Student-ന്റെ SHA-256 Receipt Token Securely Store ചെയ്യുന്നു.
>
> **'audit_logs' Table:**
> System-ൽ നടക്കുന്ന ഓരോ Event-ഉം IP Address-ഉം Timestamp-ഉം Permanently Record ആകുന്നു."**

---

## സ്ലൈഡ് 7: HTML & CSS Code

> **"ഇനി ഞാൻ Front-End Code കാണിക്കാം.**
>
> **HTML5 ഭാഗം:**
> Candidate Select ചെയ്യാനുള്ള Ballot Card HTML5-ൽ ഇങ്ങനെ Design ചെയ്തിരിക്കുന്നു. ഇവിടെ Entire Card ഒരു Label ആണ്. ഉള്ളിൽ ഒരു Radio Input ഉണ്ട്. ഇത് ഒരു Accessible, User-Friendly Design ആണ്. PHP-ൽ നിന്ന് Candidate Name, Party, Manifesto ഒക്കെ Dynamic ആയി Output ചെയ്യുന്നു. htmlspecialchars() ഉപയോഗിച്ച് XSS Attack Prevent ചെയ്യുന്നു.
>
> **CSS3 ഭാഗം:**
> ഞങ്ങൾ CSS Custom Properties (CSS Variables) ഉപയോഗിക്കുന്നു. :root-ൽ --primary, --success, --border ഇവ Define ചെയ്തിട്ടുണ്ട്. Candidate Cards-ന് Auto-Fitting CSS Grid Layout ഉണ്ട്. ഇത് Mobile-ൽ 1 Column ആയും, Desktop-ൽ 3 Column ആയും Auto Adjust ആകും. Candidate Select ചെയ്യുമ്പോൾ 'selected' CSS Class Add ആകും. Card-ന് Blue Border, Light Blue Background, Shadow ഇവ Add ആകും. ഇത് User-ന് Clear Feedback നൽകുന്നു."**

---

## സ്ലൈഡ് 8: PHP Back-End Code

> **"ഇതാണ് System-ന്റെ ഏറ്റവും Critical Part — Vote Cast ചെയ്യുന്ന PHP Code.**
>
> **Step 1:** CSRF Token Verify ചെയ്യുന്നു. ഇത് Cross-Site Attack Prevent ചെയ്യുന്നു.
>
> **Step 2:** ആ Student ഇതിനു മുൻപ് ഈ Election-ൽ Vote ചെയ്തിട്ടുണ്ടോ എന്ന് Check ചെയ്യുന്നു. ഉണ്ടെങ്കിൽ Error കാണിക്കുന്നു.
>
> **Step 3:** $db->beginTransaction() ഉപയോഗിച്ച് ACID Transaction Start ചെയ്യുന്നു.
>
> **Step 4:** SHA-256 hash() Function ഉപയോഗിച്ച് Voter ID-യും Election ID-യും Salt-ഉം Combine ചെയ്ത് ഒരു One-Way Hash Generate ചെയ്യുന്നു. ഇത് Voter-ന്റെ Identity-യും Vote-ഉം Permanently Separate ആക്കുന്നു. ഇതാണ് Secret Ballot-ന്റെ Technical Secret.
>
> **Step 5:** Votes Database-ൽ Insert ചെയ്യുന്നു. Duplicate ആണെങ്കിൽ Unique Constraint Exception Throw ആകും.
>
> **Step 6:** ഒരു Unique SHA-256 Receipt Token Generate ചെയ്ത് voter_ballots Table-ൽ Insert ചെയ്യുന്നു.
>
> **Step 7:** $db->commit() ഉപയോഗിച്ച് Transaction Complete ചെയ്യുന്നു. Error ഉണ്ടെങ്കിൽ rollBack() ഉപയോഗിച്ച് ഒന്നും Save ആകില്ല. ഇത് Data Integrity 100% Guarantee ചെയ്യുന്നു."**

---

## സ്ലൈഡ് 9: PHP with MySQL Screenshot

> **"ഈ Slide-ൽ ഞങ്ങളുടെ MySQL Server-ൽ Create ചെയ്ത Database-ന്റെ Screenshot കാണാൻ കഴിയും.**
>
> **'online_voting_db' Database-ൽ 7 Tables Successfully Create ആയിട്ടുണ്ട്.**
>
> ഓരോ Table-ഉം InnoDB Engine ഉപയോഗിക്കുന്നു. ഇത് Row-Level Locking ഉം ACID Compliance ഉം Provide ചെയ്യുന്നു.
>
> Collation: utf8mb4_unicode_ci ആണ്. ഇത് Malayalam, Hindi, Arabic ഉൾപ്പടെ ഏത് Language ഉം Support ചെയ്യും.
>
> Foreign Key Constraints 'ON DELETE CASCADE' ആണ്. ഒരു Election Delete ചെയ്താൽ, അതിന്റെ Positions, Candidates, Votes ഒക്കെ Automatically Delete ആകും. Data Integrity നഷ്ടമാകില്ല.
>
> Query Execution Time 3 Milliseconds-ൽ കുറവ് ആണ്. ഇത് System-ന്റെ High Performance ഉറപ്പ് വരുത്തുന്നു."**

---

## സ്ലൈഡ് 10: Application Screenshots & Live Demo

> **"ഇതാ ഞങ്ങളുടെ Running Web Application-ന്റെ Screenshots.**
>
> **ഇടതുവശം — Digital Ballot Interface:**
> Student Login ചെയ്ത ശേഷം കാണുന്ന Official Ballot Page ഇതാണ്. ഓരോ Position-ഉം Separate Section ആണ്. Candidates-ന്റെ Avatar, Party, Manifesto കാണാം. ഒരു Candidate-നെ Click ചെയ്യുമ്പോൾ Card Blue Highlight ആകുന്നു. Submit ചെയ്യുന്നതിന് മുൻപ് JavaScript ഒരു Confirmation Dialog കാണിക്കും.
>
> **വലതുവശം — Live Results Page:**
> 1,248 Ballots Submitted ആയിരിക്കുന്നു. Voter Turnout 78.4% ആണ്. ഓരോ Candidate-ന്റെ Votes Automatically Calculate ആകുന്നു. Leading Candidate-ന് 'Trophy' Badge കാണിക്കുന്നു. Results Real-Time ആയി Update ആകുന്നു.
>
> ഈ Application ഇപ്പോൾ ഞങ്ങളുടെ Computer-ൽ Live ആയി Running ആണ്. Live Demo ഞാൻ ഇനി കാണിക്കാം."**

---

## Live Demo Script (ലൈവ് ഡെമോ)

> **"ഇപ്പോൾ ഞാൻ Browser-ൽ http://localhost:8000 Open ചെയ്ത് Live Demo കാണിക്കുന്നു.**
>
> *[Browser Open ചെയ്യുക — index.php]*
> ഇതാ ഞങ്ങളുടെ CampusVote Home Page. ഇവിടെ Registered Voters Count, Ballots Cast Count, Active Elections ഇവ കാണാൻ കഴിയും.
>
> *[login.php Open ചെയ്യുക]*
> ഞാൻ ഇപ്പോൾ ഒരു Student Voter ആയി Login ചെയ്യുന്നു. Student ID: STU202601, Password: password123.
>
> *[Login Submit ചെയ്ത് Dashboard കാണിക്കുക]*
> ഇതാ Student Dashboard. ഇവിടെ Active Election കാണുന്നു — '2026 Campus Student Council General Elections'. Ballot ഇതുവരെ Cast ചെയ്തിട്ടില്ലാത്തതിനാൽ 'Proceed to Ballot' Button കാണാം.
>
> *['Proceed to Ballot' Click ചെയ്യുക]*
> ഇതാ Official Digital Ballot Page. Position 1: Student Council President. ഇവിടെ Aarav Patel-നെ Select ചെയ്യുന്നു — Card Blue ആകുന്നു. Position 2: Vice President-ലേക്ക് Maya Sharma-യെ Select ചെയ്യുന്നു.
>
> *['Submit & Seal My Vote' Click ചെയ്യുക]*
> JavaScript Confirmation Dialog: "Are you Sure?" — OK Click ചെയ്യുന്നു.
>
> *[Receipt Page കാണിക്കുക]*
> Vote Successfully Cast ആയി! ഇതാ Official Verification Receipt. ഇവിടെ ഒരു Unique SHA-256 Token കാണാൻ കഴിയും. ഈ Token ഉപയോഗിച്ച് Student-ന് Vote Count ആയോ എന്ന് Independently Verify ചെയ്യാൻ കഴിയും. Print ചെയ്ത് Evidence ആയി സൂക്ഷിക്കാം.
>
> *[Results Page കാണിക്കുക]*
> Live Results-ൽ ഞങ്ങൾ Cast ചെയ്ത Vote Count ആയ് Progress Bar Update ആയിട്ടുണ്ട്. ഇതാണ് Real-Time Transparency.
>
> *[Admin Login കാണിക്കുക — admin@campusvote.org]*
> ഇതാ Admin Control Panel. Election Commission-ന് Voter Turnout കാണാം, Audit Log Review ചെയ്യാം, Elections Configure ചെയ്യാം. ഇതാ Audit Log — ഞങ്ങൾ Cast ചെയ്ത Vote 'BALLOT_CAST' ആയി Record ആയിട്ടുണ്ട്."**

---

## സ്ലൈഡ് 11: Conclusion & Future Roadmap

> **"ഉപസംഹാരമായി ഞാൻ പറയട്ടെ:**
>
> **ഞങ്ങളുടെ CampusVote Project ഇനി Explain ചെയ്ത കാര്യങ്ങൾ Demonstrate ചെയ്തു:**
>
> - PHP, MySQL, HTML5, CSS3 ഉപയോഗിച്ച് ഒരു Complete Full-Stack Voting Platform Build ചെയ്തു.
> - SHA-256 Cryptographic Hashing ഉപയോഗിച്ച് Voter Privacy 100% Protect ചെയ്തു.
> - Database Unique Constraint ഉപയോഗിച്ച് Duplicate Voting Mathematically Impossible ആക്കി.
> - Paper Ballot-ന്റെ ഒരു Hours-Long Manual Count-നു പകരം Instant Automated Tabulation Implement ചെയ്തു.
> - Tamper-Evident Audit Log ഉപയോഗിച്ച് System-ൽ നടക്കുന്ന ഓരോ Event-ഉം Record ചെയ്തു.
>
> **ഭവിഷ്യത്ത് Enhancement-കൾ:**
>
> ഈ System-ൽ Future-ൽ ഞങ്ങൾ Add ചെയ്യാൻ ആഗ്രഹിക്കുന്നത്:
> - SMS / Email OTP ഉപയോഗിച്ചുള്ള Two-Factor Authentication.
> - Blockchain Technology ഉപയോഗിച്ച് Vote Hashes Immutably Store ചെയ്യൽ.
> - Department-wise Voter Turnout Heatmap Visualization.
> - Digitally Signed Election Result PDF Certificates."**

---

## സ്ലൈഡ് 12: Thank You — നന്ദി

> **"ഈ Presentation ശ്രദ്ധിച്ച് കേട്ടതിന് ഒക്കെ നന്ദി.**
>
> ഞങ്ങൾ Build ചെയ്ത CampusVote Platform PHP-ഉം MySQL-ഉം ഉപയോഗിച്ച് ഒരു Real-World Voting System എങ്ങനെ Design ചെയ്യാം, Security Implement ചെയ്യാം, Transparency ഉറപ്പ് വരുത്താം ഇവ Demonstrate ചെയ്തു.**
>
> **ഇനി Questions ഉണ്ടെങ്കിൽ Please ചോദിക്കൂ. System-ൽ Live Demo ഒന്ന് കൂടെ കാണണം എന്ന് ഉണ്ടെങ്കിൽ ഞാൻ Show ചെയ്ത് തരാം.**
>
> **Thank You! നന്ദി!"**

---

## 📋 Quick Technical Terms Reference (ഉപകരണ പദ ഗ്ലോസറി)

| English Term | Malayalam Explanation |
|---|---|
| Authentication | ആരാണ് Login ചെയ്യുന്നത് എന്ന് Verify ചെയ്യൽ |
| Authorization | ആ User-ന് ഏത് Pages Access ചെയ്യാൻ Permission ഉണ്ടോ |
| Encryption | Data Unreadable ആക്കി Protect ചെയ്യൽ |
| Hashing | Data ഒരു Fixed-Size Code ആക്കി Convert ചെയ്യൽ, Reverse ആകില്ല |
| ACID Transaction | Database Operations Safely ആകുന്നു — ഒന്നും ഇടയ്ക്ക് Fail ആകില്ല |
| CSRF Attack | User-ന്റെ Browser ഉപയോഗിച്ച് Fake Request Sent ചെയ്യൽ |
| SQL Injection | Database Query-ൽ Malicious Code Inject ചെയ്ത് Attack ചെയ്യൽ |
| PDO Prepared Statement | SQL Injection Prevent ചെയ്യാൻ PHP-ൽ ഉള്ള Secure Query Method |
| BCRYPT | Password Securely Hash ചെയ്യാൻ ഉള്ള Strong Algorithm |
| Audit Log | System-ൽ ആര്, എന്ത്, എപ്പോൾ ചെയ്തു എന്ന Permanent Record |
| Receipt Token | Vote Cast ചെയ്തതിന്റെ Cryptographic Proof |
| Responsive Design | Mobile, Tablet, Desktop ഇവ ഒക്കെ ശരിയായ് Display ആകുന്ന Design |

---

*Script prepared for Google Classroom video submission — CampusVote Online Voting System Project*
