<x-filament-panels::page>

    <div class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-gray-500 text-sm font-medium">Toplam Demirbaş</h3>
                <p class="text-3xl font-bold text-primary-600">{{ $calisan->assets->count() }} Adet</p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-gray-500 text-sm font-medium">Departman</h3>
                <p class="text-xl font-bold">{{ $calisan->department ?? 'Belirtilmemiş' }}</p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-gray-500 text-sm font-medium">İletişim</h3>
                <p class="text-lg">{{ $calisan->email }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <h2 class="font-bold text-lg">Zimmetli Ürünler Listesi</h2>
            </div>

            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white uppercase font-semibold">
                    <tr>
                        <th class="p-4">Görsel</th>
                        <th class="p-4">Ürün Adı</th>
                        <th class="p-4">Seri No</th>
                        <th class="p-4">Tip</th>
                        <th class="p-4">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($calisan->assets as $asset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="p-4">
                                @if($asset->image)
                                    <img src="{{ asset('storage/'.$asset->image) }}" class="h-10 w-10 rounded-lg object-cover border dark:border-gray-600">
                                @else
                                    <span class="text-2xl">📦</span>
                                @endif
                            </td>
                            <td class="p-4 font-medium text-gray-900 dark:text-white">{{ $asset->name }}</td>
                            <td class="p-4 font-mono">{{ $asset->serial_number }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold dark:bg-blue-900 dark:text-blue-300">
                                    {{ strtoupper($asset->type) }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($asset->is_available)
                                    <span class="text-green-600">Müsait</span>
                                @else
                                    <span class="text-red-600">Kullanımda</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($calisan->assets->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    Henüz zimmetli eşya bulunmamaktadır.
                </div>
            @endif
        </div>

    </div> </x-filament-panels::page>