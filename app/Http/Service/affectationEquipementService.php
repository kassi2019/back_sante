<?php

namespace App\Http\Service;
use App\Models\affectationEquipement;
use App\Models\histoEquipement;
use App\Models\equipement;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class affectationEquipementService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeequipement()
    {
        return affectationEquipement::all();
    }
    public function creationequipement(array $data)
    {

        $userId = auth()->user()->id;


        $data['user_id'] = $userId;
        $data['heure_creation'] = Carbon::now();
        $equipement = affectationEquipement::create($data);
        $historique = equipement::where('equipement_id', $id)->first();
        if ($historique) {
            equipement::update([
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);


        }
        return ['equipement' => $equipement];
    }



    public function updateequipement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver l'équipement à mettre à jour
        $equipement = equipement::find($id);

        // Si l'équipement n'existe pas, lever une exception
        if (!$equipement) {
            throw new ModelNotFoundException('Équipement non trouvé.');
        }

        // Mettre à jour les informations de l'équipement
        $equipement->update($data);

        // Vérifier si une entrée existe déjà dans histoEquipement
        $historique = histoEquipement::where('equipement_id', $id)->first();

        if ($historique) {
            // Mise à jour de l'historique si l'entrée existe
            $historique->update([
                'libelle' => $equipement->libelle,
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);
        } else {
            // Création d'un nouvel historique si aucune entrée n'existe
            histoEquipement::create([
                'equipement_id' => $equipement->id,
                'libelle' => $equipement->libelle,
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);
        }

        return ['equipement' => $equipement];
    }


    public function updateequipementrenouvellement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver l'équipement à mettre à jour
        $equipement = equipement::find($id);

        // Si l'équipement n'existe pas, lever une exception
        if (!$equipement) {
            throw new ModelNotFoundException('Équipement non trouvé.');
        }

        // Mettre à jour les informations de l'équipement
        $equipement->update($data);

        // Création d'un nouvel historique si aucune entrée n'existe
        histoEquipement::create([
            'equipement_id' => $equipement->id,
            'libelle' => $equipement->libelle,
            'quantite' => $equipement->quantitesaisir,
            'type_equipement_id' => $equipement->type_equipement_id,
            'heure_creation' => Carbon::now(),
            'user_id' => $equipement->user_id,
            'statut' => 1
        ]);


        return ['equipement' => $equipement];
    }
    // Méthode pour supprimer un produit
    public function deleteequipement($id)
    {
        // Trouver le produit à supprimer
        $produit = equipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Equipement non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'Equipement supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function geteById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = equipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getesByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return equipement::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}