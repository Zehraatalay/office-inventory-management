<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Proje Durumları',
                    'data' => [5, 3, 8],
                    'backgroundColor' => ['#10b981', '#6b7280', '#f97316'],
                    'hoverOffset' => 4, // uzerine gelince dilim buyuyor
                ],
            ],
            'labels' => ['Tamamlanan', 'Bekleyen', 'Devam Eden'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
