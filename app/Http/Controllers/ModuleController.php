<?php

namespace App\Http\Controllers;
use App\Http\Service\ModuleService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class ModuleController extends Controller
{

    protected $ModuleService;

    // Injection du ProductService dans le contrôleur
    public function __construct(ModuleService $serviceFonction)
    {
        $this->ModuleService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->ModuleService->listeModule();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['code', 'libelle']);

        // Utiliser le service pour créer le Fonction
        $result = $this->ModuleService->creationModule($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'Module créé avec succès.',
            'module' => $result['module']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['code', 'libelle']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service ModuleService pour mettre à jour la fonction
            $result = $this->ModuleService->updateModule($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'Module mise à jour avec succès.',
                'module' => $result['module']
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
            // Utiliser le service ModuleService pour supprimer le Fonction
            $result = $this->ModuleService->deleteModule($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
