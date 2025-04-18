<?php

namespace App\Http\Controllers;
use App\Http\Service\stockDistrictService;
use Illuminate\Http\Request;
use App\Models\stockSuperviseur;
use App\Models\stockDistrict;
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

    public function afficheEquipementParTypeEquipement($typeEquipe)
    {
        $products = $this->stockDistrictService->afficheEquipementParTypeElement($typeEquipe);
        return response()->json($products);
    }

    public function afficheListeSuperviseurParDistrict()
    {
        $products = $this->stockDistrictService->listeSuperviseurParDistrict();
        return response()->json($products);
    }


  public function listeTypeEquipementDansStockDistrict()
    {

        $products = $this->stockDistrictService->listeTypeEquipementStockDistrict();
        return response()->json($products);
    }
    public function listeTypeEquipementStockSuperviseur()
    {

        $products = $this->stockDistrictService->listeTypeEquipementStockSuperviseur();
        return response()->json($products);
    }

   public function listeEquipementDesSuperviseur()
    {

        $products = $this->stockDistrictService->listeEquipementDesSuperviseur();
        return response()->json($products);
    }


    public function listeEquipementDuDistrictParType($type)
    {

        $products = $this->stockDistrictService->listeEquipementDuDistrictParType($type);
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
        $data = $request->only(['type_equipement_id', 'equipement_id', 'numerolot', 'quantite', 'date_expiration', 'quantite_initial']);

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
        $data = $request->only(['type_equipement_id', 'equipement_id', 'numerolot', 'quantite', 'date_expiration', 'quantite_initial']);

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


    public function enregistrementStockSuperviseur(Request $request)
    {
        // Validation des données
        $request->validate([
            'type_equipement_id' => 'required|integer',
            'equipement_id' => 'required|integer',
            'numerolot' => 'required|string',
            'quantite' => 'required|integer',
            'valeur' => 'required|integer',
            'date_expiration' => 'nullable|date',
            'superviseur_id' => 'nullable|integer',
        ]);

        $userId = auth()->id();
        $heure_creation = Carbon::now();

        try {
            $historiqueDistrict = stockDistrict::where('equipement_id', $request->equipement_id)
                ->where('user_id', $userId)
                ->where('numerolot', $request->numerolot)
                ->first();

            if (!$historiqueDistrict) {
                return response()->json([
                    'message' => 'Aucun stock trouvé dans le district pour cet équipement.'
                ], 404);
            }

            // Cas 1 : Reprise par superviseur
            if ($request->valeur == 1) {
                $historiqueSuperviseur = stockSuperviseur::where('equipement_id', $request->equipement_id)
                    ->where('user_id', $userId)
                    ->where('numerolot', $request->numerolot)
                    ->first();

                if (!$historiqueSuperviseur) {
                    return response()->json([
                        'message' => 'Stock superviseur introuvable.'
                    ], 404);
                }

                $nouvelleQuantite = $historiqueSuperviseur->quantite - $request->quantite;

                $historiqueSuperviseur->update([
                    'type_equipement_id' => $request->type_equipement_id,
                    'quantite' => $nouvelleQuantite,
                    'date_expiration' => $request->date_expiration,
                    'heure_creation' => $heure_creation,
                    'quantite_initial' => $nouvelleQuantite,
                ]);

                $historiqueDistrict->update([
                    'quantite' => $historiqueDistrict->quantite + $request->quantite,
                ]);

                return response()->json([
                    'message' => 'Équipement repris du superviseur vers le district avec succès.',
                    'equipement' => $historiqueSuperviseur
                ], 201);

            } else {
                // Cas 2 : Distribution vers superviseur
                if ($request->quantite > $historiqueDistrict->quantite) {
                    return response()->json([
                        'message' => 'Quantité demandée supérieure à la quantité disponible en stock district.'
                    ], 400);
                }

                $historiqueSuperviseur = stockSuperviseur::where('equipement_id', $request->equipement_id)
                    ->where('superviseur_id', $request->superviseur_id)
                    ->where('numerolot', $request->numerolot)
                    ->first();

                if (!$historiqueSuperviseur) {
                    $equipement = stockSuperviseur::create([
                        'type_equipement_id' => $request->type_equipement_id,
                        'equipement_id' => $request->equipement_id,
                        'numerolot' => $request->numerolot,
                        'quantite' => $request->quantite,
                        'date_expiration' => $request->date_expiration,
                        'heure_creation' => $heure_creation,
                        'superviseur_id' => $request->superviseur_id,
                        'user_id' => $userId,
                        'quantite_initial' => $request->quantite,
                    ]);
                } else {
                    $nouvelleQuantite = $historiqueSuperviseur->quantite + $request->quantite;

                    $historiqueSuperviseur->update([
                        'type_equipement_id' => $request->type_equipement_id,
                        'quantite' => $nouvelleQuantite,
                        'date_expiration' => $request->date_expiration,
                        'heure_creation' => $heure_creation,
                        'quantite_initial' => $nouvelleQuantite,
                    ]);

                    $equipement = $historiqueSuperviseur;
                }

                // Mise à jour du stock du district
                $historiqueDistrict->update([
                    'quantite' => $historiqueDistrict->quantite - $request->quantite,
                ]);

                return response()->json([
                    'message' => 'Équipement transféré au superviseur avec succès.',
                    'equipement' => $equipement
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors du traitement.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function supprimerEquipementSup($id)
    {
        try {
            $userId = auth()->id();

            // Récupération de l'enregistrement dans stockSuperviseur
            $equipementSup = stockSuperviseur::find($id);

            if (!$equipementSup) {
                return response()->json(['message' => 'Équipement superviseur introuvable.'], 404);
            }

            // Récupération de l'enregistrement correspondant dans stockDistrict
            $equipementDistrict = stockDistrict::where('equipement_id', $equipementSup->equipement_id)
                ->where('user_id', $userId)
                ->where('numerolot', $equipementSup->numerolot)
                ->first();

            if (!$equipementDistrict) {
                return response()->json(['message' => 'Équipement district introuvable.'], 404);
            }

            // Mise à jour de la quantité
            $nouvelleQuantite = $equipementSup->quantite + $equipementDistrict->quantite;

            $equipementDistrict->update([
                'quantite' => $nouvelleQuantite,
            ]);

            // Suppression de l'enregistrement superviseur
            $equipementSup->delete();

            return response()->json(['message' => 'Supprimé avec succès !'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
