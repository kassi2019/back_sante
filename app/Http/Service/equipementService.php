<?php

namespace App\Http\Service;
use App\Models\equipement;
use App\Models\histoEquipement;
use Carbon\Carbon;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class equipementService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function listeequipement()
    {
        // return equipement::all();
 $res = DB::select(" SELECT eq.libelle,eq.code,eq.unite_comptage,eq.type_equipement_id,eq.numero_lot,eq.date_peremption,eq.quantite,eq.quantitesaisir,eq.id
FROM tb_equipements eq

group by eq.libelle,eq.code,eq.unite_comptage,eq.type_equipement_id,eq.numero_lot,eq.date_peremption,eq.quantite,eq.quantitesaisir,eq.id
          ;");
        return $res;

    }
    public function groupeParTypeMedicament()
    {
        // $roleId = auth()->user()->id_roles;

        $res = DB::select("SELECT eq.type_equipement_id,te.libelle as libelle_type_equipement
FROM tb_equipements eq
inner join tb_type_equipements te on te.id=eq.type_equipement_id
group by eq.type_equipement_id,te.libelle
          ;");
        return $res;


    }
    public function creationequipement(array $data)
    {
        $errors = $this->validationService->validateLibelle($data);

        if ($errors) {
            return ['errors' => $errors];
        }

        $userId = auth()->user()->id;


        $data['user_id'] = $userId;
        $data['heure_creation'] = Carbon::now();
        $equipement = equipement::create($data);
        if ($equipement) {
            histoEquipement::create([
                'equipement_id' => $equipement->id,
                'libelle' => $equipement->libelle,
                'code' => $equipement->code,
                'unite_comptage' => $equipement->unite_comptage,
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'numero_lot' => $equipement->numero_lot,
                'date_peremption' => $equipement->date_peremption,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);
        }
        return ['equipement' => $equipement];
    }



    public function updateequipement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver l'équipement à mettre à jour
        $equipement = equipement::find($id);

        // Si l'équipement n'existe pas, lever une exception
        if (!$equipement) {
            throw new ModelNotFoundException('Équipement non trouvé.');
        }

        // Mettre à jour les informations de l'équipement
        $equipement->update($data);

        // Vérifier si une entrée existe déjà dans histoEquipement
        $historique = histoEquipement::where('equipement_id', $id)->first();

        if ($historique) {
            // Mise à jour de l'historique si l'entrée existe
            $historique->update([
                'libelle' => $equipement->libelle,
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'numero_lot' => $equipement->numero_lot,
                'date_peremption' => $equipement->date_peremption,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);
        } else {
            // Création d'un nouvel historique si aucune entrée n'existe
            histoEquipement::create([
                'equipement_id' => $equipement->id,
                'libelle' => $equipement->libelle,
                'quantite' => $equipement->quantite,
                'type_equipement_id' => $equipement->type_equipement_id,
                'numero_lot' => $equipement->numero_lot,
                'date_peremption' => $equipement->date_peremption,
                'heure_creation' => Carbon::now(),
                'user_id' => $equipement->user_id,
            ]);
        }

        return ['equipement' => $equipement];
    }


    public function updateequipementrenouvellement($id, array $data)
    {
        // Valider les données d'entrée pour la mise à jour
        $errors = $this->validationService->validateLibelle($data);

        // Si des erreurs existent, retourner les erreurs
        if ($errors) {
            return ['errors' => $errors];
        }

        // Trouver l'équipement à mettre à jour
        $equipement = equipement::find($id);

        // Si l'équipement n'existe pas, lever une exception
        if (!$equipement) {
            throw new ModelNotFoundException('Équipement non trouvé.');
        }

        // Mettre à jour les informations de l'équipement
        $equipement->update($data);

        // Création d'un nouvel historique si aucune entrée n'existe
        histoEquipement::create([
            'equipement_id' => $equipement->id,
            'libelle' => $equipement->libelle,
            'quantite' => $equipement->quantitesaisir,
            'type_equipement_id' => $equipement->type_equipement_id,
            'heure_creation' => Carbon::now(),
            'user_id' => $equipement->user_id,
            'statut' => 1
        ]);


        return ['equipement' => $equipement];
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






    public function listeGroupeEquipement()
    {
        // return equipement::all();
        $res = DB::select("SELECT eq.libelle,eq.code,eq.type_equipement_id,eq.id
FROM db_asc_sante.tb_equipements eq

group by eq.libelle,eq.code,eq.type_equipement_id,eq.id
          ;");
        return $res;

    }
}
