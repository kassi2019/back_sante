<?php

namespace App\Http\Controllers;
use App\Http\Service\inventaireEquipementService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class inventaireEquipementController extends Controller
{

    protected $inventaireEquipementService;

    // Injection du ProductService dans le contrôleur
    public function __construct(inventaireEquipementService $serviceFonction)
    {
        $this->inventaireEquipementService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->inventaireEquipementService->listeinventaireEquipement();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['type_equipement_id', 'equipement_id', 'status', 'type_equipement_id']);

        // Utiliser le service pour créer le Fonction
        $result = $this->inventaireEquipementService->creationinventaireEquipement($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'equipement créé avec succès.',
            'inventaireEquipement' => $result['inventaireEquipement']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['equipement_id', 'status', 'type_equipement_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service inventaireEquipementService pour mettre à jour la fonction
            $result = $this->inventaireEquipementService->updateinventaireEquipement($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'equipement mise à jour avec succès.',
                'inventaireEquipement' => $result['inventaireEquipement']
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
            // Utiliser le service inventaireEquipementService pour supprimer le Fonction
            $result = $this->inventaireEquipementService->deleteinventaireEquipement($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}