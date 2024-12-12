<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TermsOfService;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TermsOfServiceResource;

class TermsOfServiceController extends Controller
{
    public function latest()
    {
        $latestTerms = TermsOfService::latest()->first();
   
        if (!$latestTerms ) {
            return new Response(['message' => "Not Found."], 404);
        }
        return new TermsOfServiceResource($latestTerms);
    }
}
