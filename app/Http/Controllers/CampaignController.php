<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Center;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer toutes les campagnes pour la vue personnalisée
        $campagnes = Campaign::all();
        return view('campagne.index', compact('campagnes'));
    }


    public function create()
    {
        return view('campagne.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);
        $campagne = new Campaign();
        $campagne->title = $request->title;
        $campagne->name = $request->name;
        $campagne->description = $request->description;
        $campagne->date = $request->date;
        // Remplir location avec le nom du centre connecté
        $user = auth()->user();
        if ($user && $user->center_id) {
            $center = Center::find($user->center_id);
            $campagne->location = $center ? $center->name : null;
            $campagne->center_id = $user->center_id;
        }
        $campagne->save();

        return redirect()->route('campagne.index')->with('success', 'Campagne créée avec succès.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['center', 'donationHistories.donor', 'donationHistories.bloodBag']);
        return view('campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        $centers = Center::all();
        return view('campaigns.edit', compact('campaign', 'centers'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after:date',
        ]);
        $data = $request->all();
        if (in_array($user->role, ['admin', 'manager'])) {
            $data['center_id'] = $user->center_id;
        }
        $campaign->update($data);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campagne mise à jour avec succès.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->donationHistories()->exists()) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Impossible de supprimer cette campagne car elle a des dons associés.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campagne supprimée avec succès.');
    }

    public function upcoming()
    {
        $user = auth()->user();
        $query = Campaign::upcoming()->with('center');
        if (in_array($user->role, ['admin', 'manager'])) {
            $query->where('center_id', $user->center_id);
        }
        $campaigns = $query->paginate(10);
        return view('campaigns.upcoming', compact('campaigns'));
    }
}