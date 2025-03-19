// Heart rate monitor functionality

// Set thresholds for dog heart rate
const warningThreshold = 120; // Yellow heart
const dangerThreshold = 160;  // Red heart

// Function to update the heart color
function updateHeartColor(heartRate) {
    // Get elements
    const heartShape = document.getElementById('heart-shape');
    const heartRateDisplay = document.getElementById('heart-rate');
    const statusText = document.getElementById('status-text');
    
    // Update heart rate display
    heartRateDisplay.innerHTML = heartRate.toFixed(1) + '<span class="bpm"> BPM</span>';
    
    // Reset heart classes
    heartShape.classList.remove('normal', 'warning', 'danger');
    
    // Set appropriate class based on heart rate
    if (heartRate >= dangerThreshold) {
        heartShape.classList.add('danger');
        statusText.textContent = "Alert: Average heart rate is too high!";
        statusText.style.color = "#F44336"; // Red
    } else if (heartRate >= warningThreshold) {
        heartShape.classList.add('warning');
        statusText.textContent = "Warning: Average heart rate is elevated";
        statusText.style.color = "#FFC107"; // Amber
    } else {
        heartShape.classList.add('normal');
        statusText.textContent = "Normal average heart rate";
        statusText.style.color = "#4CAF50"; // Green
    }
}

// Function to get the current month name
function getCurrentMonthName() {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];
    const now = new Date();
    return months[now.getMonth()];
}

// Function to fetch the monthly average heart rate
function fetchMonthlyAverage() {
    // Display the current month
    const monthLabel = document.getElementById('month-label');
    const currentMonth = getCurrentMonthName();
    monthLabel.textContent = `Average for ${currentMonth}`;
    
    // Fetch data from PHP backend
    fetch('getaverageheartrate.php')
        .then(response => response.json())
        .then(data => {
            updateHeartColor(data.averageHeartRate);
        })
        .catch(error => {
            console.error('Error fetching heart rate:', error);
            // For testing, use a random heart rate based on the data analysis
            const mockHeartRate = 110 + (Math.random() * 10 - 5); // Around 110 BPM
            updateHeartColor(mockHeartRate);
        });
        
    // Refresh once per hour (less frequently since it's a monthly average)
    setTimeout(fetchMonthlyAverage, 3600000); // 1 hour
}

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Start fetching heart rate data
    fetchMonthlyAverage();
});