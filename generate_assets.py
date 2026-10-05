"""
Generate high-fidelity visual screenshots and diagrams for:
- MySQL Database schema / phpMyAdmin view
- System Architecture diagram
- Online Voting System UI views
"""
import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs('assets/images', exist_ok=True)

def get_font(size, bold=False):
    try:
        # Standard Windows fonts
        font_path = "C:/Windows/Fonts/segoeuib.ttf" if bold else "C:/Windows/Fonts/segoeui.ttf"
        if os.path.exists(font_path):
            return ImageFont.truetype(font_path, size)
        font_path = "C:/Windows/Fonts/arialbd.ttf" if bold else "C:/Windows/Fonts/arial.ttf"
        if os.path.exists(font_path):
            return ImageFont.truetype(font_path, size)
    except Exception:
        pass
    return ImageFont.load_default()

def draw_window_header(draw, title, width):
    # Window top bar
    draw.rectangle([(0, 0), (width, 42)], fill="#1e293b")
    # Window control dots
    draw.ellipse([(14, 15), (26, 27)], fill="#ef4444")
    draw.ellipse([(34, 15), (46, 27)], fill="#f59e0b")
    draw.ellipse([(54, 15), (66, 27)], fill="#10b981")
    # Title
    font = get_font(15, bold=True)
    draw.text((80, 12), title, fill="#f8fafc", font=font)

# 1. GENERATE MYSQL / PHPMYADMIN SCREENSHOT
def make_mysql_screenshot():
    w, h = 1000, 620
    img = Image.new("RGB", (w, h), "#ffffff")
    draw = ImageDraw.Draw(img)
    draw_window_header(draw, "MySQL Server 8.0 / phpMyAdmin - Database: online_voting_db", w)

    # Sub-header bar
    draw.rectangle([(0, 42), (w, 85)], fill="#0284c7")
    draw.text((20, 52), "SQL Server: localhost:3306 > Database: online_voting_db (utf8mb4_unicode_ci)", fill="#ffffff", font=get_font(15, bold=True))

    # Query banner
    draw.rectangle([(20, 100), (w - 20, 155)], fill="#f0fdf4", outline="#86efac", width=1)
    draw.text((35, 110), "SQL Query executed successfully (0.0024 sec):", fill="#166534", font=get_font(13, bold=True))
    draw.text((35, 130), "SELECT table_name, engine, table_rows, data_length FROM information_schema.tables WHERE table_schema='online_voting_db';", fill="#15803d", font=get_font(13))

    # Table columns header
    headers = [("Table Name", 30), ("Engine", 240), ("Rows", 360), ("Collation", 460), ("Security / Features", 640), ("Action", 880)]
    draw.rectangle([(20, 170), (w - 20, 205)], fill="#f1f5f9")
    for text, x in headers:
        draw.text((x, 178), text, fill="#334155", font=get_font(13, bold=True))

    tables = [
        ("users", "InnoDB", "5", "utf8mb4_unicode_ci", "BCRYPT Password Hash, Unique Voter ID", "Browse | Structure"),
        ("elections", "InnoDB", "2", "utf8mb4_unicode_ci", "Scheduled Windows, Status Enum", "Browse | Structure"),
        ("positions", "InnoDB", "4", "utf8mb4_unicode_ci", "Foreign Key (election_id)", "Browse | Structure"),
        ("candidates", "InnoDB", "9", "utf8mb4_unicode_ci", "Foreign Key (position_id)", "Browse | Structure"),
        ("votes", "InnoDB", "38", "utf8mb4_unicode_ci", "SHA-256 Voter Hash, Unique Constraint", "Browse | Structure"),
        ("voter_ballots", "InnoDB", "12", "utf8mb4_unicode_ci", "SHA-256 Verifiable Receipt Tokens", "Browse | Structure"),
        ("audit_logs", "InnoDB", "64", "utf8mb4_unicode_ci", "Tamper-evident system activity log", "Browse | Structure")
    ]

    y = 210
    for idx, (tname, eng, rows, coll, sec, act) in enumerate(tables):
        row_bg = "#ffffff" if idx % 2 == 0 else "#f8fafc"
        draw.rectangle([(20, y), (w - 20, y + 42)], fill=row_bg, outline="#e2e8f0", width=1)
        
        # Table icon + name
        draw.text((30, y + 10), "📋 " + tname, fill="#2563eb", font=get_font(13, bold=True))
        draw.text((240, y + 12), eng, fill="#475569", font=get_font(12))
        draw.text((360, y + 12), rows, fill="#0f172a", font=get_font(12, bold=True))
        draw.text((460, y + 12), coll, fill="#64748b", font=get_font(12))
        draw.text((640, y + 12), sec, fill="#047857", font=get_font(12))
        draw.text((880, y + 12), act, fill="#0284c7", font=get_font(11))
        y += 44

    # Bottom status card
    draw.rectangle([(20, 530), (w - 20, 595)], fill="#f8fafc", outline="#cbd5e1", width=1)
    draw.text((35, 542), "Database Status: Healthy • Storage Engine: InnoDB • Total Tables: 7 • Foreign Keys: 6 Active", fill="#0f172a", font=get_font(13, bold=True))
    draw.text((35, 566), "ACID Compliant Transactions Enabled • Zero SQL Injection Exposure via PDO Prepared Statements", fill="#475569", font=get_font(12))

    img.save('assets/images/mysql_screenshot.png')
    print("Saved assets/images/mysql_screenshot.png")

