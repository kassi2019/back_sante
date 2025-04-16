<?php

namespace App\Http\Controllers;
use App\Http\Service\stockDistrictService;
use Illuminate\Http\Request;
use App\Models\district;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class stockDistrictController extends Controller
{

    protected $stockDistrictService;

    // Injection du ProductService dans le contrôleur
    public function __construct(stockDistrictService $serviceFonction)
    {
        $this->stockDistrictService = $serviceFonction;
    }

public function afficheEquipementParTypeEquipement($typeEquipe){
        $products = $this->stockDistrictService->afficheEquipementParTypeElement($typeEquipe);
        return response()->json($products);
}



    // public function index()
    // {

    //     $products = $this->stockDistrictService->listedistrict();
    //     return response()->json($products);
    // }
    public function listeTypeEquipementDansStockDistrict()
    {

        $products = $this->stockDistrictService->listeTypeEquipementStockDistrict();
        return response()->json($products);
    }

   public function listeStockDistrict()
    {

        $products = $this->stockDistrictService->listeStockDistrict();
        return response()->json($products);
    }

    // // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupérer les données de la requête
        $data = $request->only(['type_equipement_id', 'equipement_id', 'numerolot', 'quantite', 'date_expiration']);

        // Utiliser le service pour créer le Fonction
        $result = $this->stockDistrictService->enregistrerStockDistrict($data);

        // Vérifier si la validation a échoué
        if (isset($result['errors'])) {
            return response()->json([
                'errors' => $result['errors']
            ], 422); // Code HTTP 422 pour une erreur de validation
        }

        // Retourner une réponse avec le Fonction créé
        return response()->json([
            'message' => 'Equipement créé avec succès.',
            'stockDistrict' => $result['stockDistrict']
        ], 201); // Code HTTP 201 pour "créé"
    }



    // // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['type_equipement_id', 'equipement_id', 'numerolot', 'quantite', 'date_expiration']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service stockDistrictService pour mettre à jour la fonction
            $result = $this->stockDistrictService->updatestockDistrict($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'stock District mise à jour avec succès.',
                'stockDistrict' => $result['stockDistrict']
            ], 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }


    // // Méthode pour supprimer un Fonction
    public function destroy($id)
    {
        try {
            // Utiliser le service stockDistrictService pour supprimer le Fonction
            $result = $this->stockDistrictService->deletestockDistrict($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }


}