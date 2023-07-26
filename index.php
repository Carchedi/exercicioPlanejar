<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Calculo Horas Trabalhadas PHP</title>    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body  style="background-color:LightGray;">
    <div class="container">
        <div class="row">
            <p class="h4">Cálculo de Horas de Trabalho</p>  
        </div>
        <div class="row"> 
            <form action="index.php" method="post" class="row" > 
                <div class="col-auto">
                    <label class="form-label" for="inicial">Hora inicial</label>
                    <input id="inicial" type="time" name="inicial" min="00:00" max="23:59" required/>
                </div>
                <div class="col-auto">
                    <label class="form-label" for="final">Hora final</label>
                    <input id="final" type="time" name="final" min="00:00" max="23:59" required/>
                </div> 
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">Calcular</button>
                </div>
            </form> 
        </div> 
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

	if (isset($_POST['inicial']) and isset($_POST['final'])){  

        
        $inicio = $_POST['inicial'];
        $fim = $_POST['final'];

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
                if($periodo_inicio == 0){ 
                    $total_diurno = diferenca_tempo("22:00", $inicio);
                    $total_noturno = diferenca_tempo($fim, "22:00");
                }else{                    
                    $total_diurno = diferenca_tempo("05:00", $inicio);
                    $total_noturno = diferenca_tempo($fim, "05:00");
                }
            }
        }else{
            if( $periodo_inicio == $periodo_fim){
                if($periodo_inicio == 0){
                    $total_diurno = adiciona_tempo(diferenca_tempo("22:00", $inicio),diferenca_tempo($fim,"05:00"));
                    $total_noturno = adiciona_tempo("00:00", "07:00");
                }else{ 
                    $total_diurno = adiciona_tempo("00:00", "17:00");
                    $total_noturno = adiciona_tempo(diferenca_tempo("24:00", $inicio),diferenca_tempo("22:00", $fim)); 
                    $total_noturno = adiciona_tempo("00:00", "07:00");
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

        echo " <div class='col-md-12'>
                    <label class='form-label'>Início ".$inicio."</label> 
                    <label class='form-label'>Final ".$fim."</label>
               </div>";
          
        echo "<table>
                <hr><p class='h6'>Resultado</p></hr>
                <tr>
                    <td>Total horas diurnas: </td><td>".formata_hora($total_diurno)."</td>
                </tr>
                <tr>
                    <td>Total horas noturnas: </td><td>".formata_hora($total_noturno)."</td>
                </tr>
            </table>";
    }  


?>
</html>