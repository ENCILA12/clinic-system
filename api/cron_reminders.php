<?php
// Script to be run via Cron Job (e.g., daily at 8:00 AM)
// URL: http://yourdomain.com/api/cron_reminders.php

require_once '../includes/db.php';

echo "Starting Automated Email Reminders...<br>\n";

try {
    // Get appointments scheduled for exactly tomorrow
    $stmt = $pdo->prepare("
        SELECT a.id as appointment_id, a.appointment_date, a.appointment_time, a.procedure_name, a.dentist_name, a.status,
               p.full_name, p.email,
               c.name as clinic_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN clinics c ON a.clinic_id = c.id
        WHERE a.appointment_date = CURDATE() + INTERVAL 1 DAY
          AND a.status IN ('Pending', 'Confirmed')
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($appointments) === 0) {
        echo "No appointments found for tomorrow.<br>\n";
    } else {
        $sentCount = 0;
        foreach ($appointments as $apt) {
            $email = $apt['email'];
            $name = $apt['full_name'];
            $clinic = $apt['clinic_name'];
            $date = date('F d, Y', strtotime($apt['appointment_date']));
            $time = date('h:i A', strtotime($apt['appointment_time']));
            $dentist = $apt['dentist_name'];
            $procedure = $apt['procedure_name'];

            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                
                $subject = "Appointment Reminder: $clinic";
                $message = "
                <html>
                <head>
                  <title>Appointment Reminder</title>
                </head>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                  <div style='max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                      <div style='background-color: #2563eb; color: white; padding: 20px; text-align: center;'>
                          <h2 style='margin: 0;'>Appointment Reminder</h2>
                      </div>
                      <div style='padding: 20px;'>
                          <p>Hi <strong>$name</strong>,</p>
                          <p>This is a friendly reminder from <strong>$clinic</strong> about your upcoming dental appointment tomorrow.</p>
                          
                          <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                              <h3 style='margin-top: 0; color: #1e293b;'>Appointment Details:</h3>
                              <ul style='list-style-type: none; padding: 0; margin: 0;'>
                                <li style='margin-bottom: 8px;'><strong>📅 Date:</strong> $date</li>
                                <li style='margin-bottom: 8px;'><strong>⏰ Time:</strong> $time</li>
                                <li style='margin-bottom: 8px;'><strong>🧑‍⚕️ Dentist:</strong> Dr. $dentist</li>
                                <li style='margin-bottom: 0;'><strong>🦷 Procedure:</strong> $procedure</li>
                              </ul>
                          </div>
                          
                          <p style='color: #ef4444; font-weight: 500;'>Please arrive 10 minutes early. If you need to reschedule, please contact the clinic immediately.</p>
                          <p>Thank you and see you soon!</p>
                      </div>
                      <div style='background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;'>
                          Powered by DentaFlow
                      </div>
                  </div>
                </body>
                </html>
                ";

                // To send HTML mail, the Content-type header must be set
                $headers = array();
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                $headers[] = 'From: ' . $clinic . ' <no-reply@dentaflow.com>';

                // Send Email
                $mailSent = @mail($email, $subject, $message, implode("\r\n", $headers));
                
                if ($mailSent) {
                    echo "Reminder sent successfully to $name ($email).<br>\n";
                    $sentCount++;
                } else {
                    echo "Failed to send reminder to $name ($email). Ensure SMTP is configured.<br>\n";
                }
            } else {
                echo "Skipped $name: No valid email address provided.<br>\n";
            }
        }
        
        echo "Finished. Total reminders sent: $sentCount.<br>\n";
    }
} catch (PDOException $e) {
    echo "Error executing reminder script: " . $e->getMessage() . "<br>\n";
}
?>
