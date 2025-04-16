<?php

namespace App\Http\Service;
use App\Models\aireSanitaire;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class aireSanitaireService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeaireSanitaire()
    {
        return aireSanitaire::all();
    }
    public function affectationaireSanitaire($responsale)
    {
        return aireSanitaire::all();
    }
    public function listeDistrict()
    {

         $roleId = auth()->user()->id_roles;
        $userId = auth()->user()->id;


            $res = DB::select("SELECT distinct ta.district_id,di.libelle AS libelle_district,di.*
FROM tb_aire_sanitaires ta,
tb_districts di
WHERE ta.district_id=di.id

          ;");
            return $res;
    }
    public function listezoneResponsable($responsale)
    {
        if ($responsale == 0) {
            return aireSanitaire::all();
        } else {
            $res = DB::select("SELECT zi.libelle AS libelle_zone,zu.zone_intervention_id
FROM tb_zone_utilisateurs zu,
tb_zone_interventions zi
WHERE zu.zone_intervention_id=zi.id AND zu.utilisateur_id=$responsale
          ;");
            return $res;
        }

    }
    public function creationaireSanitaire(array $data)
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
        $dataaireSanitaire = aireSanitaire::create($data);

        return ['aireSanitaire' => $dataaireSanitaire];
    }



    // Méthode pour modifier un produit
    public function updateaireSanitaire($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $aireSanitaire = aireSanitaire::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$aireSanitaire) {
            throw new ModelNotFoundException('aireSanitaire non trouvé.');
        }

        // Mettre à jour les informations du produit
        $aireSanitaire->update($data);

        return ['aireSanitaire' => $aireSanitaire];
    }

    // Méthode pour supprimer un produit
    public function deleteaireSanitaire($id)
    {
        // Trouver le produit à supprimer
        $produit = aireSanitaire::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Distri non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'District supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getaireSanitaireById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = aireSanitaire::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getaireSanitairesByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return aireSanitaire::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }






    public function listeAireSanitaireParDistrict($responsale)
    {

            $res = DB::select("SELECT * FROM tb_aire_sanitaires
WHERE district_id='$responsale'
          ;");
            return $res;


    }
}