<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Generic panel export: wraps any collection/array of rows with headings.
 * Hostinger-safe: pure PHP, no binary deps. Used for CSV/XLSX downloads
 * from the new report methods via ReportController::exportCsvOrExcel().
 */
class PanelExport implements FromCollection, WithHeadings
{
    /**
     * @param  \Illuminate\Support\Collection|array  $rows
     * @param  array  $headings
     */
    public function __construct(
        protected $rows,
        protected array $headings = []
    ) {}

    public function collection(): Collection
    {
        if ($this->rows instanceof Collection) {
            return $this->rows->values();
        }

        return collect($this->rows)->values();
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
