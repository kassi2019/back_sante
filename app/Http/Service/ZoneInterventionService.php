<?php

namespace App\Http\Service;
use App\Models\zoneInterventions;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class zoneInterventionService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listezoneInterventions()
    {
        return zoneInterventions::all();
    }

    public function listeDistrict_ZI()
    {
        // $roleId = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
      
            $res = DB::select("SELECT distinct ta.district_id,di.libelle AS libelle_district,di.*
FROM tb_zone_interventions ta,
tb_districts di
WHERE ta.district_id=di.id
          ;");
            return $res;

    }
    public function listeAireSanitaire_zi()
    {
        // $roleId = auth()->user()->id_roles;

        $res = DB::select("SELECT distinct ta.aire_sanitaire_id,di.libelle AS libelle_district,di.*
FROM tb_zone_interventions ta,
tb_aire_sanitaires di
WHERE ta.aire_sanitaire_id=di.id
          ;");
        return $res;


    }
    public function affectationzoneInterventions($responsale)
    {
        return zoneInterventions::all();
    }

    public function listezoneResponsable($responsale)
    {
        if ($responsale == 0) {
            return zoneInterventions::all();
        } else {
            $res = DB::select("SELECT zi.libelle AS libelle_zone,zu.zone_intervention_id
FROM tb_zone_utilisateurs zu,
tb_zone_interventions zi
WHERE zu.zone_intervention_id=zi.id AND zu.utilisateur_id=$responsale
          ;");
            return $res;
        }

    }
    public function creationzoneInterventions(array $data)
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
        $data['heure_creation'] = Carbon::now();
        // Si la validation réussit, créer un nouveau nature economique
        $datazoneInterventions = zoneInterventions::create($data);

        return ['zoneInterventions' => $datazoneInterventions];
    }



    // Méthode pour modifier un produit
    public function updatezoneInterventions($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $zoneInterventions = zoneInterventions::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$zoneInterventions) {
            throw new ModelNotFoundException('zoneInterventions non trouvé.');
        }

        // Mettre à jour les informations du produit
        $zoneInterventions->update($data);

        return ['zoneInterventions' => $zoneInterventions];
    }

    // Méthode pour supprimer un produit
    public function deletezoneInterventions($id)
    {
        // Trouver le produit à supprimer
        $produit = zoneInterventions::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Distri non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'District supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getzoneInterventionsById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = zoneInterventions::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getzoneInterventionssByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return zoneInterventions::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
