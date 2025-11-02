<?php
require_once __DIR__ . '/vendor/autoload.php';

session_start();

use iutnc\deefy\auth\Authz;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

DeefyRepository::setConfig(__DIR__ . '/config/deefy.db.ini');
Authz::setConfig(__DIR__ . '/config/role.ini');

$dispatcher = new Dispatcher();

$dispatcher->run();

