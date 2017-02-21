<?php
namespace IManager\API;
use IManager\IMPlugin;

include('..\IMPlugin.php');

/**
* Porque chamar esse método antes de executar?
*
* Quando surgiu a necessidade de responder requisições Ajax 
* vindas da frontend, fez-se necessário dotar o pluhin de capacidade
* de tratamento de requisições e importação de classes independetes do PurePHP. 
* Sendo assim, é necessário registrar um autoloader próprio do IManager
* para que a execução possa ocorrer utilizando o sistema de namespace mesmo sem
* um framework gerenciando isso.
*/
IMPlugin::autoloader_registration();

$manager = new IMPlugin();
$manager->api();