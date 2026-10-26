<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
}

$app = new \OCA\Troubamix\AppInfo\Application();
$app->registerNavigationEntry();