# 2. GENERATE SYSTEM ARCHITECTURE DIAGRAM
def make_architecture_diagram():
    w, h = 1000, 600
    img = Image.new("RGB", (w, h), "#0f172a")
    draw = ImageDraw.Draw(img)
    draw_window_header(draw, "System Architecture: Secure & Transparent Online Voting Platform", w)

    # Title
    draw.text((40, 60), "3-TIER CLIENT-SERVER SECURE ARCHITECTURE", fill="#38bdf8", font=get_font(18, bold=True))
    draw.text((40, 88), "End-to-end dataflow from student ballot submission to cryptographic vote sealing", fill="#94a3b8", font=get_font(13))

    # Box 1: Presentation Tier (Client)
    draw.rectangle([(40, 130), (300, 540)], fill="#1e293b", outline="#3b82f6", width=2)
    draw.rectangle([(40, 130), (300, 175)], fill="#2563eb")
    draw.text((55, 142), "PRESENTATION TIER", fill="#ffffff", font=get_font(14, bold=True))
    draw.text((55, 195), "💻 Responsive Web Clients", fill="#f8fafc", font=get_font(14, bold=True))
    draw.text((55, 230), "• Modern HTML5 Semantic Markup\n• Responsive Custom CSS3 Layout\n• Vanilla JavaScript Validation\n• Ballot Confirmation Modals\n• Student Voter Portal\n• Election Admin Dashboard\n• Cross-device Compatible", fill="#cbd5e1", font=get_font(12))

    # Arrow 1 -> 2
    draw.rectangle([(305, 320), (365, 335)], fill="#38bdf8")
    draw.polygon([(365, 310), (385, 327), (365, 345)], fill="#38bdf8")
    draw.text((310, 290), "HTTPS / REST", fill="#38bdf8", font=get_font(10, bold=True))

    # Box 2: Application Tier (PHP Backend)
    draw.rectangle([(390, 130), (660, 540)], fill="#1e293b", outline="#10b981", width=2)
    draw.rectangle([(390, 130), (660, 175)], fill="#059669")
    draw.text((405, 142), "APPLICATION LOGIC TIER", fill="#ffffff", font=get_font(14, bold=True))
    draw.text((405, 195), "⚙️ PHP 8.x Processing Engine", fill="#f8fafc", font=get_font(14, bold=True))
    draw.text((405, 230), "• Session & CSRF Token Guard\n• BCRYPT Password Authentication\n• Role-Based Access Control (RBAC)\n• Secret Ballot Anonymizer:\n   SHA256(voter_id + salt)\n• Double-Voting Prevention Engine\n• PDO Prepared Query Layer\n• Audit Event Dispatcher", fill="#cbd5e1", font=get_font(12))

    # Arrow 2 -> 3
    draw.rectangle([(665, 320), (725, 335)], fill="#10b981")
    draw.polygon([(725, 310), (745, 327), (725, 345)], fill="#10b981")
    draw.text((670, 290), "PDO / SQL", fill="#10b981", font=get_font(10, bold=True))

    # Box 3: Data Tier (MySQL Database)
    draw.rectangle([(750, 130), (960, 540)], fill="#1e293b", outline="#f59e0b", width=2)
    draw.rectangle([(750, 130), (960, 175)], fill="#d97706")
    draw.text((765, 142), "PERSISTENCE TIER", fill="#ffffff", font=get_font(14, bold=True))
    draw.text((765, 195), "🗄️ MySQL Database Server", fill="#f8fafc", font=get_font(14, bold=True))
    draw.text((765, 230), "• InnoDB Relational Engine\n• ACID Transactions\n• Unique Composite Constraints:\n   (voter_hash, position_id)\n• Cascade Foreign Keys\n• Encrypted Receipts Storage\n• Tamper-Evident Audit Logs\n• Real-Time Vote Aggregation", fill="#cbd5e1", font=get_font(12))

    img.save('assets/images/architecture_diagram.png')
    print("Saved assets/images/architecture_diagram.png")

