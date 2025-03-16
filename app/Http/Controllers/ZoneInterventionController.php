<?php

namespace App\Http\Controllers;
use App\Http\Service\ZoneInterventionService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class ZoneInterventionController extends Controller
{

    protected $ZoneInterventionService;

    // Injection du ProductService dans le contrôleur
    public function __construct(ZoneInterventionService $serviceFonction)
    {
        $this->ZoneInterventionService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->ZoneInterventionService->listezoneIntervention();
        return response()->json($products);
    }
    public function listeZoneResponsable($responsale)
    {

        $products = $this->ZoneInterventionService->listezoneResponsable($responsale);
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle', 'longitude', 'latitude']);

        // Utiliser le service pour créer le Fonction
        $result = $this->ZoneInterventionService->creationzoneIntervention($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'zone Intervention créé avec succès.',
            'zoneIntervention' => $result['zoneIntervention']
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

            // Utiliser le service ZoneInterventionService pour mettre à jour la fonction
            $result = $this->ZoneInterventionService->updatezoneIntervention($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'zone Intervention mise à jour avec succès.',
                'zoneIntervention' => $result['zoneIntervention']
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
            // Utiliser le service ZoneInterventionService pour supprimer le Fonction
            $result = $this->ZoneInterventionService->deletezoneIntervention($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
