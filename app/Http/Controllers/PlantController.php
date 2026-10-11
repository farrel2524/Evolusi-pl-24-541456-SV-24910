<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantd;

class PlantController extends Controller
{
    public function index(Request $request)
    {
        $query = Plantd::query();

        if ($request->filled('pemilik') && $request->pemilik !== 'semua') {
            $query->where('Pemilik_lahan', $request->pemilik);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where('Pemilik_lahan', 'like', "%{$keyword}%");
        }

        $plantd = $query->orderBy('id', 'asc')->get();

        $latest5 = Plantd::orderBy('created_at', 'desc')->take(5)->get();

        $totalCount = Plantd::count();
        $totalLuas  = Plantd::sum('Luas_lahan');
        $maxLuas    = Plantd::max('Luas_lahan');
        $minLuas    = Plantd::min('Luas_lahan');
        $pemilikList = Plantd::select('Pemilik_lahan')->distinct()->orderBy('Pemilik_lahan')->pluck('Pemilik_lahan');

        return view('abt', compact('plantd','latest5','totalCount','totalLuas','maxLuas','minLuas','pemilikList'));
    }

    public function create()
    {
        return view('addu');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Pemilik_lahan' => 'required|string|max:255',
            'Luas_lahan'    => 'nullable|numeric',
        ]);

        Plantd::create($data);

        return redirect()->route('plants.index')->with('success', 'Data berhasil dibuat.');
    }


    public function edit($id)
    {
        $plant = Plantd::findOrFail($id);
        return view('addu', compact('plant'));
    }


    public function update(Request $request, $id)
    {
        $plant = Plantd::findOrFail($id);

        $data = $request->validate([
            'Pemilik_lahan' => 'required|string|max:255',
            'Luas_lahan'    => 'nullable|numeric',
        ]);

        $plant->update($data);

        return redirect()->route('plants.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $plantd = Plantd::find($id);

        if ($plantd) {
            $plantd->delete();
            return redirect()->route('plants.index')->with('success', 'Data berhasil dihapus.');
        }

        return redirect()->route('plants.index')->with('error', 'Data tidak ditemukan.');
    }
}
