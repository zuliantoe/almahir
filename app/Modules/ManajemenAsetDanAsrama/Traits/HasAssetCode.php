<?php
namespace App\Modules\ManajemenAsetDanAsrama\Traits;

use Illuminate\Support\Str;

trait HasAssetCode
{
    /**
     * Generate a abbreviated name (e.g., Meja Belajar -> MEB)
     */
    public function getAbbreviatedName(string $name): string
    {
        $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', $name);
        $words = explode(' ', $cleanName);
        
        if (count($words) >= 2) {
            // Ambil huruf pertama kata pertama, huruf pertama kata kedua, dan konsonan berikutnya
            $abbr = substr($words[0], 0, 1) . substr($words[1], 0, 2);
        } else {
            // Ambil 3 huruf pertama tanpa vokal jika memungkinkan
            $vowels = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', ' '];
            $onlyConsonants = str_replace($vowels, '', $cleanName);
            if (strlen($onlyConsonants) >= 3) {
                $abbr = substr($onlyConsonants, 0, 3);
            } else {
                $abbr = substr($cleanName, 0, 3);
            }
        }
        
        return strtoupper(str_pad($abbr, 3, 'X'));
    }

    /**
     * Universal Asset Code Generator
     * Format: [ABBR]-[DDMMYY]-[SEQ]
     */
    public function generateAssetCode(string $name, string $modelClass = \App\Modules\ManajemenAsetDanAsrama\Models\Aset::class, string $column = 'kode_aset', ?string $forcedDate = null): string
    {
        $abbr = $this->getAbbreviatedName($name);
        $date = $forcedDate ?? date('dmy');
        $prefix = "{$abbr}-{$date}-";

        // Cari kode terakhir yang punya prefix sama di model/tabel yang ditentukan
        $lastRecord = $modelClass::withTrashed()
            ->where($column, 'LIKE', "{$prefix}%")
            ->orderBy($column, 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRecord) {
            $lastCode = $lastRecord->$column;
            // Pastikan formatnya bener ada tanda strip
            if (str_contains($lastCode, '-')) {
                $parts = explode('-', $lastCode);
                $lastSeq = (int) end($parts);
                $nextNumber = $lastSeq + 1;
            }
        }

        // Safety loop: Pastikan bener-bener unik di tabel target (Pengajuan atau Master)
        do {
            $seq = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $code = "{$prefix}{$seq}";
            $exists = $modelClass::withTrashed()->where($column, $code)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $code;
    }
}
