#!/usr/bin/env php
<?php

use Consolidation\AnnotatedCommand\CommandFileDiscovery;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';

putenv("COMPOSER_MEMORY_LIMIT=-1");

$dktl_directory = DkanTools\Util\Util::getDktlDirectory();
$dktl_project_directory = DkanTools\Util\Util::getProjectDirectory();

$output = new ConsoleOutput();

$discovery = new CommandFileDiscovery();
$discovery->setSearchPattern('*Commands.php');
$defaultCommandClasses = $discovery->discover("{$dktl_directory}/src", '\\DkanTools');

$customCommandClasses = [];
if (file_exists("{$dktl_project_directory}/src/command")) {
    $customCommandClasses = $discovery->discover("{$dktl_project_directory}/src/command", '\\DkanTools\\Custom');
}

$commandClasses = array_merge($defaultCommandClasses, $customCommandClasses);

$appName = "DKAN Tools";
$appVersion = '2.0.0-rc1';
$configurationFilename = 'dktl.yml';

$runner = new \Robo\Runner($commandClasses);
$runner->setConfigurationFilename($configurationFilename);

$argv = $_SERVER['argv'];

$output = new ConsoleOutput();
$statusCode = $runner->execute($argv, $appName, $appVersion, $output);

exit($statusCode);
