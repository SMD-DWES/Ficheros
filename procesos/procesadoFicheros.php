<?php

/**
 * Autor: Sergio Matamoros Delgado
 * Descripción: Realiza una aplicación que permita subir imágenes al servidor y mostrarlas.
 * Los versiones serán las siguientes:
 * V1- Web con 2 opciones: Subir imágenes y mostrar galería de imágenes (todas las imágenes subidas).
 * La web muestra 3 enlaces: Subir imágenes, Ver nombre de la imagen (nombre del archivo), 
 * Ver imágenes.
 * V2- Validar formato de archivos y tamaño permitidos en la subida.
 */

require_once "generatePDF.php";

if(isset($_POST["enviar"])) {

    //Si el fichero existe...
    if(isset($_FILES["fichero"])) {
        
        $fichero = $_FILES["fichero"];

        $extensionCorrecta = null;

        //Refactorizar verificación, ahora se puede hacer con la función whitelist()
        //Validación del tipo/formato del archivo subido al subido al servidor.
        switch ($fichero["type"]) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
            case 'application/pdf':
                $extensionCorrecta = true;
                break;
            default:
                echo "<h2>[ERROR] El archivo contiene una extensión no soportada...</h2>";
                break;
        }

        //Si no hay ningún error, podemos subir el archivo...
        if($fichero["error"] == UPLOAD_ERR_OK && $extensionCorrecta) {
            //Comprobación de que el archivo debe pesar menos de 5MB
            if($fichero["size"] < 5 * 1024 * 1024) {

                $textSource = strtolower($fichero["name"]);

                /*//Sanitiza el texto por ASCII directamente...
                for($i=0;$i<strlen($textSource);$i++) {
                    if(!ord($textSource) < 97 || !ord($textSource) > 122) {
                        strtr();
                    }
                }*/

                //Ruta donde se va a guardar el archivo subido por el usuario.
                $uploaded_file = __DIR__ . "/../recursosPublicos/" . trim($fichero["name"]);

                //Movemos el archivo...
                move_uploaded_file($fichero["tmp_name"], $uploaded_file);

            } else {
                echo "<h2>[ERROR] La imagen es demasiado grande, tiene que ser de menos de 5MB</h2>";
            }
        }
    }
}

/**
 * Función que comprueba si una extensión está permitida o no en el sistema.
 * SOLO funciona en PHP 8.
 */
function whitelist($fichero, $foto=true) {
    $whitelistFotos = array("jpg", "jpeg", "png", "gif");
    $whitelistDocs = array("pdf");

    //Comprobamos en la whitelist el tipo de archivo que vamos a utilizar
    //e iteramos sobre el mismo para verificar que el archivo es válido.
    if($foto) { 
        for($i=0;$i<sizeof($whitelistFotos);$i++) {
            if(substr(strrchr(strtolower($fichero),"."), 1) == $whitelistFotos[$i])
                return true;
        }
    } else {
        for($i=0;$i<sizeof($whitelistDocs);$i++) {
            if(substr(strrchr(strtolower($fichero),"."), 1) == $whitelistDocs[$i])
                return true;
        }
    }
    return false;
}

/**
 * Función que crea un listado con imágenes o pdf's
 */
function listado() {

    //Comprobamos que exista el GET de imagenes
    if(isset($_GET["imagenes"])) {



        //Si envió o no envió un archivo mostrará igualmente todos los archivos que hay en el servidor
        //Abre un directorio...
        $dir_handler = opendir("../recursosPublicos/");

        if($_GET["imagenes"] == "si") {
            //Lee de la ruta especificada todos los archivos y los muestra.
            while(($listado = readdir($dir_handler)) !== false) { //Si hay un fallo parará el bucle.
                if(whitelist($listado)) {
                    //blacklist de archivos a mostrar
                    //Esta es una imagen grande a subir al servidor, para comprobar que
                    //no dejara subir la misma por el peso.
                    if($listado == "imagenGRANDE.jpg") continue;


                    echo "<div class='contenedorImagenes'>";
                        echo "<p class='titulo'>Nombre de la imagen: ". $listado . "</p>";

                        //Si hemos hecho click en mostrar imagenes, las mostramos...
                        echo "
                        <a href='../recursosPublicos/$listado' target='_blank'>
                            <img src='../recursosPublicos/$listado'>
                        </a>";
                    echo "</div>";
                        
                }
            }
        } else if($_GET["imagenes"] == "no") {
            
            //Instanciamos.
            $pdf = new GeneratePDF();

            echo "<p id='lista'>Enlaces de los PDF's disponibles:</p>";
            //Recorremos la carpeta en busca de los .pdf
            while (($listado = readdir($dir_handler)) !== false) {
                if(whitelist($listado, false)) {
                    echo "<div class='contenedorImagenes'>";
                        echo "<a href='../recursosPublicos/$listado' target='_blank'><img src='../imgs/icono-PDF.png'></a>";
                        echo "<p class='titulo'>$listado</p>";
                    echo "</div>";
                }
            }
            //$pdf->generateAndLoad("a");
        }
        //Al terminar cierra el directorio
        closedir($dir_handler);
    }
}
?>
<!DOCTYPE html>
    <!-- Sergio Matamoros Delgado -->
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ficheros</title>
        <link rel="stylesheet" href="../css/estilo.css">
    </head>
    <body>
        <nav>
            <a href="procesadoFicheros.php?imagenes=si"><button>Mostrar la galería de imágenes</button></a>
            <a href="procesadoFicheros.php?imagenes=no"><button>Mostrar archivos PDF</button></a>
            <a href="../formularioSubida.html"><button>Subir archivos</button></a>
        </nav>

        <?php
            //ESTE PHP NO DEBERÍA ESTAR DENTRO DEL HTML
            //hasta que se le ponga JS...
            if(phpversion() < 8) 
                echo "
                <p id='error'>
                    [ERROR] /!\ Su versión de PHP no es compatible con el sitio, por lo que puede contener errores. /!\
                </p>";
        ?>
        <main>
            <?php  listado(); ?>
        </main>
    </body>
</html>