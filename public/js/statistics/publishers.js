let colors = [
    'rgb(255,25,25)',
    'rgb(0,77,230)',
    'rgb(230,230,0)',
    'rgb(34,204,0)',
    'rgb(191,0,230)',
    'rgb(102,179,255)',
    'rgb(128,255,128)',
    'rgb(102,34,0)',
    'rgb(255,204,102)',
    'rgb(0,204,102)',
    'rgb(25,102,255)',
    'rgb(255,255,230)',
    'rgb(26,0,13)',
    'rgb(77,255,106)',
];
let publishersComicCountCanvas = document.getElementById('publishersComicCount');
let publishersComicCountChart = new Chart(publishersComicCountCanvas, {
    type: 'pie',
    data: {
        datasets: [{
            data: [],
            backgroundColor: [],
        }],
        labels: []
    },
    options: {
    }
});

let publishersComicCostCanvas = document.getElementById('publishersComicCost');
let publishersComicCostChart = new Chart(publishersComicCostCanvas, {
    type: 'pie',
    data: {
        datasets: [{
            data: [],
            backgroundColor: [],
        }],
        labels: []
    },
    options: {
    }
});

$.ajax({
    url: "/api/statistics",
    success: function (result) {
        resetChartData(publishersComicCountChart);
        result.publishersComicCount.forEach(function (item, index) {
            publishersComicCountChart.data.datasets[0].data.push(item.count);
            publishersComicCountChart.data.datasets[0].backgroundColor.push(colors[index]);
            publishersComicCountChart.data.labels.push(item.name);
        });
        publishersComicCountChart.update();

        resetChartData(publishersComicCostChart);
        result.publishersComicCost.forEach(function (item, index) {
            publishersComicCostChart.data.datasets[0].data.push((item.cost/100).toFixed(2));
            publishersComicCostChart.data.datasets[0].backgroundColor.push(colors[index]);
            publishersComicCostChart.data.labels.push(item.name);
        });
        publishersComicCostChart.update();
    },
    error: function (result) {
        console.log(result)
    },
});

function currencyToString(valueInCent) {
    if (valueInCent == null) {
        return 0
    }

    return (parseInt(valueInCent)).toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
    })
}

function resetChartData(chart) {
    chart.data.datasets[0].data = [];
    chart.data.datasets[0].backgroundColor = [];
    chart.data.labels = [];

    chart.reset();
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';

    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }

    return color;
}