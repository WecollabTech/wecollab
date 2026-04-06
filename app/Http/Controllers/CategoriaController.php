<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;

class CategoriaController extends Controller
{
    // =========================
    // Listado paginado con filtros y búsqueda
    // =========================
    public function index(Request $request)
    {
        $query = Categoria::with('subcategorias');

        // Filtro por búsqueda (nombre o descripción)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
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

        // Paginación con número de items por página
        $perPage = $request->input('per_page', 10);
        $categorias = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    // =========================
    // Exportar categorías a CSV
    // =========================
    public function export(Request $request)
    {
        $query = Categoria::query();

        // Aplicar los mismos filtros que en el index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Ordenar por ID descendente para exportación
        $query->orderBy('id', 'desc');

        $categorias = $query->get();

        // Nombre del archivo con timestamp
        $filename = 'categorias_' . date('Y-m-d_His') . '.csv';

        // Crear CSV en memoria
        $handle = fopen('php://temp', 'w');

        // Agregar BOM para UTF-8 (soporte caracteres especiales)
        fputs($handle, "\xEF\xBB\xBF");

        // Cabeceras del CSV
        fputcsv($handle, [
            'ID',
            'Nombre',
            'Estado',
            'Descripción',
            'Fecha Creación',
            'Fecha Actualización'
        ]);

        // Datos
        foreach ($categorias as $categoria) {
            fputcsv($handle, [
                $categoria->id,
                $categoria->nombre,
                $categoria->estado === 'activo' ? 'Activo' : 'Inactivo',
                $categoria->descripcion ?? '',
                $categoria->created_at ? $categoria->created_at->format('d/m/Y H:i:s') : '',
                $categoria->updated_at ? $categoria->updated_at->format('d/m/Y H:i:s') : '',
            ]);
        }

        // Obtener el contenido del archivo temporal
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Crear respuesta de descarga
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    // =========================
    // Crear nueva categoría
    // =========================
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $categoria = Categoria::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente.',
            'data' => $categoria
        ], 201);
    }

    // =========================
    // Ver detalles de una categoría
    // =========================
    public function show(Categoria $categoria)
    {
        return response()->json([
            'success' => true,
            'data' => $categoria->load('subcategorias')
        ]);
    }

    // =========================
    // Actualizar categoría existente
    // =========================
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $this->validateData($request, $categoria->id);

        $categoria->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada correctamente.',
            'data' => $categoria
        ]);
    }

    // =========================
    // Eliminar categoría
    // =========================
    public function destroy(Categoria $categoria)
    {
        // Verificar si tiene subcategorías antes de eliminar
        if ($categoria->subcategorias()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas.'
            ], 422);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }

    // =========================
    // Listado simple de todas las categorías (id y nombre)
    // =========================

    public function all()
    {
        $categorias = Categoria::select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }
    public function lista()
    {
        $categorias = Categoria::select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    // =========================
    // Validación centralizada
    // =========================
    private function validateData(Request $request, $categoriaId = null)
    {
        $uniqueRule = $categoriaId
            ? Rule::unique('categorias', 'nombre')->ignore($categoriaId)
            : Rule::unique('categorias', 'nombre');

        return $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:50', $uniqueRule],
            'estado' => 'required|in:activo,inactivo',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
        ]);
    }



    /**
     * Obtener lista simple de categorías (para filtros)
     */



}