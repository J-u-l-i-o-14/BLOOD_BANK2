<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmation;
use App\Mail\ReservationCancelled;

class DonsController extends Controller
{
    // ...existing code...
    // Affichage de l'historique des dons
    public function historique()
    {
        $histories = \App\Models\DonationHistory::with(['donor', 'campaign', 'bloodBag'])->latest()->paginate(20);
        return view('dons.historique', compact('histories'));
    }

    // Affichage de la liste des donneurs
    public function listeDonneurs()
    {
        $donneurs = \App\Models\User::where('role', 'donor')->paginate(20);
        return view('dons.liste_donneurs', compact('donneurs'));
    }
    public function index()
    {
        return view('dons.index');
    }

    public function inscription()
    {
        return view('dons.inscription');
    }

    public function inscriptionStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable',
            'birthday' => 'required|date',
            'blood_type_id' => 'required|integer',
        ]);
        // Adapter le nom du champ pour la base
        $data = $validated;
        $data['birthdate'] = $data['birthday'];
        unset($data['birthday']);
        \App\Models\DonneurList::create($data);
        return redirect()->route('dons.inscription')->with('success', 'Donneur inscrit avec succès.');
    }
    public function create()
    {
        return view('dons.create');
    }

    public function store(Request $request)
    {
        // Validation et création du don dans donation_histories
        $validated = $request->validate([
            'donor_id' => 'required|integer|exists:donors,id',
            'campaign_id' => 'nullable|integer|exists:campaigns,id',
            'blood_bag_id' => 'nullable|integer|exists:blood_bags,id',
            'donated_at' => 'required|date',
            'volume' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);
        \App\Models\DonationHistory::create($validated);
        return redirect()->route('dons.create')->with('success', 'Don enregistré avec succès.');
    }

    public function campagne()
    {
        return view('dons.campagne');
    }

    public function campagneStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
        ]);
        Campaign::create($validated);
        return redirect()->route('dons.campagne')->with('success', 'Campagne créée avec succès.');
    }

    public function rendezvous()
    {
        $appointments = Appointment::with('donor')->get();
        return view('dons.rendezvous', compact('appointments'));
    }

    public function confirmer(Appointment $appointment)
    {
        $appointment->status = 'confirmé';
        $appointment->save();
        Mail::to($appointment->donor->email)->send(new ReservationConfirmation($appointment));
        return back()->with('success', 'Rendez-vous confirmé et e-mail envoyé.');
    }

    public function annuler(Appointment $appointment)
    {
        $appointment->status = 'annulé';
        $appointment->save();
        Mail::to($appointment->donor->email)->send(new ReservationCancelled($appointment));
        return back()->with('success', 'Rendez-vous annulé et e-mail envoyé.');
    }
}
