<?php

class Mascotas
{
    public static function getMascotaById($id_mascota)
    {
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ?: " AND m.id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";
        return Bd::getInstance()->fetchRow("SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO FROM mascotas m INNER JOIN mascotas_tipo mt ON m.tipo=mt.id INNER JOIN mascotas_genero mg ON m.genero=mg.id WHERE 1 AND m.id='$id_mascota' $filtro_cuidador");
    }

    public static function getMascotaBySlug($slug)
    {
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ?: " AND id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";
        return Bd::getInstance()->fetchRow("SELECT * FROM mascotas WHERE ! AND slug='$slug' $filtro_cuidador");
    }

    //Funcion que devuelve las mascotas filtradas
    public static function getMascotasFiltered($comienzo, $limite, $applyLimit=true)
    {
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ?: " AND m.id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";
        //Obtenemos variables de filtros
        $filter_busqueda    = (isset($_REQUEST['busqueda'])) ? Tools::getValue('busqueda', '') : '';

        $whereBusqueda = '';
        if( $filter_busqueda != '' )
            $whereBusqueda = " AND (m.nombre LIKE '%".$filter_busqueda."%' OR m.alias LIKE '%".$filter_busqueda."%' OR m.slug LIKE '%".$filter_busqueda."%')";

        if($applyLimit && $comienzo && $limite)
            $limit = "LIMIT $comienzo, $limite";
        else
            $limit = "";

        $q = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO FROM mascotas m INNER JOIN mascotas_tipo mt ON m.tipo=mt.id INNER JOIN mascotas_genero mg ON m.genero=mg.id WHERE 1 ".$whereBusqueda." ".$filtro_cuidador." ORDER BY id DESC $limit";
        $datos = Bd::getInstance()->fetchObject($q);

        return $datos;
    }

    public static function getMascotasCaracteristicas($idmascota)
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
        return Bd::getInstance()->query("DELETE FROM mascotas WHERE id = '$id'");
    }
}
