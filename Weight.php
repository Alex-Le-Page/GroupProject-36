<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
    <link href='titleStyle.css' rel='stylesheet'>    
    <script src="Chart.js"></script>    
    <title>Weight</title>

    <style>
        div.chart{
            height: 500px;
        }
    </style>

</head>
<body>
    <div class="NavBar">
    <?php include("NavBar.php"); 

        $db = new SQLite3('ElancoDB.db');
        
        //get the date from the navbar callender
        if (!isset($_SESSION['Year'])) {
            echo "No Year Selected";
            exit;
        } else{
            $calYear = $_SESSION['Year']; 
        }

        if (!isset($_SESSION['Dog'])) {
            echo "No dog Selected";
            exit;
        } else{
            $dogID = $_SESSION['Dog'];
        }

        //get the average weight of the dog from each month 
        $query = $db-> prepare('
        SELECT 
        CASE --select the months from the year and store them as the appropriate month i.e "Jan" "Feb" etc
            WHEN substr(Date, 4, 2) = "01"  THEN "Jan"
            WHEN substr(Date, 4, 2) = "02"  THEN "Feb"
            WHEN substr(Date, 4, 2) = "03"  THEN "Mar"
            WHEN substr(Date, 4, 2) = "04"  THEN "Apr"
            WHEN substr(Date, 4, 2) = "05"  THEN "May"
            WHEN substr(Date, 4, 2) = "06"  THEN "Jun"
            WHEN substr(Date, 4, 2) = "07"  THEN "Jul"
            WHEN substr(Date, 4, 2) = "08"  THEN "Aug"
            WHEN substr(Date, 4, 2) = "09"  THEN "Sep"
            WHEN substr(Date, 4, 2) = "10"  THEN "Oct"
            WHEN substr(Date, 4, 2) = "11"  THEN "Nov"
            WHEN substr(Date, 4, 2) = "12"  THEN "Dec"
        END AS "months",
        round(AVG(weight), 1) AS avgWeight --select the average weight (to one deiclam point) for each of these months
        FROM Activity WHERE substr(Date, 7, 4) = :calYear AND DogID = :dogID
        GROUP BY months
        ORDER BY --order the data to be in order of months and not aplhabetical
        CASE 
            WHEN months = "Jan" THEN 1
            WHEN months = "Feb" THEN 2
            WHEN months = "Mar" THEN 3
            WHEN months = "Apr" THEN 4
            WHEN months = "May" THEN 5
            WHEN months = "Jun" THEN 6
            WHEN months = "Jul" THEN 7
            WHEN months = "Aug" THEN 8
            WHEN months = "Sep" THEN 9
            WHEN months = "Oct" THEN 10
            WHEN months = "Nov" THEN 11
            WHEN months = "Dec" THEN 12 
        END;
        ');

        $query->bindValue(":calYear", $calYear, SQLITE3_TEXT);
        $query->bindValue(":dogID", $dogID);
        $result = $query->execute();
        $weightData = [];

        for ($i = 0; $i < 12; $i++){
            $row = $result->fetchArray(SQLITE3_ASSOC);
            //if there isnt any data end the loop
            if (!$row) break;
    
            $weightData[] = $row['avgWeight'];
        }    
    ?>
    </div>
    <h2>Here is <?php echo $dogID; ?>'s info for Weight:</h2>

    <?php echo "<p class = 'title'> Selected year: " . $calYear ."<p><br>"; ?>

    <div class="main">

        <script>
            window.onload=function(){
                loadLineGraph(
                'weightGraph', 
                <?php echo json_encode($weightData) ?>, 
                'N/A', 
                'Dogs Weight', 
                'Weight(kg)', 
                "Months", 
                'N/A', 
                ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]); //y labels 
            }
        </script>

        <div class="chart">
            <canvas id="weightGraph"></canvas>
        </div>
        <form class = "notes">
            <label>This graph shows the average weight of the dog per month over the course of a year.</label>
        </form>
    </div>
</body>
</html>