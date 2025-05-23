<?php

class Caracteriticas
{
    public static function updateCaracteristicasByMascota($idmascota)
    {
        $idmascota	= Tools::getValue('idmascota');
        foreach($_POST as $clave=>$valor){
            if($clave == 'idmascota'){
                continue;
            }
            // Primero borramos el registro si existe
        }
        $q = "SELECT mc.* FROM mascotas_caracteristicas mc WHERE 1 AND mc.id_mascota='$idmascota'";
        $datos = Bd::getInstance()->fetchObjectWithKey($q, 'id_caracteristica');

        return $datos;
    }
    public static function getCaracteristicasByMascota()
    {
        $q = "SELECT mc.* FROM mascotas_caracteristicas mc WHERE 1 AND mc.id_mascota='$idmascota'";
        $datos = Bd::getInstance()->fetchObjectWithKey($q, 'id_caracteristica');

        return $datos;
    }

    public static function getCaracteristicas()
    {
        $q = "SELECT c.* FROM caracteristicas c WHERE 1  ORDER BY c.nombre, c.slug DESC";
        $datos = Bd::getInstance()->fetchObject($q);

        return $datos;
    }

    public static function eliminarRegistro( $id )
    {
        return Bd::getInstance()->query("DELETE FROM caracteristicas WHERE id = '$id'");
    }
}
