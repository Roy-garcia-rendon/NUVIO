<?php
class conexion
{
    public function conex()
    {
        $conexion = mysqli_connect("localhost", "root", "", "nuvio");

        if ($conexion) {
            echo 'Conectado exitosamente a la Base de datos';
        } else {
            echo 'No se pudo conectar a la base de datos';
        }

        return $conexion;
    }
}
