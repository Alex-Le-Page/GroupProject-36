<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <style>

        body{
            background: #0E253E;
            margin-top: 0px;

            text-align: center;
        }

        form {
            border-color: black;
            padding: 8px;
            text-align: left;
            width: 200px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            margin-left: 44%;
            margin-top: 10%;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: lightblue;
            color: black;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        select{
            background-color: white;
            width: 100%;
            height: 30px;
            border-color: #0E253E;
            border-radius: 10px;
            text-align: center;
        }
    </style>
    </head>
    <body>
        <div>
            <img class ="Logo" src = "ElancoLogo.png" alt = "Elanco Logo" width="200" height="100">
        </div>
        <form class="form-container" action="Home.php" method="post">
            <label>Select account type:</label> 
            <br><br>
            <select>
                <option value = "#PetOwner">Pet Owner</option>
                <option value = "#Vet">Vet</option>
            </select>
            <br><br>
            <label>Select Dog:</label> 
            <br><br>
            <select>
                <option value = "#Canine001">Dog 1</option>
                <option value = "#Canine002">Dog 2</option>
                <option value = "#Canine003">Dog 3</option>
            </select>
            <br><br>
            <button type = "submit">Submit</button>
        </form>
    </body>
</html>