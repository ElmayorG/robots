<?php
for ($i = 0; $i <= 1; $i++){
$file = file('https://www.indeed.com.mx/jobs?q=medio+tiempo&sort=date&start='.$i*10);
$encontrado = false;
$lineas = 0;
$nombreComp = false;
$boldireccion = false;
$boldinero = false;
$sidinero = false;
$boldescripcion = false;
$sidescipcion = false;
//Datos para insertar en la base de datos
$linkoferta = '';
$quienPostula = 'https://www.indeed.com.mx';
$trabajoTitle = '';
$trabajoCompa = '';
$direccionOfe = '';
$sueldo_ofert = '';
$descripcionof= '';

    foreach($file as $linenum => $line){
    //echo "<b>Line #{$linenum}</b> ".htmlspecialchars($line).'</br>';
        //data-tn-component="organicJob" EMPIEZA TRABAJO
        //6 lineas despues sale el link del trabajo
        //5 lineas despues del link sale el titulo del trabajo
        //4 lineas despues del titulo sale el nombre de la compañia, en caso de contener un <a (hipervinculo) saldra cinco despues del hipervinculo.
        //2 lineas despues del nombre sale la hubicacion, en caso de contener hipervinculo sera 6 despues.
        //5 lineas despues de la ubicacion sale la paga (en caso de tenerla) <span class=summary> requerimientos <span class="no-wrap">  Sueldo (ambos en la proxima linea)
        //4 ineas despues de la paga salen los requrimientos, en caso de no tener paga salen 5 despues de la hubicacion
        $busca = explode('data-tn-component="organicJob"', $line);
        $cuenta = count($busca);
        if($cuenta > 1){
            $encontrado = true;
            $lineas = 1;
            $nombreComp = false;
            $boldireccion = false;
            $boldinero = false;
            $sidinero = false;
            $boldescripcion = false;
            $sidescipcion = false;
            $insertarDatosTrabajo = '';
            $buscarDatosIguales = '';
            echo "trabajo encontrado !<br>";
        }
        if($encontrado){
            $lineas++;
        }
        if($lineas == 8 && $encontrado){
            $buscaLink = explode('target="_blank"', $line);
            $nuevoLink = str_replace(' ','', $buscaLink[0]);
            $nuevoLink = str_replace('href="','', $nuevoLink);
            $nuevoLink = str_replace('"','', $nuevoLink);
            echo 'Link: '.$nuevoLink.'<br>';
            $linkoferta = $nuevoLink;
        }
        if($lineas == 13 && $encontrado){
            $buscaTitulo = explode('data-tn-element="jobTitle">', $line);
            $contar = count($busca);
            if($contar > 0){
                $titulo = str_replace('</a>','', $buscaTitulo[1]);
                $titulo = str_replace('<b>','', $titulo);
                $titulo = str_replace('</b>','',$titulo);
                echo "Titulo-Trabajo: ".$titulo."<br>";
                $trabajoTitle = $titulo;
            }   
        }
        if($lineas == 17 && $encontrado){
            $buscarCompany = explode('</span>', $line);
            $verificar = explode('<a', $buscarCompany[0]);
            $verificarExistencia = count($verificar);
            if($verificarExistencia == 1){

                echo 'Compa&ntilde;ia: '.$buscarCompany[0].'<br>';
                $nombreComp = true;
                $trabajoCompa = $buscarCompany[0];
            }else{

                $nombreComp = false;
            }
        }
        if($lineas == 19 && $encontrado && $nombreComp == true){
            $buscaDireccion = explode('span class="location">', $line);
            $direccion = str_replace('</span><table cellpadding=0 cellspacing=0 border=0>', '', $buscaDireccion[1]);
            $boldireccion = true;
            echo 'Direccion: '.$direccion.'<br>';
            $direccionOfe = $direccion;
        }
        if($lineas == 22 && $encontrado && $nombreComp==false){
            $buscarCompany = explode('</a></span>', $line);
            echo "Compa&ntilde;&iacute;a: ".$buscarCompany[0].'<br>';
            $trabajoCompa = $buscarCompany[0];
            $nombreComp = true;
        }
        //Dinero peque
        if($lineas == 27 && $encontrado && $nombreComp && $boldinero == false){
            $verificaSueldo = explode('<span class=', $line);
            $contarSueldo = count($verificaSueldo);
            if($contarSueldo > 1){
                if(trim($verificaSueldo[1]) == 'summary>'){
                    echo "Sueldo: No especificado <br>";
                    $sidinero = false;
                    $boldinero = true;
                    $boldescripcion = true;
                }else if(trim($verificaSueldo[1]) == '"no-wrap">'){
                    echo "Sueldo especificado <br>";
                    $sidinero = true;
                }
            }else{
                echo "Sueldo: No especificado <br>";
                $sidinero = false;
                $boldinero = true;
            }
        }
        if($lineas == 28 && $encontrado && $boldinero == false && $sidinero == true){
            $buscaDinero = str_replace('</span>', '', $line);
            $boldinero = true;
            echo "Sueldo: ".$buscaDinero.'<br>';
            $sueldo_ofert = $buscaDinero;
        }
        if($lineas == 28 && $encontrado && $boldescripcion == true && $sidescipcion == false){
            $buscaDescrip = str_replace('</span>', '', $line);
            $descripcion = str_replace('<b>', '', $buscaDescrip);
            $descripcion = str_replace('</b>', '', $descripcion);
            echo "Descripcion: ".$descripcion;
            $boldescripcion = true;
            $sidescipcion = true;
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';
        }
        //Busca direccin
        if($lineas == 28 && $encontrado && $nombreComp == true && $boldireccion == false){
            $buscaDireccion = explode('<span class="location">', $line);
            $direccion = str_replace('</span><table cellpadding=0 cellspacing=0 border=0>','',$buscaDireccion[1]);
            echo "Direccion: ".$direccion.'<br>';
            $direccionOfe = $direccion;
            $boldireccion = true;
        }
        //Obtener descripcion
        if($lineas == 32 && $boldinero && $encontrado && $sidinero && $boldescripcion == false && $sidescipcion == false){
            $descripcion = str_replace('</span>', '', $line);
            echo "Descripcion: ".$descripcion;
            $boldescripcion = true;
            $sidescipcion = true;
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';
        }
        if($lineas == 33 && $encontrado && $sidinero == false && $boldinero && $boldescripcion == true && $sidescipcion == false){
            $descripcion = str_replace('</span>', '', $line);
            echo "Descripcion: ".$descripcion;
            $boldescripcion = true;
            $sidescipcion = true;
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';           
        }
        //Dinero Grande
        if($lineas == 32 && $encontrado && $nombreComp && $boldinero == false){
            $verificaSueldo = explode('<span class=', $line);
            $contarSueldo = count($verificaSueldo);
            if($contarSueldo > 1){
                if(trim($verificaSueldo[1]) == 'summary>'){
                    echo "Sueldo: No especificado <br>";
                    $sidinero = false;
                    $boldinero = true;
                    $boldescripcion = true;
                }else if(trim($verificaSueldo[1]) == '"no-wrap">'){
                    echo "Sueldo especificado <br>";
                    $sidinero = true;
                }
            }else{
                echo "Sueldo: No especificado <hr>";
                $sidinero = false;
                $boldinero = true;
            }
        }
        if($lineas == 33 && $encontrado && $boldinero == false && $sidinero == true){
            $buscaDinero = str_replace('</span>', '', $line);
            $boldinero = true;
            echo "Sueldo: ".$buscaDinero.'<hr>';
            $sueldo_ofert = $buscaDinero;
        }
        if($lineas == 33 && $encontrado && $boldescripcion == true && $sidescipcion == false){
            $buscaDescrip = str_replace('</span>', '', $line);
            $descripcion = str_replace('<b>', '', $buscaDescrip);
            $descripcion = str_replace('</b>', '', $descripcion);
            echo "descripcion: ".$descripcion;
            $sidescipcion = true;
            $boldescripcion = true;
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';
        }
         //Obtener DESCRIPCION
        if($lineas == 37 && $boldinero && $encontrado && $sidinero && $boldescripcion == false && $sidescipcion == false){
            $descripcion = str_replace('</span>', '', $line);
            echo "Descripcion: ".$descripcion;
            $boldescripcion = true;
            $sidescipcion = true;
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';
        }
        if($lineas == 38 && $encontrado && $sidinero == false && $boldinero && $boldescripcion == true && $sidescipcion == false){
            $descripcion = str_replace('</span>', '', $line);
            echo "Descripcion: ".$descripcion;
            $boldescripcion = true;
            $sidescipcion = true; 
            $descripcionof = $descripcion;
            $buscarDatosIguales = 'SELECT * FROM empleos WHERE link_ofert ="'.$linkoferta.'" AND titulo="'.$trabajoTitle.'"';
            $insertarDatosTrabajo = 'INSERT INTO empleos (id_empleo,titulo,link_ofert,compania,direccion,sueldo_ofert,descripcionof,tipo,peso,postulatnte)VALUES'.
            $insertarDatosTrabajo.= '("'.rand(0,99999999).'","'.$trabajoTitle.'","'.$linkoferta.'","'.$trabajoCompa.'","'.$direccionOfe.'","'.$sueldo_ofert.'","'.$descripcion.'","1","5")';
            echo $insertarDatosTrabajo.'<hr>';          
        }   
    }
}
?>