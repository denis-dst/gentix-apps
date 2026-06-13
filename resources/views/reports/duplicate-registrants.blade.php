@php
    $duplicateRoute = route('organizer.reports.duplicates');
    $formatWaUrl = function ($phone) {
        $number = preg_replace('/\D+/', '', (string) $phone);

        if ($number === '') {
            return null;
        }

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }

        return 'https://wa.me/' . $number;
    };
@endphp

<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-2">Monitoring Operasional</p>
            <h2 class="text-3xl font-black text-slate-800 font-outfit">Check Pendaftar Ganda</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar nama, WhatsApp, atau email yang terdaftar lebih dari satu kali untuk mencegah penambahan kuota yang tidak perlu.</p>
        </div>

        <form action="{{ $duplicateRoute }}" method="GET" class="flex flex-col sm:flex-row gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
            <select name="event_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500">
                <option value="">Semua Event</option>
                @foreach($eventOptions as $eventOption)
                    <option value="{{ $eventOption->id }}" {{ request('event_id') == $eventOption->id ? 'selected' : '' }}>{{ $eventOption->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden duplicate-report-container" x-ignore>
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-black text-slate-800 font-outfit">Pendaftar Ganda</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Menampilkan {{ count($duplicateRows) }} identitas dengan nama, nomor WA, atau email yang sama lebih dari 1 kali.
                </p>
            </div>

            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" id="duplicate-search-input" onkeyup="filterDuplicateTable()" placeholder="Cari pendaftar ganda..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border-slate-200 text-xs font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 bg-white">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="table-duplicate-registrants" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor WA</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-medium">
                    @forelse($duplicateRows as $row)
                        @php $waUrl = $formatWaUrl($row['phone']); @endphp
                        <tr class="duplicate-row hover:bg-slate-50/40 transition">
                            <td class="px-6 py-4 text-sm font-black text-slate-800 uppercase">
                                {{ $row['name'] }}
                                @if($row['registration_count'] > 1)
                                    <span class="ml-2 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-wider">{{ $row['registration_count'] }}× daftar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                @if($waUrl)
                                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="font-black text-emerald-600 hover:text-emerald-700 hover:underline whitespace-nowrap">
                                        {{ $row['phone'] }}
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">{{ $row['email'] ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr class="no-data-duplicate">
                            <td colspan="3" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Tidak ada pendaftar ganda untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="duplicate-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-white text-xs font-bold text-slate-500">
            <div id="duplicate-pagination-info">Menampilkan 0-0 dari 0 data</div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="prevDuplicatePage()" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Prev</button>
                <button type="button" onclick="nextDuplicatePage()" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.duplicateItemsPerPage = 15;

    function getDuplicatePerPage() {
        const perPage = Number(window.duplicateItemsPerPage);
        return perPage > 0 ? perPage : 15;
    }

    window.filterDuplicateTable = function() {
        const container = document.querySelector('.duplicate-report-container');
        if (!container) return;

        const perPage = getDuplicatePerPage();
        const queryInput = container.querySelector('#duplicate-search-input');
        const query = queryInput ? queryInput.value.toLowerCase().trim() : '';

        const rows = container.querySelectorAll('.duplicate-row');
        const visible = [];

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (query === '' || text.includes(query)) {
                row.setAttribute('data-matched', 'true');
                visible.push(row);
            } else {
                row.setAttribute('data-matched', 'false');
                row.style.display = 'none';
            }
        });

        let currentPage = parseInt(container.dataset.currentPage || '1', 10);
        const totalPages = Math.max(1, Math.ceil(visible.length / perPage));
        if (currentPage > totalPages) {
            currentPage = totalPages;
            container.dataset.currentPage = currentPage.toString();
        }

        const startIdx = (currentPage - 1) * perPage;
        const endIdx = Math.min(startIdx + perPage, visible.length);

        visible.forEach((row, idx) => {
            row.style.display = (idx >= startIdx && idx < endIdx) ? 'table-row' : 'none';
        });

        const info = container.querySelector('#duplicate-pagination-info');
        if (info) {
            info.textContent = `Menampilkan ${visible.length === 0 ? 0 : startIdx + 1}-${endIdx} dari ${visible.length} data`;
        }
    };

    window.prevDuplicatePage = function() {
        const container = document.querySelector('.duplicate-report-container');
        if (!container) return;

        let currentPage = parseInt(container.dataset.currentPage || '1', 10);
        if (currentPage > 1) {
            container.dataset.currentPage = (currentPage - 1).toString();
        }
        window.filterDuplicateTable();
    };

    window.nextDuplicatePage = function() {
        const container = document.querySelector('.duplicate-report-container');
        if (!container) return;

        const perPage = getDuplicatePerPage();
        const matched = container.querySelectorAll('.duplicate-row[data-matched="true"]').length;
        const totalPages = Math.max(1, Math.ceil(matched / perPage));
        let currentPage = parseInt(container.dataset.currentPage || '1', 10);

        if (currentPage < totalPages) {
            container.dataset.currentPage = (currentPage + 1).toString();
        }
        window.filterDuplicateTable();
    };

    function initDuplicateReport() {
        const container = document.querySelector('.duplicate-report-container');
        if (container) {
            container.dataset.currentPage = '1';
        }
        window.filterDuplicateTable();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDuplicateReport);
    } else {
        initDuplicateReport();
    }
</script>
