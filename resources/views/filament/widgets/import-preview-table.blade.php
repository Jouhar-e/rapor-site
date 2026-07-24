<x-filament-widgets::widget>
    <div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem; white-space: nowrap;">
            <thead>
                <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">#</th>
                    @foreach ($headers as $header)
                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $row)
                    <tr style="border-bottom: 1px solid #f3f4f6; {{ $loop->even ? 'background-color: #fafafa;' : '' }}">
                        <td style="padding: 0.5rem 0.75rem; color: #6b7280;">{{ $index + 1 }}</td>
                        @foreach ($headers as $header)
                            <td style="padding: 0.5rem 0.75rem; color: #111827;">{{ $row[$header] ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 1 }}" style="padding: 2rem; text-align: center; color: #9ca3af;">
                            Tidak ada data untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-widgets::widget>
