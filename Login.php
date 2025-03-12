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
            width: 50%;
            border-color: black;
            padding: 8px;
            text-align: left;
            width: 500px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center; 
        }
    </style>
    </head>
    <body>
        <img class ="Logo" src = "ElancoLogo.png" alt = "Elanco Logo" width="200" height="100">
        <form class="form-container">
            <label>Select account type:</label> 
            <br><br>
            <select>
                <option value = ""></option>
                <option value = "#PetOwner">Pet Owner</option>
                <option value = "#Vet">Vet</option>
            </select>

            Select Dog:
            <select>
                <option value = ""></option>
                <option value = "#Canine001">Dog 1</option>
                <option value = "#Canine002">Dog 2</option>
                <option value = "#Canine003">Dog 3</option>
            </select>

            <button type = "submit">
        </form>
    </body>
</html>