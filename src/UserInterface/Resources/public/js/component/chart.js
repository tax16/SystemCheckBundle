document.addEventListener("DOMContentLoaded", function() {
    const successChecks = JSON.parse(document.getElementById('successChecksData').textContent);
    const failedChecks = JSON.parse(document.getElementById('failedChecksData').textContent);
    const warningChecks = JSON.parse(document.getElementById('warningChecksData').textContent);

    const successCount = successChecks.length;
    const failedCount = failedChecks.length;
    const warningCount = warningChecks.length;

    const ctx = document.getElementById('resultChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Success', 'Failed', 'Warning'],
            datasets: [{
                data: [successCount, failedCount, warningCount],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });
});