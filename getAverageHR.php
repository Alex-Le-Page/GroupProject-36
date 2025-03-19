<?php
// get_monthly_average.php - Returns the average heart rate for the current month

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Connect to SQLite database
    $db = new SQLite3('ElancoDB.db');
    
    // Get current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    // Query to get average heart rate for the current month
    // This assumes your Date column is in format DD-MM-YYYY
    $query = "SELECT AVG(`Heart Rate (bpm)`) as averageHeartRate
              FROM activityData 
              WHERE substr(Date, 4, 2) = :month 
              AND substr(Date, 7, 4) = :year";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':month', $currentMonth, SQLITE3_TEXT);
    $stmt->bindValue(':year', $currentYear, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row && $row['averageHeartRate'] !== null) {
        echo json_encode([
            'averageHeartRate' => (float)$row['averageHeartRate'],
            'month' => $currentMonth,
            'year' => $currentYear
        ]);
    } else {
        // If no data for current month, try the previous month
        $prevMonth = $currentMonth == '01' ? '12' : sprintf('%02d', $currentMonth - 1);
        $prevYear = $currentMonth == '01' ? $currentYear - 1 : $currentYear;
        
        $query = "SELECT AVG(`Heart Rate (bpm)`) as averageHeartRate
                  FROM activityData 
                  WHERE substr(Date, 4, 2) = :month 
                  AND substr(Date, 7, 4) = :year";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':month', $prevMonth, SQLITE3_TEXT);
        $stmt->bindValue(':year', $prevYear, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($row && $row['averageHeartRate'] !== null) {
            echo json_encode([
                'averageHeartRate' => (float)$row['averageHeartRate'],
                'month' => $prevMonth,
                'year' => $prevYear,
                'note' => 'Using previous month data'
            ]);
        } else {
            echo json_encode([
                'averageHeartRate' => 110.0, // Default value if no data found
                'month' => $currentMonth,
                'year' => $currentYear,
                'note' => 'No data available, using default value'
            ]);
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'averageHeartRate' => 110.0 // Default value if error occurs
    ]);
}

// Close the database connection
$db->close();
?>