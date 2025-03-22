<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Service\AuthService;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use DB;
class AuthController extends Controller
{
    protected $AuthService;

    // Injection du ProductService dans le contrôleur
    public function __construct(AuthService $serviceAnnee)
    {
        $this->AuthService = $serviceAnnee;
    }
    public function index()
    {

        $data_actuel = array();
        $res = DB::select("SELECT DISTINCT rm.*, ro.libelle AS libelle_role,
         CONCAT(us.noms, ' ', us.prenoms) AS nom_responsable
        FROM users rm
        JOIN tb_roles ro ON rm.id_roles = ro.id
        LEFT JOIN users us ON us.responsable_id = rm.id
          ;");
        foreach ($res as $region) {

            $q = array(
                "id" => $region->id,
                "noms" => $region->noms,
                "id_roles" => $region->id_roles,
                "prenoms" => $region->prenoms,
                "numero" => $region->numero,
                "libelle_role" => $region->libelle_role,
                "nom_responsable" => $region->nom_responsable,
                "responsable_id" => $region->responsable_id,
            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }
    // Enregistrement d'un utilisateur
    public function register(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'noms' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'numero' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'id_roles' => 'required|numeric',
            'responsable_id' => 'required|numeric',
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'noms' => $validated['noms'],
            'prenoms' => $validated['prenoms'],
            'numero' => $validated['numero'],
            'affiche_password' => $validated['password'],
            'password' => bcrypt($validated['password']),
            'id_roles' => $validated['id_roles'],
            'responsable_id' => $validated['responsable_id']
        ]);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user], 201);


    }
    // Connexion d'un utilisateur
    public function login(Request $request)
    {
        $credentials = $request->only('numero', 'password');
        $user = User::where('numero', $request->get('numero'))->first();
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // return response()->json(['token' => $token]);
        return response()->json(compact('token', 'user'));
    }

    // Déconnexion
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }




    public function update(Request $request, $id)
    {
        // Récupérer les données envoyées dans la requête
        $data = $request->only(['noms', 'prenoms', 'numero', 'id_roles', 'responsable_id']);

        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = auth()->user()->id;

            // Ajouter l'ID de l'utilisateur aux données
            $data['user_id'] = $userId;

            // Utiliser le service AnneeService pour mettre à jour la fonction
            $result = $this->AuthService->updateUser($id, $data);

            // Si des erreurs de validation existent
            if (isset($result['errors'])) {
                return response()->json([
                    'errors' => $result['errors']
                ], 422); // Code HTTP 422 pour une erreur de validation
            }

            // Retourner la fonction mise à jour
            return response()->json([
                'message' => 'Utilisateur mise à jour avec succès.',
                'datautilisateur' => $result['datautilisateur']
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
            // Utiliser le service AnneeService pour supprimer le Fonction
            $result = $this->AuthService->deleteUser($id);

            // Retourner un message de succès
            return response()->json($result, 200); // Code HTTP 200 pour "OK"
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // Code HTTP 404 pour "Non trouvé"
        }
    }


    // permettre d affiche les modules de utilisateur connecte

    public function listeModuleUtilisateurConnecter()
    {
        $userId = auth()->user()->id;
        $data_actuel = array();
        $res = DB::select("SELECT distinct ro.libelle AS libelle_role,rom.id_modules,rom.id_roles
            FROM users rm,
            tb_roles ro,
            tb_roles_modules rom
            WHERE rm.id_roles=ro.id AND rom.id_roles=ro.id AND rm.id='$userId'
          ;");
        foreach ($res as $region) {

            $q = array(
                "id_modules" => $region->id_modules,
                "id_roles" => $region->id_roles,
                "libelle_role" => $region->libelle_role,

            );

            array_push($data_actuel, $q);
        }

        return response()->json($data_actuel);
    }



    public function listeAgentParsuperviseur($respo)
    {

        $products = $this->AuthService->agentparsuperviseur($respo);
        return response()->json($products);
    }
}
