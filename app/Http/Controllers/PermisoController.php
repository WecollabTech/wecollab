<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermisoController extends Controller
{
    // Obtener todos los permisos
    public function index()
    {
        // Paginación de permisos (5 elementos por página)
        $permisos = Permiso::paginate(5); // Cambia 5 por el número de elementos por página que prefieras
    
        // Devuelve la respuesta paginada en formato JSON
        return response()->json($permisos);
    }


    public function all()
    {
        $permisos = Permiso::all();
        return response()->json($permisos);
    }






    // Crear un nuevo permiso
    public function store(Request $request)
    {
        // Validar los datos con mensajes personalizados
        $request->validate([
            'nombre' => 'required|string|min:3|max:50|unique:permisos,nombre', // Validación: al menos 3 caracteres y único
            'estado' => 'required|in:activo,inactivo', // Validación para el enum
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio.',
            'nombre.string' => 'El nombre del permiso debe ser una cadena de texto.',
            'nombre.min' => 'El nombre del permiso debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre del permiso no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe un permiso con ese nombre.',
            
            'estado.required' => 'El estado del permiso es obligatorio.',
            'estado.in' => 'El estado del permiso debe ser "activo" o "inactivo".',
            
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
        ]);

        // Crear el permiso
        $permiso = Permiso::create($request->all());

        // Devolver el permiso creado con código de estado 201
        return response()->json($permiso, 201);
    }

    // Obtener un permiso por ID
    public function show(Permiso $permiso)
    {
        return response()->json($permiso);
    }

    // Actualizar un permiso
    public function update(Request $request, Permiso $permiso)
    {
        // Validar los datos con mensajes personalizados
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('permisos', 'nombre')->ignore($permiso->id), // Usa Rule aquí
            ],
            'estado' => 'required|in:activo,inactivo',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio.',
            'nombre.string' => 'El nombre del permiso debe ser una cadena de texto.',
            'nombre.min' => 'El nombre del permiso debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre del permiso no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe un permiso con ese nombre.',
            
            'estado.required' => 'El estado del permiso es obligatorio.',
            'estado.in' => 'El estado del permiso debe ser "activo" o "inactivo".',
            
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
        ]);

        // Actualizar el permiso
        $permiso->update($request->all());

        // Devolver el permiso actualizado
        return response()->json($permiso);
    }

    // Eliminar un permiso
    public function destroy(Permiso $permiso)
    {
        // Eliminar el permiso
        $permiso->delete();

        // Devolver una respuesta sin contenido con código de estado 204
        return response()->json(null, 204);
    }
}