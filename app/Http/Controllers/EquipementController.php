<?php

namespace App\Http\Controllers;
use App\Http\Service\equipementService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class EquipementController extends Controller
{

    protected $equipementService;

    // Injection du ProductService dans le contrôleur
    public function __construct(equipementService $serviceFonction)
    {
        $this->equipementService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->equipementService->listeequipement();
        return response()->json($products);
    }
    public function afficheTypeEquipement()
    {

        $products = $this->equipementService->groupeParTypeMedicament();
        return response()->json($products);
    }
    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle','code', 'type_equipement_id', "quantite",'unite_comptage']);

        // Utiliser le service pour créer le Fonction
        $result = $this->equipementService->creationequipement($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'equipement créé avec succès.',
            'equipement' => $result['equipement']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle', 'type_equipement_id', "quantite",'code','unite_comptage']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service equipementService pour mettre à jour la fonction
            $result = $this->equipementService->updateequipement($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'equipement mise à jour avec succès.',
                'equipement' => $result['equipement']
            ], 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

    public function updateRenouvellement(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle', 'type_equipement_id', "quantite", "quantitesaisir"]);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service equipementService pour mettre à jour la fonction
            $result = $this->equipementService->updateequipementrenouvellement($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'equipement mise à jour avec succès.',
                'equipement' => $result['equipement']
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
            // Utiliser le service equipementService pour supprimer le Fonction
            $result = $this->equipementService->deleteequipement($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
