@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<!-- Page Header -->
<x-page-header title="Mata Pelajaran SMA" subtitle="Kelola kurikulum mata pelajaran, fase (E/F), tingkat kelas, dan KKM / kriteria ketuntasan">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('matapelajaran.create') }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Mata Pelajaran</span>
        </x-button>
    </x-slot:actions>
</x-page-header>

<!-- Card Container -->
<x-card>
    <!-- Filter Bar -->
    <form method="GET" action="{{ route('matapelajaran.index') }}" class="flex flex-wrap items-center gap-3">
        <x-search-input name="search" placeholder="Cari nama / kode mapel..." :value="request('search')" />

        <x-filter-select name="fase">
            <option value="">Semua Fase</option>
            <option value="Fase E" {{ request('fase') == 'Fase E' ? 'selected' : '' }}>Fase E (Kelas X)</option>
            <option value="Fase F" {{ request('fase') == 'Fase F' ? 'selected' : '' }}>Fase F (Kelas XI - XII)</option>
        </x-filter-select>

        <x-button type="submit" variant="secondary">
            Filter
        </x-button>

        @if(request('search') || request('fase'))
            <x-button variant="outline" href="{{ route('matapelajaran.index') }}">
                Reset
            </x-button>
        @endif
    </form>

    <!-- Table Responsive -->
    <x-table>
        <x-thead>
            <x-th>Kode Mapel</x-th>
            <x-th>Nama Mata Pelajaran</x-th>
            <x-th align="center">Fase & Tingkat</x-th>
            <x-th align="center">KKM</x-th>
            <x-th align="center">Lingkup Materi</x-th>
            <x-th align="center">Tujuan Pembelajaran</x-th>
            <x-th align="center">Aksi</x-th>
        </x-thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($mataPelajarans as $mk)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-td class="font-mono font-bold text-slate-900">{{ $mk->kode_mapel }}</x-td>
                    <x-td>
                        <strong class="font-semibold text-slate-900 block">{{ $mk->nama_mapel }}</strong>
                        <small class="text-slate-400 text-xs">{{ \Illuminate\Support\Str::limit($mk->deskripsi, 60) }}</small>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800">
                            {{ $mk->fase }} &bull; {{ $mk->tingkat }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-bold text-slate-800">{{ $mk->kkm }}</x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                            {{ $mk->lingkup_materis_count }} Materi
                        </span>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                            {{ $mk->tujuan_pembelajarans_count }} TP
                        </span>
                    </x-td>
                    <x-td align="center">
                        <div class="inline-flex items-center gap-1.5">
                            <x-button variant="subtle" href="{{ route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $mk->id]) }}" class="px-2.5 py-1 text-xs">
                                Kelola TP
                            </x-button>
                            <x-button variant="outline" href="{{ route('matapelajaran.edit', $mk) }}" class="px-2.5 py-1 text-xs">
                                Edit
                            </x-button>
                            <form action="{{ route('matapelajaran.destroy', $mk) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mata pelajaran {{ $mk->nama_mapel }}?')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger" class="px-2 py-1 text-xs">
                                    Hapus
                                </x-button>
                            </form>
                        </div>
                    </x-td>
                </tr>
            @empty
                <x-empty-state colspan="7" message="Tidak ada data mata pelajaran." />
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $mataPelajarans->links() }}
    </div>
</x-card>
@endsection
