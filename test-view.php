<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Http\Kernel::class);

echo $app->bound('view') ? 'VIEW BOUND' : 'VIEW NOT BOUND';