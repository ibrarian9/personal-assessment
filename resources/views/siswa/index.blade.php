@extends('layouts.app')

@section('title', 'Data Siswa SMA')

@section('content')
<!-- Page Header -->
<x-page-header title="Data Siswa SMA" subtitle="Kelola data induk siswa, NISN, kelas, dan modalitas gaya belajar VARK">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('siswa.create') }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Siswa Baru</span>
        </x-button>
    </x-slot:actions>
</x-page-header>

<!-- Card Container -->
<x-card>
    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('siswa.index') }}" class="flex flex-wrap items-center gap-3">
        <x-search-input name="search" placeholder="Cari nama, NISN, atau email..." :value="request('search')" />

        <x-filter-select name="kelas">
            <option value="">Semua Kelas</option>
            @php
                $availableKelas = $kelasList->isNotEmpty() ? $kelasList : collect(['X-A', 'X-B', 'XI-A', 'XII-A']);
            @endphp
            @foreach($availableKelas as $k)
                <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
            @endforeach
        </x-filter-select>

        <x-filter-select name="gaya_belajar">
            <option value="">Semua Gaya Belajar</option>
            <option value="Visual" {{ request('gaya_belajar') == 'Visual' ? 'selected' : '' }}>Visual</option>
            <option value="Auditori" {{ request('gaya_belajar') == 'Auditori' ? 'selected' : '' }}>Auditori</option>
            <option value="Kinestetik" {{ request('gaya_belajar') == 'Kinestetik' ? 'selected' : '' }}>Kinestetik</option>
            <option value="Baca/Tulis" {{ request('gaya_belajar') == 'Baca/Tulis' ? 'selected' : '' }}>Baca dan Tulis</option>
        </x-filter-select>

        <x-filter-select name="status">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
        </x-filter-select>

        <x-button type="submit" variant="secondary">
            Filter
        </x-button>

        @if(request('search') || request('kelas') || request('gaya_belajar') || request('status'))
            <x-button variant="outline" href="{{ route('siswa.index') }}">
                Reset
            </x-button>
        @endif
    </form>

    <!-- Standard Table Component -->
    <x-table>
        <x-thead>
            <x-th>NISN</x-th>
            <x-th>Nama Siswa</x-th>
            <x-th>Kelas</x-th>
            <x-th align="center">Gaya Belajar (VARK)</x-th>
            <x-th align="center">Rata-Rata Sumatif</x-th>
            <x-th align="center">Ketuntasan TP</x-th>
            <x-th align="center">Status</x-th>
            <x-th align="center">Aksi</x-th>
        </x-thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($siswas as $s)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-td class="font-mono font-bold text-slate-900">{{ $s->nisn }}</x-td>
                    <x-td>
                        <strong class="font-semibold text-slate-900 block">{{ $s->nama }}</strong>
                        <small class="text-slate-400 text-xs">{{ $s->email ?? '-' }}</small>
                    </x-td>
                    <x-td class="font-medium text-slate-800">{{ $s->kelas }}</x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800">
                            {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $s->gaya_belajar) }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-bold text-teal-700">
                        {{ $s->rata_rata_nilai }}
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->jumlah_tp_belum_tuntas == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $s->jumlah_tp_tuntas }} Tuntas, {{ $s->jumlah_tp_belum_tuntas }} Remedial
                        </span>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $s->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $s->status }}
                        </span>
                    </x-td>
                    <x-td align="center">
                        <div class="inline-flex items-center gap-1.5">
                            <x-button variant="subtle" href="{{ route('siswa.show', $s) }}" class="px-2.5 py-1 text-xs">
                                Detail
                            </x-button>
                            <x-button variant="outline" href="{{ route('siswa.edit', $s) }}" class="px-2.5 py-1 text-xs">
                                Edit
                            </x-button>
                            <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus data siswa {{ $s->nama }} (NISN: {{ $s->nisn }})?" data-confirm-title="Hapus Data Siswa">
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
                <x-empty-state colspan="8" message="Tidak ada data siswa ditemukan." />
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $siswas->links() }}
    </div>
</x-card>
@endsection
