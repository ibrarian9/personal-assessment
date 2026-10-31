@extends('layouts.app')

@section('title', 'Matriks Nilai Sumatif per TP')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Matriks Nilai Sumatif per Tujuan Pembelajaran (TP)</h2>
        <p class="text-sm text-slate-500 mt-1">Lembar kerja penilaian Kurikulum Merdeka: Nilai sumatif per TP pada setiap Lingkup Materi</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        @if($selectedMapel)
            <a href="{{ route('penilaian.template-excel', ['mata_pelajaran_id' => $selectedMapel->id, 'kelas' => $selectedKelas]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-xl border border-teal-300 bg-teal-50 text-teal-700 hover:bg-teal-100 transition-colors shadow-xs">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Download Template Excel</span>
            </a>
            <button type="button" data-micromodal-trigger="import-excel-modal" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-xs">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <span>Import File Excel</span>
            </button>
        @endif
        <a href="{{ route('penilaian.index') }}" class="inline-flex items-center px-3.5 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            Ringkasan
        </a>
    </div>
</div>

<!-- Filter Selection Bar -->
<div class="rounded-2xl bg-white p-5 shadow-xs border border-slate-200/80">
    <form method="GET" action="{{ route('penilaian.matrix') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-xs font-bold uppercase text-slate-500" for="mata_pelajaran_id">Mata Pelajaran:</label>
            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="py-2 px-3 text-sm font-semibold rounded-xl border border-slate-300 bg-white text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" onchange="this.form.submit()">
                @forelse($mapels as $m)
                    <option value="{{ $m->id }}" {{ ($selectedMapel && $selectedMapel->id == $m->id) ? 'selected' : '' }}>
                        {{ $m->kode_mapel }} - {{ $m->nama_mapel }} ({{ $m->fase }})
                    </option>
                @empty
                    <option value="">-- Belum Ada Mata Pelajaran --</option>
                @endforelse
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-bold uppercase text-slate-500" for="kelas">Kelas SMA:</label>
            <select name="kelas" id="kelas" class="py-2 px-3 text-sm font-semibold rounded-xl border border-slate-300 bg-white text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" onchange="this.form.submit()">
                @php
                    $availableKelas = $kelasList->isNotEmpty() ? $kelasList : collect(['X-A', 'X-B', 'XI-A', 'XII-A']);
                @endphp
                @foreach($availableKelas as $k)
                    <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-xl bg-slate-800 text-white hover:bg-slate-900 transition-colors shadow-xs">
            Muat Data Kelas
        </button>
    </form>
</div>

