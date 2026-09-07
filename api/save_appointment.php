<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->query("SELECT id FROM appointments ORDER BY id DESC LIMIT 1");
    $lastApt = $stmt->fetch();
    $nextId = $lastApt ? $lastApt['id'] + 1 : 1;
    $appointment_id = "APT-" . date('ym') . "-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO appointments (
                clinic_id, appointment_id, patient_id, dentist_name, appointment_date, appointment_time, 
                procedure_name, estimated_duration, notes
            ) VALUES (
                :clinic_id, :appointment_id, :patient_id, :dentist_name, :appointment_date, :appointment_time,
                :procedure_name, :estimated_duration, :notes
            )";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':patient_id' => $_POST['patient_id'] ?? '',
            ':dentist_name' => $_POST['dentist_name'] ?? '',
            ':appointment_date' => $_POST['appointment_date'] ?? '',
            ':appointment_time' => $_POST['appointment_time'] ?? '',
            ':procedure_name' => $_POST['procedure_name'] ?? '',
            ':estimated_duration' => $_POST['estimated_duration'] ?? '',
            ':notes' => $_POST['notes'] ?? '',
            ':clinic_id' => $_SESSION['clinic_id']
        ]);
        // Fetch patient email and clinic name for sending confirmation
        $stmtDetails = $pdo->prepare("
            SELECT p.email, p.full_name, c.name AS clinic_name 
            FROM patients p 
            JOIN clinics c ON c.id = ? 
            WHERE p.patient_id = ?
        ");
        $stmtDetails->execute([$_SESSION['clinic_id'], $_POST['patient_id']]);
        $details = $stmtDetails->fetch(PDO::FETCH_ASSOC);

        if ($details && !empty($details['email']) && filter_var($details['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $details['email'];
            $name = $details['full_name'];
            $clinic = $details['clinic_name'];
            
            $aptDate = date('F d, Y', strtotime($_POST['appointment_date']));
            $aptTime = date('h:i A', strtotime($_POST['appointment_time']));
            $dentist = $_POST['dentist_name'];
            $procedure = $_POST['procedure_name'];

            $subject = "Appointment Confirmation: $clinic";
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif; color: #333;'>
              <div style='max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                  <div style='background-color: #10b981; color: white; padding: 20px; text-align: center;'>
                      <h2 style='margin: 0;'>Appointment Confirmed</h2>
                  </div>
                  <div style='padding: 20px;'>
                      <p>Hi <strong>$name</strong>,</p>
                      <p>Your appointment at <strong>$clinic</strong> has been successfully booked.</p>
                      
                      <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                          <h3 style='margin-top: 0; color: #1e293b;'>Appointment Details:</h3>
                          <ul style='list-style-type: none; padding: 0; margin: 0;'>
                            <li style='margin-bottom: 8px;'><strong>📅 Date:</strong> $aptDate</li>
                            <li style='margin-bottom: 8px;'><strong>⏰ Time:</strong> $aptTime</li>
                            <li style='margin-bottom: 8px;'><strong>🧑‍⚕️ Dentist:</strong> Dr. $dentist</li>
                            <li style='margin-bottom: 0;'><strong>🦷 Procedure:</strong> $procedure</li>
                          </ul>
                      </div>
                      <p>Thank you and see you soon!</p>
                  </div>
              </div>
            </body>
            </html>
            ";

            $headers = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            $headers[] = 'From: ' . $clinic . ' <no-reply@dentaflow.com>';

            @mail($email, $subject, $message, implode("\r\n", $headers));
        }
        
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error booking appointment: ' . $e->getMessage()]);
    }
}
?>
