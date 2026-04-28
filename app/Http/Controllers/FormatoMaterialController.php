<?php
// app/Http/Controllers/FormatoMaterialController.php

namespace App\Http\Controllers;

use App\Models\FormatoMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class FormatoMaterialController extends Controller
{
    /**
     * Lista paginada de formatos de materiales
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = FormatoMaterial::query();

            // Búsqueda por nombre
            if ($request->has('search') && $request->search) {
                $query->where('nombre', 'like', '%' . $request->search . '%');
            }

            $formatosMateriales = $query->orderBy('nombre', 'asc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $formatosMateriales,
                'message' => 'Lista de formatos de materiales obtenida exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los formatos (sin paginación)
     */
    public function all(): JsonResponse
    {
        try {
            $formatosMateriales = FormatoMaterial::orderBy('nombre', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $formatosMateriales,
                'message' => 'Todos los formatos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista simplificada para selects
     */
    public function lista(): JsonResponse
    {
        try {
            $formatosMateriales = FormatoMaterial::orderBy('nombre', 'asc')
                ->get(['id', 'nombre']);

            return response()->json([
                'success' => true,
                'data' => $formatosMateriales,
                'message' => 'Lista simplificada obtenida exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo formato de material
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100|unique:formatos_materiales,nombre',
                'descripcion' => 'nullable|string'
            ]);

            $formatoMaterial = FormatoMaterial::create($validated);

            return response()->json([
                'success' => true,
                'data' => $formatoMaterial,
                'message' => 'Formato de material creado exitosamente'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el formato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un formato específico
     */
    public function show(FormatoMaterial $formatoMaterial): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $formatoMaterial,
                'message' => 'Formato obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el formato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar formato
     */
    public function update(Request $request, FormatoMaterial $formatoMaterial): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('formatos_materiales', 'nombre')->ignore($formatoMaterial->id)
                ],
                'descripcion' => 'nullable|string'
            ]);

            $formatoMaterial->update($validated);

            return response()->json([
                'success' => true,
                'data' => $formatoMaterial,
                'message' => 'Formato actualizado exitosamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el formato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar formato
     */
    public function destroy(FormatoMaterial $formatoMaterial): JsonResponse
    {
        try {
            $formatoMaterial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formato de material eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el formato: ' . $e->getMessage()
            ], 500);
        }
    }
}