<?php
require_once '../source/file.php';
require_once '../source/override.php';

ini_set('display_errors', '1');

JOverride::map('Controller', __DIR__.'/controller.php');
JOverride::override('Controller', __DIR__.'/mycontroller1.php');
JOverride::override('Controller', __DIR__.'/mycontroller2.php');
JOverride::override('Controller', __DIR__.'/mycontroller3.php');
JOverride::override('Controller', __DIR__.'/mycontroller4.php');

JOverride::run();

echo Controller::controller1();
echo Controller::controller2();
echo Controller::controller3();
echo Controller::controller4();
echo Controller::execute();