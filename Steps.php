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
    <?php include("NavBar.php");
    
        if (!isset($_SESSION['Date'])) {
            echo "No date Selected";
            exit;
        }
        else{
            $newDate = $_SESSION['Date']; // retrieves the selected date (from navbar)
        }

        if (!isset($_SESSION['Dog'])) {
            echo "No dog Selected";
            exit;
        }
        else{
            $dogID = $_SESSION['Dog']; // retrieves the selected dog (from navbar)
        }
    ?>
    
    <h2>Here is <?php echo $dogID; ?>'s Info:</h2> <br>


    <div class = "graphText">
    <?php

    $db = new SQLite3('ElancoDB.db');
    $activityLevelData = [];
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

        // Fetch steps for the given date
        $query = $db->prepare('SELECT Activity_Level FROM Activity WHERE Date = :newDate AND Hour >= 0 AND Hour <= 23 AND DogID = :dogID');
        $query->bindValue(':newDate', $newDate, SQLITE3_TEXT);
        $query->bindValue(':dogID', $dogID, SQLITE3_TEXT);
        $result = $query->execute();

        // Check if the query executed successfully
        if (!$result) {
            echo "Error executing query for steps.";
            exit();
        }

        if ($result->numColumns() == 0) {
            echo "No steps data found for the selected date: " . $newDate;
            exit();
        }

        // Populate $activityLevelData array with breathing rates
        while($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $activityLevelData[] = $row['Activity_Level'];
        }

        $prevEmpty = 0; // used to find if there is a range of hours where the dog does not complete any steps
        $count = 0; // indexing array
        $dataToDelete = []; // array used to store which indexes of the $activityLevelData array should be deleted
        $hours = [];

        while ($count < count($activityLevelData)) { // loop though the $activityLevelData array
            if ($activityLevelData[$count] == 0) { // if the dog completes 0 steps that hour
                $prevEmpty++; 

                if ($count == 23) { // error handling if the dog completes 0 steps in the final hour of the day
                    if ($prevEmpty == 1) {
                        $hours[] = $count;
                    } 
                    else {
                        $startNum = ($count - $prevEmpty) + 1;
                        $hours[] = $startNum . " - " . ($count); // creates a range of hours where 0 steps are completed
                    }
                } 
                else {
                    if ($count + 1 < count($activityLevelData) && $activityLevelData[$count + 1] == 0) {
                        $dataToDelete[] = $count;
                    } // only adds to the $dataToDelete array if the next hour is also 0
                }
            } else {
                if ($prevEmpty > 2) {
                    $startNum = $count - $prevEmpty;
                    $hours[] = $startNum . " - " . ($count - 1); // creates a range of hours where 0 steps are completed
                } 
                else if ($prevEmpty == 1 || $prevEmpty == 2) {
                    for ($i = $count - $prevEmpty; $i < $count; $i++) {
                        $hours[] = $i;
                    } // retains the hour when only 1 or 2 hours have 0 steps
                }

                $hours[] = $count;
                $prevEmpty = 0;
            }

            $count++;
        }

        // delete data in reverse order to avoid index shifting - have to do this in a diffrent loop otherwise it would break the first while loop
        foreach (array_reverse($dataToDelete) as $index) {
            array_splice($activityLevelData, $index, 1);
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
            loadBarChart(
                'barChart', // chart ID
                <?php echo json_encode($activityLevelData); ?>, // dataset to be displayed as the line
                <?php echo json_encode($behaviourData); ?>, // dataset to be displayed when hoverin over a point on the graph
                <?php echo json_encode($hours); ?>,
                'Activity Level', // line label
                'Steps', // y axes label
                'Hour', // x axes label
                'Activity: ' // label for the dataset when hovering over a point on the graph
            );
        };
    </script>

    <div class="chart">
        <canvas id="barChart" style="width:100%;max-width:700px;"></canvas>
    </div>
</body>

</html>