-- Create the patient table
CREATE TABLE IF NOT EXISTS patient (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20),
    social_security_number VARCHAR(20),
    password_hash VARCHAR(255)
);

-- Create the doctor table
CREATE TABLE IF NOT EXISTS doctor (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20),
    specialization VARCHAR(100),
    password_hash VARCHAR(255)
);

-- Create the patient_doctor_assignment table
CREATE TABLE IF NOT EXISTS patient_doctor_assignment (
    patient_doctor_assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    is_primary BOOLEAN,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(patient_id),
    FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
);

-- Create the pharmacy table
CREATE TABLE IF NOT EXISTS pharmacy (
    pharmacy_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    location VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20)
);

-- Create the pharmaceutical table
CREATE TABLE IF NOT EXISTS pharmaceutical (
    pharmaceutical_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    location VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20)
);

-- Create the pharmacist table
CREATE TABLE IF NOT EXISTS pharmacist (
    pharmacist_id INT AUTO_INCREMENT PRIMARY KEY,
    pharmacy_id INT,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20),
    password_hash VARCHAR(255),
    FOREIGN KEY (pharmacy_id) REFERENCES pharmacy(pharmacy_id)
);

-- Create the supervisor table
CREATE TABLE IF NOT EXISTS supervisor (
    supervisor_id INT AUTO_INCREMENT PRIMARY KEY,
    pharmaceutical_id INT,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20),
    password_hash VARCHAR(255),
    FOREIGN KEY (pharmaceutical_id) REFERENCES pharmaceutical(pharmaceutical_id)
);

-- Create the contract table
CREATE TABLE IF NOT EXISTS contract (
    contract_id INT AUTO_INCREMENT PRIMARY KEY,
    pharmacy_id INT,
    pharmaceutical_id INT,
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (pharmacy_id) REFERENCES pharmacy(pharmacy_id),
    FOREIGN KEY (pharmaceutical_id) REFERENCES pharmaceutical(pharmaceutical_id)
);

-- Create the drug table
CREATE TABLE IF NOT EXISTS drug (
    drug_id INT AUTO_INCREMENT PRIMARY KEY,
    scientific_name VARCHAR(100),
    trade_name VARCHAR(100),
    formula VARCHAR(100),
    form VARCHAR(100),
    expiry_date DATE,
    manufacturing_date DATE,
    amount INT,
    contract_id INT,
    FOREIGN KEY (contract_id) REFERENCES contract(contract_id)
);

-- Create the prescription table
CREATE TABLE IF NOT EXISTS prescription (
    prescription_id INT AUTO_INCREMENT PRIMARY KEY,
    drug_id INT,
    patient_doctor_assignment_id INT,
    dosage VARCHAR(50),
    frequency VARCHAR(50),
    cost DECIMAL(10, 2),
    start_date DATE,
    end_date DATE,
is_assigned BOOLEAN,
    FOREIGN KEY (drug_id) REFERENCES drug(drug_id),
    FOREIGN KEY (patient_doctor_assignment_id) REFERENCES patient_doctor_assignment(patient_doctor_assignment_id)
);

-- Create the administrator table
CREATE TABLE IF NOT EXISTS administrator (
    administrator_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile_number VARCHAR(20),
    password_hash VARCHAR(255)
);

-- Insert an administrator into the administrator table
INSERT INTO administrator (name, email, mobile_number, password_hash)
VALUES ('Latifa Asad Madoka', 'administrator@gmail.com', '+254 112 345 678', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK');
