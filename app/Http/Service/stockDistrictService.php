<?php

namespace App\Http\Service;
use App\Models\stockDistrict;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class stockDistrictService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeTypeEquipementStockDistrict()
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT sd.type_equipement_id,tp.libelle AS libelle_type_equipement FROM tb_stock_districts sd
                                        INNER JOIN tb_type_equipements tp ON tp.id=sd.type_equipement_id
                                        GROUP BY sd.type_equipement_id,tp.libelle


          ;");
        } else {
            $res = DB::select("SELECT sd.type_equipement_id,tp.libelle AS libelle_type_equipement FROM tb_stock_districts sd
                                        INNER JOIN tb_type_equipements tp ON tp.id=sd.type_equipement_id
                                        WHERE sd.user_id='$userId'
                                        GROUP BY sd.type_equipement_id,tp.libelle
          ;");
        }

        return $res;
    }


    public function listeStockDistrict()
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT tp.code,tp.unite_comptage,tp.libelle,sd.*
                                    FROM tb_stock_districts sd
                                    INNER JOIN tb_equipements tp ON tp.id=sd.equipement_id






          ;");
        } else {
            $res = DB::select("SELECT tp.code,tp.unite_comptage,tp.libelle,sd.*
                                        FROM tb_stock_districts sd
                                        INNER JOIN tb_equipements tp ON tp.id=sd.equipement_id

                                        WHERE sd.user_id='$userId'
          ;");
        }
        return $res;
    }

    public function listeSuperviseurParDistrict()
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT CONCAT(noms, ' ' ,prenoms) AS nom_prenoms,id AS id_ustilisateur FROM users
                                        WHERE respo_superieur_id is null and responsable_id is not null
          ;");
        } else {
            $res = DB::select("SELECT CONCAT(noms, ' ' ,prenoms) AS nom_prenoms,id AS id_ustilisateur FROM users
                                        WHERE responsable_id =' $userId'
          ;");
        }
        return $res;
    }

    public function enregistrerStockDistrict(array $data)
    {

        $userId = auth()->user()->id; // Assurez-vous que l'authentification est bien configurée

        // Ajouter l'ID de l'utilisateur aux données
        $data['user_id'] = $userId;
        $data['heure_creation'] = Carbon::now();
        // Si la validation réussit, créer un nouveau nature economique
        $data = stockDistrict::create($data);

        return ['stockDistrict' => $data];
    }



    // Méthode pour modifier un produit
    public function updatestockDistrict($id, array $data)
    {

        // Trouver le produit à mettre à jour
        $equipe = stockDistrict::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$equipe) {
            throw new ModelNotFoundException('stockDistrict non trouvé.');
        }

        // Mettre à jour les informations du produit
        $equipe->update($data);

        return ['stockDistrict' => $equipe];
    }

    // Méthode pour supprimer un produit
    public function deletestockDistrict($id)
    {
        // Trouver le produit à supprimer
        $produit = stockDistrict::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$produit) {
            throw new ModelNotFoundException('stockDistrict non trouvé.');
        }

        // Supprimer le produit
        $produit->delete();

        return ['message' => 'stockDistrict supprimé avec succès.'];
    }




    // afficher l equipement en fonction du type equipement
    public function afficheEquipementParTypeElement($typeEquipement)
    {
        $res = DB::select("SELECT * FROM tb_equipements
        WHERE type_equipement_id='$typeEquipement'

          ;");
        return $res;
    }



    public function listeEquipementDuDistrictParType($type)
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT sd.*,te.libelle,te.code,te.unite_comptage
             FROM tb_stock_districts sd
                INNER JOIN tb_equipements te ON te.id=sd.equipement_id
                WHERE sd.quantite!=0 and sd.type_equipement_id='$type'


          ;");
        } else {
            $res = DB::select("SELECT sd.*,te.libelle,te.code,te.unite_comptage
             FROM tb_stock_districts sd
            INNER JOIN tb_equipements te ON te.id=sd.equipement_id
            WHERE sd.quantite!=0 AND sd.user_id='$userId' and sd.type_equipement_id='$type'
          ;");
        }

        return $res;
    }


    public function listeEquipementDesSuperviseur()
    {
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT sup.*,eq.libelle FROM tb_stock_superviseurs sup
INNER JOIN tb_equipements eq ON sup.equipement_id=eq.id



          ;");
        } else {
            $res = DB::select("SELECT sup.*,eq.libelle FROM tb_stock_superviseurs sup
INNER JOIN tb_equipements eq ON sup.equipement_id=eq.id
WHERE sup.user_id=' $userId '
          ;");
        }

        return $res;
    }
}