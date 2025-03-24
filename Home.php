<?php 

    require_once 'UpperLowerBoundFunctions.php';
    include("NavBar.php");
    
        $db = new SQLite3('ElancoDB.db');

        //get date selected from the navbar callender
        if (!isset($_SESSION['Date'])) {
            echo "No date Selected";
            exit;
        }
        else{
            $calDate = $_SESSION['Date']; // retrieves the selected date and time (from navbar)
            $calTime = $_SESSION['Hour'];
        }

        if (!isset($_SESSION['Dog'])) {
            echo "No dog Selected";
            exit;
        }
        else{
            $dogID = $_SESSION['Dog']; // retrieves the selected dog (from navbar)
        }
            
        //run a select statement to get the water intake of the dog throughout the day 
        $query = $db->prepare('SELECT 
        AVG(Weight) AS avgWeight, 
        SUM(Water_Intake) AS totalIntake, 
        SUM(Calorie_Burn) AS totalBurnt, 
        SUM(Activity_Level)AS steps, 
        SUM(Food_Intake) AS totalCalories 
        FROM Activity WHERE Hour <= :calTime AND DogID = :dogID AND Date = :calDate');
        $query->bindValue(":calTime", $calTime, SQLITE3_INTEGER);
        $query->bindValue(":calDate", $calDate, SQLITE3_TEXT);
        $query->bindValue(':dogID', $dogID, SQLITE3_TEXT);
        $result = $query->execute();

        $row = $result->fetchArray(SQLITE3_ASSOC);

        //store data from the query into variables
        $totalIntake = round($row['totalIntake']);
        $weight = $row['avgWeight'];
        $totalBurnt = round($row['totalBurnt']);
        $totalSteps = $row['steps'];
        $totalCalories = round($row['totalCalories']);


        //calculate the goals for the dog
        $totalMl = round($weight * 60);
        $burntGoal = round(($weight * 2.2) * 30);
        $calorieGoal = round(pow($weight, 0.75) * 70);


        //check if the water intake goal has been hit 
        if ($totalIntake > $totalMl){
            $intakeLeft = 0;
        } else{
            //if the goal hasn't been hit then calculate the ammount left
            $intakeLeft = $totalMl - $totalIntake;
        }

        if($totalSteps > 8000){
            $stepsLeft = 0;
        } else{
            $stepsLeft = 8000 - $totalSteps;
        }

        if($totalBurnt > $burntGoal){
            $burntLeft = 0;
        } else{
            $burntLeft = $burntGoal - $totalBurnt;
        }

        if($totalCalories > $calorieGoal){
            $caloriesLeft = 0;
        } else{
            $caloriesLeft = $calorieGoal - $totalCalories;
        }

    //get the time from when the user opens the website
    $currentTime = date("G");

    //account for databases time format (starting at 0)
    $currentTime -= 1;

    // use the date and hour from the session
    $selectedDate = isset($_SESSION['Date']) ? $_SESSION['Date'] : date('d-m-Y');
    $selectedHour = isset($_SESSION['Hour']) ? $_SESSION['Hour'] : null;
        
    //run a select statement to get the water intake of the dog throughout the day 
    $query = $db->prepare('SELECT AVG(Weight) AS avgWeight, SUM(Water_Intake) AS totalIntake, SUM(Calorie_Burn) AS totalBurnt, SUM(Activity_Level)AS steps, SUM(Food_Intake) AS totalCalories FROM Activity WHERE Hour <= :currentTime AND DogID = "CANINE001" AND Date = :selectedDate');
    $query->bindValue(":currentTime", $currentTime, SQLITE3_INTEGER);
    $query->bindValue(":selectedDate", $selectedDate, SQLITE3_TEXT);
    $result = $query->execute();

    $row = $result->fetchArray(SQLITE3_ASSOC);

    //store data from the query into variables
    $totalIntake = round($row['totalIntake']);
    $weight = $row['avgWeight'];
    $totalBurnt = round($row['totalBurnt']);
    $totalSteps = $row['steps'];
    $totalCalories = round($row['totalCalories']);


    //calculate the goals for the dog
    $totalMl = round($weight * 60);
    $burntGoal = round(($weight * 2.2) * 30);
    $calorieGoal = round(pow($weight, 0.75) * 70);


    //check if the water intake goal has been hit 
    if ($totalIntake > $totalMl){
        $intakeLeft = 0;
    } else{
        //if the goal hasn't been hit then calculate the ammount left
        $intakeLeft = $totalMl - $totalIntake;
    }

    if($totalSteps > 8000){
        $stepsLeft = 0;
    } else{
        $stepsLeft = 8000 - $totalSteps;
    }

    if($totalBurnt > $burntGoal){
        $burntLeft = 0;
    } else{
        $burntLeft = $burntGoal - $totalBurnt;
    }

    if($totalCalories > $calorieGoal){
        $caloriesLeft = 0;
    } else{
        $caloriesLeft = $calorieGoal - $totalCalories;
    }

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
    $allDayStmt->bindValue(':selectedDate', $selectedDate, SQLITE3_TEXT);
    $allDayResult = $allDayStmt->execute();

    // collect all heart rates for the day
    while ($row = $allDayResult->fetchArray(SQLITE3_ASSOC)) {
    $arrangedDataset[] = $row['Heart_Rate'];
    }

    $recordCount = count($arrangedDataset);

    // get the heart rate for the specific hour if provided
    if ($selectedHour !== null) {
        $hourQuery = "SELECT Heart_Rate
        FROM Activity
        WHERE Date = :selectedDate 
        AND Hour = :selectedHour";

    $hourStmt = $db->prepare($hourQuery);
    $hourStmt->bindValue(':selectedDate', $selectedDate, SQLITE3_TEXT);
    $hourStmt->bindValue(':selectedHour', $selectedHour, SQLITE3_INTEGER);
    $hourResult = $hourStmt->execute();

    $hourRow = $hourResult->fetchArray(SQLITE3_ASSOC);

    // if we found a heart rate for the specific hour, use it
    if ($hourRow) {
        $heartRate = $hourRow['Heart_Rate'];
        } elseif ($recordCount > 0) {
        // if no heart rate for specific hour but we have data for the day,
        // use the highest heart rate of the day
        $heartRate = max($arrangedDataset);
        $statusText = 'No data for hour ' . $selectedHour . '. Showing highest heart rate for the day.';
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
            $heartColour = '#F44336'; 
            $statusText = 'Alert: Heart rate above upper bound';
            } elseif ($heartRate < $lowerBound) {
            $heartColour = '#FFC107'; 
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
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-doughnutlabel"></script>
    <script src="chart.js"></script>
    <style>
        form.notes {
            float: left;
            margin-top: 5%;
            margin-left: 15%;

            border-color: black;
            padding: 8px;
            text-align: left;
            width: 300px;
            padding: 20px;
            background: lightblue;
            border-radius: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        select {
            background-color: white;
            width: 100%;
            border-color: #0E253E;
            border-radius: 10px;
            text-align: center;
        }

        li.dogOption {
            margin-top: 14px;
        }

        .warning{
            position: absolute;
            display: flex;
            top: 50px;
            right: 20px;
            font-size: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-style: italic;
        }

        .charts {
            margin-top: 50px;
            width: 500px;
            height: 500px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
        }

        #heart {
            position: relative;
            width: 100px;
            height: 90px;
            margin-top: 10px;
        }

        #heart::before, #heart::after {
            content: "";
            position: absolute;
            top: 0;
            width: 52px;
            height: 80px;
            border-radius: 50px 50px 0 0;
            background: <?php echo $heartColour; ?>
        }

        #heart::before {
            left: 50px;
            transform: rotate(-45deg);
            transform-origin: 0 100%;
        }

        #heart::after {
            left: 0;
            transform: rotate(45deg);
            transform-origin: 100% 100%;
        }
    </style>
