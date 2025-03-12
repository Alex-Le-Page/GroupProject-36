<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        form {
            float: right;
            margin-top: 5%;
            margin-right: 15%;
            
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
            <li><a href=#Home>Weight</a></li>
            <li><a href=#HeartRate>Heart Rate</a></li>
            <li><a href="BehaviourPattern.php">Behaviour Pattern</a></li>
            <li><a href="intake.php">Intake</a></li>
        </ul>
    </div>
    <h2>Behaviour Pattern</h2>
    <div class = "Main">
    <form>
            <label>your dogs behaviour is normal</label>
            <br><br>
        </form>
    </div>
    </body>
    