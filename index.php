<?php

//Incluimos nucleo
include('core/core.php');

//Cargamos layout
Render::getLayout();

//Mostramos debug
if ( _DEBUG_ && Render::$layout )
    Debug::callLog();
