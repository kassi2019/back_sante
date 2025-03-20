<?php

namespace App\Http\Controllers;
use App\Http\Service\aireSanitaireService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class AireSanitaireController extends Controller
{

    protected $aireSanitaireService;

    // Injection du ProductService dans le contrôleur
    public function __construct(aireSanitaireService $serviceFonction)
    {
        $this->aireSanitaireService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->aireSanitaireService->listeaireSanitaire();
        return response()->json($products);
    }
    public function District()
    {

        $products = $this->aireSanitaireService->listeDistrict();
        return response()->json($products);
    }
    public function listeZoneResponsable($responsale)
    {

        $products = $this->aireSanitaireService->listezoneResponsable($responsale);
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['libelle', 'longitude', 'latitude','district_id']);

        // Utiliser le service pour créer le Fonction
        $result = $this->aireSanitaireService->creationaireSanitaire($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'aire Sanitaire créé avec succès.',
            'aireSanitaire' => $result['aireSanitaire']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['libelle', 'longitude', 'latitude', 'district_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service aireSanitaireService pour mettre à jour la fonction
            $result = $this->aireSanitaireService->updateaireSanitaire($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'aire Sanitaire mise à jour avec succès.',
                'aireSanitaire' => $result['aireSanitaire']
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
            // Utiliser le service aireSanitaireService pour supprimer le Fonction
            $result = $this->aireSanitaireService->deleteaireSanitaire($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
