@php
// Mengambil data pertama dari tabel school_profiles
$school = \App\Models\SchoolProfile::first();

$logoBase64 = null;
if (!empty($school?->logo)) {
// Cek file di folder private (sesuai kode Anda sebelumnya)
$logoPath = storage_path('app/private/' . $school->logo);

// Jaga-jaga jika ternyata file tersimpan di public
if (!file_exists($logoPath)) {
$logoPath = storage_path('app/public/' . $school->logo);
}

// Convert ke Base64 agar pasti tampil di browser
if (file_exists($logoPath)) {
$mime = mime_content_type($logoPath);
$data = base64_encode(file_get_contents($logoPath));
$logoBase64 = "data:{$mime};base64,{$data}";
}
}
@endphp

<div style="display: flex; align-items: center; gap: 0.75rem;">
    {{-- Bagian Gambar Logo --}}
    @if ($logoBase64)
    <img
        src="{{ $logoBase64 }}"
        alt="Logo {{ $school->name ?? 'Sekolah' }}"
        style="height: 2.25rem; width: 2.25rem; object-fit: cover; border-radius: 0.375rem;">
    @else
    {{-- Ikon Default Jika Logo Kosong --}}
    <div style="height: 2.25rem; width: 2.25rem; border-radius: 0.375rem; background-color: #3b82f6; display: flex; align-items: center; justify-content: center;">
        <svg style="width: 1.25rem; height: 1.25rem; color: #ffffff;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
        </svg>
    </div>
    @endif

    {{-- Bagian Teks Nama Sekolah --}}
    <span style="font-size: 1.25rem; font-weight: 700; color: inherit; letter-spacing: -0.025em;">
        {{ $school->name ?? 'Dashboard Panel' }}
    </span>
</div>