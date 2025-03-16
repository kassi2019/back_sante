<?php

namespace App\Http\Controllers;
use App\Http\Service\menageService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class MenageController extends Controller
{

    protected $menageService;

    // Injection du ProductService dans le contrôleur
    public function __construct(menageService $serviceFonction)
    {
        $this->menageService = $serviceFonction;
    }
    public function index()
    {

        return $this->menageService->listemenage();

    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'longitude', 'latitude', 'zone_intervention_id', 'numero_cni', 'numero_cmu', 'responsable_id']);

        // Utiliser le service pour créer le Fonction
        $result = $this->menageService->creationmenage($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'menage créé avec succès.',
            'menage' => $result['menage']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'longitude', 'latitude', 'zone_intervention_id', 'numero_cni', 'numero_cmu', 'responsable_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service menageService pour mettre à jour la fonction
            $result = $this->menageService->updatemenage($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'menage mise à jour avec succès.',
                'menage' => $result['menage']
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
            // Utiliser le service menageService pour supprimer le Fonction
            $result = $this->menageService->deletemenage($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
