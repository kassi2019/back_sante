<?php

namespace App\Http\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class ValidationService
{


    // Méthode pour valider les données d'un produit
    public function validateCodeLibelle(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'code' => 'required|string',
            'libelle' => 'required|string',

        ], [
            'code.unique' => 'ce code existe déjà. Veuillez saisir un autre code.',
        ]);

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }



    // Méthode pour valider les données d'un produit
    public function validateLibelle(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'libelle' => 'required|string',

        ], );

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }

    public function validatemedicament(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'libelle' => 'required|string',
            'unite_comptage' => 'required|string',
            'dosage' => 'required|string',
        ], );

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }
    // Méthode pour récupérer l'ID de l'utilisateur authentifié
    public function getAuthenticatedUserId()
    {
        // Vérifier si l'utilisateur est authentifié
        if (Auth::check()) {
            // Retourner l'ID de l'utilisateur authentifié
            return Auth::id();
        }

        // Si l'utilisateur n'est pas authentifié, retourner null
        return null;
    }









    // Méthode pour valider les données d'un produit
    public function validateRoleModule(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [

            'id_roles' => 'required|string',
            'id_modules' => 'required|string',

        ], );

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }




    public function validateUtilisateur(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [

            'noms' => 'required|string',
            'prenoms' => 'required|string',
            'id_modules' => 'required|string',
            'numero' => 'required|string',
            'id_roles' => 'required|string',

        ], );

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }


    public function validatemenage(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'nom' => 'required|string',
            'prenoms' => 'required|string',
            // 'numero' => 'required|string',

        ], );
        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }
        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }




    public function validatePatient(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'nom' => 'required|string',
            'prenoms' => 'required|string',
            // 'numero' => 'required|string',
            // 'date_naissance' => 'required|string',
            // 'type_patient_id' => 'required|string',
            //'chef_famille_id' => 'required|string'

        ], );
        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }
        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }





    public function validateInventaireEquipement(array $data, )
    {
        // Définition des règles de validation pour le produit
        $validator = Validator::make($data, [
            'equipement_id' => 'required|string',
            'status' => 'required|string',
            'type_equipement_id' => 'required|string',
        ], );

        // Si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return $validator->errors();
        }

        // Si la validation passe, retourner null (aucune erreur)
        return null;
    }
}
