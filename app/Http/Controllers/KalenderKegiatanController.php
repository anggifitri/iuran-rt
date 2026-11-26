<?php

namespace App\Http\Controllers;

use App\Models\AgendaRapat;
use App\Models\JadwalRonda;
use App\Models\PeminjamanFasilitas;
use App\Models\Posyandu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderKegiatanController extends Controller
{
    /**
     * Display the centralized calendar and agenda for RW 018.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRw = $user->isRw();
        $isAdminRt = $user->isAdminRt();
        $isWarga = $user->isWarga();

        // 1. Month & Year Resolution
        $bulan = str_pad($request->get('bulan', date('m')), 2, '0', STR_PAD_LEFT);
        $tahun = (int) $request->get('tahun', date('Y'));
        $filterKategori = $request->get('kategori', '');
        $filterRt = $request->get('rt_number', '');
        $search = trim($request->get('search', ''));

        // Prevent invalid months
        if ((int)$bulan < 1 || (int)$bulan > 12) {
            $bulan = date('m');
        }

        $currentMonthCarbon = Carbon::createFromDate($tahun, (int)$bulan, 1);
        $prevCarbon = $currentMonthCarbon->copy()->subMonth();
        $nextCarbon = $currentMonthCarbon->copy()->addMonth();

        $prevMonth = $prevCarbon->format('m');
        $prevYear = $prevCarbon->format('Y');
        $nextMonth = $nextCarbon->format('m');
        $nextYear = $nextCarbon->format('Y');

        $startDate = $currentMonthCarbon->copy()->startOfMonth();
        $endDate = $currentMonthCarbon->copy()->endOfMonth();

        $events = collect();

        // ==========================================
        // 2. FETCH AGENDAS FROM INTEGRATED MODULES
        // ==========================================

        // A. Agenda & Rapat (Rapat & Musyawarah)
        if (empty($filterKategori) || $filterKategori === 'rapat') {
            $agendaQuery = AgendaRapat::whereBetween('tanggal_waktu', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

            // RBAC Filters
            if ($isWarga) {
                $agendaQuery->where(function ($q) {
                    $q->where('jenis_rapat', 'musyawarah_warga')
                      ->orWhere('target_peserta', 'like', '%warga%')
                      ->orWhere('target_peserta', 'like', '%terbuka%');
                });
            } elseif ($isAdminRt) {
                $agendaQuery->where(function ($q) use ($user) {
                    $q->where('jenis_rapat', 'rw')
                      ->orWhere('target_peserta', 'like', '%RT%')
                      ->orWhere('target_peserta', 'like', '%warga%')
                      ->orWhere('target_peserta', 'like', '%terbuka%')
                      ->orWhere('rt_number', $user->rt_number)
                      ->orWhere('created_by', $user->id);
                });
            }

            if ($filterRt) {
                $agendaQuery->where(function ($q) use ($filterRt) {
                    $q->where('rt_number', $filterRt)->orWhereNull('rt_number');
                });
            }

            $agendas = $agendaQuery->get();
            foreach ($agendas as $ag) {
                $startCarbon = Carbon::parse($ag->tanggal_waktu);
                $timeStr = $startCarbon->format('H:i');
                if ($ag->waktu_selesai) {
                    $timeStr .= ' - ' . Carbon::parse($ag->waktu_selesai)->format('H:i');
                }
                $timeStr .= ' WIB';

                $events->push([
                    'id' => 'agenda_' . $ag->id,
                    'raw_id' => $ag->id,
                    'title' => $ag->judul,
                    'category' => 'rapat',
                    'category_label' => 'Rapat & Musyawarah',
                    'category_color' => '#285943',
                    'category_bg' => '#DCEBDD',
                    'category_border' => '#A8C9B5',
                    'date' => $startCarbon->format('Y-m-d'),
                    'time_start' => $startCarbon->format('H:i'),
                    'time' => $timeStr,
                    'location' => $ag->lokasi,
                    'rt_number' => $ag->rt_number ? 'RT ' . $ag->rt_number : 'RW 018 (Pleno)',
                    'organizer' => $ag->penyelenggara ?: 'Pengurus RW 018',
                    'target_audience' => $ag->target_peserta,
                    'status' => $ag->status,
                    'status_label' => match($ag->status) {
                        'selesai' => 'Selesai',
                        'berlangsung' => 'Sedang Berlangsung',
                        'dibatalkan' => 'Dibatalkan',
                        default => 'Akan Datang',
                    },
                    'description' => $ag->deskripsi ?: $ag->agenda_pembahasan,
                    'link' => route('agenda.show', $ag->id),
                    'source_name' => 'Agenda & Rapat',
                    'is_cancelled' => $ag->status === 'dibatalkan',
                ]);
            }
        }

        // B. Ronda Malam (JadwalRonda)
        if (empty($filterKategori) || $filterKategori === 'ronda') {
            $rondaQuery = JadwalRonda::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

            // RBAC Filters
            if ($isWarga) {
                $rondaQuery->where('rt_number', $user->rt_number);
            } elseif ($isAdminRt && !$filterRt) {
                // By default RT sees their own RT unless RW
                $rondaQuery->where('rt_number', $user->rt_number);
            }

            if ($filterRt) {
                $rondaQuery->where('rt_number', $filterRt);
            }

            $rondas = $rondaQuery->get();
            foreach ($rondas as $rd) {
                $events->push([
                    'id' => 'ronda_' . $rd->id,
                    'raw_id' => $rd->id,
                    'title' => "Ronda Siskamling RT {$rd->rt_number}",
                    'category' => 'ronda',
                    'category_label' => 'Ronda Malam',
                    'category_color' => '#1E4B6D',
                    'category_bg' => '#DCEAF5',
                    'category_border' => '#A9C9E8',
                    'date' => $rd->tanggal,
                    'time_start' => '22:00',
                    'time' => $rd->shift ?: '22:00 - 04:00 WIB',
                    'location' => $rd->lokasi_pos ?: "Pos Ronda RT {$rd->rt_number}",
                    'rt_number' => 'RT ' . $rd->rt_number,
                    'organizer' => "Pengurus RT {$rd->rt_number}",
                    'target_audience' => "Petugas: {$rd->petugas}",
                    'status' => $rd->status ?: 'terjadwal',
                    'status_label' => match($rd->status) {
                        'selesai' => 'Selesai',
                        'berlangsung' => 'Sedang Berlangsung',
                        'dibatalkan' => 'Dibatalkan',
                        default => 'Terjadwal',
                    },
                    'description' => "Petugas Jaga Ronda: {$rd->petugas}. Keterangan: " . ($rd->keterangan ?: 'Patroli pengamanan lingkungan warga rutin.'),
                    'link' => route('ronda.index', ['rt_number' => $rd->rt_number]),
                    'source_name' => 'Ronda & Keamanan',
                    'is_cancelled' => $rd->status === 'dibatalkan',
                ]);
            }
        }

        // C. Peminjaman Fasilitas Umum (Hanya yang status = disetujui)
        if (empty($filterKategori) || $filterKategori === 'fasilitas') {
            $bookingQuery = PeminjamanFasilitas::with('fasilitas')
                ->where('status', 'disetujui')
                ->whereBetween('waktu_mulai', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

            if ($filterRt) {
                $bookingQuery->where('rt_number', $filterRt);
            }

            $bookings = $bookingQuery->get();
            foreach ($bookings as $bk) {
                $startBk = Carbon::parse($bk->waktu_mulai);
                $endBk = Carbon::parse($bk->waktu_selesai);

                $events->push([
                    'id' => 'fasilitas_' . $bk->id,
                    'raw_id' => $bk->id,
                    'title' => "Pemakaian " . ($bk->fasilitas ? $bk->fasilitas->nama : 'Fasilitas') . ": {$bk->tujuan}",
                    'category' => 'fasilitas',
                    'category_label' => 'Peminjaman Fasilitas',
                    'category_color' => '#854D0E',
                    'category_bg' => '#FEF3C7',
                    'category_border' => '#FDE68A',
                    'date' => $startBk->format('Y-m-d'),
                    'time_start' => $startBk->format('H:i'),
                    'time' => $startBk->format('H:i') . ' - ' . $endBk->format('H:i') . ' WIB',
                    'location' => ($bk->fasilitas ? $bk->fasilitas->nama : 'Fasilitas') . ' (' . ($bk->fasilitas ? $bk->fasilitas->lokasi : 'RW 018') . ')',
                    'rt_number' => 'RT ' . $bk->rt_number,
                    'organizer' => "Peminjam: {$bk->nama_peminjam}",
                    'target_audience' => "Kontak: {$bk->nomor_kontak}",
                    'status' => 'disetujui',
                    'status_label' => 'Terkonfirmasi Disetujui',
                    'description' => "Reservasi Disetujui: {$bk->tujuan}. Digunakan oleh warga {$bk->nama_peminjam} ({$bk->nomor_kontak}). Fasilitas terkonfirmasi tidak dapat dibooking pihak lain pada jam ini.",
                    'link' => route('fasilitas.index'),
                    'source_name' => 'Peminjaman Fasilitas',
                    'is_cancelled' => false,
                ]);
            }
        }

        // D. Info Posyandu
        if (empty($filterKategori) || $filterKategori === 'posyandu') {
            $posyandus = Posyandu::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->get();
            foreach ($posyandus as $ps) {
                $events->push([
                    'id' => 'posyandu_' . $ps->id,
                    'raw_id' => $ps->id,
                    'title' => $ps->nama,
                    'category' => 'posyandu',
                    'category_label' => 'Posyandu',
                    'category_color' => '#9D174D',
                    'category_bg' => '#FFE4E6',
                    'category_border' => '#FECDD3',
                    'date' => $ps->tanggal,
                    'time_start' => '08:30',
                    'time' => '08:30 - 11:30 WIB',
                    'location' => $ps->lokasi ?: 'Balai RW 018',
                    'rt_number' => 'RW 018',
                    'organizer' => 'Kader Posyandu Bougenville',
                    'target_audience' => 'Balita, Bumil, dan Lansia',
                    'status' => 'terjadwal',
                    'status_label' => 'Jadwal Rutin',
                    'description' => $ps->keterangan ?: 'Pemeriksaan rutin kesehatan balita, penimbangan berat badan, imunisasi, dan senam lansia sehat.',
                    'link' => route('posyandu.index'),
                    'source_name' => 'Info Posyandu',
                    'is_cancelled' => false,
                ]);
            }
        }

        // E. Kegiatan Terjadwal dari Pusat Pengumuman
        if (empty($filterKategori) || $filterKategori === 'kegiatan' || $filterKategori === 'pengumuman') {
            $pengumumanQuery = \App\Models\Pengumuman::where('is_kegiatan', true)
                ->where('status', 'published')
                ->where('is_archived', false)
                ->whereNotNull('kegiatan_tanggal')
                ->whereBetween('kegiatan_tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

            if ($isWarga) {
                $pengumumanQuery->where(function ($q) use ($user) {
                    $q->where('target_rt', 'all')->orWhere('target_rt', $user->rt_number);
                });
            } elseif ($isAdminRt && !$filterRt) {
                $pengumumanQuery->where(function ($q) use ($user) {
                    $q->where('target_rt', 'all')->orWhere('target_rt', $user->rt_number);
                });
            }

            if ($filterRt) {
                $pengumumanQuery->where(function ($q) use ($filterRt) {
                    $q->where('target_rt', $filterRt)->orWhere('target_rt', 'all');
                });
            }

            $pengumumanKegiatans = $pengumumanQuery->get();
            foreach ($pengumumanKegiatans as $pk) {
                // If it already created an AgendaRapat that was fetched in section A, skip to avoid duplicate
                if ($pk->agenda_rapat_id && $events->contains('id', 'agenda_' . $pk->agenda_rapat_id)) {
                    continue;
                }

                $timeStr = ($pk->kegiatan_waktu_mulai ?: '08:00') . ($pk->kegiatan_waktu_selesai ? ' - ' . $pk->kegiatan_waktu_selesai : '') . ' WIB';

                $events->push([
                    'id' => 'pengumuman_' . $pk->id,
                    'raw_id' => $pk->id,
                    'title' => $pk->title,
                    'category' => 'kegiatan',
                    'category_label' => 'Kegiatan Warga (Pengumuman)',
                    'category_color' => '#15803D',
                    'category_bg' => '#DCFCE7',
                    'category_border' => '#86EFAC',
                    'date' => $pk->kegiatan_tanggal->format('Y-m-d'),
                    'time_start' => $pk->kegiatan_waktu_mulai ?: '08:00',
                    'time' => $timeStr,
                    'location' => $pk->kegiatan_lokasi ?: 'Lingkungan RW 018',
                    'rt_number' => $pk->target_rt === 'all' ? 'Seluruh RW 018' : 'RT ' . $pk->target_rt,
                    'organizer' => $pk->creator ? $pk->creator->name : 'Pengurus Lingkungan',
                    'target_audience' => $pk->target_teks,
                    'status' => 'terjadwal',
                    'status_label' => 'Pengumuman Resmi',
                    'description' => \Illuminate\Support\Str::limit(strip_tags($pk->content), 120),
                    'link' => route('pengumuman.show', $pk->id),
                    'source_name' => 'Pusat Pengumuman',
                    'is_cancelled' => false,
                ]);
            }
        }

        // ==========================================
        // 3. SEARCH FILTER (IF USER SEARCHED)
        // ==========================================
        if (!empty($search)) {
            $events = $events->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['title']), strtolower($search))
                    || str_contains(strtolower($item['location']), strtolower($search))
                    || str_contains(strtolower($item['organizer']), strtolower($search))
                    || str_contains(strtolower($item['description']), strtolower($search));
            });
        }

        // ==========================================
        // 4. SORT CHRONOLOGICALLY & METRICS
        // ==========================================
        $sortedEvents = $events->sortBy(function ($item) {
            return $item['date'] . ' ' . $item['time_start'];
        })->values();

        $eventsByDate = $sortedEvents->groupBy('date');

        // Dynamic 3 Stat Cards (from actual database results)
        $todayStr = date('Y-m-d');
        $totalBulanIni = $sortedEvents->where('is_cancelled', false)->count();
        $totalHariIni = $sortedEvents->where('date', $todayStr)->where('is_cancelled', false)->count();
        $totalMendatang = $sortedEvents->where('date', '>=', $todayStr)->where('is_cancelled', false)->count();

        // ==========================================
        // 5. CONSTRUCT 7-COLUMN MONTHLY GRID WEEKS
        // ==========================================
        $calendarWeeks = [];
        $firstDayOfMonth = $startDate->copy();
        $startDayOfWeekIso = $firstDayOfMonth->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        
        // Start date of grid (including trailing days from previous month)
        $gridStart = $firstDayOfMonth->copy()->subDays($startDayOfWeekIso - 1);
        $lastDayOfMonth = $endDate->copy();
        $endDayOfWeekIso = $lastDayOfMonth->dayOfWeekIso;
        
        // End date of grid (including leading days from next month)
        $gridEnd = $lastDayOfMonth->copy()->addDays(7 - $endDayOfWeekIso);

        $cursor = $gridStart->copy();
        $currentWeek = [];

        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->format('Y-m-d');
            $dayEvents = $eventsByDate->get($dateStr, collect());

            $currentWeek[] = [
                'carbon' => $cursor->copy(),
                'date' => $dateStr,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $cursor->month === (int)$bulan && $cursor->year === $tahun,
                'isToday' => $dateStr === $todayStr,
                'isSunday' => $cursor->dayOfWeekIso === 7,
                'events' => $dayEvents,
                'eventCount' => $dayEvents->count(),
            ];

            if ($cursor->dayOfWeekIso === 7) {
                $calendarWeeks[] = $currentWeek;
                $currentWeek = [];
            }

            $cursor->addDay();
        }

        $rtList = ['006', '007', '008', '009', '010'];

        return view('kalender.index', compact(
            'sortedEvents',
            'eventsByDate',
            'calendarWeeks',
            'bulan',
            'tahun',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'filterKategori',
            'filterRt',
            'search',
            'totalBulanIni',
            'totalHariIni',
            'totalMendatang',
            'rtList'
        ));
    }
}
