<?php

namespace App\Http\Service;
use App\Models\User;
use App\Http\Service\ValidationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class AuthService
{

    protected $validationService;

    // Injection du service ValidationService dans le constructeur
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    // Méthode pour modifier un annee
    public function updateUser($id, array $data)
    {


        // Trouver  annee à mettre à jour
        $datautilisateur = User::find($id);

        // Si l annee n'existe pas, lever une exception
        if (!$datautilisateur) {
            throw new ModelNotFoundException('Utilisateur non trouvé.');
        }

        // Mettre à jour les informations du produit
        $datautilisateur->update($data);

        return ['datautilisateur' => $datautilisateur];
    }

    // Méthode pour supprimer une annee
    public function deleteUser($id)
    {
        // Trouver le produit à supprimer
        $datautilisateur = User::find($id);

        // Si le produit n'existe pas, lever une exception
        if (!$datautilisateur) {
            throw new ModelNotFoundException('Utilisateur non trouvé.');
        }

        // Supprimer le produit
        $datautilisateur->delete();

        return ['message' => 'Utilisateur supprimé avec succès.'];
    }

    public function agentparsuperviseur($respo)
    {

        $res = DB::select("SELECT CONCAT(zi.noms,'  ',zi.prenoms) nom_prenoms,zi.id
FROM
users zi
WHERE zi.responsable_id=$respo
          ;");
        return $res;


    }

}
