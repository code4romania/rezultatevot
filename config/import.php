<?php

declare(strict_types=1);

return [

    'awk_path' => env('AWK_PATH', '/usr/bin/awk'),

    'disk' => env('IMPORT_DISK', env('FILESYSTEM_DISK', 'local')),

    'independent_candidate_designation' => env('IMPORT_INDEPENDENT_CANDIDATE_DESIGNATION', 'CANDIDAT INDEPENDENT'),
    'candidate_votes_suffix' => env('IMPORT_CANDIDATE_VOTES_SUFFIX', '-voturi'),

];
