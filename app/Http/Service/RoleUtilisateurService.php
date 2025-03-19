<?php

namespace App\Http\Service;
use Carbon\Carbon;
use App\Models\roleUtilisateur;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
class RoleUtilisateurService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeroleUtilisateur()
    {
         return roleUtilisateur::all();

    }

    public function creationroleUtilisateur(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs de validation existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée
        $data['heure_creation'] = Carbon::now();
        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;

        // Si la validation réussit, créer un nouveau nature economique
        $dataroleUtilisateur = roleUtilisateur::create($data);

        return ['roleUtilisateur' => $dataroleUtilisateur];
    }



    // Méthode pour modifier un produit
    public function updateroleUtilisateur($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $roleUtilisateur = roleUtilisateur::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$roleUtilisateur) {
            throw new ModelNotFoundException('role Utilisateur non trouvé.');
        }

        // Mettre à jour les informations du produit
        $roleUtilisateur->update($data);

        return ['roleUtilisateur' => $roleUtilisateur];
    }

    // Méthode pour supprimer un produit
    public function deleteroleUtilisateur($id)
    {
        // Trouver le produit à supprimer
        $produit = roleUtilisateur::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('role Utilisateur non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'role Utilisateur supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getroleUtilisateurById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = roleUtilisateur::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getroleUtilisateursByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return roleUtilisateur::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
