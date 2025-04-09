<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TutorialController extends Controller
{


    public function index(Request $request): JsonResponse
    {
        $query = Tutorial::query();

        // Filtrado por búsqueda global
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('tipo_material', 'like', "%{$search}%")
                  ->orWhere('formato', 'like', "%{$search}%")
                  ->orWhere('alcance', 'like', "%{$search}%")
                  ->orWhere('estado', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('observacion', 'like', "%{$search}%");
            });
        }

        // Filtrado por tipo de material (opcional)
        if ($tipo_material = $request->input('tipo_material')) {
            $query->where('tipo_material', $tipo_material);
        }

        // Ordenamiento
        $sortBy = $request->input('sort_by', 'titulo');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->input('per_page', 3); // Por defecto, 10 por página
        $tutoriales = $query->paginate($perPage);

        return response()->json([
            'data' => $tutoriales->items(),
            'current_page' => $tutoriales->currentPage(),
            'last_page' => $tutoriales->lastPage(),
            'per_page' => $tutoriales->perPage(),
            'total' => $tutoriales->total(),
        ], 200);
    }














    public function store(Request $request): JsonResponse
    {
        // Validación de los datos
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo_material' => 'required|string|max:100',
            'formato' => 'required|string|max:50',
            'alcance' => 'required|string|max:50',
            'estado' => 'required|string|in:activo,inactivo',
            'url' => 'nullable|url',
            'observacion' => 'nullable|string',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Crear el tutorial
        $tutorial = Tutorial::create($validated);

        return response()->json(['data' => $tutorial], 201);
    }

    /**
     * Display the specified tutorial.
     *
     * @param  Tutorial  $tutorial
     * @return JsonResponse
     */
    public function show(Tutorial $tutorial): JsonResponse
    {
        return response()->json(['data' => $tutorial], 200);
    }


    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $tutorial = Tutorial::find($id);
                
                if (!$tutorial) {
                    Log::error("Tutorial no encontrado para actualizar", ['id' => $id]);
                    return response()->json(['message' => 'Tutorial no encontrado'], 404);
                }

                $validated = $request->validate([
                    'titulo' => 'required|string|max:255',
                    'descripcion' => 'required|string',
                    'tipo_material' => 'required|string|max:100',
                    'formato' => 'required|string|max:50',
                    'alcance' => 'required|string|max:50',
                    'estado' => 'required|string|in:activo,inactivo',
                    'url' => 'nullable|url',
                    'observacion' => 'nullable|string',
                    'subcategoria_id' => 'required|exists:subcategorias,id',
                    'user_id' => 'nullable|exists:users,id',
                ]);

                $tutorial->update($validated);
                
                Log::info("Tutorial actualizado correctamente", ['id' => $tutorial->id]);
                
                return response()->json($tutorial);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error al actualizar tutorial", [
                    'id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'message' => 'Error interno del servidor'
                ], 500);
            }
        });
    }



    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $tutorial = Tutorial::find($id);
                
                if (!$tutorial) {
                    Log::error("Tutorial no encontrado para eliminar", ['id' => $id]);
                    return response()->json(['message' => 'Tutorial no encontrado'], 404);
                }

                $tutorial->delete();
                
                Log::info("Tutorial eliminado correctamente", ['id' => $id]);
                
                return response()->json(null, 204);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error al eliminar tutorial", [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'message' => 'Error al eliminar el tutorial'
                ], 500);
            }
        });
    }





}