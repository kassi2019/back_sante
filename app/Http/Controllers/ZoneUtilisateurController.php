<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Service\ZoneUtilisateurService;
use App\Models\User;
use Carbon\Carbon;
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
        $userId = auth()->user()->id;
        $roleId = auth()->user()->id_roles;
        if ($roleId == 7) {
            $res = DB::select("SELECT DISTINCT CONCAT(ro.noms,' ',ro.prenoms)  AS nom_utilisateur,ro.noms,ro.prenoms,ro.id_roles,rol.code,ro.responsable_id,ro.id
            FROM  tb_zone_utilisateurs rm
           LEFT JOIN users ro  ON rm.utilisateur_id=ro.id,
           tb_roles rol
           WHERE ro.id_roles=rol.id


          ;");
            foreach ($res as $region) {

                $q = array(
                    "nom_utilisateur" => $region->nom_utilisateur,
                    "utilisateur_id" => $region->id,
                    "responsable_id" => $region->responsable_id,
                    "code_role" => $region->code,
                );

                array_push($data_actuel, $q);
            }
        } else {
            $res = DB::select("SELECT DISTINCT CONCAT(ro.noms,' ',ro.prenoms)  AS nom_utilisateur,ro.noms,ro.prenoms,ro.id_roles,rol.code,ro.responsable_id,ro.id,ro.respo_superieur_id
            FROM  tb_zone_utilisateurs rm
           LEFT JOIN users ro  ON rm.utilisateur_id=ro.id,
           tb_roles rol

           WHERE ro.id_roles=rol.id AND ro.id='$userId' OR ro.id_roles=rol.id AND ro.responsable_id='$userId' OR ro.id_roles=rol.id AND ro.respo_superieur_id='$userId'


          ;");
            foreach ($res as $region) {

                $q = array(
                    "nom_utilisateur" => $region->nom_utilisateur,
                    "utilisateur_id" => $region->id,
                    "responsable_id" => $region->responsable_id,
                    "code_role" => $region->code,
                );

                array_push($data_actuel, $q);
            }
        }

        return response()->json($data_actuel);
    }

    public function listeZoneInterventionParUtilisateur()
    {

        $data_actuel = array();
        $res = DB::select("
        SELECT DISTINCT zte.libelle AS libelle_aire_sanitaire,
zinte.libelle AS libelle_zone_intervention,dist.libelle AS libelle_district,rm.*
        FROM tb_zone_utilisateurs rm
        LEFT JOIN tb_zone_interventions zinte ON zinte.id=rm.zone_intervention_id
        LEFT JOIN tb_aire_sanitaires zte ON zte.id=rm.aire_sanitaire_id
        LEFT JOIN tb_districts dist ON dist.id=rm.district_id

          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle_aire_sanitaire" => $region->libelle_aire_sanitaire,
                "libelle_zone_intervention" => $region->libelle_zone_intervention,
                "libelle_district" => $region->libelle_district,
                "id" => $region->id,
                "zone_intervention_id" => $region->zone_intervention_id,
                "aire_sanitaire_id" => $region->aire_sanitaire_id,
                "district_id" => $region->district_id,
                "superviseur_id" => $region->superviseur_id,
                "utilisateur_id" => $region->utilisateur_id
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
                $dossierborderau->aire_sanitaire_id = $value["aire_sanitaire_id"];

            } else {

                $dossierborderau->aire_sanitaire_id = $value;  // Directly assign the integer
            }
            $dossierborderau->utilisateur_id = $request->utilisateur_id;
            $dossierborderau->superviseur_id = $request->superviseur_id;
            //$dossierborderau->district_id = $request->district_id;
            $dossierborderau->user_id = $userId;
            $dossierborderau->heure_creation = Carbon::now();
            $dossierborderau->save();
        }

    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        $data = $request->only(['zone_intervention_id', 'utilisateur_id', 'aire_sanitaire_id', 'superviseur_id']);
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
        $roleid = auth()->user()->id_roles;
        $data_actuel = array();
        if ($roleid == 7) {
            $res = DB::select("SELECT DISTINCT m.libelle AS libelle_zone,rm.zone_intervention_id
            FROM tb_zone_utilisateurs rm,
            tb_zone_interventions m
            WHERE rm.zone_intervention_id=m.id
          ;");
            foreach ($res as $region) {

                $q = array(
                    "libelle" => $region->libelle_zone,
                    "zone_intervention_id" => $region->zone_intervention_id

                );

                array_push($data_actuel, $q);
            }

            return response()->json($data_actuel);
        } else {
            $res = DB::select("SELECT DISTINCT m.libelle AS libelle_zone,rm.zone_intervention_id
                    FROM tb_zone_utilisateurs rm,
                    tb_zone_interventions m
                    WHERE rm.zone_intervention_id=m.id and rm.utilisateur_id='$userId'
          ;");
            foreach ($res as $region) {

                $q = array(
                    "libelle" => $region->libelle_zone,
                    "zone_intervention_id" => $region->zone_intervention_id,

                );

                array_push($data_actuel, $q);
            }

            return response()->json($data_actuel);
        }

    }




    public function Responsable($role)
    {

        $products = $this->ZoneUtilisateurService->listeResponsable($role);
        return response()->json($products);
    }



    public function listeAireSanitaireParsuperviseur($responsale)
    {

        $products = $this->ZoneUtilisateurService->AireSanitaireParsuperviseur($responsale);
        return response()->json($products);
    }


    public function listeZoneInterventParsuperviseur($airesanitaire)
    {

        $products = $this->ZoneUtilisateurService->zoneInterventionParsup($airesanitaire);
        return response()->json($products);
    }




    public function enregistrerzoneParAgent(Request $request)
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
            $dossierborderau->aire_sanitaire_id = $request->aire_sanitaire_id;
            $dossierborderau->superviseur_id = $request->superviseur_id;
            $dossierborderau->user_id = $userId;
            $dossierborderau->heure_creation = Carbon::now();
            $dossierborderau->save();
        }

    }






    public function enregistrerDistrictParAgent(Request $request)
    {


        // Proceed only if validation passes
        // $userid = User::where("id", Auth::user()->id)->first();
        $userId = auth()->user()->id;
        foreach ($request->DataModule as $value) {
            $dossierborderau = new zoneUtilisateurs();

            // Check if $value is an array (expected case)
            if (is_array($value)) {
                $dossierborderau->district_id = $value["district_id"];

            } else {
                // Handle the case where $value is an integer (e.g., 1)
                $dossierborderau->district_id = $value;  // Directly assign the integer
            }
            $dossierborderau->utilisateur_id = $request->utilisateur_id;
            // $dossierborderau->aire_sanitaire_id = $request->aire_sanitaire_id;
            // $dossierborderau->superviseur_id = $request->superviseur_id;
            $dossierborderau->user_id = $userId;
            $dossierborderau->heure_creation = Carbon::now();
            $dossierborderau->save();
        }

    }





    public function afficheDistrictParAgent($dist)
    {

        $data_actuel = array();
        $res = DB::select("
        SELECT DISTINCT zte.libelle AS libelle_aire_sanitaire,
zinte.libelle AS libelle_zone_intervention,dist.libelle AS libelle_district,rm.*
        FROM tb_zone_utilisateurs rm
        LEFT JOIN tb_zone_interventions zinte ON zinte.id=rm.zone_intervention_id
        LEFT JOIN tb_aire_sanitaires zte ON zte.id=rm.aire_sanitaire_id
        LEFT JOIN tb_districts dist ON dist.id=rm.district_id

        where rm.utilisateur_id='$dist'

          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle_aire_sanitaire" => $region->libelle_aire_sanitaire,
                "libelle_zone_intervention" => $region->libelle_zone_intervention,
                "libelle_district" => $region->libelle_district,
                "id" => $region->id,
                "zone_intervention_id" => $region->zone_intervention_id,
                "aire_sanitaire_id" => $region->aire_sanitaire_id,
                "district_id" => $region->district_id,
                "superviseur_id" => $region->superviseur_id,
                "utilisateur_id" => $region->utilisateur_id
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }
}
