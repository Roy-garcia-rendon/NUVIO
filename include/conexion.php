<?php
class conexion
{
    public function conex()
    {
        $conexion = mysqli_connect("localhost", "root", "", "nuvio");
        return $conexion;
    }
}
