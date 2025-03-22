<?php

namespace App\Http\Controllers;
use App\Http\Service\zoneInterventionService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class zoneInterventionController extends Controller
{

    protected $zoneInterventionService;

    // Injection du ProductService dans le contrôleur
    public function __construct(zoneInterventionService $serviceFonction)
    {
        $this->zoneInterventionService = $serviceFonction;
    }
    public function listeDistrict_zi()
    {

        $products = $this->zoneInterventionService->listeDistrict_ZI();
        return response()->json($products);
    }
    public function listeaireSanitaire_zi()
    {

        $products = $this->zoneInterventionService->listeAireSanitaire_zi();
        return response()->json($products);
    }
    public function index()
    {
        $products = $this->zoneInterventionService->listezoneInterventions();
        return response()->json($products);
    }
    public function listeZoneResponsable($responsale)
    {

        $products = $this->zoneInterventionService->listezoneResponsable($responsale);
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle', 'longitude', 'latitude', 'aire_sanitaire_id', 'district_id']);

        // Utiliser le service pour créer le Fonction
        $result = $this->zoneInterventionService->creationzoneInterventions($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'zoneInterventions créé avec succès.',
            'zoneInterventions' => $result['zoneInterventions']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle', 'longitude', 'latitude', 'aire_sanitaire_id', 'district_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service zoneInterventionService pour mettre à jour la fonction
            $result = $this->zoneInterventionService->updatezoneInterventions($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'zoneInterventions mise à jour avec succès.',
                'zoneInterventions' => $result['zoneInterventions']
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
            // Utiliser le service zoneInterventionService pour supprimer le Fonction
            $result = $this->zoneInterventionService->deletezoneInterventions($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}