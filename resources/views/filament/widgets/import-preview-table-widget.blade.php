<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                @foreach (array_keys($this->data[0] ?? []) as $header)
                    @php
                        $label = match ($header) {
                            'nis' => 'NIS',
                            'nisn' => 'NISN',
                            'nip' => 'NIP',
                            'name' => 'Nama',
                            'gender' => 'Gender',
                            'birth_place' => 'Tempat Lahir',
                            'birth_date' => 'Tgl Lahir',
                            'address' => 'Alamat',
                            'email' => 'Email',
                            'phone' => 'Phone',
                            'is_active' => 'Status',
                            'password' => 'Password',
                            'class_name' => 'Kelas',
                            'task_score' => 'Tugas',
                            'pts_score' => 'PTS',
                            'pas_score' => 'PAS',
                            'practice_score' => 'Praktik',
                            default => ucfirst(str_replace('_', ' ', $header)),
                        };
                    @endphp
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($this->data as $row)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    @foreach (array_keys($this->data[0] ?? []) as $header)
                        @php
                            $value = $row[$header] ?? '-';
                        @endphp
                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                            @if (in_array($header, ['status', 'is_active']))
                                @php
                                    $isActive = in_array($value, ['aktif', '1', true, 1], true);
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' }}">
                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            @elseif ($header === 'gender')
                                {{ $value === 'L' ? 'Laki-laki' : ($value === 'P' ? 'Perempuan' : $value) }}
                            @elseif ($header === 'class_name')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ $value }}</span>
                            @elseif ($header === 'name')
                                <span class="font-medium">{{ $value }}</span>
                            @elseif (in_array($header, ['nis', 'nisn', 'nip', 'password']))
                                <span class="font-mono text-xs">{{ $value }}</span>
                            @elseif (in_array($header, ['task_score', 'pts_score', 'pas_score', 'practice_score']))
                                <div class="text-center">{{ $value }}</div>
                            @elseif ($header === 'address')
                                <span class="max-w-xs block truncate" title="{{ $value }}">{{ $value }}</span>
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
