<?php

declare(strict_types=1);

return [

    'awk_path' => env('AWK_PATH', '/usr/bin/awk'),

    'disk' => env('IMPORT_DISK', 'local'),

    'independent_candidate_prefix' => env('IMPORT_INDEPENDENT_CANDIDATE_PREFIX', 'CANDIDAT INDEPENDENT - '),

];
