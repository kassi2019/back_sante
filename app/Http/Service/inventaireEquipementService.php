<?php

namespace App\Http\Service;
use App\Models\inventaireEquipement;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class inventaireEquipementService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeinventaireEquipement()
    {
        return inventaireEquipement::all();
    }

    public function creationinventaireEquipement(array $data)
    {

        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée
        $superviseurId = auth()->user()->responsable_id;
        $districtId = auth()->user()->respo_superieur_id;
        $respoEquipeId = auth()->user()->respo_superieur_id;
        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;
        $data['superviseur_id'] = $superviseurId;
        $data['resp_sup_id'] = $districtId;
        $data['responsable_equipe_id'] = $respoEquipeId;
        $data['heure_creation'] = Carbon::now();
        // Si la validation réussit, créer un nouveau nature economique
        $data = inventaireEquipement::create($data);

        return ['inventaireEquipement' => $data];
    }



    // Méthode pour modifier un produit
    public function updateinventaireEquipement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        // $errors = $this->validationService->validateInventaireEquipement($data);

        // // Si des erreurs existent, retourner les erreurs
        // if ($errors) {
        //     return ['errors' => $errors];
        // }

        // Trouver le produit à mettre à jour
        $equipe = inventaireEquipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$equipe) {
            throw new ModelNotFoundException('inventaireEquipement non trouvé.');
        }

        // Mettre à jour les informations du produit
        $equipe->update($data);

        return ['inventaireEquipement' => $equipe];
    }

    // Méthode pour supprimer un produit
    public function deleteinventaireEquipement($id)
    {
        // Trouver le produit à supprimer
        $produit = inventaireEquipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('inventaireEquipement non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'inventaireEquipement supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function geteById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = inventaireEquipement::find($id);

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
            return inventaireEquipement::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
