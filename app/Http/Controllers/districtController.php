<?php

namespace App\Http\Controllers;
use App\Http\Service\districtService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class districtController extends Controller
{

    protected $districtService;

    // Injection du ProductService dans le contrôleur
    public function __construct(districtService $serviceFonction)
    {
        $this->districtService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->districtService->listedistrict();
        return response()->json($products);
    }
    public function listeZoneResponsable($responsale)
    {

        $products = $this->districtService->listezoneResponsable($responsale);
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle', 'longitude', 'latitude']);

        // Utiliser le service pour créer le Fonction
        $result = $this->districtService->creationdistrict($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'district créé avec succès.',
            'district' => $result['district']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle', 'longitude', 'latitude']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service districtService pour mettre à jour la fonction
            $result = $this->districtService->updatedistrict($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'district mise à jour avec succès.',
                'district' => $result['district']
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
            // Utiliser le service districtService pour supprimer le Fonction
            $result = $this->districtService->deletedistrict($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}