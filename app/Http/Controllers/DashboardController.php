<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilesResource;

use function inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return inertia()->render('Dashboard', [
            'files' => FilesResource::collection($request->user()->files()->latest()->get()),
        ]);
    }
}
