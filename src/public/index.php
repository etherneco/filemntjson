<?php
/*
 * PHP Simple File Manager
 * Copyright Daniel Wojtak (etherneco)
 */


require '../config/config.php';
require '../class/lib.php';
require '../controller/controller.php';
require '../controller/main.controller.php';


$main = new MainController();
$main->index();
