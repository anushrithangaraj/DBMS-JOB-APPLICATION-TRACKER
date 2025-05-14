-- Create the database
CREATE DATABASE IF NOT EXISTS job;
USE job;

-- Table: applications
CREATE TABLE applications (
    application_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    job_title VARCHAR(255),
    company_name VARCHAR(255),
    location VARCHAR(255),
    job_type VARCHAR(100),
    application_status VARCHAR(100),
    applied_date DATE,
    source VARCHAR(100),
    salary VARCHAR(100),
    notes TEXT,
    attachment VARCHAR(255)
);

-- Table: mock_interviews
CREATE TABLE mock_interviews (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    file_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
);

-- Table: resumes
CREATE TABLE resumes (
    resume_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    candidate_name VARCHAR(100),
    position VARCHAR(100),
    file_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: saved_jobs
CREATE TABLE saved_jobs (
    saved_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    application_id INT(11),
    saved_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: schedules
CREATE TABLE schedules (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    schedule_date DATE,
    title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: users
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255),
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_image VARCHAR(255)
);
CREATE TABLE calendar_notes (
    note_id INT(11) NOT NULL AUTO_INCREMENT,
    note_title VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    note_description TEXT COLLATE utf8mb4_general_ci NULL,
    note_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (note_id)
);
