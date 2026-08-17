document.addEventListener('DOMContentLoaded', function() {
    // Common Chart.js options for beautiful styling
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = '#e2e8f0';

    // 1. Appointments This Week (Bar Chart)
    const ctxAppointments = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(ctxAppointments, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Appointments',
                data: [12, 19, 15, 25, 22, 30],
                backgroundColor: '#0ea5e9',
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 2. Monthly Revenue (Line Chart)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    // Create gradient
    let gradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(20, 184, 166, 0.5)'); // Teal
    gradient.addColorStop(1, 'rgba(20, 184, 166, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [120000, 150000, 140000, 180000, 175000, 210000, 230000],
                borderColor: '#14b8a6',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#14b8a6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱ ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // 3. Most Common Procedures (Doughnut Chart)
    const ctxProcedures = document.getElementById('proceduresChart').getContext('2d');
    new Chart(ctxProcedures, {
        type: 'doughnut',
        data: {
            labels: ['Cleaning/Prophylaxis', 'Tooth Extraction', 'Fillings', 'Root Canal', 'Braces/Orthodontics'],
            datasets: [{
                data: [45, 20, 15, 10, 10],
                backgroundColor: [
                    '#0ea5e9', // Blue
                    '#14b8a6', // Teal
                    '#8b5cf6', // Purple
                    '#f59e0b', // Orange
                    '#ef4444'  // Red
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });

    // 4. New Patients per Month (Bar Chart)
    const ctxNewPatients = document.getElementById('newPatientsChart').getContext('2d');
    new Chart(ctxNewPatients, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'New Patients',
                data: [15, 22, 18, 30, 25, 35, 42],
                backgroundColor: '#8b5cf6',
                borderRadius: 4,
                barThickness: 16
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
});
