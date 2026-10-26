<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use Filament\Pages\Page;

class ZimmetDetay extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.zimmet-detay';

    // menude gizle (sol menude yer almiyor)
    protected static bool $shouldRegisterNavigation = false;

    // url yapisi (id alabilir)
    // /admin/zimmet-detay/1
    protected static ?string $slug = 'zimmet-detay/{record}';

    // datayi tutacak degisken
    public Employee $calisan;

    // page acilinca calisan fonksiyon
    public function mount($record)
    {
        $this->calisan = Employee::with('assets')->findOrFail($record);
    }

    // title
    public function getTitle(): string
    {
        if (! isset($this->calisan)) {
        return 'Zimmet Detayı';
    }

    return $this->calisan->first_name . ' ' . $this->calisan->last_name . ' - Zimmet Detayı';
    }
}