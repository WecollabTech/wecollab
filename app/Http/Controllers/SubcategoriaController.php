<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubcategoriaController extends Controller
{
    public function index()
    {
        $subcategorias = Subcategoria::select('id', 'nombre')
            ->whereNotNull('id')
            ->whereNotNull('nombre')
            ->get();
    
        return response()->json([
            'data' => $subcategorias,
            'message' => 'Subcategorías obtenidas correctamente'
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:50|unique:subcategorias,nombre',
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

        $subcategoria = Subcategoria::create($request->all());
        return response()->json($subcategoria->load('categoria'), 201);
    }

    public function show(Subcategoria $subcategoria)
    {
        return response()->json($subcategoria->load('categoria'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('subcategorias', 'nombre')->ignore($subcategoria->id),
            ],
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

        $subcategoria->update($request->all());
        return response()->json($subcategoria->load('categoria'));
    }

    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();
        return response()->json(null, 204);
    }
}