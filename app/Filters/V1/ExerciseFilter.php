<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ExerciseFilter extends ApiFilter
{
    protected $safeParams = [
        'name' => ['eq', 'like'],
    ];
}