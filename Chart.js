function loadLineGraph(canvasId, dataset, outputDataset, graphLabel, yLabel, xLabel, extraOutputLabel){

    if (!dataset.length || !outputDataset.length) {
        alert("Some data is missing for the selected date.");
        return;
    } // error handling if theres some data missing
    else{
        const xValues = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];

        const ctx = document.getElementById(canvasId);

        return new Chart(ctx, { 
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    label: graphLabel, // label for the line
                    fill: false, // below the graph
                    backgroundColor:"rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)", // colour of line
                    data: dataset // dataset from database
                }] 
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel:{
                            display: true,
                            labelString: yLabel
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: xLabel
                        }

                    }]
                },
                tooltips: { // change the text when hovering over a value on the graph
                    displayColors: false, // stops the colour from being shown
                    callbacks: {
                        title: function() {
                            return null;  // Stops the title from showing 
                        },
                        label: function(tooltipItem) {
                            const hour = xValues[tooltipItem.index]; // Hour
                            const yData = tooltipItem.yLabel; // Breathing rate value
                            const outputData = outputDataset[hour]; // Fallback if behaviour data is missing

                            return[
                                xLabel + ": " + hour,
                                yLabel + ": " + yData, 
                                extraOutputLabel + outputData
                            ] // What we want to be shown when hovering over points on the graph
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }
}


function loadBehaviorPieChart(canvasId, behaviorDataset, graphLabel) {
    
    if (!behaviorDataset.length) {
        alert("Behavior data is missing for the selected date.");
        return;
    } else {
        // Count occurrences of each behavior
        const behaviorCounts = {};
        
        // Process behavior data
        for (let i = 0; i < behaviorDataset.length; i++) {
            const behavior = behaviorDataset[i];
            
            if (behavior && behavior !== "") {
                if (!behaviorCounts[behavior]) {
                    behaviorCounts[behavior] = 0;
                }
                behaviorCounts[behavior]++;
            }
        }
        
        // Prepare data for the pie chart
        const pieLabels = Object.keys(behaviorCounts);
        const pieData = pieLabels.map(label => behaviorCounts[label]);
        
        const backgroundColors = [
            'rgba(255, 99, 132, 0.8)',   // Red
            'rgba(54, 162, 235, 0.8)',   // Blue
            'rgba(255, 206, 86, 0.8)',   // Yellow
            'rgba(75, 192, 192, 0.8)',   // Green
            'rgba(153, 102, 255, 0.8)',  // Purple
            'rgba(255, 159, 64, 0.8)'    // Orange
        ];
        
        const ctx = document.getElementById(canvasId);
        
        return new Chart(ctx, {
            type: "pie",
            data: {
                labels: pieLabels,
                datasets: [{
                    backgroundColor: backgroundColors.slice(0, pieLabels.length),
                    data: pieData
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: graphLabel
                },
                legend: {
                    position: 'right'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const behavior = data.labels[tooltipItem.index];
                            const count = data.datasets[0].data[tooltipItem.index];
                            const total = pieData.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((count / total) * 100);
                            
                            return [
                                behavior + ": " + count + " hours",
                                percentage + "% of the day"
                            ];
                        }
                    }
                }
            }
        });
    }

function loadBarChart(canvasId, dataset, outputDataset, graphLabel, yLabel, xLabel, extraOutputLabel){

    if (!dataset.length || !outputDataset.length) {
        alert("Some data is missing for the selected date.");
        return;
    } // error handling if theres some data missing
    else{
        const xValues = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];

        const ctx = document.getElementById(canvasId);

        return new Chart(ctx, { 
        type: "bar",
        data: {
                labels: xValues,
                datasets: [{
                    label: graphLabel, // label for the bars
                    backgroundColor: "rgba(13, 13, 94, 0.82)", // colour of bars
                    data: dataset // dataset from database
                }] 
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel:{
                            display: true,
                            labelString: yLabel
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: xLabel
                        }

                    }]
                },
                tooltips: { // change the text when hovering over a bar
                    displayColors: false, // stops the colour from being shown when hovering over a bar
                    callbacks: {
                        title: function() {
                            return null;  // Stops the title from showing 
                        },
                        label: function(tooltipItem) {
                            const hour = xValues[tooltipItem.index]; // Hour
                            const yData = tooltipItem.yLabel; // Breathing rate value
                            const outputData = outputDataset[hour]; // Fallback if behaviour data is missing

                            return[
                                xLabel + ": " + hour,
                                yLabel + ": " + yData, 
                                extraOutputLabel + outputData
                            ] // What we want to be shown when hovering over points on the graph
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }   
}
}