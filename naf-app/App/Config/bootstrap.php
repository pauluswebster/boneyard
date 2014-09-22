<?php
namespace App\Config;

use Naf\Core\App;

require __DIR__ . '/paths.php';

require ROOT . '/vendor/autoload.php';

App::config('debug', true);

App::startup();