@php
    $previewData = $this->previewData;
@endphp

<div class="space-y-6">
    @if (empty($previewData))
        <div class="text-center text-gray-500 py-8">
            Belum ada data review. Silakan selesaikan mapping kelas terlebih dahulu.
        </div>
    @else
        @php
            $total = collect($previewData)->sum('count');
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-success-50 dark:bg-success-950 rounded-xl p-4 border border-success-200 dark:border-success-800">
                <p class="text-3xl font-bold text-success-600 dark:text-success-400">{{ $total }}</p>
                <p class="text-sm text-success-700 dark:text-success-300 mt-1">Total Warga Belajar</p>
            </div>
            <div class="bg-info-50 dark:bg-info-950 rounded-xl p-4 border border-info-200 dark:border-info-800">
                <p class="text-3xl font-bold text-info-600 dark:text-info-400">{{ count($previewData) }}</p>
                <p class="text-sm text-info-700 dark:text-info-300 mt-1">Mapping Kelas</p>
            </div>
            <div class="bg-primary-50 dark:bg-primary-950 rounded-xl p-4 border border-primary-200 dark:border-primary-800">
                <p class="text-sm font-medium text-primary-700 dark:text-primary-300">Tahun Ajaran</p>
                <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $this->summary['source_year'] ?? '-' }} → {{ $this->summary['dest_year'] ?? '-' }}</p>
            </div>
        </div>

        @foreach ($previewData as $item)
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $item['source_class']->name }}
                            </span>
                            <x-filament::icon alias="heroicon-o-arrow-right" class="h-5 w-5 text-gray-400" />
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                {{ $item['destination_class']->name }}
                            </span>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg px-4 py-1.5 border border-gray-200 dark:border-gray-600">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $item['count'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">Warga Belajar</span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3">
                    @if ($item['count'] > 0)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                    <th class="py-3 pr-4">NIS</th>
                                    <th class="py-3 pr-4">Nama</th>
                                    <th class="py-3 pr-4">Program</th>
                                    <th class="py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @foreach ($item['learners'] as $learner)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="py-3 pr-4">
                                            <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $learner->nis ?? '-' }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $learner->name }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                                {{ $learner->program?->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                                Aktif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex items-center justify-center py-8 text-gray-400 dark:text-gray-500">
                            <x-filament::icon alias="heroicon-o-user-group" class="h-8 w-8 mr-2" />
                            <span class="text-sm">Tidak ada warga belajar aktif di kelas ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
