<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleUtilisateurController;
use App\Http\Controllers\RoleModuleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ZoneInterventionController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\ZoneUtilisateurController;
use App\Http\Controllers\MenageController;
use App\Http\Controllers\TypePatientController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VaccinController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');




// route utilisateur
Route::post('register', [AuthController::class, 'register']);
Route::post('login', action: [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('listeUtilisateur', [AuthController::class, 'index'])->middleware('auth:api');
Route::put('/modifierUtilisateur/{id}', [AuthController::class, 'update'])->middleware('auth:api'); // Modifier un produit
Route::delete('/supprimerUtilisateur/{id}', [AuthController::class, 'destroy'])->middleware('auth:api'); // Supprimer un produit
Route::get('listeModuleUtilisateurConnecter', [AuthController::class, 'listeModuleUtilisateurConnecter'])->middleware('auth:api');
//route fonction




//route role utilisateur
Route::get('/listeRoleUtilisateur', [RoleUtilisateurController::class, 'index'])->middleware('auth:api');
Route::post('ajouterRoleUtilisateur', [RoleUtilisateurController::class, 'store'])->middleware('auth:api');
Route::put('/modifierRoleUtilisateur/{id}', [RoleUtilisateurController::class, 'update'])->middleware('auth:api'); // Modifier un RoleUtilisateur
Route::delete('/supprimerRoleUtilisateur/{id}', [RoleUtilisateurController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur



//route module
Route::get('/listeModule', [ModuleController::class, 'index'])->middleware('auth:api');
Route::post('ajouterModule', [ModuleController::class, 'store'])->middleware('auth:api');
Route::put('/modifierModule/{id}', [ModuleController::class, 'update'])->middleware('auth:api'); // Modifier un RoleModule
Route::delete('/supprimerModule/{id}', [ModuleController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur



//route role module
Route::get('/listeRoleModule', [RoleModuleController::class, 'index'])->middleware('auth:api');
Route::post('ajouterRoleModule', [RoleModuleController::class, 'store'])->middleware('auth:api');
Route::put('/modifierRoleModule/{id}', [RoleModuleController::class, 'update'])->middleware('auth:api'); // Modifier un RoleModule
Route::delete('/supprimerRoleModule/{id}', [RoleModuleController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur
Route::get('/listeModuleParRole', [RoleModuleController::class, 'listeModuleParRole'])->middleware('auth:api');//route role module

// route zone intervention
Route::get('/listeZoneIntervention', [ZoneInterventionController::class, 'index'])->middleware('auth:api');
Route::post('ajouterZoneIntervention', [ZoneInterventionController::class, 'store'])->middleware('auth:api');
Route::put('/modifierZoneIntervention/{id}', [ZoneInterventionController::class, 'update'])->middleware('auth:api'); // Modifier un ZoneIntervention
Route::delete('/supprimerZoneIntervention/{id}', [ZoneInterventionController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur

Route::get('/listeZoneResponsable/{id}', [ZoneInterventionController::class, 'listeZoneResponsable'])->middleware('auth:api');


// route zone intervention
Route::get('/listemedicament', [MedicamentController::class, 'index'])->middleware('auth:api');
Route::post('ajoutermedicament', [MedicamentController::class, 'store'])->middleware('auth:api');
Route::put('/modifiermedicament/{id}', [MedicamentController::class, 'update'])->middleware('auth:api'); // Modifier un M
Route::delete('/supprimermedicament/{id}', [MedicamentController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur



//route zone intervention utilisateur
Route::get('/listeZoneUtilisateur', [ZoneUtilisateurController::class, 'index'])->middleware('auth:api');
Route::post('ajouterZoneUtilisateur', [ZoneUtilisateurController::class, 'store'])->middleware('auth:api');
Route::put('/modifierZoneUtilisateur/{id}', [ZoneUtilisateurController::class, 'update'])->middleware('auth:api'); // Modifier un ZoneUtilisateur
Route::delete('/supprimerZoneUtilisateur/{id}', [ZoneUtilisateurController::class, 'destroy'])->middleware('auth:api'); // Supprimer un RoleUtilisateur
Route::get('/listeZoneParUtilisateur', [ZoneUtilisateurController::class, 'listeZoneInterventionParUtilisateur'])->middleware('auth:api');//route role module
Route::get('/zoneParAgent', [ZoneUtilisateurController::class, 'zoneParAgent'])->middleware('auth:api');//route role module




//route menage
Route::get('/menage', [MenageController::class, 'index'])->middleware('auth:api');
Route::post('menage', [MenageController::class, 'store'])->middleware('auth:api');
Route::put('/menage/{id}', [MenageController::class, 'update'])->middleware('auth:api');
Route::delete('/menage/{id}', [MenageController::class, 'destroy'])->middleware('auth:api');


//route type patient
Route::get('/typepatient', [TypePatientController::class, 'index'])->middleware('auth:api');
Route::post('typepatient', [TypePatientController::class, 'store'])->middleware('auth:api');
Route::put('/typepatient/{id}', [TypePatientController::class, 'update'])->middleware('auth:api');
Route::delete('/typepatient/{id}', [TypePatientController::class, 'destroy'])->middleware('auth:api');



//route  patient
Route::get('/patient', [PatientController::class, 'index'])->middleware('auth:api');
Route::post('patient', [PatientController::class, 'store'])->middleware('auth:api');
Route::put('/patient/{id}', [PatientController::class, 'update'])->middleware('auth:api');
Route::delete('/patient/{id}', [PatientController::class, 'destroy'])->middleware('auth:api');


//route  vaccin
Route::get('/vaccin', [VaccinController::class, 'index'])->middleware('auth:api');
Route::post('vaccin', [VaccinController::class, 'store'])->middleware('auth:api');
Route::put('/vaccin/{id}', [VaccinController::class, 'update'])->middleware('auth:api');
Route::delete('/vaccin/{id}', [VaccinController::class, 'destroy'])->middleware('auth:api');
