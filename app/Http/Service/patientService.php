<?php

namespace App\Http\Service;
use App\Models\patient;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
class patientService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listepatient()
    {
        // return patient::all();


        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        if ($roleid == 7) {
            $res = DB::select("SELECT
    pt.*,
    DATEDIFF(NOW(), pt.date_naissance) AS age_en_jours
FROM tb_patients pt


          ;");
            return $res;
        } else {
            $res = DB::select("
        SELECT
    pt.*,
    DATEDIFF(NOW(), pt.date_naissance) AS age_en_jours
FROM tb_patients pt
   WHERE pt.user_id='$userId' OR pt.responsable_id='$userId'

          ;");
            return $res;
        }
    }

    public function creationpatient(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validatePatient($data);

        // Si des erreurs de validation existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée
        $reponsableid = auth()->user()->responsable_id;

        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;
        $data['responsable_id'] = $reponsableid;
        $data['encours'] = 0;
        $data['heure_creation'] = Carbon::now();
        // Si la validation réussit, créer un nouveau nature economique
        $datapatient = patient::create($data);

        return ['patient' => $datapatient];
    }



    // Méthode pour modifier un produit
    public function updatepatient($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validatePatient($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver le produit à mettre à jour
        $patient = patient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$patient) {
            throw new ModelNotFoundException('patient non trouvé.');
        }

        // Mettre à jour les informations du produit
        $patient->update($data);

        return ['patient' => $patient];
    }

    // Méthode pour supprimer un produit
    public function deletepatient($id)
    {
        // Trouver le produit à supprimer
        $produit = patient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('patient non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'me supprimé avec succès.'];
    }




    // Méthode pour récupérer un produit par son ID
    public function getpatientById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = patient::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException("Produit avec ID {$id} non trouvé.");
        }

        return $produit;
    }


    // Méthode pour récupérer les produits d'un utilisateur connecté
    public function getpatientsByUser()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $this->validationService->getAuthenticatedUserId();

        // Si l'utilisateur est authentifié
        if ($userId) {
            // Retourner les produits de cet utilisateur
            return patient::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }
}
