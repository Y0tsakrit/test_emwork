<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบรายรับ-รายจ่าย</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding-top: 20px;
            min-height: 100vh;
        }
    </style>
</head>
<body class="w-[80%] h-screen flex flex-col items-center gap-5 mx-auto">
    <div class="text-white text-4xl">บันทึกรายรับ รายจ่าย</div>
    <div class="text-white text-xl">Income & Expense Tracker</div>
    <div class="flex justify-between flex-row gap-7">
        <div id="home" class="text-black bg-white border border-transparent w-[150px] h-[75px] flex items-center justify-center text-center rounded-2xl cursor-pointer hover:-translate-y-2 transition-transform duration-300">บันทึกรายการ</div>
        <div id="report" class="text-white bg-[#FFFFFF33] border border-transparent w-[150px] h-[75px] flex items-center justify-center text-center rounded-2xl cursor-pointer hover:-translate-y-2 transition-transform duration-300">รายงาน</div>
    </div>
    

    <div class="flex flex-row justify-center border border-transparent w-[400px] h-[75px] rounded-2xl bg-gray-100 items-center gap-5">
        <div class="text-black text-lg">เลือกเดือน:</div>
        <input id="datePicker" type="month" class="w-[200px] h-[50px] rounded-2xl text-black px-2" onchange="fetchData()">
        <div id="refresh" class="text-2xl cursor-pointer" onclick="document.getElementById('datePicker').value = ''; fetchData();">🔄</div>
    </div>

    <div class="w-full h-fit bg-gray-100 rounded-2xl p-5 flex flex-col gap-5">

        <div class="flex justify-center gap-4">
            <div class="bg-green-500 text-white p-4 rounded-lg text-center">
                <div class="text-lg font-bold">รวมรายรับ</div>
                <div id="totalIncome" class="text-2xl">0 บาท</div>
            </div>
            <div class="bg-red-500 text-white p-4 rounded-lg text-center">
                <div class="text-lg font-bold">รวมรายจ่าย</div>
                <div id="totalExpense" class="text-2xl">0 บาท</div>
            </div>
            <div id="balanceCard" class="bg-blue-500 text-white p-4 rounded-lg text-center">
                <div class="text-lg font-bold">ยอดคงเหลือ</div>
                <div id="netBalance" class="text-2xl">0 บาท</div>
            </div>
        </div>

        <div class="flex flex-row gap-5">
            <div class="w-[60%] border border-transparent bg-gray-200 rounded-2xl p-4">
                <h3 class="text-center text-lg font-bold mb-4">กราฟแท่ง: รายรับ vs รายจ่าย ทุกเดือน</h3>
                <canvas id="barChart" style="width:100%;height:400px;"></canvas>
            </div>
            <div class="w-[40%] border border-transparent bg-gray-200 rounded-2xl p-4">
                <h3 class="text-center text-lg font-bold mb-4">กราฟวงกลม: <span id="pieChartTitle">รายรับ vs รายจ่าย ทั้งปี</span></h3>
                <canvas id="pieChart" style="width:100%;height:400px;"></canvas>
            </div>
        </div>

        <div class="w-full border border-transparent bg-gray-200 rounded-2xl p-4">
            <h3 class="text-center text-lg font-bold mb-4">กราฟเส้น: ยอดคงเหลือแต่ละเดือน</h3>
            <canvas id="lineChart" style="width:100%;height:300px;"></canvas>
        </div>
    </div>
</body>
</html>

