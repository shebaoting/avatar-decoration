<?php

use Flarum\Database\Migration;

return Migration::addColumns('users', [
    'avatar_decoration' => ['mediumText', 'nullable' => true],
    'avatar_decoration_updated_at' => ['dateTime', 'nullable' => true],
]);
