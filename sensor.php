<?php 
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $dbname = "esp32";

    $conn = new mysqli($servername,$username,$password,$dbname);

    $temp = isset($_POST['temperature']) ? $_POST['temperature'] : null;
    $umid = isset($_POST['humidity']) ? $_POST['humidity'] : null;

    if ($temp != null && $umid != null){
        $sql = "INSERT INTO dht_data (temperatura, umidade) VALUES ('$temp', '$umid')";

        $conn->query($sql);
    }

    $sql = "SELECT * FROM dht_data ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $temp = $row["temperatura"];
        $umid = $row["umidade"];

        $sql = "SELECT temperatura, data_hora
                FROM dht_data
                WHERE temperatura = (SELECT min(temperatura) as temperatura
                                     FROM dht_data
                                     WHERE temperatura <> 0)
                                     LIMIT 1";
        $resultMenor = $conn->query($sql);

        if($resultMenor->num_rows > 0){
            $row = $resultMenor->fetch_assoc();
            $menorTemp = $row["temperatura"];
            $menorDataHora = date('d/m/Y H:i:s', strtotime($row["data_hora"]));
        } else {
            $menorTemp = "--";
            $menorDataHora = "--";
        }
        $sql = "SELECT temperatura, data_hora
                FROM dht_data
                WHERE temperatura = (SELECT max(temperatura) as temperatura
                                     FROM dht_data
                                     WHERE temperatura <> 0)
                                     LIMIT 1";
        $resultMaior = $conn->query($sql);

        if($resultMaior && $resultMaior ->num_rows > 0){
            $row = $resultMaior->fetch_assoc();
            $maiorTemp = $row["temperatura"];
            $maiorDataHora = date('d/m/Y H:i:s', strtotime($row["data_hora"]));
        } else {
            $maiorTemp = "--";
            $maiorDataHora = "--";
        }
        $sql = "SELECT umidade, data_hora
                FROM dht_data
                WHERE umidade = (SELECT min(umidade) as umidade
                                    FROM dht_data
                                    WHERE temperatura<>0)
                LIMIT 1";


        $resultMenorUmidade = $conn->query($sql);
        if($resultMenorUmidade->num_rows > 0){
            $row = $resultMenorUmidade->fetch_assoc();
            $menorUmidade = $row["umidade"];
            $menorUmidadeDataHora = date ('d/m/Y H:i:s', strtotime($row["data_hora"]));
        } else {
            $menorUmidade = "--";
            $menorUmidadeDataHora = "--";
        }
        $sql = "SELECT umidade, data_hora
                FROM dht_data
                WHERE umidade = (SELECT max(umidade) as umidade
                                    FROM dht_data
                                    WHERE temperatura <>0)
                LIMIT 1";
        $resultMaiorUmidade = $conn-> query ($sql);

        if($resultMaiorUmidade->num_rows>0){
            $row = $resultMaiorUmidade->fetch_assoc();
            $maiorUmidade = $row["umidade"];
            $maiorUmidadeDataHora = date('d/m/Y H:i:s', strtotime($row["data_hora"]));
        } else {
            $maiorUmidade = "--";
            $maiorUmidadeDataHora = "--";
        }
    } else {
        $temp = "--";
        $umid = "--";
        $menorTemp = "--";
        $menorDataHora = "--";
        $maiorTemp = "--";
        $maiorDataHora = "--";
        $menorUmidade = "--";
        $menorUmidadeDataHora = "--";
        $maiorUmidade = "--";
        $maiorUmidadeDataHora= "--";
    }


    $conn->close();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="refresh" content="20"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leitura do Sensor DHT11 - IoT Redes de Computadores</title>
    <style>
        body{
            background-color:gray;
            font-family: 'Verdana';
        }
        .container{
            display:flex;
            justify-content:center;
            margin-top:150px;
        }
        .containercenter{
            width:500px;
            height:325px;
            background-color:#f0f0f0;
            display:flex;
            justify-content:center;
            padding:20px;
            align-items:center;
            border-radius:8px;
        }
        .containercenter p,h2 {
            justify-content:center;
            align-items:center;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="containercenter">
            <div class="text">
                <h2>Leitura do Sensor DHT11</h2>
                <p>🌡️ Temperatura: 
                    <?php echo $temp;?>°C
                </p>
                <p>💧 Umidade: 
                    <?php echo $umid; ?>%
                </p>
                <p>📅 Data/Hora de Verificação:
                    <?php date_default_timezone_set('America/Sao_Paulo'); ?> -->
                    <?php echo date ('d/m/Y H:i:s'); ?>
                </p>
                <p>🥵 Maior Temperatura:
                    <?php echo $maiorTemp . " °C ás " . $maiorDataHora; ?>
                </p>
                <p>🥶 Menor Temperatura:
                    <?php echo $menorTemp . " °C ás " . $menorDataHora; ?>
                </p>
                <p>⬇️💧 Menor Umidade: 
                    <?php echo $menorUmidade . " % ás " . $menorUmidadeDataHora;?>
                </p>
                <p>⬆️💧 Maior Umidade:
                    <?php echo $maiorUmidade . " % ás " . $maiorUmidadeDataHora;?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>