# 📂 Job Application Tracker

The **Job Application Tracker** is a web-based application that helps users keep track of job applications, company details, interviews, and status updates — all in one organized place.

## 🚀 Features

- 🔎 Add and view job applications with relevant details
- 🏢 Manage company information including location, email, and contact number
- 📅 Track interview dates and outcomes
- 🔄 Update the status of each job application (e.g., Applied, Interview Scheduled, Offer Received, Rejected)
- 📊 User-friendly interface with organized sections

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: Python (Flask)  
- **Database**: MySQL (phpMyAdmin)  
- **IDE**: VSCode  

## 📁 Folder Structure

JOB_TRACKER_COMPLETE/
├── pycache/
├── templates/
│ ├── add.html # Form to add new applications
│ ├── auth.html # Login/signup interface
│ ├── index.html # Homepage/dashboard
│ ├── interviews.html # Interview scheduling & tracking
│ ├── update.html # Update application status/details
│ └── view.html # View all job applications
├── venv/ # Python virtual environment
├── app.py # Main Flask backend app
├── job_tracker_schema.sql # SQL schema for MySQL database
├── requirements.txt # Python dependencies
├── test_flask.py # Unit tests for Flask routes
└── test_phpmyadmin_connect.py # Test for DB connection

## 🧾 Database Tables

- `applications`: Stores job application details
- `companies`: Stores company data (name, location, email, phone)
- `interviews`: Interview schedules and outcomes
- `status_updates`: Tracks the status history of each application

