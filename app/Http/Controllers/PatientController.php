<?php

namespace App\Http\Controllers;
use App\Http\Service\patientService;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
class PatientController extends Controller
{

    protected $patientService;

    // Injection du ProductService dans le contrôleur
    public function __construct(patientService $serviceFonction)
    {
        $this->patientService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->patientService->listepatient();
        return response()->json($products);
    }

    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'sexe', 'date_naissance', 'encours', 'type_patient_id', 'lieu_naissance', 'numero_cni', 'numero_cmu','chef_famille_id','encours']);

        // Utiliser le service pour créer le Fonction
        $result = $this->patientService->creationpatient($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'patient créé avec succès.',
            'patient' => $result['patient']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'sexe', 'date_naissance', 'encours', 'type_patient_id', 'lieu_naissance', 'numero_cni', 'numero_cmu','chef_famille_id','encours']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service patientService pour mettre à jour la fonction
            $result = $this->patientService->updatepatient($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'patient mise à jour avec succès.',
                'patient' => $result['patient']
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
            // Utiliser le service patientService pour supprimer le Fonction
            $result = $this->patientService->deletepatient($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
