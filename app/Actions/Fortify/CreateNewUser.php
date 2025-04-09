<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Role;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Log; // Añadimos Log
use Illuminate\Support\Facades\Http; // Añadimos esto para hacer la solicitud HTTP
use Laravel\Jetstream\Jetstream;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */

     public function create(array $input): User
     {
        
 
         Validator::make($input, [
             'name' => ['required', 'string', 'max:255'],
             'apellido' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
             'password' => $this->passwordRules(),
             'status' => ['required', 'in:activo,inactivo'],
             'direccion' => ['nullable', 'string', 'max:255'],
             'fotoperfil' => ['nullable', 'file', 'image', 'max:10240'],
             'telefono' => ['nullable', 'string', 'max:15'],
             'country_code' => ['required', 'string', 'max:10'],
             'country_name' => ['required', 'string', 'max:255'],
             'state' => ['required', 'string', 'max:255'],
             'city' => ['required', 'string', 'max:255'],
             'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
         ])->validateWithBag('default');
 
         $pais = Pais::firstOrCreate(
             ['codigo' => $input['country_code']],
             ['nombre' => $input['country_name']]
         );
         
 
         $estado = Estado::firstOrCreate(
             ['nombre' => $input['state'], 'pais_id' => $pais->id]
         );
      
 
         $ciudad = Ciudad::firstOrCreate(
             ['nombre' => $input['city'], 'estado_id' => $estado->id]
         );
         
         $fotoPath = null;
         if (isset($input['fotoperfil']) && $input['fotoperfil']) {
             $fotoPath = $input['fotoperfil']->store('profile-photos', 'public');
             
         }
 
         $role = Role::firstOrCreate(['nombre' => 'usuario']);
         
 
         $user = User::create([
             'name' => $input['name'],
             'apellido' => $input['apellido'],
             'email' => $input['email'],
             'password' => Hash::make($input['password']),
             'status' => $input['status'],
             'direccion' => $input['direccion'] ?? null,
             'fotoperfil' => $fotoPath,
             'telefono' => $input['telefono'] ?? null,
             'pais_id' => $pais->id,
             'role_id' => $role->id,
         ]);
        
 
         return $user;
     }











}