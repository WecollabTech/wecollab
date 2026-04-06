<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users (paginado) - PARA API
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['role', 'pais']);

            // Filtros
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('apellido', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('telefono', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('role_id') && !empty($request->role_id)) {
                $query->where('role_id', $request->role_id);
            }

            if ($request->has('company_id') && !empty($request->company_id)) {
                $query->where('company_id', $request->company_id);
            }

            $perPage = $request->get('per_page', 10);
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $users->items(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                    'links' => $users->linkCollection()->toArray(),
                ],
                'message' => 'Usuarios obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users (sin paginación) - PARA API
     */
    public function all()
    {
        try {
            $users = User::with(['role', 'pais'])->get();

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Todos los usuarios obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user - PARA API
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'apellido' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'status' => ['nullable', 'in:activo,inactivo'],
                'direccion' => ['nullable', 'string', 'max:255'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'role_id' => ['required', 'exists:roles,id'],
                'company_id' => ['nullable', 'string', 'max:255'], // Cambiado de 'numeric' a 'string'
                'pais_id' => ['required', 'exists:paises,id'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'] ?? 'activo',
                'direccion' => $validated['direccion'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'role_id' => $validated['role_id'],
                'company_id' => $validated['company_id'] ?? null, // Ahora acepta texto
                'pais_id' => $validated['pais_id'],
            ]);

            $user->load(['role', 'pais']);

            Log::info('Usuario creado', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario creado exitosamente'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Error de validación'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user - PARA API
     */
    public function show($id)
    {
        try {
            $user = User::with(['role', 'pais'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified user - PARA API
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'apellido' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'string', 'min:8'],
                'status' => ['nullable', 'in:activo,inactivo'],
                'direccion' => ['nullable', 'string', 'max:255'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'role_id' => ['required', 'exists:roles,id'],
                'company_id' => ['nullable', 'string', 'max:255'], // Cambiado de 'numeric' a 'string'
                'pais_id' => ['required', 'exists:paises,id'],
            ]);

            $user->name = $validated['name'];
            $user->apellido = $validated['apellido'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->status = $validated['status'] ?? $user->status;
            $user->direccion = $validated['direccion'] ?? null;
            $user->telefono = $validated['telefono'] ?? null;
            $user->role_id = $validated['role_id'];
            $user->company_id = $validated['company_id'] ?? null; // Ahora acepta texto
            $user->pais_id = $validated['pais_id'];

            $user->save();
            $user->load(['role', 'pais']);

            Log::info('Usuario actualizado', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Error de validación'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified user - PARA API
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 403);
            }

            $user->delete();

            Log::warning('Usuario eliminado', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status - PARA API
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes cambiar tu propio estado'
                ], 403);
            }

            $user->status = $user->status === 'activo' ? 'inactivo' : 'activo';
            $user->save();

            return response()->json([
                'success' => true,
                'data' => ['status' => $user->status],
                'message' => 'Estado actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get roles list - PARA API
     */
    public function getRoles()
    {
        try {
            $roles = Role::all();
            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get countries list - PARA API
     */
    public function getCountries()
    {
        try {
            // Cambiar a paises
            $paises = Pais::all();
            return response()->json([
                'success' => true,
                'data' => $paises
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener países: ' . $e->getMessage()
            ], 500);
        }
    }
}