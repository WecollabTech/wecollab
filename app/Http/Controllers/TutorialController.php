<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TutorialController extends Controller
{
    // =========================================================================
    // METODOS AUXILIARES
    // =========================================================================

    private function obtenerRol($user): string
    {
        if (!$user)
            return '';

        if (is_string($user->role)) {
            return trim($user->role);
        }

        if (is_object($user->role)) {
            return trim($user->role->nombre ?? $user->role->name ?? '');
        }

        if (is_array($user->role)) {
            return trim($user->role['nombre'] ?? $user->role['name'] ?? '');
        }

        return '';
    }

    private function esAdministrador($user): bool
    {
        $rolUsuario = $this->obtenerRol($user);
        $rolesAdmin = ['Superadmin We collab', 'Admin We collab'];
        return in_array($rolUsuario, $rolesAdmin, true);
    }

    private function tieneAcceso($tutorial, $user = null): bool
    {
        if (empty($tutorial->alcance) || trim($tutorial->alcance) === '') {
            return true;
        }

        if (!$user)
            return false;

        $rolUsuario = $this->obtenerRol($user);
        if (empty($rolUsuario))
            return false;

        if ($rolUsuario === 'Superadmin We collab') {
            return true;
        }

        return $rolUsuario === $tutorial->alcance;
    }

    // =========================================================================
    // INDEX - LISTAR TUTORIALES
    // =========================================================================

    public function index(Request $request): JsonResponse
    {
        $query = Tutorial::with(['categoria', 'subcategoria'])->where('estado', 'activo');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($tipo = $request->input('tipo_material')) {
            $query->where('tipo_material', $tipo);
        }

        if ($categoriaId = $request->input('categorias_id')) {
            $query->where('categorias_id', $categoriaId);
        }

        if ($subcategoriaId = $request->input('subcategoria_id')) {
            $query->where('subcategoria_id', $subcategoriaId);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $perPage = $request->input('per_page', 12);

        $tutoriales = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tutoriales->items(),
            'current_page' => $tutoriales->currentPage(),
            'last_page' => $tutoriales->lastPage(),
            'per_page' => $tutoriales->perPage(),
            'total' => $tutoriales->total(),
        ], 200);
    }

    // =========================================================================
    // STORE - CREAR TUTORIAL (CON MENSAJES PERSONALIZADOS)
    // =========================================================================

    public function store(Request $request): JsonResponse
    {
        try {
            // ✅ Mensajes de validación personalizados
            $messages = [
                'titulo.required' => 'El título es obligatorio.',
                'titulo.min' => 'El título debe tener al menos 3 caracteres.',
                'titulo.max' => 'El título no puede tener más de 100 caracteres.',
                'titulo.unique' => 'Ya existe un tutorial con este título.',
                'tipo_material.required' => 'Debe seleccionar un tipo de material.',
                'tipo_material.in' => 'El tipo de material seleccionado no es válido.',
                'formato.required' => 'Debe seleccionar un formato.',
                'formato.in' => 'El formato seleccionado no es válido.',
                'alcance.required' => 'Debe seleccionar un alcance.',
                'alcance.in' => 'El alcance seleccionado no es válido.',
                'estado.required' => 'Debe seleccionar un estado.',
                'estado.in' => 'El estado seleccionado no es válido.',
                'url.url' => 'La URL ingresada no es válida.',
                'url.max' => 'La URL no puede tener más de 500 caracteres.',
                'categorias_id.exists' => 'La categoría seleccionada no existe.',
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe.',
                'user_id.exists' => 'El usuario seleccionado no existe.',
            ];

            // ✅ Validación con min:3
            $validated = $request->validate([
                'titulo' => 'required|string|min:3|max:100|unique:tutoriales,titulo',
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico,avisos importantes',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Público,Prospecto,usuario',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'categorias_id' => 'nullable|exists:categorias,id',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
                'user_id' => 'nullable|exists:users,id',
            ], $messages);

            // ✅ Log para depurar (opcional, quitar en producción)
            Log::info('Datos validados:', $validated);

            // Convertir vacíos a null
            $validated['categorias_id'] = empty($validated['categorias_id']) ? null : $validated['categorias_id'];
            $validated['subcategoria_id'] = empty($validated['subcategoria_id']) ? null : $validated['subcategoria_id'];

            $tutorial = Tutorial::create($validated);
            $tutorial->load(['categoria', 'subcategoria']);

            return response()->json([
                'success' => true,
                'message' => 'Tutorial creado exitosamente',
                'data' => $tutorial
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ✅ Log para depurar errores de validación
            Log::warning('Error de validación:', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al crear tutorial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // SHOW - MOSTRAR TUTORIAL
    // =========================================================================

    public function show(Tutorial $tutorial, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($tutorial->estado !== 'activo') {
            return response()->json(['success' => false, 'message' => 'Tutorial no disponible'], 404);
        }

        if (!$this->tieneAcceso($tutorial, $user)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para ver este contenido'], 403);
        }

        $tutorial->load(['categoria', 'subcategoria', 'user']);

        return response()->json(['success' => true, 'data' => $tutorial], 200);
    }

    // =========================================================================
    // UPDATE - ACTUALIZAR TUTORIAL (CON MENSAJES PERSONALIZADOS)
    // =========================================================================

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tutorial = Tutorial::find($id);
            if (!$tutorial) {
                return response()->json(['success' => false, 'message' => 'Tutorial no encontrado'], 404);
            }

            // ✅ Mensajes de validación personalizados en español
            $messages = [
                'titulo.required' => 'El título es obligatorio.',
                'titulo.max' => 'El título no puede tener más de 100 caracteres.',
                'titulo.unique' => 'Ya existe otro tutorial con este título.',
                'tipo_material.required' => 'Debe seleccionar un tipo de material.',
                'tipo_material.in' => 'El tipo de material seleccionado no es válido.',
                'formato.required' => 'Debe seleccionar un formato.',
                'formato.in' => 'El formato seleccionado no es válido.',
                'alcance.required' => 'Debe seleccionar un alcance.',
                'alcance.in' => 'El alcance seleccionado no es válido.',
                'estado.required' => 'Debe seleccionar un estado.',
                'estado.in' => 'El estado seleccionado no es válido.',
                'url.url' => 'La URL ingresada no es válida.',
                'url.max' => 'La URL no puede tener más de 500 caracteres.',
                'categorias_id.exists' => 'La categoría seleccionada no existe.',
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe.',
            ];

            $validated = $request->validate([
                'titulo' => 'required|string|max:100|unique:tutoriales,titulo,' . $id,
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico,avisos importantes',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Público,Prospecto,usuario',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'categorias_id' => 'nullable|exists:categorias,id',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
            ], $messages);  // ✅ Aplicar mensajes personalizados

            $validated['categorias_id'] = empty($validated['categorias_id']) ? null : $validated['categorias_id'];
            $validated['subcategoria_id'] = empty($validated['subcategoria_id']) ? null : $validated['subcategoria_id'];

            $tutorial->update($validated);
            $tutorial->load(['categoria', 'subcategoria']);

            return response()->json([
                'success' => true,
                'message' => 'Tutorial actualizado exitosamente',
                'data' => $tutorial
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar tutorial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    // =========================================================================
    // DESTROY - ELIMINAR TUTORIAL
    // =========================================================================

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        if (!$this->esAdministrador($user)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar'], 403);
        }

        try {
            $tutorial = Tutorial::find($id);
            if (!$tutorial) {
                return response()->json(['success' => false, 'message' => 'Tutorial no encontrado'], 404);
            }

            $tutorial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tutorial eliminado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar tutorial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tutorial'
            ], 500);
        }
    }

    // =========================================================================
    // REGISTRAR VISTA
    // =========================================================================

    public function registrarVista(Request $request, $id): JsonResponse
    {
        try {
            $tutorial = Tutorial::find($id);
            if (!$tutorial) {
                return response()->json(['success' => false, 'message' => 'Tutorial no encontrado'], 404);
            }

            $tutorial->increment('vistas');

            return response()->json(['success' => true, 'message' => 'Vista registrada'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar vista'], 500);
        }
    }

    // =========================================================================
    // CATEGORIAS Y SUBCATEGORIAS
    // =========================================================================

    public function getCategorias(): JsonResponse
    {
        $categorias = Categoria::where('estado', 'activo')->get();
        return response()->json(['success' => true, 'data' => $categorias], 200);
    }

    public function getSubcategorias(Request $request): JsonResponse
    {
        $query = Subcategoria::where('estado', 'activo');

        if ($categoriaId = $request->input('categorias_id')) {
            $query->where('categorias_id', $categoriaId);
        }

        $subcategorias = $query->get();
        return response()->json(['success' => true, 'data' => $subcategorias], 200);
    }
}