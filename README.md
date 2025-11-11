COLLEGE-EVENT-MANAGEMENT SYSTEM:

A full-stack, role-based web application built with PHP and MySQL. This project provides a complete system for managing college fests, allowing administrators, organizers, and participants to manage and access event-related information seamlessly.

🚀 Features
Role-Based Access Control: Separate dashboards and permissions for Admins, Organizers, and Participants.

Event Management: Admins and Organizers can create, edit, and delete events.

Event Browsing: Participants can browse all available events, view details, and register for them.

User Profiles: Users can view and update their profile information.

Feedback System: Participants can submit feedback about their experience.

Resource Management: (Requires manage_resources.php integration) Admins can allocate and manage event resources.

Reporting: Admins can view reports on event registrations and other metrics.

📁 Project Structure
This project has been refactored from its original flat structure into a clean, maintainable, and secure Model-View-Controller (MVC)-like format.

/FMS
├── 📁 auth/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── create_admin.php
│
├── 📁 assets/
│   ├── style.css
│   ├── main.js
│   └── bg.png
│
├── 📁 dashboards/
│   ├── admin.php
│   ├── organizer.php
│   ├── participant.php
│   ├── create_event.php
│   ├── manage_events.php
│   ├── give_feedback.php
│   └── (all other main app pages)
│
├── 📁 includes/
│   ├── db.php (Database Connection)
│   ├── functions.php (Core functions)
│   ├── nav.php (Navigation bar)
│   └── sidebar.php (Dashboard sidebar)
│
├── 📄 index.php (Main landing page)
│
└── 📄 (SQL setup files)
    ├── fms.sql.txt
    ├── db_updates.sql
    ├── setup_resources_table.sql
    └── insert_admin.sql
🛠️ Steps to Execute
Follow these instructions to set up and run the project on your local machine.

1. Prerequisites
XAMPP: Download and install XAMPP. This bundles Apache, MySQL, and PHP.

Git: Download and install Git (optional, for cloning).

2. Clone the Repository
Clone this project into your XAMPP htdocs directory:

Bash

cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/YOUR_NEW_REPO_NAME.git FMS
(Or, you can download the ZIP and extract it as a folder named FMS inside htdocs)

3. Start Services
Open the XAMPP Control Panel and start the Apache and MySQL services.

4. Create the Database
Open your browser and go to http://localhost/phpmyadmin/.

Click on the "Databases" tab.

Create a new database named fms_db.

5. Import the SQL Tables
This is a critical step. You must import the SQL files in the correct order.

Select your fms_db database in the left sidebar.

Click the "Import" tab.

Import File 1: Click "Choose File" and select fms.sql.txt from the project folder. Click "Go".

Import File 2: Click "Import" again. Choose db_updates.sql. Click "Go". (This adds the category column to the events table).

Import File 3: Click "Import" again. Choose setup_resources_table.sql. Click "Go". (This adds the resources table).

6. Configure the Database Connection
Open the project in a code editor.

Navigate to FMS/includes/db.php.

Make sure the connection details match your XAMPP setup (they should be correct by default):

PHP

<?php
$con = mysqli_connect("localhost", "root", "", "fms_db");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>
7. Run the Project
You're all set! Open your browser and navigate to:

http://localhost/FMS/

You can register a new user, or use phpMyAdmin to manually change a user's role to admin to access the admin dashboard.