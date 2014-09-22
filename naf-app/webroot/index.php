<?php

require dirname(__DIR__) . '/App/Config/action.php';

use Naf\Action;

echo Action::dispatch();

?>