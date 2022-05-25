// GRAPH ----------------------------------------
const ctx = document.getElementById('myChart');

const data1 = [];       //wheel
const data2 = [];       //car
var sizeOutput = 500;   //pocet udajov

//loadne json
function loadJSON(filePath) {
    const request = new XMLHttpRequest();
    request.open("GET", filePath, false);
    request.send(null);
    return JSON.parse(request.responseText);
}
var ocataveOutputJson = loadJSON("./output.json");

//parse data
for (var i = 0; i < sizeOutput; i++) {
    data1.push({x: parseFloat(ocataveOutputJson.values[i].time), y: parseFloat(ocataveOutputJson.values[i].wheel)});
    data2.push({x: parseFloat(ocataveOutputJson.values[i].time), y: parseFloat(ocataveOutputJson.values[i].car)});
}

const totalDuration = 5000;
const delayBetweenPoints = totalDuration / 500; //data length
const previousY = (ctx) => ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(100) : ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;
const animation = {
    x: {
        type: 'number',
        easing: 'linear',
        duration: delayBetweenPoints,
        from: NaN, // the point is initially skipped
        delay(ctx) {
            if (ctx.type !== 'data' || ctx.xStarted) {
                return 0;
            }
            ctx.xStarted = true;
            return ctx.index * delayBetweenPoints;
        }
    },
    y: {
        type: 'number',
        easing: 'linear',
        duration: delayBetweenPoints,
        from: previousY,
        delay(ctx) {
            if (ctx.type !== 'data' || ctx.yStarted) {
                return 0;
            }
            ctx.yStarted = true;
            return ctx.index * delayBetweenPoints;
        }
    }
};

const config = {
    type: 'line',
    data: {
        datasets: [{
            label: 'car',
            borderColor: "red",
            tension: 0.4,
            borderWidth: 1,
            radius: 0,
            data: data1,
        },
        {
            label: 'wheel',
            borderColor: "blue",
            tension: 0.4,
            borderWidth: 1,
            radius: 0,
            data: data2,
        }]
    },
    options: {
        animation,
        interaction: {
            intersect: false
        },
        plugins: {
            legend: true
        },
        scales: {
            x: {
                type: 'linear'
            }
        }
    }
};

//Graph
const chart = new Chart(ctx, config)
var graphButton = document.getElementsByClassName("btn btn-success graph")
var numberOfClicks = 0;
for (var j = 0; j<2; j++){
    graphButton[j].addEventListener('click', () => {

        numberOfClicks += 5;
        for (var i = 0; i < sizeOutput; i++) {
            data1.push({x: parseFloat(ocataveOutputJson.values[i].time)+numberOfClicks, y: parseFloat(ocataveOutputJson.values[i].wheel)});
            data2.push({x: parseFloat(ocataveOutputJson.values[i].time)+numberOfClicks, y: parseFloat(ocataveOutputJson.values[i].car)});
        }
        chart.update();
        for (var i = 0; i < sizeOutput; i++) {
            data1.shift();
            data2.shift();
        }
        
    })
}

