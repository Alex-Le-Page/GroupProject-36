<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        form {
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
    </style>
</head>

<body>
    <?php include("NavBar.php") ?>
    <div class="NavBar">
        <ul>
            <li><a class="Logo" href=#home><img class ="Logo" src="ElancoLogo.png" width="60" height="30"></a></li>
            <li><a href=#Home>Weight</a></li>
            <li><a href=#HeartRate>Heart Rate</a></li>
            <li><a href=#BehaviourPattern>Behaviour Pattern</a></li>
            <li><a href=#Intake>Intake</a></li>
        </ul>
    </div>
    <h2>Here is Cainine001's Info:</h2>
    <div class="Main">
        
        <form>
            <label>Heart-Rate: 109 BPM</label>
            <br><br>
            <label>Behaviour Pattern: Normal</label>
            <br><br>
            <label>Weight: 29.8kg</label>
            <br><br>
            <label>Temperature: 28.5 C</label>
        </form>
        <img class="Placeholder" src="PlaceholderAnalysis.png" alt="Placeholder">

    </div>
</body>

</html>