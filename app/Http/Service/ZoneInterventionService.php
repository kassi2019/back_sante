<?php

namespace App\Http\Service;
use App\Models\zoneIntervention;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class ZoneInterventionService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listezoneIntervention()
    {
        return zoneIntervention::all();
    }
    public function affectationzoneIntervention($responsale)
    {
        return zoneIntervention::all();
    }

    public function listezoneResponsable($responsale)
    {
        if ($responsale == 0) {
            return zoneIntervention::all();
        } else {
            $res = DB::select("SELECT zi.libelle AS libelle_zone,zu.zone_intervention_id
FROM tb_zone_utilisateurs zu,
tb_zone_interventions zi
WHERE zu.zone_intervention_id=zi.id AND zu.utilisateur_id=$responsale
          ;");
            return $res;
        }

    }
    public function creationzoneIntervention(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs de validation existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée

        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;

        // Si la validation réussit, créer un nouveau nature economique
        $datazoneIntervention = zoneIntervention::create($data);

        return ['zoneIntervention' => $datazoneIntervention];
    }



    // Méthode pour modifier un produit
    public function updatezoneIntervention($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $zoneIntervention = zoneIntervention::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$zoneIntervention) {
            throw new ModelNotFoundException('zone interventions non trouvé.');
        }

        // Mettre à jour les informations du produit
        $zoneIntervention->update($data);

        return ['zoneIntervention' => $zoneIntervention];
    }

    // Méthode pour supprimer un produit
    public function deletezoneIntervention($id)
    {
        // Trouver le produit à supprimer
        $produit = zoneIntervention::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('zone interventions non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'zone interventions supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getzoneInterventionById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = zoneIntervention::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getzoneInterventionsByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return zoneIntervention::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
