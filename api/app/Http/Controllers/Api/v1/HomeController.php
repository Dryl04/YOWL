<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request){
            return [
                "status" => true,
                "message" => "Welcome to YOWL API",
                "data" =>[
                    "service" => "YOWL API",
                    "version" => "1.0.0",
                    "langage" => app()->getLocale(),
                    "support" => "king-of-php@epitech.eu"
                ]
            ];
    }
}
