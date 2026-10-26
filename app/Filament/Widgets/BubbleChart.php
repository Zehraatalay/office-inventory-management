<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BubbleChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Proje Analizi',
                    'data' => [
                        // x: yatay eksen, y: dikey eksen, r: yaricap (balon buyuklugu)
                        ['x' => 10, 'y' => 20, 'r' => 15], // Proje A
                        ['x' => 15, 'y' => 10, 'r' => 10], // Proje B
                        ['x' => 25, 'y' => 30, 'r' => 25], // Proje C (uzun süren)
                    ],
                    'backgroundColor' => '#8b5cf6', // mor
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
