<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PieChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Cihazlar',
                    'data' => [30, 20, 10], // dilim buyuklukleri
                    'backgroundColor' => [ // her dilim ayri renk
                        '#f59e0b', // Sarı (Laptop)
                        '#3b82f6', // Mavi (Monitör)
                        '#ef4444', // Kırmızı (Telefon)
                    ],
                ],
            ],
            'labels' => ['Laptop', 'Monitör', 'Telefon'],
        ];
    }
    protected function getType(): string
    {
        return 'pie';
    }
}
