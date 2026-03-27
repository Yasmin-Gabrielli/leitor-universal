<?php

require_once '../config/config.php';
require_once '../src/Controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();