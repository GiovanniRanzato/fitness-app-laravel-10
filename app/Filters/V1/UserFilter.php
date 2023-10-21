<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class UserFilter extends ApiFilter
{
    protected $safeParams = [
        'name' => ['eq', 'like'],
        'email' => ['eq', 'like'],
    ];

}