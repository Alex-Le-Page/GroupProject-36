<!DOCTYPE html>
<html>
<head>
    <title>Monthly Average Heart Rate Monitor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .heart-container {
            margin: 20px auto;
            width: 150px;
            height: 150px;
        }
        .heart {
            width: 100%;
            height: 100%;
            transition: fill 0.5s ease;
        }
        /* Heart colors for different states */
        .normal {
            fill: #4CAF50; /* Green */
        }
        .warning {
            fill: #FFC107; /* Amber/Yellow */
        }
        .danger {
            fill: #F44336; /* Red */
        }
        #heart-rate {
            font-size: 32px;
            font-weight: bold;
        }
        .status {
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .bpm {
            font-size: 18px;
            color: #666;
        }
        .month-label {
            font-size: 14px;
            color: #666;
            margin-top: 15px;
        }
    </style>
    <!-- Link to the external JavaScript file -->
    <script src="trafficlight.js"></script>
</head>
<body>
    <div class="container">
        <h2>Monthly Average Heart Rate</h2>
        
        <div class="heart-container">
            <!-- SVG Heart that will change color -->
            <svg class="heart" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <path id="heart-shape" class="normal" d="M16,28.261c0,0-14-7.926-14-17.046c0-9.356,13.159-10.399,14-0.454
                c1.011-9.938,14-8.903,14,0.454C30,20.335,16,28.261,16,28.261z"/>
            </svg>
        </div>
        
        <div>
            <p id="heart-rate">--<span class="bpm"> BPM</span></p>
            <p class="status" id="status-text">Loading...</p>
            <p class="month-label" id="month-label"></p>
        </div>
    </div>
</body>
</html>