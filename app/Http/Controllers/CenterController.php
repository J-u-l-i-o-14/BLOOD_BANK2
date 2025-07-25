<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Region;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $regionId = $user->center->region_id ?? null;
        $centers = Center::with('region')
            ->when($regionId, function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })
            ->paginate(15);
        return view('centers.index', compact('centers'));
    }

    public function create()
    {
        $user = auth()->user();
        $regionId = $user->center->region_id ?? null;
        // L'admin ne peut créer que dans sa région
        $regions = $regionId ? \App\Models\Region::where('id', $regionId)->get() : \App\Models\Region::all();
        return view('centers.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $regionId = $user->center->region_id ?? null;
        $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'address' => 'nullable|string',
        ]);
        if ($regionId && $request->region_id != $regionId) {
            abort(403, 'Vous ne pouvez créer un centre que dans votre région.');
        }
        \App\Models\Center::create($request->all());
        return redirect()->route('centers.index')->with('success', 'Centre créé avec succès.');
    }

    public function edit(Center $center)
    {
        $user = auth()->user();
        $regionId = $user->center->region_id ?? null;
        // Empêcher l'édition d'un centre hors de la région de l'admin
        if ($regionId && $center->region_id != $regionId) {
            abort(403, 'Vous ne pouvez modifier que les centres de votre région.');
        }
        $regions = \App\Models\Region::all();
        return view('centers.edit', compact('center', 'regions'));
    }

    public function update(Request $request, Center $center)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'address' => 'nullable|string',
        ]);
        $center->update($request->all());
        return redirect()->route('centers.index')->with('success', 'Centre mis à jour avec succès.');
    }

    public function destroy(Center $center)
    {
        $user = auth()->user();
        $regionId = $user->center->region_id ?? null;
        if ($regionId && $center->region_id != $regionId) {
            abort(403, 'Vous ne pouvez supprimer que les centres de votre région.');
        }
        $center->delete();
        return redirect()->route('centers.index')->with('success', 'Centre supprimé avec succès.');
    }
} 