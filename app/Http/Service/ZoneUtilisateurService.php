<?php

namespace App\Http\Service;
use App\Models\zoneUtilisateurs;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class ZoneUtilisateurService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }


    // Méthode pour modifier un produit
    public function updateZoneUtilisateur($id, array $data)
    {

        $roleModule = zoneUtilisateurs::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$roleModule) {
            throw new ModelNotFoundException('Zone Intervention non trouvé.');
        }

        // Mettre à jour les informations du produit
        $roleModule->update($data);

        return ['roleModule' => $roleModule];
    }

    // Méthode pour supprimer un produit
    public function deleteZoneUtililisateur($id)
    {
        // Trouver le produit à supprimer
        $produit = zoneUtilisateurs::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Zone Intervention non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'Zone Intervention supprimé avec succès.'];
    }

    public function listeResponsable()
    {
        $res = DB::select("SELECT distinct responsable_id FROM users us
        WHERE us.responsable_id IS NOT NULL
          ;");
        return $res;
    }




    public function AireSanitaireParsuperviseur($responsale)
    {

            $res = DB::select("SELECT zi.libelle AS libelle_aire_Sanitaire,zu.aire_sanitaire_id
FROM tb_zone_utilisateurs zu,
tb_aire_sanitaires zi
WHERE zu.aire_sanitaire_id=zi.id AND zu.utilisateur_id=$responsale
          ;");
            return $res;


    }
    public function zoneInterventionParsup($airesanitaire)
    {

        $res = DB::select("SELECT zi.*
FROM
tb_zone_interventions zi
WHERE   zi.aire_sanitaire_id=$airesanitaire
          ;");
        return $res;


    }
}
