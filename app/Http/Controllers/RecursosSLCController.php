<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RecursosSLCController extends Controller
{
    /**
     * Mostrar lista de recursos (API) - Para Index.vue
     */
    public function indexApi(Request $request): JsonResponse
    {
        try {
            $query = Tutorial::query();

            // Filtro por tipo
            if ($request->has('tipo') && $request->tipo !== 'todos') {
                $query->where('tipo_material', $request->tipo);
            }

            // Filtro por múltiples tipos
            if ($request->has('tipos') && $request->tipos) {
                $tipos = explode(',', $request->tipos);
                $query->whereIn('tipo_material', $tipos);
            }

            // Búsqueda por título o descripción
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%")
                        ->orWhere('descripcion', 'LIKE', "%{$search}%")
                        ->orWhere('formato', 'LIKE', "%{$search}%");
                });
            }

            // Ordenamiento
            switch ($request->get('sort', 'recent')) {
                case 'title':
                    $query->orderBy('titulo', 'asc');
                    break;
                case 'format':
                    $query->orderBy('formato', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $recursos = $query->paginate(15);

            return response()->json($recursos);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los recursos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un recurso específico (API) - Para Redirect.vue y Show.vue
     */
    public function showApi($id): JsonResponse
    {
        try {
            $recurso = Tutorial::findOrFail($id);
            return response()->json($recurso);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Recurso no encontrado',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mostrar lista de recursos (Vista Inertia) - Para Index.vue
     */
    public function index(Request $request)
    {
        return Inertia::render('Recursos/Index', [
            'tipo' => $request->get('tipo', 'todos')
        ]);
    }

    /**
     * Mostrar un recurso específico (Vista Inertia) - Para Show.vue
     */
    public function show($id)
    {
        try {
            $recurso = Tutorial::findOrFail($id);

            return Inertia::render('Recursos/Show', [
                'id' => (int) $id,
                'recurso' => $recurso
            ]);
        } catch (\Exception $e) {
            abort(404, 'Recurso no encontrado');
        }
    }

    /**
     * Mostrar formulario para crear recurso (Vista Inertia)
     */
    public function create($tipo)
    {
        return Inertia::render('Recursos/Create', [
            'tipo' => $tipo
        ]);
    }

    /**
     * Guardar nuevo recurso (API)
     */
    public function storeApi(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'url' => 'nullable|url',
                'formato' => 'nullable|string|max:50',
                'alcance' => 'nullable|string|max:50',
                'tipo_material' => 'required|string|max:50',
                'estado' => 'required|in:activo,inactivo,borrador',
            ]);

            $recurso = Tutorial::create($validated);

            return response()->json([
                'message' => 'Recurso creado correctamente',
                'data' => $recurso
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el recurso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar nuevo recurso (Vista Inertia)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'url' => 'nullable|url',
                'formato' => 'nullable|string|max:50',
                'alcance' => 'nullable|string|max:50',
                'tipo_material' => 'required|string|max:50',
                'estado' => 'required|in:activo,inactivo,borrador',
            ]);

            Tutorial::create($validated);

            return redirect()->route('recursos.index')->with('success', 'Recurso creado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el recurso: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar recurso (Vista Inertia)
     */
    public function edit($id)
    {
        try {
            $recurso = Tutorial::findOrFail($id);

            return Inertia::render('Recursos/Edit', [
                'recurso' => $recurso
            ]);
        } catch (\Exception $e) {
            abort(404, 'Recurso no encontrado');
        }
    }

    /**
     * Actualizar recurso (API)
     */
    public function updateApi(Request $request, $id): JsonResponse
    {
        try {
            $recurso = Tutorial::findOrFail($id);

            $validated = $request->validate([
                'titulo' => 'sometimes|string|max:255',
                'descripcion' => 'nullable|string',
                'url' => 'nullable|url',
                'formato' => 'nullable|string|max:50',
                'alcance' => 'nullable|string|max:50',
                'tipo_material' => 'sometimes|string|max:50',
                'estado' => 'sometimes|in:activo,inactivo,borrador',
            ]);

            $recurso->update($validated);

            return response()->json([
                'message' => 'Recurso actualizado correctamente',
                'data' => $recurso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el recurso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar recurso (Vista Inertia)
     */
    public function update(Request $request, $id)
    {
        try {
            $recurso = Tutorial::findOrFail($id);

            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'url' => 'nullable|url',
                'formato' => 'nullable|string|max:50',
                'alcance' => 'nullable|string|max:50',
                'tipo_material' => 'required|string|max:50',
                'estado' => 'required|in:activo,inactivo,borrador',
            ]);

            $recurso->update($validated);

            return redirect()->route('recursos.index')->with('success', 'Recurso actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el recurso: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar recurso (API)
     */
    public function destroyApi($id): JsonResponse
    {
        try {
            $recurso = Tutorial::findOrFail($id);
            $recurso->delete();

            return response()->json([
                'message' => 'Recurso eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el recurso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar recurso (Vista Inertia)
     */
    public function destroy($id)
    {
        try {
            $recurso = Tutorial::findOrFail($id);
            $recurso->delete();

            return redirect()->route('recursos.index')->with('success', 'Recurso eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el recurso: ' . $e->getMessage());
        }
    }
}