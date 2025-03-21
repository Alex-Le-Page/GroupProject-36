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
    </style>

</head>
<body>
    <div class="NavBar">
        <?php include("NavBar.php") ?>
    </div>
    
    <h2>Heart Rate</h2>

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
       $query = $db->prepare('SELECT Heart_Rate FROM Activity WHERE Date = :newDate AND Hour >= 0 AND Hour <= 23 AND DogID = "CANINE001"');
       $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
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

       // Populate $behaviourData array with heart rates
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
            <?php echo json_encode($heartData); ?>, // dataset to be displayed as the line
            <?php echo json_encode($behaviourData); ?>, // dataset to be displayed when hoverin over a point on the graph
            'Heart Rate', // line label
            'Beats / Minute', // y axes label
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
    
</body>
</html>