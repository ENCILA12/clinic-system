<?php
require_once 'includes/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clinic_id INT NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT,
            amount DECIMAL(10,2) NOT NULL,
            expense_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS prescriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clinic_id INT NOT NULL,
            patient_id VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
            dentist_name VARCHAR(100) NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
            FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS prescription_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            prescription_id INT NOT NULL,
            medicine_name VARCHAR(150) NOT NULL,
            dosage VARCHAR(100) NOT NULL,
            frequency VARCHAR(100) NOT NULL,
            duration VARCHAR(100) NOT NULL,
            FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Tables created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
