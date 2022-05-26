<?php

//display errors
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require("config.php");

//databaza pripojenie, ak bude treba
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the pdo error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; 
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

//Overenie dopytu cez API key
if ($_POST['key'] == $APIkey) {

    //Vypocet a pristupovanie k Octave, Vystup ulozeny do output.json
    if ($_POST['rSK'] != null || $_POST['rEN'] != null) {

        //Vyska prekazky 'r'
        $infoObstacle = "success";
        $inputObstacle = "";                //r
        if ($_POST['rSK'] != null) {
            if ((float)($_POST['rSK']) > 2) {
                $inputObstacle = "2";
            } else if ((float)($_POST['rSK']) < -2) {
                $inputObstacle = "-2";
            } else if (($_POST['rSK']) == "0") {
                $inputObstacle = "0.1";
            } else {
                $inputObstacle = $_POST['rSK'];
            }
        }
        if ($_POST['rEN'] != null) {
            if ((float)($_POST['rEN']) > 2) {
                $inputObstacle = "2";
            } else if ((float)($_POST['rEN']) < -2) {
                $inputObstacle = "-2";
            } else if (($_POST['rEN']) == "0") {
                $inputObstacle = "0.1";
            } else {
                $inputObstacle = $_POST['rEN'];
            }
        }

        $errorFlag = 0;
        // Zapisovanie Logov do databazy
        if (floatval($inputObstacle) == 0) {
            $infoObstacle = "error - Not number";
            $errorFlag = 1;
        } else if (strpos($inputObstacle, ",")) {
            $errorFlag = 1;
            $infoObstacle = "error - used ',' instead of '.'";
        }

        $sql = "INSERT INTO requests (date, obstacleHeight, info) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        //echo substr($weatherResponse->location->localtime,10). ":00";
        $result = $stmt->execute([
            date("Y-m-d h:i:sa"), strval($inputObstacle), $infoObstacle
        ]);

        if ($errorFlag == 0) {
            //Octave vypocet
            $output = "";
            echo exec('octave-cli --eval "pkg load control; m1 = 2500; m2 = 320;
    k1 = 80000; k2 = 500000;
    b1 = 350; b2 = 15020;
    A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];
    B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];
    C=[0 0 1 0]; D=[0 0];
    Aa = [[A,[0 0 0 0]\'];[C, 0]];
    Ba = [B;[0 0]];
    Ca = [C,0]; Da = D;
    K = [0 2.3e6 5e8 0 8e6];
    sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);
    t = 0:0.01:5;
    r="' . $inputObstacle . '";
    initX1=0; initX1d=0;
    initX2=0; initX2d=0;
    [y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,[initX1;initX1d;initX2;initX2d;0]); x"', $outputOctave);

            //Parsovanie dat(x1,x3) z vektora 'x'
            $sizeOutput = 503;
            $parsedArray = array();
            $time = 0;
            for ($i = 2; $i < $sizeOutput; $i++) {
                //filter dat do array
                $splitOutput = explode(" ", $outputOctave[$i]);
                $splitOutput = array_filter($splitOutput);
                $splitOutput = array_values($splitOutput);
                //naplni array
                $time = round($time + 0.01, 3);
                $parsedArray[$i - 2] = array('wheel' => $splitOutput[2], 'car' => $splitOutput[0], 'time' => $time);
            }
            $response['values'] = $parsedArray;
            //Zapis do json
            $fp = fopen('output.json', 'w');
            fwrite($fp, json_encode($response));
            fclose($fp);

        } else {
            echo '<script>alert("Wrong Input")</script>';
        }
    } 

    //Vypocet commandu cez Octave
    if ($_POST['commandSK'] != null || $_POST['commandEN'] != null) {

        $infoCommand = "success";
        $inputCommand = "";
        if ($_POST['commandSK'] != null) {
            $inputCommand = $_POST['commandSK'];
        }
        if ($_POST['commandEN'] != null) {
            $inputCommand = $_POST['commandEN'];
        }

        $errorFlag = 0;
        // Zapisovanie Logov do databazy
        if (floatval($inputCommand) == 0) {
            $infoCommand = "error - Not number";
            $errorFlag = 1;
        } else if (strpos($inputCommand, ",")) {
            $errorFlag = 1;
            $infoCommand = "error - used ',' instead of '.'";
        }

        $sql = "INSERT INTO requests (date, command, info) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            date("Y-m-d h:i:sa"), strval($inputCommand), $infoCommand
        ]);

        if ($errorFlag == 0){
            exec('octave-cli --eval "pkg load control;"' . $inputCommand . '""', $outCommandValue);
        } else {
            echo '<script>alert("Wrong Input")</script>';
        }
    }

    
    $sql = "SELECT * FROM requests";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fp = fopen('csvDBoutput.csv', 'w');
    foreach ($result as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);



    //var_dump($result);
    //   $connect = mysqli_connect("localhost", "root", "", "testing");
    //   header('Content-Type: text/csv; charset=utf-8');
    //   header('Content-Disposition: attachment; filename=data.csv');
    //   $output = fopen("php://output", "w");
    //   fputcsv($output, array('ID', 'Name', 'Address', 'Gender', 'Designation', 'Age'));
    //   $query = "SELECT * from tbl_employee ORDER BY id DESC";
    //   $result = mysqli_query($connect, $query);
    //   while($row = mysqli_fetch_assoc($result))
    //   {
    //        fputcsv($output, $row);
    //   }
    //   fclose($output);
 
}

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
    <title>Car Go Brrr...</title>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center flex-column" id="backboard">
            
            <div id="sk" style="display: none;">
                <div style="margin: 10px;">
                    <nav style="border-radius: 7px; padding: 10px;" class="navbar navbar-expand-lg navbar-dark bg-secondary">
                        <form class="form-inline">
                            <button class="btn btn-success switch" type="button">EN / SK</button>
                        </form>
                        <button class="navbar-toggler navbar-brand" type="button" data-toggle="collapse" data-target="#navbarNav SK" aria-controls="navbarNav SK" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav SK">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="index.php">Domov</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="guide.html">Návod</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="mb-3">
                        <h2>Vstupy</h2>
                    </div>
                </div>

                <div class="d-flex justify-content-center flex-column" id="formBackground1">

                    <!-- Formy so sebou nemaju nic spolocne, kazdy robi nieco ine  -->
                    <!-- Form pre zadavanie hodnoty 'r' na vykreslenie animacie -->
                    <div class="d-flex justify-content-center">
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="r" class="form-label">Výška prekážky (r):</label>
                                <input type="text" name="rSK" id="rSK" class="form-control">
                            </div>
                            <div class="mb-3">
                                <input type="submit" id="buttonGraphSK" name="buttonGraphSK" class="btn btn-warning graph" value="Spustiť Animáciu">
                            </div>
                            <div class="mb-3">
                                <input type="hidden" name="key" class="form-control" value="APIkey2000">
                            </div>
                        </form>
                    </div>

                    <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
                    ten treba niekam vypisat, mozno staci len pod form TODO)-->
                    <div class="d-flex justify-content-center">
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="command" class="form-label">Výpočet:</label>
                                <input type="text" name="commandSK" id="commandSK" class="form-control">
                            </div>
                            <div class="mb-3">
                                <input type="submit" name="commandInputSK" class="btn btn-warning" value="Výpočet">
                            </div>
                            <div class="mb-3">
                                <input type="hidden" name="key" class="form-control" value="APIkey2000">
                            </div>
                        </form>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="mb-3">
                            <?php
                            if (isset($outCommandValue[0])) {
                            ?>
                                <label class="form-label">------------------------------</label>
                                <br>
                                <h4>Výsledok:: </h4>
                                <br>
                                <label class="form-label"><?php
                                                            echo $outCommandValue[0];
                                                            ?></label>
                                <br>
                                <label class="form-label">------------------------------</label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                    <!-- Tlacitko na stihanutie celej stranky / navodu do pdf -->
                    <div class="d-flex justify-content-center">
                        <div class="mb-3">
                            <button class="btn btn-success abc"><a href="csvDBoutput.csv" download>Stiahnúť logy do CSV</a></button>
                        </div>
                    </div>

                    <!-- toto nehat uplne na konci  -->
                    <div class="d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="graphShowSK">
                            <label class="form-check-label" for="graphShow"> Ukáž graf</label><br>
                        </div>
                    </div>
                </div>
            </div>

            <div id="en">
                <div style="margin: 10px;">
                    <nav style="border-radius: 7px; padding: 10px;" class="navbar navbar-expand-lg navbar-dark bg-secondary">
                        <form class="form-inline">
                            <button class="btn btn-success switch" type="button">EN / SK</button>
                        </form>
                        <button class="navbar-toggler navbar-brand" type="button" data-toggle="collapse" data-target="#navbarNav EN" aria-controls="navbarNav EN" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav EN">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="index.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="guide.html">Guide</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="mb-3">
                        <h2>Inputs</h2>
                    </div>
                </div>

                <div class="d-flex justify-content-center flex-column" id="formBackground2">

                    <!-- Formy so sebou nemaju nic spolocne, kazdy robi nieco ine  -->
                    <!-- Form pre zadavanie hodnoty 'r' na vykreslenie animacie -->
                    <div class="d-flex justify-content-center">
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="r" class="form-label">Obstackle height (r):</label>
                                <input type="text" name="rEN" id="rEN" class="form-control">
                            </div>
                            <div class="mb-3">
                                <input type="submit" id="buttonGraphEN" name="buttonGraphEN" class="btn btn-warning graph" value="Start Animation">
                            </div>
                            <div class="mb-3">
                                <input type="hidden" name="key" class="form-control" value="APIkey2000">
                            </div>
                        </form>
                    </div>

                    <!-- Form pre zadavanie comandov na vypocitanie cez Octave (posle command napr. '1+1' octave vrati spat vysledok,
                     ten treba niekam vypisat, mozno staci len pod form TODO)-->
                    <div class="d-flex justify-content-center">
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="command" class="form-label">Command to compute:</label>
                                <input type="text" name="commandEN" id="commandEN" class="form-control">
                            </div>
                            <div class="mb-3">
                                <input type="submit" name="commandInputEN" class="btn btn-warning" value="Compute">
                            </div>
                            <div class="mb-3">
                                <input type="hidden" name="key" class="form-control" value="APIkey2000">
                            </div>
                        </form>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="mb-3">
                            <?php
                            if (isset($outCommandValue[0])) {
                            ?>
                                <label class="form-label">------------------------------</label>
                                <br>
                                <h4>Result:: </h4>
                                <br>
                                <label class="form-label"><?php
                                                            echo $outCommandValue[0];
                                                            ?></label>
                                <br>
                                <label class="form-label">------------------------------</label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Sem tlacitko na stiahnutie logov (a.k.a. stiahnut udaje z databazy ako CSV subor.) -->
                    <div class="d-flex justify-content-center">
                        <div class="mb-3">
                            <button class="btn btn-success abc"><a href="csvDBoutput.csv" download>Download logs as CSV</a></button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="graphShowEN">
                            <label class="form-check-label" for="graphShow"> Show graph</label><br>
                        </div>
                    </div>
                    
                </div>
            </div>

            <!-- Graf -->
            <div class="d-flex justify-content-center" id="divCanvas" style="display: none">
                <canvas id="myChart"></canvas>
            </div>

        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js" integrity="sha512-GMGzUEevhWh8Tc/njS0bDpwgxdCJLQBWG3Z2Ct+JGOpVnEmjvNx6ts4v6A2XJf1HOrtOsfhv3hBKpK9kE5z8AQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js" integrity="sha512-UXumZrZNiOwnTcZSHLOfcTs0aos2MzBWHXOHOuB0J/R44QB0dwY5JgfbvljXcklVf65Gc4El6RjZ+lnwd2az2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="script.js"></script>


        <script>
            //Dvojjazycnost na buttonclick
            var btn = document.getElementsByClassName("btn btn-success switch");
            var en_div = document.getElementById("en");
            var sk_div = document.getElementById("sk");

            for (var j = 0; j < 2; j++) {
                btn[j].addEventListener('click', () => {
                    if (en_div.style.display === 'none' && sk_div.style.display === 'block') {
                        en_div.style.display = 'block';
                        sk_div.style.display = 'none';
                    } else {
                        en_div.style.display = 'none';
                        sk_div.style.display = 'block';
                    }
                })
            }

            //Skrytie grafu dorobit tie id a upravit divka aby mali style display: none
            // id checkbox pridat
            var hide = document.getElementById('graphShow');
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