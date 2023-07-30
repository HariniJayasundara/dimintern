<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <div class="chart-container">
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="pieChart"></canvas>
    </div>

    <?php
    // Include your database connection here
    // Replace "your_db_host", "your_db_username", "your_db_password", and "your_db_name" with appropriate values
    $conn = new mysqli("your_db_host", "your_db_username", "your_db_password", "your_db_name");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch data for the bar chart
    $barChartQuery = "SELECT preference_name, COUNT(*) as count FROM your_table_name GROUP BY preference_name ORDER BY count DESC LIMIT 3";
    $barChartResult = $conn->query($barChartQuery);

    // Query to fetch data for the pie chart
    $pieChartQuery = "SELECT company_name, COUNT(*) as count FROM your_table_name GROUP BY company_name";
    $pieChartResult = $conn->query($pieChartQuery);

    $barChartLabels = [];
    $barChartData = [];
    $pieChartLabels = [];
    $pieChartData = [];

    // Process data for the bar chart
    if ($barChartResult->num_rows > 0) {
        while ($row = $barChartResult->fetch_assoc()) {
            $barChartLabels[] = $row['preference_name'];
            $barChartData[] = $row['count'];
        }
    }

    // Process data for the pie chart
    if ($pieChartResult->num_rows > 0) {
        while ($row = $pieChartResult->fetch_assoc()) {
            $pieChartLabels[] = $row['company_name'];
            $pieChartData[] = $row['count'];
        }
    }
    ?>

    <script>
        // JavaScript to create and render the bar chart
        var ctx1 = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($barChartLabels); ?>,
                datasets: [{
                    label: 'Top 3 Preferences by Company',
                    data: <?php echo json_encode($barChartData); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // JavaScript to create and render the pie chart
        var ctx2 = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($pieChartLabels); ?>,
                datasets: [{
                    label: 'Students Per Company',
                    data: <?php echo json_encode($pieChartData); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        // Add more colors as needed
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        // Add more colors as needed
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>