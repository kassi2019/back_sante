<?php

namespace App\Http\Controllers;
use App\Http\Service\typeEquipementService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class typeEquipementController extends Controller
{

    protected $typeEquipementService;

    // Injection du ProductService dans le contrôleur
    public function __construct(typeEquipementService $serviceFonction)
    {
        $this->typeEquipementService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->typeEquipementService->listetypeequipement();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle']);

        // Utiliser le service pour créer le Fonction
        $result = $this->typeEquipementService->creationtypeequipement($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'equipement créé avec succès.',
            'typeequipement' => $result['typeequipement']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service typeEquipementService pour mettre à jour la fonction
            $result = $this->typeEquipementService->updatetypeequipement($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'type equipement mise à jour avec succès.',
                'typeequipement' => $result['typeequipement']
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
            // Utiliser le service typeEquipementService pour supprimer le Fonction
            $result = $this->typeEquipementService->deletetypeequipement($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}