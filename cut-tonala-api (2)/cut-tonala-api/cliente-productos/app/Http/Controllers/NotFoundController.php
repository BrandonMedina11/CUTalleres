<?php

namespace App\Http\Controllers;

use App\Helpers\BladeHelper;

class NotFoundController extends Controller
{
    public function index()
    {
        http_response_code(404);
        BladeHelper::render('not-found', []);
    }
}


