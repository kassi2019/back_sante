<?php

namespace App\Http\Service;
use App\Models\roleModule;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class RoleModuleService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeroleModule()
    {

        $data_actuel = array();
        $res = DB::select("SELECT distinct ro.libelle AS libelle_role
FROM tb_roles_modules rm,
tb_roles ro
WHERE rm.id_roles=ro.id


          ;");
        foreach ($res as $region) {

            $q = array(
                "name" => $region->libelle_role,
                // "email" => $region->email,
                // "service_id" => $region->service_id,
                // "libelle_role" => $region->libelle_role,
                // "libelle_service" => $region->libelle_service,
                // "role_id" => $region->role_id,
                // "bascule" => $region->bascule,

            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);

    }
    public function creationroleModule(array $data)
    {
        // Utilisation du service ValidationService pour valider les données
        $errors = $this->validationService->validateRoleModule($data);

        // Si des erreurs de validation existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée

        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;

        // Si la validation réussit, créer un nouveau nature economique
        $dataroleModule = roleModule::create($data);

        return ['roleModule' => $dataroleModule];
    }



    // Méthode pour modifier un produit
    public function updateroleModule($id, array $data)
    {
        // // Valider les données d'entrée pour la mise à jour
        // $errors = $this->validationService->validateRoleModule($data);
        // // Si des erreurs existent, retourner les erreurs
        // if ($errors) {
        //     return ['errors' => $errors];
        // }
        // Trouver le produit à mettre à jour
        $roleModule = roleModule::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$roleModule) {
            throw new ModelNotFoundException('role Utilisateur non trouvé.');
        }

        // Mettre à jour les informations du produit
        $roleModule->update($data);

        return ['roleModule' => $roleModule];
    }

    // Méthode pour supprimer un produit
    public function deleteroleModule($id)
    {
        // Trouver le produit à supprimer
        $produit = roleModule::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('role Utilisateur non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'role Utilisateur supprimé avec succès.'];
    }


}