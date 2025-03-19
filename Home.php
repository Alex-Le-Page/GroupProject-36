<?php 

    $db = new SQLite3('ElancoDB.db');

    //get the time from when the user opens the website
    $currentTime = date("G");

    //account for databases time format (starting at 0)
    $currentTime -= 1;
        
    //run a select statement to get the water intake of the dog throughout the day 
    $query = $db->prepare('SELECT AVG(Weight) AS avgWeight, SUM(Water_Intake) AS totalIntake, SUM(Calorie_Burn) AS totalBurnt, SUM(Activity_Level)AS steps, SUM(Food_Intake) AS totalCalories FROM Activity WHERE Hour <= :currentTime AND DogID = "CANINE001" AND Date = "01-01-2021" ');
    $query->bindValue(":currentTime", $currentTime, SQLITE3_INTEGER);
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
    </style>
</head>

<body>
    <?php include("NavBar.php");
    ?>
    
    <h2>Here is Cainine001's Info:</h2>
    <div class="Main">

        <form class = "notes">
            <label>Heart-Rate: 109 BPM</label>
            <br><br>
            <label>Behaviour Pattern: Normal</label>
            <br><br>
            <label>Weight: 29.8kg</label>
            <br><br>
            <label>Temperature: 28.5 C</label>
        </form>

    </div>

    <div class="warning">
        <h1>*Please note all goals are a general calculation and may not be specific to your dog</h1>
    </div>

    <script>
    window.onload = function() {
        loadDoughChart('doughChart', <?php echo json_encode($totalIntake)?>, <?php echo json_encode($intakeLeft)?>, 'Water Intake(ml)', <?php echo json_encode($totalMl)?>);
        loadDoughChart('caloriesBurnt', <?php echo json_encode($totalBurnt)?>, <?php echo json_encode($burntLeft)?>, 'Calories Burnt', <?php echo json_encode($burntGoal)?>);
        loadDoughChart('calorieIntake', <?php echo json_encode($totalCalories)?>, <?php echo json_encode($caloriesLeft)?>, 'Calorie Intake', <?php echo json_encode($calorieGoal)?>);
        loadDoughChart('steps', <?php echo json_encode($totalSteps) ?>, <?php echo json_encode($stepsLeft)?>, 'Step Goal', 8000);
    }
    </script>

    <!-- Add any more graphs into the charts class to have it be apart of the grid layout -->
    <div class="charts">

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