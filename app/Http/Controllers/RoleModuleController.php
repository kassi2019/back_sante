<?php

namespace App\Http\Controllers;
use App\Http\Service\RoleModuleService;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\roleModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class RoleModuleController extends Controller
{

    protected $RoleModuleService;

    // Injection du ProductService dans le contrôleur
    public function __construct(RoleModuleService $serviceFonction)
    {
        $this->RoleModuleService = $serviceFonction;
    }
    public function index()
    {

        $data_actuel = array();
        $res = DB::select("SELECT distinct ro.libelle AS libelle_role,ro.id
            FROM tb_roles_modules rm,
            tb_roles ro
            WHERE rm.id_roles=ro.id


          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle" => $region->libelle_role,
                "role_id" => $region->id,
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }

    public function listeModuleParRole()
    {

        $data_actuel = array();
        $res = DB::select("SELECT DISTINCT m.libelle AS libelle_module,rm.id_roles,rm.id_modules,rm.id
FROM tb_roles_modules rm,
tb_modules m
WHERE rm.id_modules=m.id
          ;");
        foreach ($res as $region) {

            $q = array(
                "libelle" => $region->libelle_module,
                "id_modules" => $region->id_modules,
                "id_roles" => $region->id_roles,
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
            $dossierborderau = new roleModule();

            // Check if $value is an array (expected case)
            if (is_array($value)) {
                $dossierborderau->id_modules = $value["id_modules"];
            } else {
                // Handle the case where $value is an integer (e.g., 1)
                $dossierborderau->id_modules = $value;  // Directly assign the integer
            }

            $dossierborderau->id_roles = $request->id_roles;
            $dossierborderau->user_id = $userId;
            $dossierborderau->save();
        }

    }



    // Méthode pour modifier un Fonction
    public function update(Request $request, $id)
    {
        $data = $request->only(['id_roles', 'id_modules']);

        try {
            $userId = auth()->user()->id;
            $data['user_id'] = $userId;
            $result = $this->RoleModuleService->updateroleModule($id, $data);
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
            // Utiliser le service RoleModuleService pour supprimer le Fonction
            $result = $this->RoleModuleService->deleteroleModule($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }

}
