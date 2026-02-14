<?php

namespace app\models;

use yii\db\ActiveRecord;


class Voyage extends ActiveRecord
{
    /*
    int $id
    int $conducteur
    int $trajet
    int $idtypev
    int $idmarquev
    float $tarif // per kilometer per person
    int $nbplacedispo
    int $nbbagage
    int $heuredepart
    string $contraintes
    
    Internaute $conducteur
    Trajet $trajet
    TypeVehicule $typeVehicule
    MarqueVehicule $marqueVehicule
    Reservation[] $reservations
    */

    public static function tableName()
    {
        return 'fredouil.voyage';
    }

    public function rules()
    {
        return [
            [['conducteur', 'trajet', 'idtypev', 'idmarquev', 'tarif', 'nbplacedispo', 'nbbagage', 'heuredepart'], 'required'],
            [['conducteur', 'trajet', 'idtypev', 'idmarquev', 'nbplacedispo', 'nbbagage', 'heuredepart'], 'integer'],
            [['tarif'], 'number'],
            [['contraintes'], 'string', 'max' => 500],
        ];
    }

    // we do all getters of the objects this class has, Drivers, Trajets, TypeVehicule, MarqueVehicule...
    public function getConducteurObject()
    {
        // internaute.id = voyage.conducteur
        return $this->hasOne(Internaute::class, ['id' => 'conducteur']);
    }

    public function getTrajetObject()
    {
        // trajet.id = voyage.trajet
        return $this->hasOne(Trajet::class, ['id' => 'trajet']);
    }

    public function getTypeVehiculeObject()
    {
        return $this->hasOne(TypeVehicule::class, ['id' => 'idtypev']);
    }

    public function getMarqueVehiculeObject()
    {
        return $this->hasOne(MarqueVehicule::class, ['id' => 'idmarquev']);
    }

    public function getReservationsObject()
    {
        // reservation.voyage -> voyage.id
        return $this->hasMany(Reservation::class, ['voyage' => 'id']);
    }

    // Nb total de places déjà réservées 
    public function getReservedSeats(): int
    {
        $sum = $this->getReservationsObject()->sum('nbplaceresa');
        return (int) $sum;
    }

    // Nb de places restantes 
    public function getRemainSeats(): int
    {
        return $this->nbplacedispo - $this->getReservedSeats();
    }

    public function isComplete(): bool
    {
        return $this->getRemainSeats() <= 0;
    }

    public function canItAccept(int $nbPlaces): bool
    {
        return $this->getRemainSeats() >= $nbPlaces;
    }


    public function priceFor(int $nbPlaces): float
    {
        if ($nbPlaces<=0) {
            return 0.0;
        }
        $distance = $this->trajetObject ? $this->trajetObject->distance : 0 ;
        return $this->tarif * $distance * $nbPlaces;
    }

    public static function getVoyagesByTrajetId(int $trajetId)
    {
        return static::find(['trajet' => $trajetId])->all();
    }

    public function hasReservations(): bool
    {
        return $this->getReservationsObject()->exists();
    }

    public function getNombreReservations(): int
    {
        return (int) $this->getReservationsObject()->count();
    }

    public function isReservedBy(Internaute $user): bool
    {
        return Reservation::find(['voyage' => $this->id, 'voyageur' => $user->id])
            ->exists();
    }

    public function getDuree(): string
    {
        // durée = distance en minutes
        $minutes = $this->trajetObject ? (int)$this->trajetObject->distance : 0;

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        if ($h > 0 && $m > 0) {
            return sprintf("%dh %dmin", $h, $m);
        } elseif ($h > 0) {
            return sprintf("%dh", $h);
        } else {
            return sprintf("%dmin", $m);
        }
    }

    public function getHeureArrivee(): string
    {
        $distanceMinutes = $this->trajetObject ? (int)$this->trajetObject->distance : 0;

        // départ = H:00 -> converti en minutes
        $departMinutes = ((int)$this->heuredepart) * 60;

        $arriveeMinutes = $departMinutes + $distanceMinutes;

        // ramener dans 24h si > 24h
        $arriveeMinutes = $arriveeMinutes % (24 * 60);

        $h = intdiv($arriveeMinutes, 60);
        $m = $arriveeMinutes % 60;

        return sprintf("%02d:%02d", $h, $m);
    }

    //--- RECURSIVE SEARCH ---

    public static function searchCorrespondences($depart, $arrivee, $nbPersonnes)
    {
        $allPaths = [];

        self::findPathsRecursive($depart, $arrivee, $nbPersonnes, 0, [], [], $allPaths);
        return $allPaths;
    }

    private static function findPathsRecursive($currentCity, $targetCity, $nbPersonnes, $minTime, $currentPath, $visitedCities, &$allPaths)
    {
        //standrdize inputs
        $currentClean = mb_strtolower(trim($currentCity));
        $targetClean  = mb_strtolower(trim($targetCity));

        // if we are visiting the same city again in the same pthe -> cycle -> stop this path searching 
        if (in_array($currentClean, $visitedCities)) return;

        $visitedCities[] = $currentClean;

        //Find all the trajets that starts with our starting ville 
        $trajets = Trajet::findAll(['depart' => $currentCity]);

        foreach ($trajets as $trajet) { // we loop all over the found cities

            //if there are not travels in this ttrajet no need to keep them
            $voyages = $trajet->voyagesObject;
            if (empty($voyages)) continue;

            // in case there is we loop we check: if it can accept, the timing ! 
            foreach ($voyages as $voyage) {
                // Checks
                if (!$voyage->canItAccept($nbPersonnes)) continue;
                if ($voyage->heuredepart < $minTime) continue;

                // Build Path
                $newPath = $currentPath;
                $newPath[] = $voyage;

                //Destination Check
                if (mb_strtolower(trim($trajet->arrivee)) === $targetClean) {

                    $allPaths[] = $newPath;

                    // if we wanna limit timing cuz now it takes long 
                    //if (count($allPaths) >= 5) return;

                } else {
                    
                    // Calculate Next Start Time
                    // Default to 60 mins (1 hour) if distance is missing/zero
                    $dist = ($trajet->distance > 0) ? $trajet->distance : 60;
                    
                    $arrivalTime = $voyage->heuredepart + ($dist / 60);

                    self::findPathsRecursive(
                        $trajet->arrivee,
                        $targetCity,
                        $nbPersonnes,
                        $arrivalTime,
                        $newPath,
                        $visitedCities,
                        $allPaths
                    );
                }
            }
        }
    }
}