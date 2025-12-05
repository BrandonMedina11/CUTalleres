<?php

namespace App\Http\Controllers;

use App\Helpers\BladeHelper;

class HomeController extends Controller
{
    public function index()
    {
        BladeHelper::render('home', []);
    }
}


