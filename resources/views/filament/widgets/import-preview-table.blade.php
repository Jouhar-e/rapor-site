<x-filament-widgets::widget>
    <div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem; white-space: nowrap;">
            <thead>
                <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">#</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">NIS</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Nama</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Gender</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Tempat Lahir</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Tgl Lahir</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Status</th>
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Agama</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $row)
                    <tr style="border-bottom: 1px solid #f3f4f6; {{ $loop->even ? 'background-color: #fafafa;' : '' }}">
                        <td style="padding: 0.5rem 0.75rem; color: #6b7280;">{{ $index + 1 }}</td>
                        <td style="padding: 0.5rem 0.75rem; font-weight: 500; color: #111827;">{{ $row['nis'] ?? '' }}</td>
                        <td style="padding: 0.5rem 0.75rem; color: #111827;">{{ $row['name'] ?? '' }}</td>
                        <td style="padding: 0.5rem 0.75rem;">
                            <span style="padding: 0.125rem 0.375rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; {{ ($row['gender'] ?? '') === 'L' ? 'background: #dbeafe; color: #1d4ed8;' : 'background: #fce7f3; color: #db2777;' }}">
                                {{ $row['gender'] ?? '-' }}
                            </span>
                        </td>
                        <td style="padding: 0.5rem 0.75rem; color: #6b7280;">{{ $row['birth_place'] ?? '' }}</td>
                        <td style="padding: 0.5rem 0.75rem; color: #6b7280;">{{ $row['birth_date'] ?? '' }}</td>
                        <td style="padding: 0.5rem 0.75rem;">
                            <span style="padding: 0.125rem 0.375rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; background: #f3f4f6; color: #374151;">
                                {{ $row['status'] ?? '-' }}
                            </span>
                        </td>
                        <td style="padding: 0.5rem 0.75rem; color: #6b7280;">{{ $row['religion'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 2rem; text-align: center; color: #9ca3af;">
                            Tidak ada data untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-widgets::widget>
