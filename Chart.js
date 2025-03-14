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