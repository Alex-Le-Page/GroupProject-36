<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="Chart.js"></script>
    <title>HeartRate</title>

    <style>
       
       form :not(.calendar){
            float: left;
            margin-top: 10%;
            margin-left: 70%;
            
            border-color: black;
            padding: 8px;
            text-align: left;
            width: 300px;
            padding: 20px;
            background: lightblue;
            border-radius: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
            
        .chart:first-of-type {
        position: relative;
        width: 700px;
        display: block;
    }

    .chart:first-of-type canvas {
        height: 300px !important; /* Reduce the height while maintaining width */
        width: 100% !important;
    }

    .chart:last-of-type {
        position: absolute;
        top: 400px;     
        left: 850px;    
        width: 200px;
        height: 200px;
    }

    /* Optional hover effect */
    .chart:last-of-type svg {
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .chart:last-of-type svg:hover {
        transform: scale(1.1);
        filter: drop-shadow(0 0 8px rgba(76, 175, 80, 0.6));
    }
    </style>

</head>
<body>
    <div class="NavBar">
    <?php include("NavBar.php");

if (!isset($_SESSION['Date'])) {
    echo "No date Selected";
    exit;
}
else{
    $newDate = $_SESSION['Date']; // retrieves the selected date (from navbar)
    $newTime = $_SESSION['Hour'];
}

if (!isset($_SESSION['Dog'])) {
    echo "No dog Selected";
    exit;
}
else{
    $dogID = $_SESSION['Dog']; // retrieves the selected dog (from navbar)
}
    ?>
    </div>
    
    <h2>Here is <?php echo $dogID; ?>'s info for Heart Rate:</h2> <br>

    <div class="main">
        <form>
            <label>Your dog's heart rate is above average</label>
        </form>
    </div>
    
    <div class = "graphText">
    <?php 

    if (!isset($_SESSION['Date'])) {
        echo "No date Selected";
        exit;
    }
    $newDate = $_SESSION['Date']; // retrieves the selected date (from navbar)

    $db = new SQLite3('ElancoDB.db');
    $heartData = [];
    $behaviourData = [];

    // Get the date the user entered in the form 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['day'])) {
        $newDate = $_POST['day'];
    }

    if ($newDate != null) {

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
        echo "Selected Date: " . $newDate ."<br>";

       // Fetch heart rates for the given date
       $query = $db->prepare('SELECT Heart_Rate FROM Activity WHERE Date = :newDate AND Hour >= 0 AND Hour <= 23 AND DogID = :dogID');
       $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
       $query->bindValue(':dogID', $dogID, SQLITE3_TEXT);
       $result = $query->execute();

       // Check if the query executed successfully
       if (!$result) {
           echo "Error executing query for heart rates.";
           exit();
       }

       if ($result->numColumns() == 0) {
           echo "No heart rate data found for the selected date: " . $newDate;
           exit();
       }

       // Populate $heartData array with heart rates
       while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
           $heartData[] = $row['Heart_Rate'];
       }

       // Fetch behaviour patterns for the given date
       $query = $db->prepare('
       SELECT Behaviour.Behaviour_Pattern 
       FROM Activity 
       INNER JOIN Behaviour ON Activity.BehaviourID = Behaviour.BehaviourID
       WHERE Date = :newDate 
       AND Hour >= 0 AND Hour <= 23 
       AND DogID = :dogID
       ');

       $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
       $query->bindValue(':dogID', $dogID, SQLITE3_TEXT);
       $result = $query->execute();

       // Check if the query executed successfully
       if (!$result) {
           echo "Error executing query for behaviour patterns.";
           exit();
       }

       if ($result->numColumns() == 0) {
           echo "No behaviour patterns found for the selected date: " . $newDate;
           exit();
       }

       // Populate $behaviourData array with heart rates
       while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
           $behaviourData[] = $row['Behaviour_Pattern'];
       }
   } else {
       echo "Invalid or missing date input.";
       exit();
   }

   require_once 'UpperLowerBoundFunctions.php';

   $heartRate = 0;
    $heartColour = '#4CAF50';
    $statusText = '';
    $recordCount = 0;
    $arrangedDataset = [];

    try {
        // get all heart rates for the day to calculate bounds
    $allDayQuery = "SELECT Heart_Rate
    FROM Activity
    WHERE Date = :selectedDate";

    $allDayStmt = $db->prepare($allDayQuery);
    $allDayStmt->bindValue(':selectedDate', $newDate, SQLITE3_TEXT);
    $allDayResult = $allDayStmt->execute();

    // populate array
    while ($row = $allDayResult->fetchArray(SQLITE3_ASSOC)) {
    $arrangedDataset[] = $row['Heart_Rate'];
    }

    $recordCount = count($arrangedDataset);

    // get the heart rate for the specific hour if provided
    if ($newTime !== null) {
        $hourQuery = "SELECT Heart_Rate
        FROM Activity
        WHERE Date = :selectedDate 
        AND Hour = :selectedHour";

    $hourStmt = $db->prepare($hourQuery);
    $hourStmt->bindValue(':selectedDate', $newDate, SQLITE3_TEXT);
    $hourStmt->bindValue(':selectedHour', $newTime, SQLITE3_INTEGER);
    $hourResult = $hourStmt->execute();

    $hourRow = $hourResult->fetchArray(SQLITE3_ASSOC);

    // if we found a heart rate for the specific hour, use it
    if ($hourRow) {
        $heartRate = $hourRow['Heart_Rate'];
        } elseif ($recordCount > 0) {
        // if no heart rate for specific hour but we have data for the day,
        // use the highest heart rate of the day
        $heartRate = max($arrangedDataset);
        $statusText = 'No data for hour ' . $newTime . '. Showing highest heart rate for the day.';
        }
        } elseif ($recordCount > 0) {
        // no hour selected, use the highest heart rate for the day
        $heartRate = max($arrangedDataset);
        }

        // calculate bounds only if we have records
        if ($recordCount > 0) {
        // calculate the bounds using our imported functions
            $lowerBound = FindLowerBound($arrangedDataset);
            $upperBound = FindUpperBound($arrangedDataset);

        // set status text if it wasn't set by the hour check
        if (empty($statusText)) {
        // logic to decide the status based on calculated bounds
            if ($heartRate > $upperBound) {
            $heartColour = '#FF9C09'; 
            $statusText = 'Alert: Heart rate above upper bound';
            } elseif ($heartRate < $lowerBound) {
            $heartColour = '#FF9C09'; 
            $statusText = 'Alert: Heart rate below lower bound';
            } else {
            $heartColour = '#4CAF50';
            $statusText = 'Normal heart rate (within bounds)';
            }
        }
        } else {
            $statusText = 'No data found for selected date';
        }
    } catch (Exception $e) {
        $statusText = 'Error: ' . $e->getMessage(); // catch to see if any errors occur
    }

    $db->close();
    ?>
    </div>

    <script>
    window.onload = function() {
        loadLineGraph(
            'lineGraph', // chart ID
            <?php echo json_encode($heartData); ?>, // dataset to be displayed as the line
            <?php echo json_encode($behaviourData); ?>, // dataset to be displayed when hoverin over a point on the graph
            'Heart Rate', // line label
            'Beats / Minute', // y axes label
            'Hour', // x axes label
            'Activity: ', // label for the dataset when hovering over a point on the graph
            [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23] //x axis labels 
        );
    };
    </script>

    <div class="chart">
        <canvas id="lineGraph" style="width:100%;max-width:700px;"></canvas>
    </div>
    <div class="chart">
        <div style="text-align: center; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <svg width="420" height="330" viewBox="0 0 100 90">
                <!-- heart path for heart shape -->
                <path d="M50,30 C60,10 90,10 90,40 C90,65 50,85 50,85 C50,85 10,65 10,40 C10,10 40,10 50,30 Z" 
                    style="fill: <?php echo $heartColour; ?>;" />
                    <!-- text for the heart rate in bpm inside heart -->
                <text x="50" y="55" text-anchor="middle" fill="white" font-weight="bold" font-size="14">
                    <?php echo $heartRate; ?>
                </text>
                <text x="50" y="70" text-anchor="middle" fill="white" font-size="10">
                    BPM
                </text>
            </svg>
        </div>
    </div>

</body>

</html>
    
</body>
</html>