<?php

namespace App\Http\Controllers;
use App\Http\Service\RoleUtilisateurService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class RoleUtilisateurController extends Controller
{

    protected $RoleUtilisateurService;

    // Injection du ProductService dans le contrôleur
    public function __construct(RoleUtilisateurService $serviceFonction)
    {
        $this->RoleUtilisateurService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->RoleUtilisateurService->listeroleUtilisateur();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['code','libelle']);

        // Utiliser le service pour créer le Fonction
        $result = $this->RoleUtilisateurService->creationroleUtilisateur($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'Role créé avec succès.',
            'roleUtilisateur' => $result['roleUtilisateur']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['code','libelle']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service RoleUtilisateurService pour mettre à jour la fonction
            $result = $this->RoleUtilisateurService->updateroleUtilisateur($id, $data);
            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'role mise à jour avec succès.',
                'roleUtilisateur' => $result['roleUtilisateur']
            ], 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }


    // Méthode pour supprimer un Fonction
    public function destroy($id)
    {
        try {
            // Utiliser le service RoleUtilisateurService pour supprimer le Fonction
            $result = $this->RoleUtilisateurService->deleteroleUtilisateur($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
