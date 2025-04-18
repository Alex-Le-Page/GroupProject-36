<?php 
$db = new SQLite3('ElancoDB.db');
$newDate = null;
$behaviourData = [];
$dogID = isset($_POST['dog']) ? $_POST['dog'] : 'CANINE001';

// Get the date the user entered in the form 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['day'])) {
    $newDate = $_POST['day'];
}

// If no date is provided, get the first date from the database
if ($newDate == null || empty($newDate)) {
    // Query to get the first date in the database
    $firstDateQuery = $db->query('SELECT DISTINCT Date FROM Activity ORDER BY Date ASC LIMIT 1');
    if ($firstDateQuery) {
        $firstDateRow = $firstDateQuery->fetchArray(SQLITE3_ASSOC);
        if ($firstDateRow) {
            $newDate = $firstDateRow['Date'];
        }
    }
    
    // If still no date (maybe database issue), default to a known date
    if ($newDate == null || empty($newDate)) {
        $newDate = "01-01-2021"; // Default to this date if query fails
    }
}

// Now proceed with your existing code using the $newDate
$rowID = $db->prepare('
WITH cte AS (
    SELECT Date, ROW_NUMBER() OVER() AS row_num 
    FROM (SELECT DISTINCT Date FROM Activity)
)
SELECT row_num FROM cte WHERE Date=:newDate');

$rowID->bindValue(":newDate", $newDate, SQLITE3_TEXT);
$rowResult = $rowID->execute();

// Check if query execution is successful
if (!$rowResult) {
    echo "Error fetching row number for the selected date.";
    exit();
}

$row = $rowResult->fetchArray(SQLITE3_ASSOC);
if ($row === false) {
    echo "No data found for the selected date: " . $newDate;
    exit();
}

// Get the previous date (row number - 1)
if ($row != null) {
    $currentRow = $row['row_num'];
    $currentRow = $currentRow - 1;

    $prevQuery = $db->prepare('
    WITH cte AS (
        SELECT Date, ROW_NUMBER() OVER() AS row_num 
        FROM (SELECT DISTINCT Date FROM Activity)
    )
    SELECT Date FROM cte WHERE row_num=:row_num');

    $prevQuery->bindValue(":row_num", $currentRow, SQLITE3_INTEGER);
    $prevResult = $prevQuery->execute();

    // Check if previous date query execution is successful
    if (!$prevResult) {
        echo "Error fetching previous date.";
        exit();
    }

    $prevRow = $prevResult->fetchArray(SQLITE3_ASSOC);
    $prevDate = $prevRow['Date'] ?? null;
}

// Get the next date (row number + 1)
if ($row != null) {
    $currentRow = $row['row_num'];
    $currentRow = $currentRow + 1;

    $nextQuery = $db->prepare('
    WITH cte AS (
        SELECT Date, ROW_NUMBER() OVER() AS row_num 
        FROM (SELECT DISTINCT Date FROM Activity)
    )
    SELECT Date FROM cte WHERE row_num=:row_num');

    $nextQuery->bindValue(":row_num", $currentRow, SQLITE3_INTEGER);
    $nextResult = $nextQuery->execute();

    // Check if next date query execution is successful
    if (!$nextResult) {
        echo "Error fetching next date.";
        exit();
    }

    $nextRow = $nextResult->fetchArray(SQLITE3_ASSOC);
    $nextDate = $nextRow['Date'] ?? null;
}

// Fetch behaviour patterns for the given date and dog
$query = $db->prepare('
SELECT Hour, Behaviour_Pattern 
FROM Activity 
JOIN Behaviour ON Activity.BehaviourID = Behaviour.BehaviourID
WHERE Date = :newDate 
AND Hour >= 0 AND Hour <= 23 
AND DogID = :dogID
ORDER BY Hour
');

$query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
$query->bindValue(':dogID', $dogID, SQLITE3_TEXT);
$result = $query->execute();

// Check if the query executed successfully
if (!$result) {
    echo "Error executing query for behaviour patterns.";
    exit();
}

// Populate $behaviourData array with behavior patterns
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $behaviourData[] = $row['Behaviour_Pattern'];
}

if (count($behaviourData) == 0) {
    echo "No behaviour data found for the selected date and dog.";
    exit();
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="newchart.js"></script>
    <title>Pet Behavior Chart</title>
    <style>
        form :not(.calendar){
            float: right;
            margin-top: 5%;
            margin-right: 15%;
            
            border-color: black;
            padding: 8px;
            text-align: center;
            width: 225px;
            padding: 20px;
            background: lightblue;
            border-radius: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }
        h1 {
            margin: 30px;
            color: #333;
            text-align: center;
            margin-bottom: 15px;
        }
        h3 {
            color: white;
        }
        p {
            color: white;
        }
        .chart-container {
            height: 400px;
            margin: 20px 0;
        }
        .controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .date-nav {
            display: flex;
            align-items: center;
        }
        .date-nav form {
            margin: 10px 10px;
        }
        .dog-select {
            display: flex;
            align-items: center;
        }
        input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 5px;
        }
        .explanation {
            background-color: #0E253E;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .explanation ul {
            background-color: transparent;
            border: none;
            list-style-type: disc;
            padding-left: 20px;
            }

            .explanation li {
            float: none;
            background-color: transparent;
            color: white;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="NavBar">
        <?php include("NavBar.php") ?>
    </div>
    <h2>Behaviour Pattern</h2>
    <div class="container">
        <h1>Dog Behavior Distribution</h1>

        <div class="date-info">
        Selected Date: <?php echo $newDate; ?><br>
        Selected Dog: Dog 1
        </div>
        
        
        <div class="chart-container">
            <canvas id="behaviorPieChart"></canvas>
        </div>
        
        <div class="explanation">
            <h3>What This Means For Pet Owners:</h3>
            <p>This pie chart shows how your dog spends their day. Each slice represents the percentage of time your dog spends in different activities:</p>
        </div>
    </div>

    <!-- Include the JavaScript file with the pie chart function -->
    <script>
    // Include the loadBehaviorPieChart function
    function loadBehaviorPieChart(canvasId, behaviorDataset, graphLabel) {
        if (!behaviorDataset || !behaviorDataset.length) {
            console.error("Behavior data is missing for the selected date.");
            return;
        } else {
            // Count occurrences of each behavior
            const behaviorCounts = {};
            
            // Process behavior data
            for (let i = 0; i < behaviorDataset.length; i++) {
                const behavior = behaviorDataset[i];
                
                if (behavior && behavior !== "") {
                    if (!behaviorCounts[behavior]) {
                        behaviorCounts[behavior] = 0;
                    }
                    behaviorCounts[behavior]++;
                }
            }
            
            // Prepare data for the pie chart
            const pieLabels = Object.keys(behaviorCounts);
            const pieData = pieLabels.map(label => behaviorCounts[label]);
            
            const backgroundColors = [
                'rgba(255, 99, 132, 0.8)',   // Red
                'rgba(54, 162, 235, 0.8)',   // Blue
                'rgba(255, 206, 86, 0.8)',   // Yellow
                'rgba(75, 192, 192, 0.8)',   // Green
                'rgba(153, 102, 255, 0.8)',  // Purple
                'rgba(255, 159, 64, 0.8)'    // Orange
            ];
            
            const ctx = document.getElementById(canvasId);
            
            // Make sure the canvas exists
            if (!ctx) {
                console.error("Canvas element not found:", canvasId);
                return;
            } 
            
            return new Chart(ctx, {
                type: "pie",
                data: {
                    labels: pieLabels,
                    datasets: [{
                        backgroundColor: backgroundColors.slice(0, pieLabels.length),
                        data: pieData
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: graphLabel,
                        fontSize: 16
                    },
                    legend: {
                        position: 'right'
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const behavior = data.labels[tooltipItem.index];
                                const count = data.datasets[0].data[tooltipItem.index];
                                const total = pieData.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((count / total) * 100);
                                
                                return [
                                    behavior + ": " + count + " hours",
                                    percentage + "% of the day"
                                ];
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize the chart when the page loads
    window.onload = function() {
        console.log("Window loaded, initializing chart");
        const behaviorData = <?php echo json_encode($behaviourData); ?>;
        console.log("Behavior data:", behaviorData);
        
        loadBehaviorPieChart(
            'behaviorPieChart',
            behaviorData,
            'Daily Behavior Distribution for <?php echo ($dogID == "CANINE001" ? "Dog 1" : ($dogID == "CANINE002" ? "Dog 2" : "Dog 3")); ?>'
        );
    };
    </script>
</body>
</html>

    