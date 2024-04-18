<div class="row">
    <?php
    //vd($accounts);
    if($total > 0)
    {
        foreach($accounts as $account)?>

    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
        <div class="card bg-light d-flex flex-fill">
            <div class="card-header text-muted border-bottom-0">
                <?=$account->TYPE?>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-7">
                        <h2 class="lead"><b><?=$account->name?></b></h2>
                        <ul class="ml-4 mb-0 fa-ul text-muted">
                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> <?=$account->email?></li>
                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: + 800 - 12 12 23 52</li>
                        </ul>
                    </div>
                    <div class="col-5 text-center">
                        <img src="<?=_ASSETS_._ADMIN_;?>dist/img/user2-160x160.jpg" alt="user-avatar" class="img-circle img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-right">
                    <a href="#" class="btn btn-sm bg-teal" hidden>
                        <i class="fas fa-comments"></i>
                    </a>
                    <a href="<?=_DOMINIO_._ADMIN_?>account/<?=$account->id?>/" class="btn btn-sm btn-primary">
                        <i class="fas fa-user"></i> View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<?php }
else
{?>
    <div class="alert alert-dark text-center">
        <p class="mb-0">No se han encontrado cuentas</p>
    </div>
    <?php
}
?>
<div class="row">
    <div class="col-sm-6">
        <div class="dataTables_info">
            <?=($total > 1 || $total == '0') ? $total.' Cuentas encontradas' : '1 Cuenta encontrada'?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_bootstrap">
            <?php Tools::getPaginador($pagina, $limite, 'Admin', 'getAccountsWithFiltros', 'ajax_get_accounts', '', '', 'end'); ?>
        </div>
    </div>
</div>
