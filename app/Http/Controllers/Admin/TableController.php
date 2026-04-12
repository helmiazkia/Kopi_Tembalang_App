<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::latest()->get();

        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'table_number' => 'required|string|max:50|unique:tables,table_number'
            ]);

            $table = Table::create([
                'table_number' => $request->table_number,
                'status' => 'available'
            ]);

            $table->qr_code = url('/menu?table=' . $table->id);
            $table->save();

            return back()->with('success', 'Meja berhasil ditambahkan');
        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        try {

            $validatedData = $request->validate([
                'table_number' => 'required|string|max:50'
            ]);

            $table->update($validatedData);
            $table->qr_code = url('/menu?table=' . $table->id);
            $table->save();

            return back()->with('success', 'Meja berhasil diupdate');
        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function downloadQR(Table $table)
    {
        $qr = QrCode::format('png')
            ->size(300)
            ->generate($table->qr_code);

        return response($qr)
            ->header('Content-Type', 'image/png')
            ->header(
                'Content-Disposition',
                'attachment; filename="table-' . $table->id . '.png"'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        try {

            $table->delete();

            return back()->with('success', 'Meja berhasil dihapus');
        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