<script>
    let allYearIncomeData = [0,0,0,0,0,0,0,0,0,0,0,0]; 
    let allYearExpenseData = [0,0,0,0,0,0,0,0,0,0,0,0]; 
    let monthlyIncomeData = [0,0,0,0,0,0,0,0,0,0,0,0]; 
    let monthlyExpenseData = [0,0,0,0,0,0,0,0,0,0,0,0]; 
    let monthlyDataLabels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    let monthNames = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

    let barChart = null;
    let pieChart = null;
    let lineChart = null;

    const init = () => {
        const home = document.getElementById('home');
        const report = document.getElementById('report');
        const url = window.location.href;

        try {
            if ((url.split('/'))[3] == 'report') {
                report.style.backgroundColor = '#FFFFFF';
                report.style.color = '#000000';
                home.style.backgroundColor = '#FFFFFF33';
                home.style.color = '#FFFFFF';
            }
        } catch (e) {
            home.style.backgroundColor = '#FFFFFF';
            home.style.color = '#000000';
            report.style.backgroundColor = '#FFFFFF33';
            report.style.color = '#FFFFFF';
        }
    }

    init();

    const home = document.getElementById('home');
    const report = document.getElementById('report');
    home.addEventListener('click', () => {
        window.location.href = '/';
    });
    report.addEventListener('click', () => {
        window.location.href = '/report';
    });

    const fetchAllYearData = () => {

        fetch('api/accounts')
            .then(response => response.json())
            .then(data => {
                allYearIncomeData = [0,0,0,0,0,0,0,0,0,0,0,0];
                allYearExpenseData = [0,0,0,0,0,0,0,0,0,0,0,0];

                data.forEach(item => {
                    const month = new Date(item.accDate).getMonth();
                    const amount = parseFloat(item.accAmount);

                    if (item.accType === 'รายรับ') {
                        allYearIncomeData[month] += amount;
                    } else if (item.accType === 'รายจ่าย') {
                        allYearExpenseData[month] += amount;
                    }
                });

                fetchData();
            })
            .catch(error => {
                console.error('Error fetching all year data:', error);
            });
    };

    const fetchData = () => {
        const datePicker = document.getElementById('datePicker');
        let url = 'api/accounts';
        let selectedMonth = null;
        
        if (datePicker.value) {
            const selectedDate = datePicker.value;
            url = `api/accounts?month=${selectedDate}`;
            selectedMonth = parseInt(selectedDate.split('-')[1]) - 1;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                monthlyIncomeData = [0,0,0,0,0,0,0,0,0,0,0,0];
                monthlyExpenseData = [0,0,0,0,0,0,0,0,0,0,0,0];
                
                let totalIncome = 0;
                let totalExpense = 0;

                data.forEach(item => {
                    const month = new Date(item.accDate).getMonth();
                    const amount = parseFloat(item.accAmount);

                    if (item.accType === 'รายรับ') {
                        monthlyIncomeData[month] += amount;
                        totalIncome += amount;
                    } else if (item.accType === 'รายจ่าย') {
                        monthlyExpenseData[month] += amount;
                        totalExpense += amount;
                    }
                });

                updateSummary(totalIncome, totalExpense);
                updateCharts(selectedMonth);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    };

    const updateSummary = (totalIncome, totalExpense) => {
        const netBalance = totalIncome - totalExpense;
        
        document.getElementById('totalIncome').textContent = totalIncome.toLocaleString() + ' บาท';
        document.getElementById('totalExpense').textContent = totalExpense.toLocaleString() + ' บาท';
        document.getElementById('netBalance').textContent = netBalance.toLocaleString() + ' บาท';
        
        const balanceCard = document.getElementById('balanceCard');
        if (netBalance >= 0) {
            balanceCard.className = 'bg-green-500 text-white p-4 rounded-lg text-center';
        } else {
            balanceCard.className = 'bg-red-500 text-white p-4 rounded-lg text-center';
        }
    };

    const updateCharts = (selectedMonth = null) => {
        const barCtx = document.getElementById('barChart').getContext('2d');
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const lineCtx = document.getElementById('lineChart').getContext('2d');

        if (barChart && typeof barChart.destroy === 'function') {
            barChart.destroy();
        }
        if (pieChart && typeof pieChart.destroy === 'function') {
            pieChart.destroy();
        }
        if (lineChart && typeof lineChart.destroy === 'function') {
            lineChart.destroy();
        }

        const createBackgroundColors = (baseColor, selectedMonth) => {
            return monthlyDataLabels.map((_, index) => {
                if (selectedMonth !== null && index === selectedMonth) {
                    return baseColor.replace('0.8', '1'); 
                } else {
                    return baseColor.replace('0.8', selectedMonth !== null ? '0.3' : '0.8'); 
                }
            });
        };

        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: monthlyDataLabels,
                datasets: [
                    {
                        label: 'รายรับ',
                        data: allYearIncomeData,
                        backgroundColor: createBackgroundColors('rgba(34, 197, 94, 0.8)', selectedMonth),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        maxBarThickness: 40
                    },
                    {
                        label: 'รายจ่าย',
                        data: allYearExpenseData,
                        backgroundColor: createBackgroundColors('rgba(239, 68, 68, 0.8)', selectedMonth),
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        maxBarThickness: 40
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return value.toLocaleString() + ' บาท';
                            }
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.datasets[tooltipItem.datasetIndex].label;
                            const value = tooltipItem.yLabel.toLocaleString();
                            return label + ': ' + value + ' บาท';
                        }
                    }
                }
            }
        });

        let pieIncome, pieExpense, pieTitle;
        
        if (selectedMonth !== null) {
            pieIncome = monthlyIncomeData[selectedMonth];
            pieExpense = monthlyExpenseData[selectedMonth];
            pieTitle = `รายรับ vs รายจ่าย เดือน${monthNames[selectedMonth]}`;
        } else {
            pieIncome = monthlyIncomeData.reduce((a, b) => a + b, 0);
            pieExpense = monthlyExpenseData.reduce((a, b) => a + b, 0);
            pieTitle = 'รายรับ vs รายจ่าย ทั้งปี';
        }

        document.getElementById('pieChartTitle').textContent = pieTitle;

        pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['รายรับ', 'รายจ่าย'],
                datasets: [{
                    data: [pieIncome, pieExpense],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.labels[tooltipItem.index];
                            const value = data.datasets[0].data[tooltipItem.index];
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value.toLocaleString() + ' บาท (' + percentage + '%)';
                        }
                    }
                }
            }
        });

        const balanceData = allYearIncomeData.map((income, index) => income - allYearExpenseData[index]);

        lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: monthlyDataLabels,
                datasets: [{
                    label: 'ยอดคงเหลือ',
                    data: balanceData,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: balanceData.map((value, index) => {
                        if (selectedMonth !== null && index === selectedMonth) {
                            return 'rgba(255, 193, 7, 1)';
                        }
                        return value >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                    }),
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' บาท';
                            }
                        },
                        gridLines: {
                            color: function(context) {
                                return context.tick.value === 0 ? 'rgba(0, 0, 0, 0.8)' : 'rgba(0, 0, 0, 0.1)';
                            }
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const value = tooltipItem.yLabel;
                            const status = value >= 0 ? 'กำไร' : 'ขาดทุน';
                            return `ยอดคงเหลือ: ${value.toLocaleString()} บาท (${status})`;
                        }
                    }
                }
            }
        });
    };

    fetchAllYearData();
</script>