<?php

class Debug
{
    //Creamos entrada en log de mysql
    public static function mlog($time,$sql,$result)
    {
        if( _DEBUG_ )
            $_SESSION['debug']['bd'][] = array($time,$sql,$result);
    }   

    //Llamamos a log al escribir la palabra LOG
    public static function callLog()
    {
        ?>
    	<script>
    		var enter_log = 0;
    		function PulsarTecla(event)
            {
			    tecla = event.keyCode;
			    if ( tecla == 76 && enter_log == 0 )
			    	enter_log = 1;
			    else if ( tecla == 79 && enter_log == 1 )
			    	enter_log = 2;
			    else if ( tecla == 71 && enter_log == 2 )
			    	enter_log = 3;
			    else
			    	enter_log = 0;

			    if ( enter_log == 3 )
                {
			    	window.open('<?=_DOMINIO_?>debug/bd/');
			    	enter_log = 0;
			    }
			}
			window.onkeydown=PulsarTecla;
    	</script>
    	<?php
    }
}
