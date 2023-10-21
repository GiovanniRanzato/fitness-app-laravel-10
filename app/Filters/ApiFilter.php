<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{

    protected $safeParams = [
    ];

    protected $columnMap = [
    ];

    protected $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gr' => '>',
        'ls' => '<',
        'gre' => '>=',
        'lse' => '<=',
        'like' => 'like',
        'in' => 'in',
    ];

    public function transform(Request $request)
    {
        $eloquentQuery = [];   
        foreach ($this->safeParams as $param => $operators) {
            $query = $request->query($param);

            if (!isset($query)) continue;

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($query[$operator]) ) {
                    $value = $query[$operator];
                    switch($operator) {
                        case 'like': $value = '%'.$value.'%'; break;
                        case 'in': $value = '('.$value.')'; break;
                    }
                    $eloquentQuery[] = Array($column, $this->operatorMap[$operator], $value);
                }
            }
        }
        return $eloquentQuery;
    }
}