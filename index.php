<?php

//display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("config.php");

//databaza pripojenie, ak bude treba
// try {
//     $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
//     // set the pdo error mode to exception
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     //echo "Connected successfully"; 
// } catch (PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
// }

$output = "";
//TU ukladat logy do databazy
echo exec('octave-cli --eval "pkg load control; m=5; m2 = 320;k1 = 80000; k2 = 500000"', $output);
var_dump($output);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <!--  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js" integrity="sha512-GMGzUEevhWh8Tc/njS0bDpwgxdCJLQBWG3Z2Ct+JGOpVnEmjvNx6ts4v6A2XJf1HOrtOsfhv3hBKpK9kE5z8AQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js" integrity="sha512-UXumZrZNiOwnTcZSHLOfcTs0aos2MzBWHXOHOuB0J/R44QB0dwY5JgfbvljXcklVf65Gc4El6RjZ+lnwd2az2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!--  -->
    <title>Document</title>
</head>

<body>
    <div class="container">
        <button id="switch_language" class="btn btn-success">Zmeniť jazyk / Change language</button>
        <div id="sk" style="display: none;">
            <br>
            <p>Slovenčina</p>
            <div class="d-flex justify-content-center">
                <div class="mb-3">
                    <h2>Vstupy</h2>
                </div>
            </div>

            <div class="d-flex justify-content-center flex-column">

                <!-- Formy so sebou nemaju nic spolocne, kazdy robi nieco ine  -->
                <!-- Form pre zadavanie hodnoty 'r' na vykreslenie animacie -->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="r" class="form-label">r</label>
                            <input type="text" name="r" id="r" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="rInput" class="btn btn-success" value="Spustiť animáciu">
                        </div>
                    </form>
                </div>



                <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
        ten treba niekam vypisat, mozno staci len pod form TODO)-->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="command" class="form-label">Príkaz na výpočet</label>
                            <input type="text" name="command" id="command" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="commandInput" class="btn btn-success" value="Vypočítať">
                        </div>
                    </form>
                </div>

                <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                <!-- Tlacitko na stihanutie celej stranky / navodu do pdf -->
            </div>
        </div>

        <div id="en">
            <br>
            <p>English</p>
            <div class="d-flex justify-content-center">
                <div class="mb-3">
                    <h2>Inputs</h2>
                </div>
            </div>

            <div class="d-flex justify-content-center flex-column">

                <!-- Formy so sebou nemaju nic spolocne, kazdy robi nieco ine  -->
                <!-- Form pre zadavanie hodnoty 'r' na vykreslenie animacie -->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="r" class="form-label">r</label>
                            <input type="text" name="r" id="r" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="rInput" class="btn btn-success" value="Start Animation">
                        </div>
                    </form>
                </div>



                <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
        ten treba niekam vypisat, mozno staci len pod form TODO)-->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="command" class="form-label">Command to compute</label>
                            <input type="text" name="command" id="command" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="commandInput" class="btn btn-success" value="Compute">
                        </div>
                    </form>
                </div>

                <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                <!-- Tlacitko na stihanutie celej stranky / navodu do pdf -->
            </div>
        </div>

        <!-- Graf -->
        <div class="container">
            <canvas id="myChart"></canvas>
        </div>
        
        <!-- Animacia -->
        <div>

        </div>
    </div>


    <script>
        //Dvojjazycnost na buttonclick
        var btn = document.getElementById("switch_language");
        var en_div = document.getElementById("en");
        var sk_div = document.getElementById("sk");
        btn.addEventListener('click', () => {
            if (en_div.style.display === 'none' && sk_div.style.display === 'block') {
                en_div.style.display = 'block';
                sk_div.style.display = 'none';
            } else {
                en_div.style.display = 'none';
                sk_div.style.display = 'block';
            }
        })
        //


        // let myChart = document.getElementById('myChart').getContext('2d');
        // 
        const ctx = document.getElementById('MyChart');
        //datasety
        const data = {
            labels: [],
            datasets: [{
                label: 'graf',
                data: [],
                fill: false,
                borderColor: 'red',
                tension: 0,
            }]
        };
        //config grafu
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    // zoom: {
                    zoom: {
                        wheel: {
                            enabled: true,
                        },
                        pinch: {
                            enabled: true,
                        },
                        mode: 'xy',
                    }
                }
            }
        }
        //pushovanie dat
        const chart = new Chart(ctx, config)
        const source = "";
        let Update = true
        source.addEventListener("message", event => {
            if (Update === true) {
                const data = JSON.parse(event.data)
                console.log(data)
                chart.data.labels.push(data.x);
                chart.data.datasets[0].data.push(data.y1)
                chart.update();
            }
        })
        // 
    </script>
</body>

</html>