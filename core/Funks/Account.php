<?php

class Account
{
    public static function getAccountsWithFiltros($comienzo, $limite, $applyLimit = true)
    {
        $busqueda = Tools::getValue('busqueda', '');
        $search = "";
        $limit = "";

        if ($busqueda != '')
            $search .= "AND (a.name LIKE '%" . $busqueda . "%' OR a.email LIKE '%" . $busqueda . "%')";

        if ($applyLimit)
            $limit = " LIMIT $comienzo, $limite";

        $q = "SELECT a.*, DATE_FORMAT(a.create_time, '%d-%m-%Y %H:%i:%s') as CREATE_TIME, IFNULL(at.nombre, 'ROOT') AS TYPE FROM account a LEFT JOIN account_types at ON a.type_id=at.id WHERE 1 $search ORDER BY a.name ASC";

        $listado = Bd::getInstance()->fetchObject($q . $limit);

        $total = Bd::getInstance()->countRows($q);

        return array(
            'listado' => $listado,
            'total' => $total
        );
    }

    public static function getAccountById($id)
    {
        return Bd::getInstance()->fetchRow("SELECT * FROM account WHERE id=" . (int)$id);
    }

    public static function getAccountByEmail($email)
    {
        return Bd::getInstance()->fetchRow("SELECT * FROM account WHERE 1 AND email='$email'");
    }

    public static function getAccountBySlug($slug)
    {
        return Bd::getInstance()->fetchRow("SELECT * FROM account WHERE 1 AND slug='$slug'");
    }

    public static function updateAccount($updAccount, $id_account)
    {
        return Bd::getInstance()->update('account', $updAccount, "id = " . (int)$id_account);
    }

    public static function crearAccount($addAccount)
    {
        return Bd::getInstance()->insert('account', $addAccount);
    }


	public static function eliminarRegistro( $id )
	{
		return Bd::getInstance()->query("DELETE FROM account WHERE id = ".(int)$id);
	}
}
