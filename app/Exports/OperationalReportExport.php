<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class OperationalReportExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $rows,
        private readonly string $title = 'Laporan Pendaftar',
    ) {}

    public function collection(): Collection
    {
        return $this->rows->values()->map(function (array $row, int $index) {
            return [
                $index + 1,
                $row['name'] ?? '-',
                $this->formatGender($row['gender'] ?? null),
                $row['phone'] ?? '-',
                $row['email'] ?? '-',
                $row['umroh_answer'] ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nomor', 'Nama', 'Gender', 'No WA', 'Email', 'Tgl Umroh'];
    }

    public function title(): string
    {
        return $this->title;
    }

    private function formatGender(?string $gender): string
    {
        if ($gender === 'ikhwan') {
            return 'Ikhwan';
        }

        if ($gender === 'akhwat') {
            return 'Akhwat';
        }

        return $gender ? ucfirst($gender) : '-';
    }
}
