<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="Chart.js"></script>
    <title>Pet Behavior Chart</title>
</head>

<body>
    <div class="NavBar">
        <?php include("NavBar.php");

            $db = new SQLite3('ElancoDB.db');

            //get the date from the callender
            $date = $_SESSION['Date'];

            //select the total ammount of hours for each behaviour during the day 
            //using CASE WHEN statements https://www.geeksforgeeks.org/sql-server-case-expression/ 
            $query = $db->prepare('
            SELECT
            CASE --select each different behaviour type and store it 
                WHEN BehaviourID = 1 THEN "normal" 
                WHEN BehaviourID = 2 THEN "walking"  
                WHEN BehaviourID = 3 THEN "eating" 
                WHEN BehaviourID = 4 THEN "sleeping" 
                WHEN BehaviourID = 5 THEN "playing"
            END AS "behaviourType",
            COUNT(Hour) AS totalHours --get the total hours of each behaviour
            FROM Activity WHERE DogID = "CANINE001" AND Date = :calDate
            GROUP BY behaviourType
            ORDER BY --reorder from alphabetical to correct order 
            CASE 
                WHEN behaviourType = "normal" THEN 1
                WHEN behaviourType = "walking" THEN 2
                WHEN behaviourType = "eating" THEN 3
                WHEN behaviourType = "sleeping" THEN 4
                WHEN behaviourType = "playing" THEN 5
            END;
            ');
        
            $query->bindValue(":calDate", $date, SQLITE3_TEXT);
            $result = $query->execute();
        
            //loop through the result for the ammount of behaviours
            for ($i = 0; $i < 5; $i++){
                $row = $result->fetchArray(SQLITE3_ASSOC);
                //if there isnt any data end the loop
                if (!$row) break;
        
                $hours[] = $row['totalHours'];
            }
        
            //seperate each number with a comma to make it a list
            $hours = implode(",", $hours);
        ?>
    </div>

    <script>
        window.onload = function() {
            loadRadarChart('behaviour', [<?php echo $hours?>]);
        }
    </script>

    <div class="radar">
        <canvas id="behaviour"></canvas>
    </div>

</body>
</html>