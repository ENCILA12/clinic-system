<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
header('Content-Type: application/json');

try {
    $isDentist = ($_SESSION['role'] === 'Dentist');
    $dentistName = $_SESSION['username'];
    
    $events = [];

    // Base query
    $sql = "
        SELECT a.*, p.full_name 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.patient_id
    ";
    
    // Filter by dentist if it's a dentist logged in
    $params = [];
    if ($isDentist) {
        $sql .= " WHERE a.dentist_name = ?";
        $params[] = $dentistName;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($appointments as $apt) {
        // Determine color based on status
        $color = '#3b82f6'; // Default Blue (In Treatment / Normal)
        $status = $apt['status'];

        if ($status === 'Pending') {
            $color = '#f59e0b'; // Orange
        } elseif ($status === 'Confirmed') {
            $color = '#10b981'; // Green
        } elseif ($status === 'Completed') {
            $color = '#64748b'; // Gray
        } elseif ($status === 'Cancelled' || $status === 'No Show') {
            $color = '#ef4444'; // Red
        } elseif ($status === 'Waiting') {
            $color = '#8b5cf6'; // Purple
        }

        // FullCalendar Event Format
        $events[] = [
            'id' => $apt['id'],
            'title' => $apt['full_name'] . ' (' . $apt['procedure_name'] . ')',
            'start' => $apt['appointment_date'] . 'T' . $apt['appointment_time'],
            'color' => $color,
            'extendedProps' => [
                'status' => $status,
                'dentist' => $apt['dentist_name'],
                'notes' => $apt['notes'],
                'duration' => $apt['estimated_duration']
            ]
        ];
    }

    echo json_encode($events);

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
