<?php

namespace App\Config;

use Naf\Core\App;
use Naf\Action;
use Naf\Action\ErrorHandler;

require __DIR__ . '/bootstrap.php';

ErrorHandler::register(App::config('error'));

Action::connect('#(?<path>.*)#');

?>