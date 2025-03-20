<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="NavBarCss.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .date-picker-icon {
            cursor: pointer;
            width: 40px;
            height: 40px;
        }

        #datePicker {
            visibility: hidden;
            position: absolute;
            /* Hide the input box but keep it in navbar so that the calendar dropdown is in the correct place */
        }
    </style>
</head>
<body>

<?php
    session_start();

    $date = $_SESSION['Date']; // retrieves the selected date from previous pages

    // Get the date the user clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['datePicker'])) {
        $date = $_POST['datePicker'];

        $_SESSION['Date'] = $date;
    }
?>

<div class="NavBar">
    <ul>
        <li><a class="Logo" href="Home.php"><img class="Logo" src="ElancoLogo.png" width="60" height="30"></a></li>
        <li><a href="Weight.php">Weight</a></li>
        <li><a href="HeartRate.php">Heart Rate</a></li>
        <li><a href="BehaviourPattern.php">Behaviour Pattern</a></li>
        <li><a href="intake.php">Intake</a></li>
        <li><a href="BreathingRate.php">Breathing Rate</a></li>

        <!-- Date picker -->
        <form method="post">
            <input type="text" name="datePicker" id="datePicker" placeholder="Select a date" readonly onchange="this.form.submit()">
            <img src="CalendarIcon.png" alt="Date Picker Icon" id="datePickerIcon" class="date-picker-icon">
        </form>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            // Get the session date value, or give a default date
            const phpDate = "<?php echo !empty($date) ? $date : '31-12-2023'; ?>";

            const datePicker = flatpickr("#datePicker", {
                enableTime: false, // Time selection option
                dateFormat: "d-m-Y", // Set date format
                defaultDate: phpDate, // Set the date in calendar dropdown
                minDate: "01-01-2021", // Minimum date
                maxDate: "31-12-2023", // Maximum date
            });

            // Image trigger to allow user to select date from image
            document.getElementById("datePickerIcon").addEventListener("click", () => {
                datePicker.open();
            });
        </script>
    </ul>
</div>
</body>
</html>
