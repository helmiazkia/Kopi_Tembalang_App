<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function downloadQR(Request $request, Table $table)
    {
        $format = strtolower($request->query('format', 'png'));
        $format = $format === 'jpg' || $format === 'jpeg' ? 'jpg' : 'png';
        $filename = 'table-' . $table->id . '.' . $format;
        $mimeType = $format === 'png' ? 'image/png' : 'image/jpeg';

        [$qrImage, $format] = $this->generateQrImage($table->qr_code, $format);

        return response($qrImage, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function generateQrImage(string $text, string $format = 'png', int $moduleSize = 10, int $marginModules = 4): array
    {
        $qrCode = Encoder::encode($text, ErrorCorrectionLevel::L());
        $matrix = $qrCode->getMatrix();
        $matrixSize = $matrix->getWidth();
        $imageSize = ($matrixSize + ($marginModules * 2)) * $moduleSize;

        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefilledrectangle($image, 0, 0, $imageSize, $imageSize, $white);

        for ($y = 0; $y < $matrixSize; ++$y) {
            for ($x = 0; $x < $matrixSize; ++$x) {
                if ($matrix->get($x, $y) === 1) {
                    $x1 = ($x + $marginModules) * $moduleSize;
                    $y1 = ($y + $marginModules) * $moduleSize;
                    $x2 = $x1 + $moduleSize - 1;
                    $y2 = $y1 + $moduleSize - 1;
                    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $black);
                }
            }
        }

        ob_start();

        if ($format === 'jpg') {
            imagejpeg($image, null, 90);
        } else {
            imagepng($image);
        }

        $imageData = ob_get_clean();
        imagedestroy($image);

        return [$imageData, $format];
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
