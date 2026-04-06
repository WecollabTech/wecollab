<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubcategoriaController extends Controller
{
    // =========================
    // Listado paginado con filtros
    // =========================
    public function index(Request $request)
    {
        $query = Subcategoria::with('categoria');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'LIKE', "%{$search}%");
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Ordenamiento
        if ($request->filled('orden')) {
            switch ($request->orden) {
                case 'id_asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'id_desc':
                    $query->orderBy('id', 'desc');
                    break;
                case 'nombre_asc':
                    $query->orderBy('nombre', 'asc');
                    break;
                case 'nombre_desc':
                    $query->orderBy('nombre', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = $request->input('per_page', 10);
        $subcategorias = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $subcategorias
        ]);
    }

    // =========================
    // Exportar a CSV
    // =========================
    public function export(Request $request)
    {
        $query = Subcategoria::with('categoria');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'LIKE', "%{$search}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $subcategorias = $query->get();

        $filename = 'subcategorias_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['ID', 'Nombre', 'Categoría', 'Estado', 'Descripción', 'Fecha Creación', 'Fecha Actualización']);

        foreach ($subcategorias as $subcategoria) {
            fputcsv($handle, [
                $subcategoria->id,
                $subcategoria->nombre,
                $subcategoria->categoria?->nombre ?? 'N/A',
                $subcategoria->estado === 'activo' ? 'Activo' : 'Inactivo',
                $subcategoria->descripcion ?? '',
                $subcategoria->created_at?->format('d/m/Y H:i:s') ?? '',
                $subcategoria->updated_at?->format('d/m/Y H:i:s') ?? '',
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response()->make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // =========================
    // Crear nueva subcategoría
    // =========================
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $subcategoria = Subcategoria::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subcategoría creada correctamente.',
            'data' => $subcategoria->load('categoria')
        ], 201);
    }

    // =========================
    // Ver detalle
    // =========================
    public function show($id)
    {
        $subcategoria = Subcategoria::with('categoria')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $subcategoria
        ]);
    }

    // =========================
    // Actualizar
    // =========================
    public function update(Request $request, Subcategoria $subcategoria)
    {
        $validated = $this->validateData($request, $subcategoria->id);

        $subcategoria->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subcategoría actualizada correctamente.',
            'data' => $subcategoria->load('categoria')
        ]);
    }

    // =========================
    // Eliminar
    // =========================
    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subcategoría eliminada correctamente.'
        ]);
    }

    // =========================
    // Select simple de subcategorías
    // =========================
    public function all()
    {
        $subcategorias = Subcategoria::select('id', 'nombre', 'categoria_id')
            ->with('categoria:id,nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subcategorias
        ]);
    }

    // =========================
    // Validación centralizada
    // =========================
    private function validateData(Request $request, $subcategoriaId = null)
    {
        $uniqueRule = $subcategoriaId
            ? Rule::unique('subcategorias', 'nombre')->ignore($subcategoriaId)
            : Rule::unique('subcategorias', 'nombre');

        return $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:50', $uniqueRule],
            'estado' => 'required|in:activo,inactivo',
            'descripcion' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
        ], [
            'nombre.required' => 'El nombre de la subcategoría es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe una subcategoría con ese nombre.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ]);
    }
}