<?php

namespace App\Http\Service;
use App\Models\affectationEquipement;
use App\Models\histoEquipement;
use App\Models\equipement;
use App\Models\histoAffectationEquipement;

use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class affectationEquipementService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }
    public function listehistoAffectation()
    {
        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        if ($roleid == 7) {
            $res = DB::select("SELECT *
FROM tb_histo_affectation_equipements eq
          ;");
        } else {
            $res = DB::select("SELECT *
FROM tb_histo_affectation_equipements eq
where eq.agent_id='$userId' or eq.superviseur_id='$userId'

          ;");
        }
        return $res;
    }
    public function listeequipementAffecte()
    {
        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        if ($roleid == 7) {
            $res = DB::select("SELECT eq.agent_id,te.libelle as libelle_equipement,eq.quantite_affecte,eq.id as id_table,eq.status,eq.quantite_utilise,eq.equipement_id,eq.mouvement,eq.quantite_saisir,eq.qte_recu_sup,eq.numerolot,eq.date_expiration
FROM tb_affectation_equipements eq
inner join tb_equipements te on te.id=eq.equipement_id
inner join users us on us.id=eq.agent_id


group by eq.agent_id,te.libelle,eq.quantite_affecte,eq.id,eq.status,eq.quantite_utilise,eq.equipement_id
          ;");
        }else{
            $res = DB::select("SELECT eq.agent_id,te.libelle as libelle_equipement,eq.quantite_affecte,eq.id as id_table,eq.status,eq.quantite_utilise,eq.equipement_id,eq.mouvement,eq.quantite_saisir,eq.qte_recu_sup,eq.numerolot,eq.date_expiration
FROM tb_affectation_equipements eq
inner join tb_equipements te on te.id=eq.equipement_id
inner join users us on us.id=eq.agent_id

where eq.agent_id='$userId' or eq.superviseur_id='$userId'

group by eq.agent_id,te.libelle,eq.quantite_affecte,eq.id,eq.status,eq.quantite_utilise,eq.equipement_id
          ;");
        }
        return $res;
    }


    public function groupeParAgent()
    {
        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        // $roleId = auth()->user()->id_roles;
        if ($roleid == 7) {
            $res = DB::select("SELECT us.id as agent_id,concat(us.noms,' ',us.prenoms) nom_agent,us.responsable_id,concat(us1.noms,' ',us1.prenoms) nom_superviseur

FROM  users us,
 tb_roles ro,
 users us1
WHERE us.responsable_id IS NOT NULL AND us.id_roles=ro.id AND ro.code=2 AND us.responsable_id=us1.id

          ;");
        }else{
            $res = DB::select("
SELECT us.id as agent_id,concat(us.noms,' ',us.prenoms) nom_agent,us.responsable_id,concat(us1.noms,' ',us1.prenoms) nom_superviseur

FROM  users us,
 tb_roles ro,
 users us1
WHERE us.responsable_id IS NOT NULL AND us.id_roles=ro.id AND ro.code=2 AND us.responsable_id=us1.id and us.responsable_id='$userId' or us.respo_superieur_id='$userId'

          ;");
        }

        return $res;


    }

    // Méthode pour supprimer un produit
    public function deleteequipement($id)
    {
        // Trouver le produit à supprimer
        $produit = equipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('Equipement non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'Equipement supprimé avec succès.'];
    }


    public function updateAffectationEquipement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour


        // Trouver le produit à mettre à jour
        $affectation = affectationEquipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$affectation) {
            throw new ModelNotFoundException('affectation non trouvé.');
        }

        // Mettre à jour les informations du produit
        $affectation->update($data);

        return ['affectation' => $affectation];
    }

    // Méthode pour récupérer un produit par son ID
    public function geteById($id)
    {
        // Essayer de trouver le produit avec l'ID
        $produit = equipement::find($id);

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
            return equipement::where('user_id', $userId)->get();
        }

        // Si l'utilisateur n'est pas authentifié, retourner une liste vide ou une erreur
        return [];
    }




    public function updateHistoAffectationEquipement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour


        // Trouver le produit à mettre à jour
        $affectation = histoAffectationEquipement::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$affectation) {
            throw new ModelNotFoundException('affectation non trouvé.');
        }

        // Mettre à jour les informations du produit
        $affectation->update($data);

        return ['histoAffectationEquipement' => $affectation];
    }




    public function AfficheAscAuMoinUnEquipement()
    {
        $roleid = auth()->user()->id_roles;
        $userId = auth()->user()->id;
        // $roleId = auth()->user()->id_roles;
        if ($roleid == 7) {
            $res = DB::select("SELECT distinct CONCAT(ut.noms,' ',ut.prenoms) AS nom_prenoms_asc,ae.agent_id
FROM tb_affectation_equipements ae
INNER JOIN users ut ON ae.agent_id=ut.id
          ;");
        } else {
            $res = DB::select("
SELECT distinct CONCAT(ut.noms,' ',ut.prenoms) AS nom_prenoms_asc,ae.agent_id
FROM tb_affectation_equipements ae
INNER JOIN users ut ON ae.agent_id=ut.id WHERE ut.id='$userId'

          ;");
        }

        return $res;


    }
}
