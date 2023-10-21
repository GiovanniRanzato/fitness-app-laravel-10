<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CategoryFilter extends ApiFilter
{
    protected $safeParams = [
        'name' => ['eq', 'like'],
    ];
}