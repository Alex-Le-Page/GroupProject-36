<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartRate</title>

    <style>
       
       form {
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
    <?php include("NavBar.php") ?>
    <div class="NavBar">
    <ul>
            <li><a class="Logo" href="Home.php"><img class ="Logo" src="ElancoLogo.png" width="60" height="30"></a></li>
            <li><a href="Weight.php">Weight</a></li>
            <li><a href="HeartRate.php">Heart Rate</a></li>
            <li><a href="BehaviourPattern.php">Behaviour Pattern</a></li>
            <li><a href="intake.php">Intake</a></li>
        </ul>
    </div>
    <h2>Heart Rate</h2>

    <div class="main">
        <form>
            <label>Your dog's heart rate is above average</label>
        </form>
    </div>
    

    
</body>
</html>