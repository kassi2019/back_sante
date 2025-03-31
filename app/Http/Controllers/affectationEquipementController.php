<?php

namespace App\Http\Controllers;
use App\Http\Service\affectationEquipementService;
use Illuminate\Http\Request;
use App\Models\affectationEquipement;
use App\Models\histoEquipement;
use App\Models\equipement;
use App\Models\histoAffectationEquipement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class affectationEquipementController extends Controller
{

    protected $affectationEquipementService;

    // Injection du ProductService dans le contrôleur
    public function __construct(affectationEquipementService $serviceFonction)
    {
        $this->affectationEquipementService = $serviceFonction;
    }
    public function index()
    {

        $products = $this->affectationEquipementService->listeequipementAffecte();
        return response()->json($products);
    }

    public function groupeAgentAffecte()
    {

        $products = $this->affectationEquipementService->groupeParAgent();
        return response()->json($products);
    }


    // Méthode pour créer un Fonction
    public function store(Request $request)
    {
        // Récupération de l'ID de l'utilisateur authentifié
        $userId = auth()->user()->id;
        $heure_creation = Carbon::now();

        // Recherche de l'affectation de l'équipement
        $historique1 = affectationEquipement::where('equipement_id', $request->equipement_id)
            ->where('agent_id', $request->agent_id)
            ->first();

        // Si l'historique existe, on met à jour l'affectation
        if ($historique1) {
            $sommeQte = $historique1->quantite_affecte + $request->quantite_affecte;
            $historique1->update([
                'type_equipement_id' => $request->type_equipement_id,
                'quantite_affecte' => $sommeQte,
                'superviseur_id' => $request->superviseur_id,
                'agent_id' => $request->agent_id,
                'heure_creation' => $heure_creation,
                'user_id' => $userId,
                'quantite_dispo' => $request->quantite_dispo,
            ]);
            $equipement = $historique1; // L'équipement mis à jour
        } else {
            // Si l'historique n'existe pas, on crée une nouvelle affectation
            $equipement = affectationEquipement::create([
                'type_equipement_id' => $request->type_equipement_id,
                'equipement_id' => $request->equipement_id,
                'quantite_affecte' => $request->quantite_affecte,
                'superviseur_id' => $request->superviseur_id,
                'agent_id' => $request->agent_id,
                'heure_creation' => $heure_creation,
                'user_id' => $userId,
                'quantite_dispo' => $request->quantite_dispo,
            ]);
        }

        // Mise à jour de l'équipement dans l'historique
        $historique = equipement::find($equipement->equipement_id);
        if ($historique) {
            $historique->update([
                'quantite' => $equipement->quantite_dispo,
                'heure_creation' => $heure_creation,
                'user_id' => $userId,
            ]);
        }

        // Enregistrement dans l'historique des affectations d'équipement
        histoAffectationEquipement::create([
            'affectation_id' => $equipement->id,
            'type_equipement_id' => $equipement->type_equipement_id,
            'equipement_id' => $equipement->equipement_id,
            'quantite_affecte' => $request->quantite_affecte,
            'agent_id' => $equipement->agent_id,
            'heure_creation' => $heure_creation,
            'user_id' => $equipement->user_id,
        ]);

        // Retourner la réponse JSON avec le statut 201
        return response()->json([
            'message' => 'Équipement créé avec succès.',
            'equipement' => $equipement
        ], 201); // Code HTTP 201 pour "créé"
    }






    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['type_equipement_id', 'equipement_id', 'quantite_affecte','status','superviseur_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service districtService pour mettre à jour la fonction
            $result = $this->affectationEquipementService->updateAffectationEquipement($id, $data);

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'equipement mise à jour avec succès.',
                'affectation' => $result['affectation']
            ], 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }
}