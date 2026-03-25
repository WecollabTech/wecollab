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
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:100|unique:tutoriales,titulo',
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Publico,Prospecto',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
                'user_id' => 'nullable|exists:users,id',
            ], [
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe',
                'alcance.in' => 'El valor de alcance no es válido',
                'titulo.unique' => 'Ya existe un tutorial con este título',
            ]);

            // Convertir strings vacíos a null para foreign keys
            if (empty($validated['subcategoria_id'])) {
                $validated['subcategoria_id'] = null;
            }



            $tutorial = Tutorial::create($validated);

            return response()->json([
                'message' => 'Tutorial creado exitosamente',
                'data' => $tutorial
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear tutorial: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }













    public function show(Tutorial $tutorial): JsonResponse
    {
        if ($tutorial->estado !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Tutorial no disponible'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tutorial
        ], 200);
    }





    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tutorial = Tutorial::find($id);

            if (!$tutorial) {
                return response()->json([
                    'message' => 'Tutorial no encontrado'
                ], 404);
            }

            $validated = $request->validate([
                'titulo' => 'required|string|max:100|unique:tutoriales,titulo,' . $tutorial->id,
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Publico,Prospecto',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
            ], [
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe',
                'alcance.in' => 'El valor de alcance no es válido',
                'titulo.unique' => 'Ya existe un tutorial con este título',
            ]);

            // ✅ Convertir vacío a null
            if (empty($validated['subcategoria_id'])) {
                $validated['subcategoria_id'] = null;
            }

            $tutorial->update($validated);

            return response()->json([
                'message' => 'Tutorial actualizado exitosamente',
                'data' => $tutorial
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            Log::error('Error al actualizar tutorial: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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