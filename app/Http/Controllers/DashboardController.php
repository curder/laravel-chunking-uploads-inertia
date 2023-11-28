<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return inertia()->render('Dashboard', [
            'files' => $request->user()->files()->latest()->get(),
        ]);
    }
}
