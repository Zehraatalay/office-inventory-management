<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BarChart extends ChartWidget
{
    // grafik title
    protected static ?string $heading = 'Departman Personel Dağılımı';

    // grafik tam genislikte oluyor
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Personel Sayısı',
                    'data' => [12, 19, 3, 5, 2], // static datalar

                    // column colors
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)', // Kırmızımsı
                        'rgba(54, 162, 235, 0.2)', // Mavimsi
                        'rgba(255, 206, 86, 0.2)', // Sarımsı
                        'rgba(75, 192, 192, 0.2)', // Yeşilimsi
                        'rgba(153, 102, 255, 0.2)', // Morumsu
                    ],
                    // kenarlik renkleri (biraz daha koyu)
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    'borderWidth' => 1, //kenarlik kalinligi
                ],
            ],
            // alt kisimdaki etiketler
            'labels' => ['Yazılım', 'İK', 'Muhasebe', 'Satış', 'Pazarlama'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}