<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{

    public function index()
    {
        $properties = auth()->user()->properties()->with('assets')->latest()->get();

        return view('dashboard', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Home,Office,Store,Other',
            'address' => 'nullable|string|max:500',
        ]);

        auth()->user()->properties()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Lokasi berhasil ditambahkan!');
    }
}