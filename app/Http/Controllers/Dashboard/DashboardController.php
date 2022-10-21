<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Actions [ The mehods inside controller ]
    public function index()
    {
        // Return Response: view, json, redirect, file
        return view('dashboard.index');
    }
}
