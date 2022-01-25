<?php

//Utilizamos una librería externa para generar un PDF
require_once "../librerias/dompdf/autoload.inc.php";

/**
 * Autor: Sergio Matamoros Delgado
 * Descripción: Genera un PDF con los datos seleccionados de la B.D
 */

class GeneratePDF
{
    function generateAndLoad($urlFichero) {

        //Esto hace referencia a un trait
        //Un trait es similar a una clase, con la diferencia de que esta agrupa funcionalidades
        //especificas para poder reutilizar código de una manera sencilla.
        //Los traits no se pueden instanciar directamente.
        $pdf = new Dompdf\Dompdf();
    
        //Incluimos el fichero
        include $urlFichero;
    
        //Carga el HTML maquetandolo
        //Y carga el contenido del buffer de entrada.
        $pdf->loadHtml(ob_get_clean());
    
        $pdf->render();
    
        //Especificamos que es un aplicación de tipo PDF para que el navegador sea capaz de mostrarala.
        header("Content-type: application/pdf");
        //y le especificamos un nombre por si el usuario quiere descargarlo.
        header("Content-Disposition: inline; filename=empleados.pdf");
    
        //Mostramos el PDF en el navegador directamente
        echo $pdf->output();
    }
}