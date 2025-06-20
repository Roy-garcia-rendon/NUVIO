<?php

class conexion
{
    public function conex()
    {
        $conexion = mysqli_connect("localhost", "root", "", "db_ventas");
        return $conexion;
    }
}
