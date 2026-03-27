<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;

class RecursosSLCController extends Controller
{
    // 🔍 LISTAR (con filtro por tipo)
    // public function index(Request $request)
    // {
    //     $query = Tutorial::query();

    //     // Filtro por tipo (video, manual, etc)
    //     if ($request->tipo_material) {
    //         $query->where('tipo_material', $request->tipo_material);
    //     }

    //     // Filtro opcional por búsqueda
    //     if ($request->search) {
    //         $query->where('titulo', 'like', '%' . $request->search . '%');
    //     }

    //     return response()->json(
    //         $query->latest()->paginate(10)
    //     );
    // }



    public function index(Request $request)
    {
        $query = Tutorial::query();

        // SOPORTE PARA MÚLTIPLES FILTROS
        // Puede recibir: tipos=video,manual,guia o tipos[]=video&tipos[]=manual
        if ($request->has('tipos')) {
            $tipos = is_array($request->tipos)
                ? $request->tipos
                : explode(',', $request->tipos);

            $query->whereIn('tipo_material', $tipos);
        }
        // Compatibilidad con versión anterior (un solo tipo)
        elseif ($request->tipo_material) {
            $query->where('tipo_material', $request->tipo_material);
        }

        // Filtro por búsqueda
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenamiento
        switch ($request->sort) {
            case 'title':
                $query->orderBy('titulo');
                break;
            case 'format':
                $query->orderBy('formato');
                break;
            default:
                $query->latest();
                break;
        }

        return response()->json(
            $query->paginate(12)
        );
    }










    // ➕ CREAR
    public function store(Request $request)
    {
        $recurso = Tutorial::create($request->all());
        return response()->json($recurso);
    }

    // ✏️ ACTUALIZAR
    public function update(Request $request, $id)
    {
        $recurso = Tutorial::findOrFail($id);
        $recurso->update($request->all());

        return response()->json($recurso);
    }

    // ❌ ELIMINAR
    public function destroy($id)
    {
        Tutorial::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Recurso eliminado'
        ]);
    }
}