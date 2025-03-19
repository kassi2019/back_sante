<?php

namespace App\Http\Service;
use App\Models\menage;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
class menageService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listemenage()
    {
        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        if ($roleid == 7) {
            $res = DB::select("SELECT me.*,CONCAT(me.nom,' ',me.prenoms) AS nom_chef FROM tb_menages me
          ;");
            return $res;
        } else {
            $res = DB::select("SELECT me.*,CONCAT(me.nom,' ',me.prenoms) AS nom_chef FROM tb_menages me
        WHERE me.user_id='$userId' OR me.responsable_id='$userId'
          ;");
            return $res;
        }
    }

    public function creationmenage(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validatemenage($data);

        // Si des erreurs de validation existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée
        $responsableId = auth()->user()->responsable_id;
        $data['heure_creation'] = Carbon::now();
        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;
        $data['responsable_id'] = $responsableId;
        // Si la validation réussit, créer un nouveau nature economique
        $datamenage = menage::create($data);

        return ['menage' => $datamenage];
    }



    // Méthode pour modifier un produit
    public function updatemenage($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validatemenage($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $menage = menage::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$menage) {
            throw new ModelNotFoundException('menage non trouvé.');
        }

        // Mettre à jour les informations du produit
        $menage->update($data);

        return ['menage' => $menage];
    }

    // Méthode pour supprimer un produit
    public function deletemenage($id)
    {
        // Trouver le produit à supprimer
        $produit = menage::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('menage non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'me supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getmenageById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = menage::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getmenagesByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return menage::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
