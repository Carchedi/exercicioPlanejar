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
    function formata_hora($tempo){
        if(strlen($tempo) > 1){
            $tempo = explode(":",$tempo);
            if(strlen($tempo[0]) == 1){
                $tempo[0] = "0".$tempo[0];
            }
            if(strlen($tempo[1]) == 1){
                $tempo[1] = "0".$tempo[1];
            }
            return $tempo[0].":".$tempo[1];
        }
        return 0;
    }

    function tempo_em_horas($horaA){  
        $horas = intdiv(strtotime($horaA), 3600);
        $minutos = intdiv($horaA - (3600* $horas), 60); 
        if($horas < 10 ){
            $horas = '0'.$horas;
        }
        if($minutos < 10){
            $minutos = '0'.$minutos;
        } 
        return $horas.':'.$minutos;
    }

    function get_periodo_horario($hora){ 
        $hora = explode(":", $hora);
        $hora = $hora[0];  
        if($hora >= 5 and $hora < 22 ){
            return 0; // periodo diurno
        }else{
            return 1; // periodo noturno
        }
    }

    function diferenca_tempo($horaA,$horaB){
        $datetime_1 = date('Y-m-d')." ".$horaA; 
        $datetime_2 = date('Y-m-d')." ".$horaB;

        $start_datetime = new DateTime($datetime_1); 
        $end_datetime = new DateTime($datetime_2);

        if($start_datetime < $end_datetime){
            $end_datetime ->modify('+1 day');
        }

        $diff = $start_datetime->diff($end_datetime); 
        return $diff->h.":".$diff->i; 
    }

    function adiciona_tempo($horaA, $horaB){
        $horaA = explode(":", $horaA);
        $horaB = explode(":",$horaB);
        $total = ($horaA[0] + $horaB[0]).":".($horaA[1] + $horaB[1]);

        $total = explode(":",$total);
        if($total[1] > 59){
            $total[0] = $total[0] + 1;
            $total[1] = $total[1] - 60;
        }
        return $total[0].":".$total[1];
    }


    $inicio = $_POST['inicial'];
    $fim = $_POST['final'];

	if (isset($inicio) and isset($fim)){  
        $total_diurno = 0;
        $total_noturno = 0;
        
        $periodo_inicio = get_periodo_horario($inicio);
        $periodo_fim = get_periodo_horario($fim); 

        if($inicio < $fim){
            if( $periodo_inicio == $periodo_fim){
                if($periodo_inicio == 0){
                    $total_diurno = diferenca_tempo($fim, $inicio);
                }else{
                    $total_noturno = diferenca_tempo($fim, $inicio);
                }
            }else{
                $total_diurno = diferenca_tempo("22:00", $inicio);
                $total_noturno = diferenca_tempo($fim, "22:00");
            }
        }else{
            if( $periodo_inicio == $periodo_fim){
                if($periodo_inicio == 0){
                    $total_diurno = adiciona_tempo(diferenca_tempo("22:00", $inicio),diferenca_tempo($fim,"05:00"));
                    $total_noturno = adiciona_tempo("00:00", "07:00");
                }else{ 
                    $total_noturno = adiciona_tempo(diferenca_tempo("24:00", $inicio),diferenca_tempo("00:00", $fim));
                }
            }else{
                if($periodo_inicio == 0){
                    $total_diurno = diferenca_tempo("22:00", $inicio);
                    $total_noturno = adiciona_tempo("02:00", $fim);
                }else{
                    $total_diurno = diferenca_tempo($fim, "05:00");                    
                    $total_noturno = adiciona_tempo(diferenca_tempo("24:00", $inicio),"05:00");
                }
            }
        }

        echo "<br>Inicio: ".$inicio."     final: ".$fim;
        echo "<br><br> Resultado:<br>Total horas diurnas: ".formata_hora($total_diurno).
             "<br>Total horas noturnas: ".formata_hora($total_noturno);    
    }  
?>
</html>