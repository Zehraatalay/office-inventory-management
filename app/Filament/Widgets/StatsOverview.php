<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // toplam calisan
            Stat::make('Toplam Çalışan', Employee::count())
                ->description('Şirket bünyesindeki personel')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            // toplam asset
            Stat::make('Toplam Demirbaş', Asset::count())
                ->description('Kayıtlı tüm cihazlar')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('primary'),

            // bostaki assetler
            Stat::make('Boştaki Cihazlar', Asset::where('is_available', true)->count())
                ->description('Depoda bekleyenler')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('danger'),
        ];
    }
}