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

//------------------------------------------------------------------

if (isset($_POST['buttonGraphSK']) || isset($_POST['buttonGraphEN'])) {

    //echo "hej";
    echo $_REQUEST['rEN'];


    // $output = "";
    // echo exec('octave-cli --eval "pkg load control; m1 = 2500; m2 = 320;
    // k1 = 80000; k2 = 500000;
    // b1 = 350; b2 = 15020;
    // A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];
    // B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];
    // C=[0 0 1 0]; D=[0 0];
    // Aa = [[A,[0 0 0 0]\'];[C, 0]];
    // Ba = [B;[0 0]];
    // Ca = [C,0]; Da = D;
    // K = [0 2.3e6 5e8 0 8e6];
    // sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);
    // t = 0:0.01:5;
    // r =-0.9;
    // initX1=0; initX1d=0;
    // initX2=0; initX2d=0;
    // [y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,[initX1;initX1d;initX2;initX2d;0]); x"', $output);

    // $sizeOutput = 503;
    // $parsedArray = array();
    // $time = 0;
    // for ($i = 2; $i < $sizeOutput; $i++) {
    //     //filter dat do array
    //     $splitOutput = explode(" ", $output[$i]);
    //     $splitOutput = array_filter($splitOutput);
    //     $splitOutput = array_values($splitOutput);
    //     //naplni array
    //     $time = round($time + 0.01, 3);
    //     $parsedArray[$i - 2] = array('wheel' => $splitOutput[2], 'car' => $splitOutput[0], 'time' => $time);
    // }
    // $response['values'] = $parsedArray;
    // $fp = fopen('output.json', 'w');
    // fwrite($fp, json_encode($response));
    // fclose($fp);
}

if (isset($_POST['commandInputSK']) || isset($_POST['commandInputEN']) ) {
    echo "hej";
}
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
                            <label for="r" class="form-label">Výška prekážky (r)</label>
                            <input type="text" name="rSK" id="rSK" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" id="buttonGraphSK" name="buttonGraphSK" class="btn btn-success graph" value="Spustiť Animáciu">
                        </div>
                    </form>
                </div>

                <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
        ten treba niekam vypisat, mozno staci len pod form TODO)-->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="command" class="form-label">Výpočet</label>
                            <input type="text" name="commandSK" id="commandSK" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="commandInputSK" class="btn btn-success" value="Výpočet">
                        </div>
                    </form>
                </div>

                <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                <!-- Tlacitko na stihanutie celej stranky / navodu do pdf -->

                <!-- toto nehat uplne na konci  -->
                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="graphShow">
                        <label class="form-check-label" for="graphShow"> Ukáž graf</label><br>
                        <input class="form-check-input" type="checkbox" id="animationShow">
                        <label class="form-check-label" for="animationShow"> Ukáž animáciu</label><br>
                    </div>
                </div>

            </div>
        </div>

        <div id="en">

            <div class="d-flex justify-content-center">
                <br>
                <p>English</p>
            </div>

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
                            <label for="r" class="form-label">Obstackle height (r)</label>
                            <input type="text" name="rEN" id="rEN" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" id="buttonGraphEN" name="buttonGraphEN" class="btn btn-success graph" value="Start Animation">
                        </div>
                    </form>
                </div>

                <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
        ten treba niekam vypisat, mozno staci len pod form TODO)-->
                <div class="d-flex justify-content-center">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="command" class="form-label">Command to compute</label>
                            <input type="text" name="commandEN" id="commandEN" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="submit" name="commandInputEN" class="btn btn-success" value="Compute">
                        </div>
                    </form>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="graphShow">
                        <label class="form-check-label" for="graphShow"> Show graph</label><br>
                        <input class="form-check-input" type="checkbox" id="animationShow">
                        <label class="form-check-label" for="animationShow"> Show animation</label><br>
                    </div>
                </div>
                <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                <!-- Tlacitko na stihanutie celej stranky / navodu do pdf -->
            </div>
        </div>

        <!-- Graf -->
        <div class="d-flex justify-content-center" id="divCanvas" style="display: none">
            <canvas id="myChart"></canvas>
        </div>

        <!-- Animacia -->
        <div class="d-flex justify-content-center" id="divAnimation">

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js" integrity="sha512-GMGzUEevhWh8Tc/njS0bDpwgxdCJLQBWG3Z2Ct+JGOpVnEmjvNx6ts4v6A2XJf1HOrtOsfhv3hBKpK9kE5z8AQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js" integrity="sha512-UXumZrZNiOwnTcZSHLOfcTs0aos2MzBWHXOHOuB0J/R44QB0dwY5JgfbvljXcklVf65Gc4El6RjZ+lnwd2az2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="script.js"></script>


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

        //Skrytie grafu dorobit tie id a upravit divka aby mali style display: none
        // id checkbox pridat
        // var hide = document.getElementById('graphShow');
        // hide.addEventListener('click', () => {
        //     // id canvasu pridat
        //     var divEle = document.getElementById('divCanvas');
        //     if (this.checked) {
        //         divEle.style.display = 'block';
        //     } else {
        //         divEle.style.display = 'none';
        //     }
        // });
    </script>
</body>

</html>