<?php

namespace App\Http\Controllers;
use App\Http\Service\MedicamentService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class MedicamentController extends Controller
{

    protected $MedicamentService;

    // Injection du ProductService dans le contrôleur
    public function __construct(MedicamentService $serviceFonction)
    {
        $this->MedicamentService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->MedicamentService->listemedicament();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['unite_comptage', 'libelle', 'dosage']);

        // Utiliser le service pour créer le Fonction
        $result = $this->MedicamentService->creationmedicament($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'medicament créé avec succès.',
            'medicament' => $result['medicament']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['unite_comptage', 'libelle', 'dosage']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service MedicamentService pour mettre à jour la fonction
            $result = $this->MedicamentService->updatemedicament($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'medicament mise à jour avec succès.',
                'medicament' => $result['medicament']
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
            // Utiliser le service MedicamentService pour supprimer le Fonction
            $result = $this->MedicamentService->deletemedicament($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
