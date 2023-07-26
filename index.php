<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>Cálculo Horas de Trabalho</h3>
    <br>
    <form action="index.php" method="post">
        Hora inicial &nbsp;
        <input type="time" name="inicial" min="00:00" max="23:59" required/>
        <br>
        Hora final &nbsp;
        <input type="time" name="final" min="00:00" max="23:59" required/>
        <br>
        <input type="submit" value="Calcular"/>
    </form>
</body>

<?php

    function tempo_trabalho($horaA, $horaB){
        $ha = strtotime($horaA);
        $hb = strtotime($horaB);
        $segundos = $hb - $ha;
        $hora = intdiv($segundos, 3600);
        $segundos = $segundos - (3600 * $hora);
        $minutos = intdiv($segundos, 60);

        if($hora < 10 ){
            $hora = "0".$hora;
        }
        if($minutos < 10 ){
            $minutos = "0".$minutos;
        }
        return $hora.":".$minutos;
    }




    $inicio = $_POST['inicial'];
    $fim = $_POST['final'];

	if (isset($inicio) and isset($fim)){ 
        echo $inicio."\t".$fim;
        $tempo = tempo_trabalho($inicio, $fim);
        echo "<br><br>".$tempo;
    }    
?>

</html>