<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeSlotService
{
    /**
     * Heures d'ouverture et de fermeture
     */
    const OPENING_HOUR = 8;  // 8h00
    const CLOSING_HOUR = 17; // 17h00
    const SLOT_DURATION = 30; // 30 minutes par créneau

    /**
     * Nombre maximum de rendez-vous par créneau
     */
    const MAX_APPOINTMENTS_PER_SLOT = 3;

    /**
     * Jours fériés fixes au Togo
     */
    protected $holidays = [
        '01-01', // Nouvel An
        '01-13', // Fête de la libération
        '04-27', // Fête de l'indépendance
        '05-01', // Fête du travail
        '08-15', // Assomption
        '11-01', // Toussaint
        '12-25', // Noël
    ];

    /**
     * Récupérer les créneaux disponibles pour une date donnée
     */
    public function getAvailableTimeSlots(string $date, ?int $centerId = null): Collection
    {
        $date = Carbon::parse($date);
        
        // Si c'est un weekend ou un jour férié, retourner une collection vide
        if ($this->isWeekendOrHoliday($date)) {
            return collect([]);
        }

        $slots = collect();
        $currentTime = $date->copy()->setHour(self::OPENING_HOUR)->setMinute(0);
        $endTime = $date->copy()->setHour(self::CLOSING_HOUR)->setMinute(0);

        // Récupérer les rendez-vous existants pour la date
        $existingAppointments = Appointment::where('appointment_date', $date->format('Y-m-d'))
            ->when($centerId, function ($query) use ($centerId) {
                return $query->where('center_id', $centerId);
            })
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->appointment_time)->format('H:i');
            });

        // Générer tous les créneaux
        while ($currentTime < $endTime) {
            $timeString = $currentTime->format('H:i');
            $existingCount = $existingAppointments->get($timeString, collect())->count();

            // Si le créneau n'est pas complet, l'ajouter aux disponibilités
            if ($existingCount < self::MAX_APPOINTMENTS_PER_SLOT) {
                $slots->push([
                    'time' => $timeString,
                    'available_spots' => self::MAX_APPOINTMENTS_PER_SLOT - $existingCount,
                    'formatted_time' => $this->formatTimeSlot($timeString)
                ]);
            }

            $currentTime->addMinutes(self::SLOT_DURATION);
        }

        return $slots;
    }

    /**
     * Vérifier si une date est un weekend ou un jour férié
     */
    protected function isWeekendOrHoliday(Carbon $date): bool
    {
        // Weekend (samedi = 6, dimanche = 0)
        if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
            return true;
        }

        // Jours fériés fixes
        if (in_array($date->format('m-d'), $this->holidays)) {
            return true;
        }

        // Pâques et autres fêtes mobiles (à implémenter si nécessaire)
        // ...

        return false;
    }

    /**
     * Formater l'heure pour l'affichage
     */
    protected function formatTimeSlot(string $time): string
    {
        return Carbon::createFromFormat('H:i', $time)->format('H\hi');
    }

    /**
     * Vérifier si un créneau est valide et disponible
     */
    public function isValidTimeSlot(string $date, string $time, ?int $centerId = null): bool
    {
        $slots = $this->getAvailableTimeSlots($date, $centerId);
        return $slots->contains('time', $time);
    }

    /**
     * Récupérer la prochaine date disponible à partir d'une date donnée
     */
    public function getNextAvailableDate(Carbon $startDate, ?int $centerId = null): ?Carbon
    {
        $date = $startDate->copy();
        $maxDays = 90; // Limite de recherche à 3 mois
        $daysChecked = 0;

        while ($daysChecked < $maxDays) {
            if (!$this->isWeekendOrHoliday($date) && 
                $this->getAvailableTimeSlots($date->format('Y-m-d'), $centerId)->isNotEmpty()) {
                return $date;
            }
            $date->addDay();
            $daysChecked++;
        }

        return null;
    }

    /**
     * Vérifier si un créneau est encore disponible pour la réservation
     */
    public function isSlotStillAvailable(string $date, string $time, ?int $centerId = null): bool
    {
        $count = Appointment::where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->when($centerId, function ($query) use ($centerId) {
                return $query->where('center_id', $centerId);
            })
            ->count();

        return $count < self::MAX_APPOINTMENTS_PER_SLOT;
    }
}
