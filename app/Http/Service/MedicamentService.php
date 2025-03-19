<?php

namespace App\Http\Service;
use App\Models\medicament;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class MedicamentService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listemedicament()
    {
        return medicament::all();
    }

    public function creationmedicament(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validatemedicament($data);

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
        $datamedicament = medicament::create($data);

        return ['medicament' => $datamedicament];
    }



    // Méthode pour modifier un produit
    public function updatemedicament($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validatemedicament($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $medicament = medicament::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$medicament) {
            throw new ModelNotFoundException('médicament non trouvé.');
        }

        // Mettre à jour les informations du produit
        $medicament->update($data);

        return ['medicament' => $medicament];
    }

    // Méthode pour supprimer un produit
    public function deletemedicament($id)
    {
        // Trouver le produit à supprimer
        $produit = medicament::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('médicament non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'médicament supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getmedicamentById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = medicament::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getmedicamentsByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return medicament::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
