<?php
/**
 * @var $breadcrumb
 */
?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= isset($mod) ? $mod : 'Dashboard' ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <?php if($breadcrumb){?>
                    <ol class="breadcrumb float-sm-right">
                        <?php foreach ($breadcrumb as $bc){?>
                            <li class="breadcrumb-item <?=isset($bc['active']) &&  $bc['active'] ? 'active' : ''?>">
                                <i class="<?=$bc['icon']?>"></i>
                                <?php if(empty($bc['url'])){
                                    echo $bc['title'];
                                } else{?>
                                    <a href="<?=$bc['url']?>"><?=$bc['title']?></a>
                                <?php }?>
                            </li>
                        <?php } ?>
                    </ol>
                <?php } ?>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
