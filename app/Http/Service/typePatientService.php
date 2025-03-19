<?php

namespace App\Http\Service;
use App\Models\typePatient;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class typePatientService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listetypePatient()
    {
        return typePatient::all();
    }

    public function creationtypePatient(array $data)
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
        $datatypePatient = typePatient::create($data);

        return ['typePatient' => $datatypePatient];
    }



    // Méthode pour modifier un produit
    public function updatetypePatient($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $typePatient = typePatient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$typePatient) {
            throw new ModelNotFoundException('type patient non trouvé.');
        }

        // Mettre à jour les informations du produit
        $typePatient->update($data);

        return ['typePatient' => $typePatient];
    }

    // Méthode pour supprimer un produit
    public function deletetypePatient($id)
    {
        // Trouver le produit à supprimer
        $produit = typePatient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('type patient non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'type patient supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function gettypePatientById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = typePatient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function gettypePatientsByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return typePatient::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
