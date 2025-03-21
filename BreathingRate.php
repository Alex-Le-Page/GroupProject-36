<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="Chart.js"></script>
    <title>Document</title>

    <style>
        div.graphText{
            margin-top: 5%;
        }
    </style>
</head>

<body>
    <?php include("NavBar.php");?>
    
    <h2>Here is Cainine001's Info:</h2> <br>


    <div class = "graphText">
    <?php
    
    if (!isset($_SESSION['Date'])) {
        echo "No date Selected";
        exit;
    }
    else{
        $newDate = $_SESSION['Date']; // retrieves the selected date (from navbar)
    }

    $db = new SQLite3('ElancoDB.db');
    $breathingData = [];
    $behaviourData = [];


    // Get the row number of the date the user enters
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

        // Fetch breathing rates for the given date
        $query = $db->prepare('SELECT Breathing_Rate FROM Activity WHERE Date = :newDate AND Hour >= 0 AND Hour <= 23 AND DogID = "CANINE001"');
        $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
        $result = $query->execute();

        // Check if the query executed successfully
        if (!$result) {
            echo "Error executing query for breathing rates.";
            exit();
        }

        if ($result->numColumns() == 0) {
            echo "No breathing rate data found for the selected date: " . $newDate;
            exit();
        }

        // Populate $breathingData array with breathing rates
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $breathingData[] = $row['Breathing_Rate'];
        }

        // Fetch behaviour patterns for the given date
        $query = $db->prepare('
        SELECT Behaviour.Behaviour_Pattern 
        FROM Activity 
        INNER JOIN Behaviour ON Activity.BehaviourID = Behaviour.BehaviourID
        WHERE Date = :newDate 
        AND Hour >= 0 AND Hour <= 23 
        AND DogID = "CANINE001"
        ');

        $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
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

        // Populate $behaviourData array with breathing rates
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $behaviourData[] = $row['Behaviour_Pattern'];
        }
    } else {
        echo "Invalid or missing date input.";
        exit();
    }

    $db->close();
    ?>
    </div>

    <script>
        window.onload = function() {
            loadLineGraph(
                'lineGraph', // chart ID
                <?php echo json_encode($breathingData); ?>, // dataset to be displayed as the line
                <?php echo json_encode($behaviourData); ?>, // dataset to be displayed when hoverin over a point on the graph
                'Breathing Rate', // line label
                'Breaths / Minute', // y axes label
                'Hour', // x axes label
                'Activity: ' // label for the dataset when hovering over a point on the graph
            );
        };
    </script>

    <div class="chart">
        <canvas id="lineGraph" style="width:100%;max-width:700px;"></canvas>
    </div>
</body>

</html>