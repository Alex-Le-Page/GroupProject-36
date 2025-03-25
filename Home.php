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
            margin-top: 0;
            width: 600px;
            height: 600px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 5px;
        }
    </style>
</head>

<body>

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
        
    //run a select statement to get the water intake of the dog throughout the day 
    $query = $db->prepare('SELECT AVG(Weight) AS avgWeight, SUM(Water_Intake) AS totalIntake, SUM(Calorie_Burn) AS totalBurnt, SUM(Activity_Level)AS steps, SUM(Food_Intake) AS totalCalories FROM Activity WHERE Hour <= :currentTime AND DogID = "CANINE001" AND Date = :selectedDate');
    $query->bindValue(":currentTime", $calTime, SQLITE3_INTEGER);
    $query->bindValue(":selectedDate", $calDate, SQLITE3_TEXT);
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
    $allDayStmt->bindValue(':selectedDate', $calDate, SQLITE3_TEXT);
    $allDayResult = $allDayStmt->execute();

    // populate array
    while ($row = $allDayResult->fetchArray(SQLITE3_ASSOC)) {
    $arrangedDataset[] = $row['Heart_Rate'];
    }

    $recordCount = count($arrangedDataset);

    // get the heart rate for the specific hour if provided
    if ($calTime !== null) {
        $hourQuery = "SELECT Heart_Rate
        FROM Activity
        WHERE Date = :selectedDate 
        AND Hour = :selectedHour";

    $hourStmt = $db->prepare($hourQuery);
    $hourStmt->bindValue(':selectedDate', $calDate, SQLITE3_TEXT);
    $hourStmt->bindValue(':selectedHour', $calTime, SQLITE3_INTEGER);
    $hourResult = $hourStmt->execute();

    $hourRow = $hourResult->fetchArray(SQLITE3_ASSOC);

    // if we found a heart rate for the specific hour, use it
    if ($hourRow) {
        $heartRate = $hourRow['Heart_Rate'];
        } elseif ($recordCount > 0) {
        // if no heart rate for specific hour but we have data for the day,
        // use the highest heart rate of the day
        $heartRate = max($arrangedDataset);
        $statusText = 'No data for hour ' . $calTime . '. Showing highest heart rate for the day.';
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
    
    
    $weightValue = 0;
    $weightColour = '#4CAF50';
    $weightStatusText = '';
    $weightRecordCount = 0;
    $weightDataset = [];

    try {
        // get all weights for the day to calculate bounds
        $allWeightQuery = "SELECT Weight
        FROM Activity
        WHERE Date = :selectedDate";

        $allWeightStmt = $db->prepare($allWeightQuery);
        $allWeightStmt->bindValue(':selectedDate', $calDate, SQLITE3_TEXT);
        $allWeightResult = $allWeightStmt->execute();

        // populate array
        while ($row = $allWeightResult->fetchArray(SQLITE3_ASSOC)) {
            $weightDataset[] = $row['Weight'];
        }

        $weightRecordCount = count($weightDataset);

        // get the weight for the specific hour if provided
        if ($calTime !== null) {
            $hourWeightQuery = "SELECT Weight
            FROM Activity
            WHERE Date = :selectedDate 
            AND Hour = :selectedHour";

            $hourWeightStmt = $db->prepare($hourWeightQuery);
            $hourWeightStmt->bindValue(':selectedDate', $calDate, SQLITE3_TEXT);
            $hourWeightStmt->bindValue(':selectedHour', $calTime, SQLITE3_INTEGER);
            $hourWeightResult = $hourWeightStmt->execute();

            $hourWeightRow = $hourWeightResult->fetchArray(SQLITE3_ASSOC);

            // if we found a weight for the specific hour, use it
            if ($hourWeightRow) {
                $weightValue = $hourWeightRow['Weight'];
            } elseif ($weightRecordCount > 0) {
                // if no weight for specific hour but we have data for the day,
                // use the average weight of the day
                $weightValue = array_sum($weightDataset) / $weightRecordCount;
                $weightStatusText = 'No data for hour ' . $calTime . '. Showing average weight for the day.';
            }
        } elseif ($weightRecordCount > 0) {
            // no hour selected, use the average weight for the day
            $weightValue = array_sum($weightDataset) / $weightRecordCount;
        }

    // calculate bounds only if we have records
    if ($weightRecordCount > 0) {
        // calculate the bounds using imported functions
        $weightLowerBound = FindLowerBound($weightDataset);
        $weightUpperBound = FindUpperBound($weightDataset);

        // set status text if it wasn't set by the hour check
        if (empty($weightStatusText)) {
            // logic to decide the status based on calculated bounds
            if ($weightValue > $weightUpperBound) {
                $weightColour = '#F44336'; 
                $weightStatusText = 'Alert: Weight above upper bound';
            } elseif ($weightValue < $weightLowerBound) {
                $weightColour = '#FFC107'; 
                $weightStatusText = 'Alert: Weight below lower bound';
            } else {
                $weightColour = '#4CAF50';
                $weightStatusText = 'Normal weight (within bounds)';
            }
        }
    } else {
        $weightStatusText = 'No weight data found for selected date';
    }
    } catch (Exception $e) {
        $weightStatusText = 'Error: ' . $e->getMessage();
    }

?>
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
        <div style="text-align: center; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <svg width="120" height="110" viewBox="0 0 100 90">
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
    <div class="chart">
        <div style="text-align: center; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <svg width="120" height="110" viewBox="0 0 100 90">
                <!-- circle for weight -->
                <circle cx="50" cy="45" r="40" 
                    style="fill: <?php echo $weightColour; ?>;" />
                
                <!-- text inside the circle -->
                <text x="50" y="45" text-anchor="middle" fill="white" font-weight="bold" font-size="14">
                    <?php echo number_format($weightValue, 1); ?>
                </text>
                <text x="50" y="60" text-anchor="middle" fill="white" font-size="10">
                    KG
                </text>
            </svg>
        </div>
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