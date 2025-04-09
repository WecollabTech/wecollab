<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Obtener todos los roles con sus permisos
    public function index(Request $request)
    {
        $query = Role::query();

        // Filtrar por nombre
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->input('nombre') . '%');
        }

        // Filtrar por estado
        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Paginación
        $perPage = $request->input('perPage', 5); // Valor por defecto es 5
        $roles = $query->with('permisos')->paginate($perPage);

        return response()->json($roles);
    }

    // Crear un nuevo rol con permisos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:255|unique:roles,nombre', // Validación: al menos 3 caracteres y único
            'estado' => 'required|in:activo,inactivo,pagopendiente', // Validación para el enum
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'nullable|array', // Validación para los permisos
            'permisos.*' => 'exists:permisos,id', // Validación para cada permiso
        ], [
            'nombre.required' => 'El nombre del rol es requerido.',
            'nombre.min' => 'El nombre del rol debe tener al menos 3 caracteres.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'permisos.*.exists' => 'Uno o más permisos no existen.',
        ]);

        // Crear el rol
        $role = Role::create($request->only(['nombre', 'estado', 'descripcion']));

        // Asociar permisos al rol
        if ($request->has('permisos')) {
            $permisosInvalidos = array_diff($request->permisos, Permiso::pluck('id')->toArray());
            if (!empty($permisosInvalidos)) {
                return response()->json([
                    'message' => 'Los siguientes permisos no existen: ' . implode(', ', $permisosInvalidos),
                ], 422);
            }
            $role->permisos()->attach($request->permisos);
        }

        // Devolver el rol creado con sus permisos
        return response()->json($role->load('permisos'), 201);
    }

    // Obtener un rol por ID con sus permisos
    public function show(Role $role)
    {
        return response()->json($role->load('permisos'));
    }

    // Actualizar un rol y sus permisos
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('roles', 'nombre')->ignore($role->id), // Usa Rule aquí
            ],
            'estado' => 'required|in:activo,inactivo',
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'nullable|array', // Validación para los permisos
            'permisos.*' => 'exists:permisos,id', // Validación para cada permiso
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre.min' => 'El nombre del rol debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre del rol no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'permisos.*.exists' => 'Uno o más permisos no existen.',
        ]);

        // Actualizar el rol
        $role->update($request->only(['nombre', 'estado', 'descripcion']));

        // Sincronizar permisos del rol
        if ($request->has('permisos')) {
            $permisosInvalidos = array_diff($request->permisos, Permiso::pluck('id')->toArray());
            if (!empty($permisosInvalidos)) {
                return response()->json([
                    'message' => 'Los siguientes permisos no existen: ' . implode(', ', $permisosInvalidos),
                ], 422);
            }
            $role->permisos()->sync($request->permisos);
        } else {
            $role->permisos()->detach(); // Si no se proporcionan permisos, eliminar todos los permisos asociados
        }

        // Devolver el rol actualizado con sus permisos
        return response()->json($role->load('permisos'));
    }

    // Eliminar un rol y sus relaciones con permisos
    public function destroy(Role $role)
    {
        // Eliminar las relaciones con permisos
        $role->permisos()->detach();

        // Eliminar el rol
        $role->delete();

        // Devolver una respuesta de éxito
        return response()->json(['message' => 'Rol eliminado correctamente'], 200);
    }
}