<?php

namespace App\Http\Controllers;

use App\Http\Resources\FilesResource;
use Illuminate\Http\Request;
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
