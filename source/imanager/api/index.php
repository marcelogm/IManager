<?php
namespace IManager\API;
use IManager\IMPlugin;

include('..\IMPlugin.php');

/**
* Porque chamar esse m�todo antes de executar?
*
* Quando surgiu a necessidade de responder requisi��es Ajax 
* vindas da frontend, fez-se necess�rio dotar o pluhin de capacidade
* de tratamento de requisi��es e importa��o de classes independetes do PurePHP. 
* Sendo assim, � necess�rio registrar um autoloader pr�prio do IManager
* para que a execu��o possa ocorrer utilizando o sistema de namespace mesmo sem
* um framework gerenciando isso.
*/
IMPlugin::autoloader_registration();

$manager = new IMPlugin();
$manager->api();