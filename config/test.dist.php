<?php

$config = [];

return array_replace_recursive(require('dev.env.php'), $config);