</head>

<body>
    <?php echo "Selected date: " . $selectedDate; ?>
    <?php echo "Records found: " . $recordCount; ?>
    <h2>Here is Cainine001's Info:</h2>
    
    <h2>Here is <?php echo $dogID; ?>'s Summary:</h2>
    <div class="Main">

        <form class = "notes">
            <label>Heart-Rate: <?php echo $heartRate; ?></label>
            <br><br>
            <label>Behaviour Pattern: Normal</label>
            <br><br>
            <label>Weight: 29.8kg</label>
            <br><br>
            <label>Temperature: 28.5 C</label>
        </form>

    </div>

    <div class="warning">
        <h1>*Please note all goals are a general calculations and may not be specific to your dog</h1>
    </div>

    <script>
    window.onload = function() {
        loadDoughChart('doughChart', <?php echo json_encode($totalIntake)?>, <?php echo json_encode($intakeLeft)?>, 'Water Intake(ml)', <?php echo json_encode($totalMl)?>);
        loadDoughChart('caloriesBurnt', <?php echo json_encode($totalBurnt)?>, <?php echo json_encode($burntLeft)?>, 'Calories Burnt', <?php echo json_encode($burntGoal)?>);
        loadDoughChart('calorieIntake', <?php echo json_encode($totalCalories)?>, <?php echo json_encode($caloriesLeft)?>, 'Calorie Intake', <?php echo json_encode($calorieGoal)?>);
        loadDoughChart('steps', <?php echo json_encode($totalSteps) ?>, <?php echo json_encode($stepsLeft)?>, 'Step Goal', 8000);
    }
    </script>

    <!--add any more graphs into the charts class to have it be apart of the grid layout -->
    <div class="charts">

        <div class="chart">
            <div id="heart"></div>
        </div>
        <div class="chart">
            <canvas id="doughChart"></canvas>
        </div>
        <div class="chart">
            <canvas id="caloriesBurnt"></canvas>
        </div>
        <div class="chart">
            <canvas id="calorieIntake"></canvas>
        </div>  
        <div class="chart">
            <canvas id="steps"></canvas>
        </div>  

    </div>
</body>

</html>