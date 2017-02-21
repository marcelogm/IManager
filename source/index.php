<?php
namespace imanager;

include('imanager\IMPlugin.php');

IMPlugin::autoloader_registration();
$manager = new IMPlugin();
$manager->browse();