<?php
require_once 'vendor/autoload.php';

session_start();

use iutnc\deefy\auth\Authz;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

DeefyRepository::setConfig('./config/deefy.db.ini');
Authz::setConfig('./config/role.ini');

$dispatcher = new Dispatcher();

$dispatcher->run();

