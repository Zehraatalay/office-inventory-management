<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class LineChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Yeni Cihaz Alımları',
                    'data' => [2, 5, 3, 10, 8, 15], // noktalarin yerleri
                    'fill' => 'start', // altini boya
                    'borderColor' => '#ec4899', // pembe line
                    'tension' => 0.5, // cizgiyi smoothlastir(kivrimli oluyor)
                ],
            ],
            'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
