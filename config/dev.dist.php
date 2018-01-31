<?php

$config = [];

return array_replace_recursive(require('prod.env.php'), $config);
