<?php

namespace App\Http\Controllers;
use App\Http\Service\patientService;
use App\Models\patient;
use App\Models\patientVaccin;

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
    public function store89(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'sexe', 'date_naissance', 'encours', 'type_patient_id', 'lieu_naissance', 'numero_cni', 'numero_cmu', 'chef_famille_id']);

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

    public function store(Request $request)
    {
        $userId = auth()->user()->id;
        $responsableId = auth()->user()->responsable_id;
        $idetat = 0;
        $resultat = patient::create([
            'nom' => $request->nom,
            'prenoms' => $request->prenoms,
            'numero' => $request->numero,
            'sexe' => $request->sexe,
            'date_naissance' => $request->date_naissance,
            'encours' => $idetat,
            'type_patient_id' => $request->type_patient_id,
            'lieu_naissance' => $request->lieu_naissance,
            'numero_cni' => $request->numero_cni,
            'numero_cmu' => $request->numero_cmu,
            'chef_famille_id' => $request->chef_famille_id,
            'user_id' => $userId,
            'responsable_id' => $responsableId,

        ]);
        if ($resultat) {
            foreach ($request->DataModule as $value) {
                $dossierborderau = new patientVaccin();
                // Check if $value is an array (expected case)
                if (is_array($value)) {
                    $dossierborderau->vaccin_id = $value["vaccin_id"];
                } else {
                    // Handle the case where $value is an integer (e.g., 1)
                    $dossierborderau->vaccin_id = $value;  // Directly assign the integer
                }
                $dossierborderau->patient_id = $resultat->id;
                $dossierborderau->user_id = $userId;
                $dossierborderau->save();


            }
        }

        return response()->json($resultat, 201);
    }

    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['nom', 'prenoms', 'numero', 'sexe', 'date_naissance', 'encours', 'type_patient_id', 'lieu_naissance', 'numero_cni', 'numero_cmu', 'chef_famille_id', 'encours']);

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
