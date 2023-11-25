<?php

namespace App\Filters\V1;
use Illuminate\Http\Request;

class UserFilter
{
    public function applyFilters(Request $request)
    {
        if (!$request->has('search'))
            return function ($query) { $query; };

        $searchValue = $request->input('search');
    
        return function ($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%')
            ->orWhere('last_name', 'like', '%' . $searchValue . '%')
            ->orWhere('email', 'like', '%' . $searchValue . '%');
        };
    }
}
