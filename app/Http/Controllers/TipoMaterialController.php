<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class TipoMaterialController extends Controller
{
    /**
     * Lista paginada de tipos de materiales
     */
    public function index(Request $request): JsonResponse
    {
        $query = TipoMaterial::query();

        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $tiposMateriales = $query->orderBy('nombre')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales,
            'message' => 'Lista de tipos de materiales obtenida exitosamente'
        ]);
    }

    /**
     * Obtener todos los tipos de materiales (sin paginación)
     */
    public function all(): JsonResponse
    {
        $tiposMateriales = TipoMaterial::orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales,
            'message' => 'Todos los tipos de materiales obtenidos exitosamente'
        ]);
    }

    /**
     * Lista simplificada para selects
     */
    public function lista(): JsonResponse
    {
        $tiposMateriales = TipoMaterial::orderBy('nombre')->get(['id', 'nombre']);

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales,
            'message' => 'Lista simplificada obtenida exitosamente'
        ]);
    }

    /**
     * Crear nuevo tipo de material
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_materiales,nombre',
            'descripcion' => 'nullable|string'
        ]);

        $tipoMaterial = TipoMaterial::create($validated);

        return response()->json([
            'success' => true,
            'data' => $tipoMaterial,
            'message' => 'Tipo de material creado exitosamente'
        ], 201);
    }

    /**
     * Mostrar un tipo de material específico
     */
    public function show(TipoMaterial $tipoMaterial): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $tipoMaterial,
            'message' => 'Tipo de material obtenido exitosamente'
        ]);
    }

    /**
     * Actualizar tipo de material
     */
    public function update(Request $request, TipoMaterial $tipoMaterial): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tipos_materiales', 'nombre')->ignore($tipoMaterial->id)
            ],
            'descripcion' => 'nullable|string'
        ]);

        $tipoMaterial->update($validated);

        return response()->json([
            'success' => true,
            'data' => $tipoMaterial,
            'message' => 'Tipo de material actualizado exitosamente'
        ]);
    }

    /**
     * Eliminar tipo de material
     */
    public function destroy(TipoMaterial $tipoMaterial): JsonResponse
    {
        $tipoMaterial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de material eliminado exitosamente'
        ]);
    }
}