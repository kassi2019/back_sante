<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Service\ZoneUtilisateurService;
use App\Models\User;
use DB;
use App\Models\zoneUtilisateurs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ZoneUtilisateurController extends Controller
{
    protected $ZoneUtilisateurService;

    // Injection du ProductService dans le contrôleur
    public function __construct(ZoneUtilisateurService $serviceFonction)
    {
        $this->ZoneUtilisateurService = $serviceFonction;
    }
    public function index()
    {

        $data_actuel = array();
        $res = DB::select("SELECT DISTINCT CONCAT(ro.noms,' ',ro.prenoms)  AS nom_utilisateur,ro.id
            FROM tb_zone_utilisateurs rm,
            users ro
            WHERE rm.utilisateur_id=ro.id


          ;");
        foreach ($res as $region) {

            $q = array(
                "nom_utilisateur" => $region->nom_utilisateur,
                "utilisateur_id" => $region->id,
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }

    public function listeZoneInterventionParUtilisateur()
    {

        $data_actuel = array();
        $res = DB::select("SELECT DISTINCT m.libelle AS libelle_zone,rm.zone_intervention_id,rm.utilisateur_id,rm.id
FROM tb_zone_utilisateurs rm,
tb_zone_interventions m
WHERE rm.zone_intervention_id=m.id
          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle" => $region->libelle_zone,
                "zone_intervention_id" => $region->zone_intervention_id,
                "utilisateur_id" => $region->utilisateur_id,
                "id" => $region->id,
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }
    // Méthode pour créer un Fonction
    public function store(Request $request)
    {


        // Proceed only if validation passes
        // $userid = User::where("id", Auth::user()->id)->first();
        $userId = auth()->user()->id;
        foreach ($request->DataModule as $value) {
            $dossierborderau = new zoneUtilisateurs();

            // Check if $value is an array (expected case)
            if (is_array($value)) {
                $dossierborderau->zone_intervention_id = $value["zone_intervention_id"];
            } else {
                // Handle the case where $value is an integer (e.g., 1)
                $dossierborderau->zone_intervention_id = $value;  // Directly assign the integer
            }

            $dossierborderau->utilisateur_id = $request->utilisateur_id;
            $dossierborderau->user_id = $userId;
            $dossierborderau->save();
        }

    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        $data = $request->only(['zone_intervention_id', 'utilisateur_id']);
        try {
            $userId = auth()->user()->id;
            $data['user_id'] = $userId;
            $result = $this->ZoneUtilisateurService->updateZoneUtilisateur($id, $data);
            return response()->json([
                'message' => 'role mise à jour avec succès.',
                'roleModule' => $result['roleModule']
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }


    // Méthode pour supprimer un Fonction
    public function destroy($id)
    {
        try {
            // Utiliser le service ZoneUtilisateurService pour supprimer le Fonction
            $result = $this->ZoneUtilisateurService->deleteZoneUtililisateur($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }





    public function zoneParAgent()
    {
        $userId = auth()->user()->id;
        $data_actuel = array();
        $res = DB::select("SELECT DISTINCT m.libelle AS libelle_zone,rm.zone_intervention_id,rm.utilisateur_id,rm.id
FROM tb_zone_utilisateurs rm,
tb_zone_interventions m
WHERE rm.zone_intervention_id=m.id and rm.utilisateur_id='$userId'
          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle" => $region->libelle_zone,
                "zone_intervention_id" => $region->zone_intervention_id,
                "utilisateur_id" => $region->utilisateur_id,
                "id" => $region->id,
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }
}