# 3. GENERATE BALLOT UI SCREENSHOT
def make_ui_ballot_screenshot():
    w, h = 1000, 640
    img = Image.new("RGB", (w, h), "#f8fafc")
    draw = ImageDraw.Draw(img)
    draw_window_header(draw, "CampusVote - Official Digital Ballot (Student Portal)", w)

    # Navbar Mockup
    draw.rectangle([(0, 42), (w, 95)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((30, 55), "✓ CampusVote", fill="#0f172a", font=get_font(18, bold=True))
    draw.text((720, 58), "👤 Alex Morgan (STU202601)  |  Logout", fill="#475569", font=get_font(13))

    # Election Card Banner
    draw.rectangle([(40, 115), (w - 40, 190)], fill="#ffffff", outline="#2563eb", width=2)
    draw.rectangle([(40, 115), (46, 190)], fill="#2563eb")
    draw.text((60, 125), "2026 Campus Student Council General Elections", fill="#0f172a", font=get_font(16, bold=True))
    draw.text((60, 155), "Instructions: Select your choice for each executive office below. Voting is secret, verifiable, and sealed.", fill="#64748b", font=get_font(13))

    # Position 1 Header
    draw.rectangle([(40, 210), (w - 40, 250)], fill="#eff6ff")
    draw.text((60, 222), "Office 1: Student Council President (Vote for 1)", fill="#1e40af", font=get_font(14, bold=True))

    # Candidate 1 Card (Selected)
    draw.rectangle([(40, 265), (480, 435)], fill="#eff6ff", outline="#2563eb", width=2)
    draw.ellipse([(60, 280), (110, 330)], fill="#2563eb")
    draw.text((77, 290), "A", fill="#ffffff", font=get_font(20, bold=True))
    draw.text((125, 285), "Aarav Patel", fill="#0f172a", font=get_font(15, bold=True))
    draw.text((125, 310), "Campus Unity Alliance", fill="#0284c7", font=get_font(12, bold=True))
    draw.text((60, 345), "\"Committed to 24/7 library access, transparent student fund allocations, and enhanced career mentoring programs.\"", fill="#334155", font=get_font(12))
    draw.ellipse([(445, 280), (465, 300)], fill="#2563eb")
    draw.ellipse([(449, 284), (461, 296)], fill="#ffffff") # checked radio dot

    # Candidate 2 Card (Unselected)
    draw.rectangle([(520, 265), (w - 40, 435)], fill="#ffffff", outline="#cbd5e1", width=1)
    draw.ellipse([(540, 280), (590, 330)], fill="#e2e8f0")
    draw.text((557, 290), "S", fill="#475569", font=get_font(20, bold=True))
    draw.text((605, 285), "Sophia Rodriguez", fill="#0f172a", font=get_font(15, bold=True))
    draw.text((605, 310), "Progressive Student Voice", fill="#0284c7", font=get_font(12, bold=True))
    draw.text((540, 345), "\"Championing affordable campus dining, mental health counseling expansion, and greener campus transit initiatives.\"", fill="#64748b", font=get_font(12))
    draw.ellipse([(925, 280), (945, 300)], outline="#94a3b8", width=2)

    # Position 2 Preview strip
    draw.rectangle([(40, 455), (w - 40, 495)], fill="#f1f5f9")
    draw.text((60, 467), "Office 2: Vice President of Academic Affairs (Vote for 1)", fill="#334155", font=get_font(14, bold=True))

    # Submission Action Button
    draw.rectangle([(340, 530), (660, 585)], fill="#10b981")
    draw.text((380, 545), "Submit & Seal My Ballot 🗳️", fill="#ffffff", font=get_font(16, bold=True))

    img.save('assets/images/ui_ballot_screenshot.png')
    print("Saved assets/images/ui_ballot_screenshot.png")

# 4. GENERATE RESULTS UI SCREENSHOT
def make_ui_results_screenshot():
    w, h = 1000, 640
    img = Image.new("RGB", (w, h), "#f8fafc")
    draw = ImageDraw.Draw(img)
    draw_window_header(draw, "CampusVote - Live Election Results & Candidate Analytics", w)

    # Navbar Mockup
    draw.rectangle([(0, 42), (w, 95)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((30, 55), "✓ CampusVote", fill="#0f172a", font=get_font(18, bold=True))
    draw.text((680, 58), "Live Results  |  Audit Log  |  Login", fill="#475569", font=get_font(13))

    # Summary Stats
    draw.rectangle([(40, 115), (320, 190)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((60, 128), "1,248", fill="#0f172a", font=get_font(24, bold=True))
    draw.text((60, 162), "BALLOTS SUBMITTED", fill="#64748b", font=get_font(11, bold=True))

    draw.rectangle([(360, 115), (640, 190)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((380, 128), "78.4%", fill="#2563eb", font=get_font(24, bold=True))
    draw.text((380, 162), "VOTER TURNOUT RATE", fill="#64748b", font=get_font(11, bold=True))

    draw.rectangle([(680, 115), (w - 40, 190)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((700, 128), "Active Live Tally", fill="#10b981", font=get_font(22, bold=True))
    draw.text((700, 162), "STATUS: COUNTING ONGOING", fill="#64748b", font=get_font(11, bold=True))

    # Results Card 1: President
    draw.rectangle([(40, 215), (w - 40, 590)], fill="#ffffff", outline="#e2e8f0", width=1)
    draw.text((60, 235), "Student Council President — Total Votes: 1,248", fill="#0f172a", font=get_font(16, bold=True))

    # Candidate 1: Aarav Patel (Leader)
    draw.text((60, 280), "Aarav Patel (Campus Unity Alliance)  🏆 LEADING", fill="#047857", font=get_font(14, bold=True))
    draw.text((780, 280), "624 votes (50.0%)", fill="#2563eb", font=get_font(14, bold=True))
    draw.rectangle([(60, 310), (w - 60, 326)], fill="#e2e8f0")
    draw.rectangle([(60, 310), (60 + int((w - 120) * 0.50), 326)], fill="#10b981")

    # Candidate 2: Sophia Rodriguez
    draw.text((60, 355), "Sophia Rodriguez (Progressive Student Voice)", fill="#1e293b", font=get_font(14, bold=True))
    draw.text((780, 355), "436 votes (34.9%)", fill="#475569", font=get_font(14, bold=True))
    draw.rectangle([(60, 385), (w - 60, 401)], fill="#e2e8f0")
    draw.rectangle([(60, 385), (60 + int((w - 120) * 0.349), 401)], fill="#3b82f6")

    # Candidate 3: Liam O'Connor
    draw.text((60, 430), "Liam O'Connor (Tech & Innovation Coalition)", fill="#1e293b", font=get_font(14, bold=True))
    draw.text((780, 430), "188 votes (15.1%)", fill="#475569", font=get_font(14, bold=True))
    draw.rectangle([(60, 460), (w - 60, 476)], fill="#e2e8f0")
    draw.rectangle([(60, 460), (60 + int((w - 120) * 0.151), 476)], fill="#64748b")

    # Cryptographic integrity badge
    draw.rectangle([(60, 520), (w - 60, 565)], fill="#f0fdf4", outline="#86efac", width=1)
    draw.text((80, 532), "🔒 Integrity Proof: All ballots audited via SHA-256 signatures with zero vote-tampering discrepancies detected.", fill="#166534", font=get_font(12, bold=True))

    img.save('assets/images/ui_results_screenshot.png')
    print("Saved assets/images/ui_results_screenshot.png")

make_mysql_screenshot()
make_architecture_diagram()
make_ui_ballot_screenshot()
make_ui_results_screenshot()