@if($selectedMapel)
<!-- Main Spreadsheet Matrix Form (Matching Image 1) -->
<form action="{{ route('penilaian.matrix.save') }}" method="POST" id="matrix-form">
    @csrf
    <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
    <input type="hidden" name="kelas" value="{{ $selectedKelas }}">

    <div class="rounded-2xl bg-white shadow-md border border-slate-300 overflow-hidden space-y-4">
        <!-- Matrix Document Header -->
        <div class="bg-gradient-to-r from-teal-800 via-teal-900 to-slate-900 text-white p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-teal-300 block">LEMBAR PENILAIAN KURIKULUM MERDEKA SMA</span>
                <h3 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight mt-0.5">
                    NILAI SUMATIF PER TUJUAN PEMBELAJARAN SETIAP LINGKUP MATERI
                </h3>
                <p class="text-xs text-slate-300 mt-1">
                    <strong>Mata Pelajaran:</strong> {{ $selectedMapel->nama_mapel }} &bull; <strong>Kelas:</strong> {{ $selectedKelas }} &bull; <strong>KKM TP:</strong> {{ $selectedMapel->kkm }}
                </p>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-md transition-all focus:ring-2 focus:ring-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    <span>Simpan Seluruh Matriks Nilai</span>
                </button>
            </div>
        </div>

        <!-- Spreadsheet Scrollable Container -->
        <div class="overflow-x-auto custom-scrollbar border-y border-slate-300 max-h-[70vh] relative">
            <table class="w-full text-left text-xs border-collapse border border-slate-300">
                <thead class="bg-teal-900 text-white font-bold sticky top-0 z-30 shadow-xs">
                    <tr>
                        <!-- Sticky Col 1: Select All Checkbox -->
                        <th class="border border-slate-400 px-2 py-2 text-center w-10 min-w-[40px] max-w-[40px] sticky left-0 bg-teal-950 z-40" rowspan="2">
                            <input type="checkbox" id="select-all-students" class="rounded border-slate-400 text-teal-600 focus:ring-teal-500 cursor-pointer h-4 w-4" title="Pilih Semua Siswa">
                        </th>
                        <!-- Sticky Col 2: No -->
                        <th class="border border-slate-400 px-2 py-2 text-center w-10 min-w-[40px] max-w-[40px] sticky left-[40px] bg-teal-950 z-40" rowspan="2">No</th>
                        <!-- Sticky Col 3: Nama Siswa -->
                        <th class="border border-slate-400 px-3 py-2 w-48 min-w-[170px] max-w-[190px] sticky left-[80px] bg-teal-950 z-40" rowspan="2">NAMA SISWA</th>
                        <!-- Sticky Col 4: NISN -->
                        <th class="border border-slate-400 px-2.5 py-2 w-28 min-w-[100px] max-w-[110px] sticky left-[250px] bg-teal-950 z-40 font-mono" rowspan="2">NISN</th>
                        <!-- Sticky Col 5: Aksi -->
                        <th class="border border-slate-400 px-2 py-2 text-center w-24 min-w-[90px] max-w-[100px] sticky left-[350px] bg-teal-950 z-40 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.25)]" rowspan="2">AKSI</th>
                        
                        <!-- Level 1: Lingkup Materi Headers -->
                        @foreach($lingkupMateris as $lm)
                            @php $tpSpan = $lm->tujuanPembelajarans->count(); @endphp
                            @if($tpSpan > 0)
                                <th class="border border-slate-400 px-2 py-2 text-center bg-teal-800 text-teal-100 font-extrabold uppercase whitespace-nowrap" colspan="{{ $tpSpan }}" title="{{ $lm->judul_materi }}">
                                    {{ $lm->kode_lm }}
                                </th>
                            @endif
                        @endforeach

                        <th class="border border-slate-400 px-3 py-2 text-center w-24 min-w-[80px] bg-teal-950 text-white font-bold" rowspan="2">RATA-RATA</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28 min-w-[100px] bg-teal-950 text-white font-bold" rowspan="2">KETUNTASAN</th>
                    </tr>

                    <!-- Level 2: Tujuan Pembelajaran (TP) Codes -->
                    <tr class="bg-teal-950 text-teal-200 text-[11px]">
                        @foreach($lingkupMateris as $lm)
                            @foreach($lm->tujuanPembelajarans as $tp)
                                <th class="border border-slate-400 px-1.5 py-1.5 text-center min-w-[70px] max-w-[90px] font-mono cursor-help hover:bg-teal-800 transition-colors" title="{{ $lm->kode_lm }} - {{ $tp->kode_tp }}: {{ $tp->deskripsi_tp }}">
                                    <span class="block font-bold text-white">{{ $tp->kode_tp }}</span>
                                    <span class="block text-[9px] text-teal-300 font-normal truncate max-w-[75px]">{{ $tp->deskripsi_tp }}</span>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>

                <!-- Table Rows: Siswa & Scores -->
                <tbody class="divide-y divide-slate-300 bg-white">
                    @forelse($siswas as $sIndex => $siswa)
                        <tr class="hover:bg-slate-50 transition-colors group siswa-row" data-siswa-id="{{ $siswa->id }}">
                            <!-- Sticky Col 1: Checkbox (Solid Opaque Background) -->
                            <td class="border border-slate-300 px-2 py-1.5 text-center sticky left-0 bg-white group-hover:bg-slate-100 z-20 w-10 min-w-[40px] max-w-[40px]">
                                <input type="checkbox" name="selected_siswas[]" value="{{ $siswa->id }}" class="student-select-cb rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer h-4 w-4" data-nama="{{ $siswa->nama }}" data-nisn="{{ $siswa->nisn }}" data-gaya="{{ $siswa->gaya_belajar }}">
                            </td>
                            <!-- Sticky Col 2: No (Solid Opaque Background) -->
                            <td class="border border-slate-300 px-2 py-1.5 text-center font-mono font-bold text-slate-500 sticky left-[40px] bg-white group-hover:bg-slate-100 z-20 w-10 min-w-[40px] max-w-[40px]">
                                {{ $sIndex + 1 }}
                            </td>
                            <!-- Sticky Col 3: Nama Siswa (Solid Opaque Background) -->
                            <td class="border border-slate-300 px-3 py-1.5 font-bold text-slate-900 sticky left-[80px] bg-white group-hover:bg-slate-100 z-20 w-48 min-w-[170px] max-w-[190px] whitespace-nowrap">
                                <a href="{{ route('personalisasi.show', $siswa) }}" class="hover:text-teal-600 hover:underline block leading-tight truncate max-w-[160px]" title="{{ $siswa->nama }}">
                                    {{ $siswa->nama }}
                                </a>
                                <span class="text-[10px] font-normal text-purple-700 block">{{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $siswa->gaya_belajar) }}</span>
                            </td>
                            <!-- Sticky Col 4: NISN (Solid Opaque Background) -->
                            <td class="border border-slate-300 px-2.5 py-1.5 font-mono text-[11px] text-slate-700 sticky left-[250px] bg-white group-hover:bg-slate-100 z-20 w-28 min-w-[100px] max-w-[110px] whitespace-nowrap">
                                {{ $siswa->nisn }}
                            </td>
                            <!-- Sticky Col 5: Aksi (Solid Opaque Background with Drop Shadow Border) -->
                            <td class="border border-slate-300 px-1.5 py-1.5 text-center sticky left-[350px] bg-white group-hover:bg-slate-100 z-20 w-24 min-w-[90px] max-w-[100px] shadow-[4px_0_6px_-2px_rgba(0,0,0,0.18)] whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="openQuickEditModal({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}', '{{ $siswa->nisn }}', '{{ $siswa->gaya_belajar }}')" class="p-1 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition-colors" title="Edit Siswa">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                    </button>
                                    <button type="button" onclick="openResetSingleModal({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')" class="p-1 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Reset Nilai Mapel Ini">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>
                                    </button>
                                    <button type="button" onclick="openDeleteSingleModal({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')" class="p-1 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Siswa dari Kelas">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            </td>

                            <!-- TP Input Cells (Non-Sticky, Slides Cleanly Behind Sticky Header/Columns) -->
                            @php $rowTotal = 0; $rowCount = 0; $rowTuntas = 0; @endphp
                            @foreach($lingkupMateris as $lm)
                                @foreach($lm->tujuanPembelajarans as $tp)
                                    @php
                                        $val = $scoreMap[$siswa->id][$tp->id] ?? '';
                                        if ($val !== '') {
                                            $rowTotal += (float)$val;
                                            $rowCount++;
                                            if ((float)$val >= $tp->target_ketercapaian) $rowTuntas++;
                                        }
                                    @endphp
                                    <td class="border border-slate-300 p-0 text-center bg-white group-hover:bg-slate-50/50">
                                        <input type="number" step="1" min="0" max="100"
                                               name="scores[{{ $siswa->id }}][{{ $tp->id }}]"
                                               value="{{ $val }}"
                                               class="w-full text-center py-1.5 px-0.5 text-xs font-mono font-bold bg-transparent border-0 focus:bg-teal-100 focus:ring-1 focus:ring-teal-600 outline-none matrix-input {{ ((float)$val > 0 && (float)$val < $tp->target_ketercapaian) ? 'text-rose-600 bg-rose-50/70' : 'text-slate-900' }}"
                                               data-target="{{ $tp->target_ketercapaian }}"
                                               placeholder="-">
                                    </td>
                                @endforeach
                            @endforeach

                            @php
                                $rowAvg = $rowCount > 0 ? round($rowTotal / $rowCount, 1) : 0;
                                $isAllTuntas = $rowCount > 0 && ($rowTuntas === $rowCount);
                            @endphp

                            <!-- Rata-Rata Row Live -->
                            <td class="border border-slate-300 px-2 py-1.5 text-center font-mono font-extrabold text-teal-700 bg-slate-50 row-avg min-w-[80px]">
                                {{ $rowAvg }}
                            </td>

                            <!-- Ketuntasan Row Live -->
                            <td class="border border-slate-300 px-2 py-1.5 text-center bg-slate-50 min-w-[100px]">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold row-status {{ $isAllTuntas ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $rowTuntas }} Tuntas dari {{ $rowCount }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($allTps) + 7 }}" class="py-12 text-center text-slate-400">
                                Belum ada data siswa di kelas {{ $selectedKelas }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Action & Legend -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-xs text-slate-600">
                <span class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded bg-emerald-100 border border-emerald-300"></span>
                    Tuntas (&ge; {{ $selectedMapel->kkm }})
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded bg-rose-100 border border-rose-300"></span>
                    Perlu Remedial (&lt; {{ $selectedMapel->kkm }})
                </span>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md transition-all focus:ring-2 focus:ring-teal-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>Simpan Seluruh Nilai Matriks</span>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Floating Bulk Actions Toolbar -->
<div id="bulk-action-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/95 text-white shadow-2xl backdrop-blur-md border border-slate-700 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2 border-r border-slate-700 pr-3">
        <span class="flex h-2.5 w-2.5 rounded-full bg-teal-400 animate-pulse"></span>
        <span class="text-xs font-bold text-slate-200"><span id="selected-count" class="font-mono text-teal-300 font-extrabold text-sm">0</span> Siswa Terpilih</span>
    </div>
    <button type="button" onclick="openBulkResetModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-bold transition-colors">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>
        <span>Reset Nilai TP Terpilih</span>
    </button>
    <button type="button" onclick="openBulkDeleteModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold transition-colors">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
        <span>Hapus Siswa Terpilih</span>
    </button>
    <button type="button" onclick="deselectAllStudents()" class="p-1 rounded-lg text-slate-400 hover:text-white transition-colors" title="Batal Pilihan">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<!-- Modal Quick Edit Siswa (MicroModal) -->
<div class="modal micromodal-slide" id="quick-edit-siswa-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200" role="dialog" aria-modal="true" aria-labelledby="quick-edit-siswa-modal-title">
            <header class="modal__header flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="modal__title text-base font-bold text-slate-900 flex items-center gap-2" id="quick-edit-siswa-modal-title">
                    <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    Edit Data Siswa
                </h3>
                <button type="button" class="modal__close p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" aria-label="Close modal" data-micromodal-close>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <form action="{{ route('penilaian.matrix.quick-update-siswa') }}" method="POST" class="space-y-4 pt-3">
                @csrf
                <input type="hidden" name="siswa_id" id="edit_siswa_id">
                <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full py-2 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">NISN (Nomor Induk Siswa Nasional)</label>
                    <input type="text" name="nisn" id="edit_nisn" required class="w-full py-2 px-3 text-sm font-mono font-semibold rounded-xl border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Gaya Belajar (VARK)</label>
                    <select name="gaya_belajar" id="edit_gaya_belajar" class="w-full py-2 px-3 text-sm font-semibold rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                        <option value="Visual">Visual</option>
                        <option value="Auditori">Auditori</option>
                        <option value="Kinestetik">Kinestetik</option>
                        <option value="Baca/Tulis">Baca dan Tulis</option>
                    </select>
                </div>

                <div class="modal__footer flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" data-micromodal-close>Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-xs transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reset Nilai (MicroModal) -->
<div class="modal micromodal-slide" id="reset-nilai-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4" role="dialog" aria-modal="true" aria-labelledby="reset-nilai-modal-title">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>
                </div>
                <div class="flex-1 space-y-1">
                    <h3 class="modal__title text-base font-bold text-slate-900" id="reset-nilai-modal-title">Konfirmasi Reset Nilai Sumatif TP</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" id="reset-nilai-desc">
                        Apakah Anda yakin ingin mereset atau menghapus seluruh nilai TP untuk siswa terpilih pada mata pelajaran ini? Personalisasi AI akan disinkronkan ulang.
                    </p>
                </div>
            </div>
            <form action="{{ route('penilaian.matrix.reset-scores') }}" method="POST" id="reset-scores-form">
                @csrf
                <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">
                <div id="reset-student-ids-container"></div>
                
                <div class="modal__footer flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" data-micromodal-close>Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded-xl shadow-xs transition-colors">Ya, Reset Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Siswa (MicroModal) -->
<div class="modal micromodal-slide" id="delete-siswa-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4" role="dialog" aria-modal="true" aria-labelledby="delete-siswa-modal-title">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                </div>
                <div class="flex-1 space-y-1">
                    <h3 class="modal__title text-base font-bold text-slate-900" id="delete-siswa-modal-title">Konfirmasi Hapus Data Siswa</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" id="delete-siswa-desc">
                        Apakah Anda yakin ingin menghapus data siswa terpilih? Seluruh nilai dan riwayat personalisasi siswa tersebut akan dihapus dari sistem.
                    </p>
                </div>
            </div>
            <form action="{{ route('penilaian.matrix.bulk-delete-siswa') }}" method="POST" id="delete-siswa-form">
                @csrf
                <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">
                <div id="delete-student-ids-container"></div>
                
                <div class="modal__footer flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" data-micromodal-close>Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition-colors">Ya, Hapus Data Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Live Matrix Calculation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Live Matrix Calculation
        const rows = document.querySelectorAll('.siswa-row');

        rows.forEach(row => {
            const inputs = row.querySelectorAll('.matrix-input');
            const avgCell = row.querySelector('.row-avg');
            const statusBadge = row.querySelector('.row-status');

            function recalculateRow() {
                let total = 0;
                let count = 0;
                let tuntas = 0;

                inputs.forEach(input => {
                    const val = parseFloat(input.value);
                    const target = parseFloat(input.dataset.target) || 75;

                    if (!isNaN(val)) {
                        total += val;
                        count++;
                        if (val >= target) {
                            tuntas++;
                            input.classList.remove('text-rose-600', 'bg-rose-50/70');
                            input.classList.add('text-slate-900');
                        } else {
                            input.classList.add('text-rose-600', 'bg-rose-50/70');
                            input.classList.remove('text-slate-900');
                        }
                    }
                });

                if (count > 0) {
                    const avg = (total / count).toFixed(1);
                    if (avgCell) avgCell.textContent = avg;
                    if (statusBadge) {
                        statusBadge.textContent = `${tuntas} Tuntas dari ${count}`;
                        if (tuntas === count) {
                            statusBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold row-status bg-emerald-100 text-emerald-800';
                        } else {
                            statusBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold row-status bg-rose-100 text-rose-800';
                        }
                    }
                }
            }

            inputs.forEach(input => {
                input.addEventListener('input', recalculateRow);
            });
        });

        // 2. Checkbox & Bulk Actions Toolbar Logic
        const selectAllCb = document.getElementById('select-all-students');
        const studentCbs = document.querySelectorAll('.student-select-cb');
        const bulkBar = document.getElementById('bulk-action-bar');
        const selectedCountSpan = document.getElementById('selected-count');

        function updateBulkBar() {
            const selected = document.querySelectorAll('.student-select-cb:checked');
            const count = selected.length;
            if (selectedCountSpan) selectedCountSpan.textContent = count;

            if (count > 0) {
                bulkBar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                bulkBar.classList.add('translate-y-0', 'opacity-100');
            } else {
                bulkBar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                bulkBar.classList.remove('translate-y-0', 'opacity-100');
            }

            if (selectAllCb) {
                selectAllCb.checked = (count > 0 && count === studentCbs.length);
                selectAllCb.indeterminate = (count > 0 && count < studentCbs.length);
            }
        }

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function () {
                studentCbs.forEach(cb => {
                    cb.checked = selectAllCb.checked;
                });
                updateBulkBar();
            });
        }

        studentCbs.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        window.deselectAllStudents = function () {
            if (selectAllCb) selectAllCb.checked = false;
            studentCbs.forEach(cb => cb.checked = false);
            updateBulkBar();
        };

        // 3. Quick Edit Siswa Modal with MicroModal
        window.openQuickEditModal = function (id, nama, nisn, gaya) {
            document.getElementById('edit_siswa_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_nisn').value = nisn;
            document.getElementById('edit_gaya_belajar').value = gaya;
            if (window.openModal) window.openModal('quick-edit-siswa-modal');
        };

        window.closeQuickEditModal = function () {
            if (window.closeModal) window.closeModal('quick-edit-siswa-modal');
        };

        // 4. Reset Nilai Modal (Single & Bulk) with MicroModal
        window.openResetSingleModal = function (id, nama) {
            const container = document.getElementById('reset-student-ids-container');
            container.innerHTML = `<input type="hidden" name="siswa_ids[]" value="${id}">`;
            document.getElementById('reset-nilai-desc').textContent = `Apakah Anda yakin ingin mereset seluruh nilai TP mata pelajaran ini untuk ${nama}?`;
            if (window.openModal) window.openModal('reset-nilai-modal');
        };

        window.openBulkResetModal = function () {
            const selected = document.querySelectorAll('.student-select-cb:checked');
            if (selected.length === 0) return;

            const container = document.getElementById('reset-student-ids-container');
            container.innerHTML = '';
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'siswa_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            document.getElementById('reset-nilai-desc').textContent = `Apakah Anda yakin ingin mereset seluruh nilai TP mata pelajaran ini untuk ${selected.length} siswa terpilih?`;
            if (window.openModal) window.openModal('reset-nilai-modal');
        };

        window.closeResetModal = function () {
            if (window.closeModal) window.closeModal('reset-nilai-modal');
        };

        // 5. Delete Siswa Modal (Single & Bulk) with MicroModal
        window.openDeleteSingleModal = function (id, nama) {
            const container = document.getElementById('delete-student-ids-container');
            container.innerHTML = `<input type="hidden" name="siswa_ids[]" value="${id}">`;
            document.getElementById('delete-siswa-desc').textContent = `Apakah Anda yakin ingin menghapus data siswa ${nama} dari kelas ini? Seluruh riwayat nilai dan personalisasi akan dihapus.`;
            if (window.openModal) window.openModal('delete-siswa-modal');
        };

        window.openBulkDeleteModal = function () {
            const selected = document.querySelectorAll('.student-select-cb:checked');
            if (selected.length === 0) return;

            const container = document.getElementById('delete-student-ids-container');
            container.innerHTML = '';
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'siswa_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            document.getElementById('delete-siswa-desc').textContent = `Apakah Anda yakin ingin menghapus ${selected.length} data siswa terpilih dari kelas ini?`;
            if (window.openModal) window.openModal('delete-siswa-modal');
        };

        window.closeDeleteModal = function () {
            if (window.closeModal) window.closeModal('delete-siswa-modal');
        };
    });
