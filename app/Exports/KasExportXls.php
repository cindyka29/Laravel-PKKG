<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KasExportXls implements WithEvents,FromView
{

    protected $date;
    protected $records;

    public function __construct($date, $records)
    {
        $this->date = $date;
        $this->records = $records;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $cellRange = "A1";
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center')->setHorizontal('center');

                $columns = ['A','B','C','D','E','F','G'];
                foreach ($columns as $column){
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(20);
                }
            }
        ];
    }

    public function view(): View
    {
        return view('export.kas.xls',[
            'date' => $this->date,
            'records' => $this->records
        ]);
    }
}
