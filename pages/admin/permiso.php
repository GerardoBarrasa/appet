<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize"><?=l('admin-permiso-title', array(Admin::getNombrePerfilById($id_perfil)));?></span>
            </h4>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-20 px-3">
                <div class="card-body">
                    <form method="post" action="">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="tablaPermisos" class="table table-striped bg-default table-primary">
                                    <input type="hidden" name="id_perfil" value="<?=$id_perfil?>">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th data-breakpoints="xs"><?=l('admin-permiso-nombre');?></th>
                                            <th data-breakpoints="xs"><?=l('admin-permiso-descripcion');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach( $permisos as $permiso )
                                        {
                                            $checked = '';
                                            foreach( $permisosPerfil as $permisoPerfil )
                                            {
                                                if( $permisoPerfil->id_permiso == $permiso->id_permiso )
                                                    $checked = 'checked';
                                            }
                                            ?>
                                            <tr class="gradeX">
                                                <td class="text-center"><input type="checkbox" value="<?=$permiso->id_permiso?>" name="id_permiso[]" <?=$checked?>></td>
                                                <td><?=$permiso->nombre?></td>
                                                <td><?=$permiso->descripcion?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 mt-3 d-flex justify-content-end align-items-center">
                                <div class="justify-self-end">
                                    <button type="submit" name="submitUpdatePermisos" class="btn btn-primary  waves-effect waves-light"><?=l('admin-actualizar');?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>

<script>
    $(document).ready(function() { 
        $("#idiomas").addClass("active");
        $("#idiomas").parent().addClass("active");
        $("#idiomas").attr("aria-expanded","true");
        $("#idiomas").parent().children('ul').addClass("in");
        $("#idiomas").parent().children('ul').attr("aria-expanded","true");
    });
</script>