</script>

<!-- Import Excel Modal (MicroModal) -->
<div class="modal micromodal-slide" id="import-excel-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container max-w-xl rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200/80 space-y-6" role="dialog" aria-modal="true" aria-labelledby="import-excel-modal-title">
            <!-- Modal Header -->
            <header class="modal__header flex items-start justify-between pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-teal-50 text-teal-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </span>
                        <h3 class="modal__title text-lg font-bold text-slate-900" id="import-excel-modal-title">Upload File Excel Nilai Sumatif</h3>
                    </div>
                    <p class="text-xs text-slate-500">Mata Pelajaran: <strong>{{ $selectedMapel->nama_mapel }}</strong> &bull; Kelas: <strong>{{ $selectedKelas }}</strong></p>
                </div>
                <button type="button" class="modal__close p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" aria-label="Close modal" data-micromodal-close>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <!-- Structure Guidance Box -->
            <div class="rounded-2xl bg-teal-50/70 p-4 border border-teal-100 text-xs text-teal-900 space-y-2">
                <div class="font-bold flex items-center gap-1.5 text-teal-800">
                    <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    Struktur Format Excel yang Didukung:
                </div>
                <ul class="list-disc list-inside space-y-1 text-slate-700">
                    <li><strong>Baris 1 (Header Atas):</strong> Nama Lingkup Materi (misal: <em>Materi 1: Struktur Sel</em>, <em>Materi 2: Jaringan</em>).</li>
                    <li><strong>Baris 2 (Header Bawah):</strong> Kolom <code>NO</code>, <code>NAMA SISWA</code>, <code>NISN</code>, lalu kode masing-masing TP (<code>TP 1.1</code>, <code>TP 1.2</code>, <code>TP 2.1</code>, dst.).</li>
                    <li><strong>Baris 3+:</strong> Data siswa beserta skor nilai numerik (rentang 0 - 100).</li>
                </ul>
                <div class="pt-1">
                    <a href="{{ route('penilaian.template-excel', ['mata_pelajaran_id' => $selectedMapel->id, 'kelas' => $selectedKelas]) }}" class="inline-flex items-center gap-1 font-semibold text-teal-700 hover:text-teal-800 underline">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Download format template Excel siap pakai di sini</span>
                    </a>
                </div>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('penilaian.import-excel') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <div class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition-colors cursor-pointer" onclick="document.getElementById('file_excel_input').click()">
                        <svg class="h-10 w-10 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-sm font-semibold text-slate-700 text-center" id="file-name-display">Klik untuk memilih file Excel atau seret file ke sini</p>
                        <p class="text-xs text-slate-400 mt-1">Maksimal ukuran file: 10 MB</p>
                        <input type="file" name="file_excel" id="file_excel_input" accept=".xlsx,.xls,.csv" class="hidden" required onchange="document.getElementById('file-name-display').textContent = this.files[0] ? this.files[0].name : 'Klik untuk memilih file Excel'">
                    </div>
                </div>

                <div class="modal__footer flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors" data-micromodal-close>
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        <span>Upload & Sinkronkan Nilai</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<!-- Empty State when No Subject Exists -->
<div class="rounded-2xl bg-white p-12 text-center shadow-xs border border-slate-200/80 max-w-2xl mx-auto space-y-4 my-6">
    <div class="h-16 w-16 mx-auto rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shadow-2xs">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
        </svg>
    </div>
    <div class="space-y-1">
        <h3 class="text-lg font-bold text-slate-900">Belum Ada Mata Pelajaran Terdaftar</h3>
        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
            Silakan tambahkan data mata pelajaran terlebih dahulu untuk menyusun indikator Tujuan Pembelajaran dan memulai pengisian matriks penilaian.
        </p>
    </div>
    <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('matapelajaran.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Mata Pelajaran Baru</span>
        </a>
        <a href="{{ route('siswa.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm shadow-xs transition-colors">
            <span>Kelola Data Siswa</span>
        </a>
    </div>
</div>
@endif
@endsection
