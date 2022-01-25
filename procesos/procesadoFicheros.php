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

if(isset($_POST["enviar"])) {

    //Si el fichero existe...
    if(isset($_FILES["fichero"])) {
        
        $fichero = $_FILES["fichero"];

        $extensionCorrecta = null;

        //Validación del tipo/formato del archivo subido al subido al servidor.
        switch ($fichero["type"]) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
                $extensionCorrecta = true;
                break;
            default:
                echo "<h2>[ERROR] El archivo contiene una extensión no soportada...</h2>";
                break;
        }

        //Si no hay ningún error, podemos subir el archivo...
        if($fichero["error"] == UPLOAD_ERR_OK && $extensionCorrecta) {

            if($fichero["size"] < 6 * 1024 * 1024) {

                //Ruta donde se va a guardar el archivo subido por el usuario.
                $uploaded_file = __DIR__ . "/../recursosPublicos/" . $fichero["name"];

                //Movemos el archivo...
                move_uploaded_file($fichero["tmp_name"], $uploaded_file);

            } else {
                echo "<h2>[ERROR] La imagen es demasiado grande, tiene que ser de menos de 5MB</h2>";
            }
        }
    }
}

function listado() {

    //Comprobamos que exista el GET de imagenes
    if(isset($_GET["imagenes"])) {

        //Si envió o no envió un archivo mostrará igualmente todos los archivos que hay en el servidor
        //Abre un directorio...
        $dir_handler = opendir("../recursosPublicos/");

        //Lee de la ruta especificada todos los archivos y los muestra.
        while(($listado = readdir($dir_handler)) !== false) { //Si hay un fallo parará el bucle.
            if($listado != "." && $listado!= "..") {

                //blacklist de archivos a mostrar
                //Esta es una imagen grande a subir al servidor, para comprobar que
                //no dejara subir la misma por el peso.
                if($listado == "imagenGRANDE.jpg") continue;


                echo "Nombre de la imagen: ". $listado . "<br>";

                //Si hemos hecho click en mostrar imagenes, las mostramos...
                if($_GET["imagenes"] == "si")
                    echo "
                    <a href='../recursosPublicos/$listado' target='_blank'>
                        <img src='../recursosPublicos/$listado'>
                    </a>";
                    
                echo "<br><br><br>";
            }
        }
        //Al terminar cierra el directorio
        closedir($dir_handler);
    }
}
?>

<!DOCTYPE html>
    <!-- Sergio Matamoros Delgado -->
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ficheros</title>
        <link rel="stylesheet" href="../css/estilo.css">
    </head>
    <body>
        <nav>
            <button><a href="procesadoFicheros.php?imagenes=si">Mostrar la galería de imágenes</a></button>
            <button><a href="procesadoFicheros.php?imagenes=no">Mostrar nombres de las imágenes</a></button>
            <button><a href="../formularioSubida.html">Subir archivos</a></button>
        </nav>

        <main>
            <?php  listado(); ?>
        </main>
    </body>
</html>