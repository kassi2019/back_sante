<?php

namespace App\Http\Controllers;
use App\Http\Service\districtService;
use Illuminate\Http\Request;
use App\Models\district;
use Carbon\Carbon;
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




    public function importationDistrict(Request $request)
    {
        $userId = auth()->user()->id;


        // Vérifie si 'dataExcel' existe et est un tableau
        if (!isset($request->dataExcel) || !is_array($request->dataExcel)) {
            return response()->json(['message' => 'Les données Excel sont manquantes ou incorrectes.'], 400);
        }

        $increment = 0;

        foreach ($request->dataExcel as $line) {
            // Vérifie que la ligne contient les clés attendues
            if (isset($line["LIBELLE"])) {

                $libelle = $line["LIBELLE"];
                $longitude = $line["LONGITUDE"];
                $latitude = $line["LATITUDE"];

                // Optionnel : Validation des données (par exemple vérifier que le code n'est pas vide)
                if ( empty($libelle)) {
                    // Tu peux ajouter un log ou un message d'erreur ici pour les lignes invalides
                    continue; // Ignorer cette ligne si elle est invalide
                }

                try {
                    // Créer l'enregistrement dans la base de données
                    district::create([

                        'libelle' => $libelle,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'heure_creation'=> Carbon::now(),
                        'user_id'=> $userId
                    ]);
                    $increment++;
                } catch (\Exception $e) {
                    // Gérer l'exception, par exemple en loggant l'erreur ou en retournant un message
                    \Log::error("Erreur lors de l'importation de la ligne : " . $e->getMessage());
                }
            } else {
                // Gérer le cas où les champs attendus sont absents dans la ligne
                \Log::warning("Ligne invalide détectée sans CODE ou LIBELLE.");
            }
        }

        // Retourne la réponse basée sur le nombre d'enregistrements traités
        if ($increment > 0) {
            return response()->json([
                'message' => "$increment enregistrements importés avec succès."
            ], 201);
        } else {
            return response()->json([
                'message' => "Aucun enregistrement importé."
            ], 419);
        }
    }

}