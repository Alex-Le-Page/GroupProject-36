<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <script src="Chart.js"></script>
    </head>
    <body>

    <?php
    $db = new SQLite3('ElancoDB.db');

        $sql = "
            SELECT Heart_Rate
            FROM Activity
            WHERE Date BETWEEN '01-01-2021' AND '31-12-2023'
            ORDER BY Heart_Rate DESC";
        $results = $db->query($sql);

        $arrangedDataset = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $arrangedDataset[] = $row['Heart_Rate']; 
        } // gets data and sets array
        print_r($arrangedDataset);

    $db->close();
    ?>

    <script>
        console.log("Lower Bound:", FindLowerBound(<?php echo json_encode($arrangedDataset); ?>)); // calls js function, the array must be in descending order
        console.log("Upper Bound:", FindUpperBound(<?php echo json_encode($arrangedDataset); ?>));
    </script>

    </body>
</html>

    <!-- Delete once implemented, not for actual program -->