<?php

namespace App\Http\Service;
use App\Models\district;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class districtService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listedistrict()
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;

            $res = DB::select("SELECT *
            FROM tb_districts
          ;");
            return $res;


    }
    public function affectationdistrict($responsale)
    {
        return district::all();


    }

    public function listezoneResponsable($responsale)
    {
        if ($responsale == 0) {
            return district::all();
        } else {
            $res = DB::select("SELECT zi.libelle AS libelle_zone,zu.zone_intervention_id
FROM tb_zone_utilisateurs zu,
tb_districts zi
WHERE zu.zone_intervention_id=zi.id AND zu.utilisateur_id=$responsale
          ;");
            return $res;
        }

    }
    public function creationdistrict(array $data)
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
        $datadistrict = district::create($data);

        return ['district' => $datadistrict];
    }



    // Méthode pour modifier un produit
    public function updatedistrict($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $district = district::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$district) {
            throw new ModelNotFoundException('District non trouvé.');
        }

        // Mettre à jour les informations du produit
        $district->update($data);

        return ['district' => $district];
    }

    // Méthode pour supprimer un produit
    public function deletedistrict($id)
    {
        // Trouver le produit à supprimer
        $produit = district::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Distri non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'Distri supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getdistrictById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = district::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getdistrictsByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return district::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}