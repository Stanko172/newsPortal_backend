<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbilitiesController extends Controller
{
    public function index()
    {
        $permissions = auth()->user()->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('title')
            ->toArray();

        return $permissions;
    }
}
