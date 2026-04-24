<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::latest()->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number'
        ]);

        $table = Table::create([
            'table_number' => $request->table_number,
            'status' => 'available'
        ]);

        $this->updateQr($table);

        return back()->with('success', 'Meja berhasil ditambahkan');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|max:50'
        ]);

        $table->update([
            'table_number' => $request->table_number
        ]);

        $this->updateQr($table);

        return back()->with('success', 'Meja berhasil diupdate');
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return back()->with('success', 'Meja berhasil dihapus');
    }

    // ================= QR =================

    public function downloadQR(Request $request, Table $table)
    {
        $format = in_array($request->query('format'), ['jpg','jpeg']) ? 'jpg' : 'png';

        [$qrImage] = $this->generateQrImage($table->qr_code, $format);

        return response($qrImage, 200, [
            'Content-Type' => $format === 'png' ? 'image/png' : 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="table-'.$table->id.'.'.$format.'"',
        ]);
    }

    private function updateQr(Table $table)
    {
        // 🔥 gunakan route lebih clean
        $table->qr_code = url('/menu/' . $table->id);
        $table->save();
    }

    private function generateQrImage(string $text, string $format = 'png'): array
    {
        $qrCode = Encoder::encode($text, ErrorCorrectionLevel::L());
        $matrix = $qrCode->getMatrix();

        $size = 10;
        $margin = 4;

        $matrixSize = $matrix->getWidth();
        $imageSize = ($matrixSize + ($margin * 2)) * $size;

        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $matrixSize; $y++) {
            for ($x = 0; $x < $matrixSize; $x++) {
                if ($matrix->get($x, $y)) {
                    imagefilledrectangle(
                        $image,
                        ($x + $margin) * $size,
                        ($y + $margin) * $size,
                        ($x + $margin + 1) * $size,
                        ($y + $margin + 1) * $size,
                        $black
                    );
                }
            }
        }

        ob_start();

        $format === 'jpg'
            ? imagejpeg($image, null, 90)
            : imagepng($image);

        $output = ob_get_clean();
        imagedestroy($image);

        return [$output];
    }
}