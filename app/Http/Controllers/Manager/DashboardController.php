<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BloodBag;
use App\Models\BloodType;
use App\Models\BloodBagHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du gestionnaire.
     */
    public function index()
    {
        // Statistiques globales
        $totalBloodBags = BloodBag::count();
        $availableBloodBags = BloodBag::where('status', 'available')->count();
        $reservedBloodBags = BloodBag::where('status', 'reserved')->count();
        $expiringBloodBags = BloodBag::where('status', 'available')
            ->where('expiry_date', '<=', now()->addDays(7))
            ->where('expiry_date', '>', now())
            ->count();

        // Statistiques par type sanguin
        $bloodTypeStats = BloodType::select('blood_types.name')
            ->leftJoin('blood_bags', 'blood_types.id', '=', 'blood_bags.blood_type_id')
            ->where(function ($query) {
                $query->where('blood_bags.status', 'available')
                      ->orWhereNull('blood_bags.status');
            })
            ->groupBy('blood_types.id', 'blood_types.name')
            ->select(
                'blood_types.name',
                DB::raw('COUNT(blood_bags.id) as available_count')
            )
            ->get();

        // Tendances sur 7 jours
        $trends = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trends->push([
                'date' => now()->subDays($i)->format('d/m'),
                'in_count' => BloodBag::whereDate('created_at', $date)->count(),
                'out_count' => BloodBag::where('status', 'used')
                    ->whereDate('updated_at', $date)
                    ->count()
            ]);
        }

        // Dernières activités
        $recentActivities = BloodBagHistory::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Résumé des alertes
        $alerts = [
            'low_stock' => BloodType::select('blood_types.name')
                ->leftJoin('blood_bags', function ($join) {
                    $join->on('blood_types.id', '=', 'blood_bags.blood_type_id')
                         ->where('blood_bags.status', 'available');
                })
                ->groupBy('blood_types.id', 'blood_types.name')
                ->havingRaw('COUNT(blood_bags.id) < ?', [5])
                ->pluck('name'),
            'expiring_soon' => BloodBag::where('status', 'available')
                ->where('expiry_date', '<=', now()->addDays(7))
                ->where('expiry_date', '>', now())
                ->count(),
            'expired_today' => BloodBag::where('status', 'available')
                ->whereDate('expiry_date', now())
                ->count()
        ];

        return view('manager.dashboard', compact(
            'totalBloodBags',
            'availableBloodBags',
            'reservedBloodBags',
            'expiringBloodBags',
            'bloodTypeStats',
            'trends',
            'recentActivities',
            'alerts'
        ));
    }

    /**
     * Génère un rapport personnalisé.
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:inventory,movement,expiry'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($request->report_type) {
            case 'inventory':
                return $this->generateInventoryReport($startDate, $endDate);
            case 'movement':
                return $this->generateMovementReport($startDate, $endDate);
            case 'expiry':
                return $this->generateExpiryReport($startDate, $endDate);
            default:
                abort(400, 'Type de rapport invalide');
        }
    }

    /**
     * Génère un rapport d'inventaire.
     */
    protected function generateInventoryReport($startDate, $endDate)
    {
        $data = BloodType::select(
            'blood_types.name',
            DB::raw('COUNT(CASE WHEN blood_bags.status = "available" THEN 1 END) as available'),
            DB::raw('COUNT(CASE WHEN blood_bags.status = "reserved" THEN 1 END) as reserved'),
            DB::raw('COUNT(CASE WHEN blood_bags.status = "used" THEN 1 END) as used'),
            DB::raw('COUNT(CASE WHEN blood_bags.status = "expired" THEN 1 END) as expired')
        )
        ->leftJoin('blood_bags', 'blood_types.id', '=', 'blood_bags.blood_type_id')
        ->whereBetween('blood_bags.created_at', [$startDate, $endDate])
        ->groupBy('blood_types.id', 'blood_types.name')
        ->get();

        return view('manager.reports.inventory', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Génère un rapport de mouvements.
     */
    protected function generateMovementReport($startDate, $endDate)
    {
        $data = BloodBagHistory::with(['bloodBag.bloodType', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('manager.reports.movement', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Génère un rapport d'expiration.
     */
    protected function generateExpiryReport($startDate, $endDate)
    {
        $data = BloodBag::with('bloodType')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->orderBy('expiry_date')
            ->get()
            ->groupBy('bloodType.name');

        return view('manager.reports.expiry', compact('data', 'startDate', 'endDate'));
    }
}
