<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\User;
use App\Models\MutasiWarga;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class WargaController extends Controller
{
    /**
     * Halaman Utama Database Warga.
     * Untuk Admin RW: Master Database Warga (Statistik RW, Antrean Verifikasi RW, Card Grid RT, Pencarian Global).
     * Untuk Admin RT: Tampilan Data Warga tingkat RT.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        // ==========================================
        // 1. KHUSUS ADMIN RW: MASTER DATABASE WARGA
        // ==========================================
        if ($user->isAdminRw()) {
            // A. Ringkasan Statistik Kependudukan Aktual
            $totalWarga = Warga::where('status_warga', '!=', 'nonaktif')->count();
            $totalKk = Warga::where('status_warga', '!=', 'nonaktif')->where('is_kk', true)->count();
            $totalTerverifikasi = Warga::where('status_warga', '!=', 'nonaktif')->where('status_verifikasi', 'terverifikasi')->count();
            $totalMenunggu = Warga::where('status_verifikasi', 'menunggu')->count();
            $totalPerbaikan = Warga::where('status_verifikasi', 'perbaikan')->count();
            $totalDitolak = Warga::where('status_verifikasi', 'ditolak')->count();

            // Demografi Gender
            $totalLakiLaki = Warga::where('status_warga', '!=', 'nonaktif')->where('gender', 'L')->count();
            $totalPerempuan = Warga::where('status_warga', '!=', 'nonaktif')->where('gender', 'P')->count();

            // Demografi Kelompok Usia (Anak < 18, Dewasa 18-59, Lansia >= 60)
            $totalAnak = Warga::where('status_warga', '!=', 'nonaktif')
                ->whereNotNull('tanggal_lahir')
                ->where('tanggal_lahir', '>', now()->subYears(18)->format('Y-m-d'))
                ->count();
            $totalLansia = Warga::where('status_warga', '!=', 'nonaktif')
                ->whereNotNull('tanggal_lahir')
                ->where('tanggal_lahir', '<=', now()->subYears(60)->format('Y-m-d'))
                ->count();
            $totalDewasa = Warga::where('status_warga', '!=', 'nonaktif')
                ->whereNotNull('tanggal_lahir')
                ->whereBetween('tanggal_lahir', [now()->subYears(60)->addDay()->format('Y-m-d'), now()->subYears(18)->format('Y-m-d')])
                ->count();

            // B. Antrean Verifikasi Warga Tingkat RW
            $verifStatus = $request->input('status', 'menunggu');
            $verifQuery = Warga::query();
            if ($verifStatus !== 'semua' && $verifStatus !== '') {
                $verifQuery->where('status_verifikasi', $verifStatus);
            }
            if ($request->filled('search_verif')) {
                $sv = trim($request->search_verif);
                $verifQuery->where(function ($q) use ($sv) {
                    $q->where('nama', 'like', "%{$sv}%")
                      ->orWhere('nik', 'like', "%{$sv}%")
                      ->orWhere('no_kk', 'like', "%{$sv}%");
                });
            }
            if ($request->filled('rt_verif')) {
                $verifQuery->where('rt_number', $request->rt_verif);
            }

            // Urutkan pengajuan
            $antreanVerifikasi = $verifQuery
                ->with('user')
                ->orderByRaw("CASE WHEN status_verifikasi = 'menunggu' THEN 1 WHEN status_verifikasi = 'perbaikan' THEN 2 WHEN status_verifikasi = 'ditolak' THEN 3 ELSE 4 END")
                ->orderBy('updated_at', 'desc')
                ->paginate(6, ['*'], 'verif_page')
                ->withQueryString();

            // C. Daftar RT Seluruh Wilayah RW
            $rtListKnown = ['006', '007', '008', '009', '010'];
            $rtFromDb = Warga::select('rt_number')->distinct()->whereNotNull('rt_number')->pluck('rt_number')->toArray();
            $allRtNumbers = array_values(array_unique(array_merge($rtListKnown, $rtFromDb)));
            sort($allRtNumbers);

            $daftarRt = [];
            foreach ($allRtNumbers as $rt) {
                if ($rt === '000') continue; // Skip RW dummy code
                $rtPad = str_pad($rt, 3, '0', STR_PAD_LEFT);
                $ketuaUser = User::where(function($q) {
                    $q->where('role', 'admin')->orWhere('role', 'admin_rt');
                })->where('rt_number', $rtPad)->first();

                $wargaCount = Warga::where('rt_number', $rtPad)->where('status_warga', '!=', 'nonaktif')->count();
                $kkCount = Warga::where('rt_number', $rtPad)->where('status_warga', '!=', 'nonaktif')->where('is_kk', true)->count();
                $terverifikasiCount = Warga::where('rt_number', $rtPad)->where('status_verifikasi', 'terverifikasi')->count();
                $menungguCount = Warga::where('rt_number', $rtPad)->where('status_verifikasi', '!=', 'terverifikasi')->count();

                $daftarRt[] = [
                    'rt_number' => $rtPad,
                    'ketua_rt' => $ketuaUser ? $ketuaUser->name : 'Ketua RT ' . $rtPad,
                    'kontak_rt' => $ketuaUser ? ($ketuaUser->phone ?? '-') : '-',
                    'total_warga' => $wargaCount,
                    'total_kk' => $kkCount,
                    'total_terverifikasi' => $terverifikasiCount,
                    'total_menunggu' => $menungguCount,
                ];
            }

            // D. Pencarian Global Cepat
            $globalSearchResult = null;
            if ($request->filled('q')) {
                $keyword = trim($request->q);
                $globalSearchResult = Warga::with('user')->where(function($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('nik', 'like', "%{$keyword}%")
                      ->orWhere('no_kk', 'like', "%{$keyword}%")
                      ->orWhere('alamat', 'like', "%{$keyword}%")
                      ->orWhere('blok_rumah', 'like', "%{$keyword}%")
                      ->orWhereHas('user', function($uq) use ($keyword) {
                          $uq->where('email', 'like', "%{$keyword}%");
                      });
                })->orderBy('nama')->take(20)->get();
            }

            return view('warga.rw_database', compact(
                'totalWarga',
                'totalKk',
                'totalTerverifikasi',
                'totalMenunggu',
                'totalPerbaikan',
                'totalDitolak',
                'totalLakiLaki',
                'totalPerempuan',
                'totalAnak',
                'totalDewasa',
                'totalLansia',
                'antreanVerifikasi',
                'verifStatus',
                'daftarRt',
                'globalSearchResult'
            ));
        }

        // ==========================================
        // 2. KHUSUS ADMIN RT: DATA WARGA RT
        // ==========================================
        $viewMode = $request->input('view', 'family');
        $query = Warga::query()->where('rt_number', $user->rt_number);

        if ($viewMode === 'table') {
            $wargas = $query->with('user')->orderBy('nama', 'asc')->paginate(20);
        } else {
            $wargas = $query->where(function($q) {
                $q->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
            })->with(['user', 'anggotaKeluarga.user'])->orderBy('nama', 'asc')->paginate(10);
        }

        return view('warga.index', compact('wargas', 'viewMode'));
    }

    /**
     * Halaman Khusus Detail RT (Database Warga > RT 00X)
     */
    public function rtDetail(Request $request, string $rt)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $rtPad = str_pad($rt, 3, '0', STR_PAD_LEFT);

        if ($user->isAdminRt() && $user->rt_number !== $rtPad) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak: Anda hanya dapat melihat data RT Anda.');
        }

        // Data Ketua RT
        $ketuaUser = User::where(function($q) {
            $q->where('role', 'admin')->orWhere('role', 'admin_rt');
        })->where('rt_number', $rtPad)->first();

        // Statistik RT
        $rtStats = [
            'rt_number' => $rtPad,
            'ketua_rt' => $ketuaUser ? $ketuaUser->name : 'Ketua RT ' . $rtPad,
            'kontak_rt' => $ketuaUser ? ($ketuaUser->phone ?? '-') : '-',
            'total_warga' => Warga::where('rt_number', $rtPad)->where('status_warga', '!=', 'nonaktif')->count(),
            'total_kk' => Warga::where('rt_number', $rtPad)->where('status_warga', '!=', 'nonaktif')->where('is_kk', true)->count(),
            'total_terverifikasi' => Warga::where('rt_number', $rtPad)->where('status_verifikasi', 'terverifikasi')->count(),
            'total_belum_verifikasi' => Warga::where('rt_number', $rtPad)->where('status_verifikasi', '!=', 'terverifikasi')->count(),
        ];

        // Query Data Warga di RT ini
        $query = Warga::with('user')->where('rt_number', $rtPad);

        // Filter Pencarian Lokal RT
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('no_kk', 'like', "%{$s}%")
                  ->orWhere('blok_rumah', 'like', "%{$s}%")
                  ->orWhereHas('user', function($uq) use ($s) {
                      $uq->where('email', 'like', "%{$s}%");
                  });
            });
        }

        // Filter Status Verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter Status Kependudukan
        if ($request->filled('status_warga')) {
            $query->where('status_warga', $request->status_warga);
        }

        // Filter Jenis Kelamin
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter Hubungan Keluarga (Kepala Keluarga / Anggota)
        if ($request->filled('hubungan')) {
            if ($request->hubungan === 'kk') {
                $query->where('is_kk', true);
            } elseif ($request->hubungan === 'anggota') {
                $query->where('is_kk', false);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'usia_asc':
                $query->orderBy('tanggal_lahir', 'desc');
                break;
            case 'usia_desc':
                $query->orderBy('tanggal_lahir', 'asc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $wargas = $query->paginate(15)->withQueryString();

        return view('warga.rt_detail', compact('rtStats', 'wargas', 'rtPad'));
    }

    /**
     * Halaman Detail Profil Warga (Database Warga > RT 00X > Nama Warga)
     */
    public function show(string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            // Warga hanya boleh melihat profilnya sendiri atau keluarganya
            $userWarga = Warga::where('nik', $user->nik)->first();
            if (!$userWarga || ($userWarga->id != $id && $userWarga->no_kk !== Warga::find($id)?->no_kk)) {
                abort(403, 'Akses ditolak: Anda hanya dapat melihat data profil keluarga Anda sendiri.');
            }
        }

        $warga = Warga::with(['user', 'kepalaKeluarga.user', 'anggotaKeluarga.user', 'verifikator'])->findOrFail($id);

        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak: Warga berada di luar RT Anda.');
        }

        // Semua anggota keluarga dalam KK yang sama
        $semuaKeluarga = $warga->semua_anggota_keluarga;
        $kepalaKeluarga = $warga->is_kk ? $warga : ($warga->kepalaKeluarga ?? Warga::where('no_kk', $warga->no_kk)->where('is_kk', true)->first());

        // Data Ketua RT
        $ketuaUser = User::where(function($q) {
            $q->where('role', 'admin')->orWhere('role', 'admin_rt');
        })->where('rt_number', $warga->rt_number)->first();

        // Riwayat Mutasi Kependudukan
        $riwayatMutasi = MutasiWarga::with('creator')
            ->where(function($q) use ($warga) {
                $q->where('warga_id', $warga->id);
                if (!empty($warga->nik)) {
                    $q->orWhere('nik', $warga->nik);
                }
            })
            ->orderBy('tanggal_mutasi', 'desc')
            ->get();

        // Riwayat Aktivitas & Audit Log
        $auditLogs = AuditLog::where(function($q) use ($warga) {
                $q->where(function($sq) use ($warga) {
                    $sq->where('module', 'like', '%Warga%')
                       ->where('record_id', (string)$warga->id);
                })
                ->orWhere('description', 'like', "%{$warga->nama}%");
            })
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('warga.show', compact('warga', 'semuaKeluarga', 'kepalaKeluarga', 'ketuaUser', 'riwayatMutasi', 'auditLogs'));
    }

    /**
     * Keputusan Verifikasi Warga oleh Admin RW.
     */
    public function verifyRw(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->back()->with('error', 'Hanya Admin RW yang memiliki wewenang untuk tindakan verifikasi ini.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:terverifikasi,perbaikan,ditolak',
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        if (in_array($request->status_verifikasi, ['perbaikan', 'ditolak']) && empty(trim($request->catatan_verifikasi ?? ''))) {
            return redirect()->back()->with('error', 'Admin RW wajib memberikan catatan/alasan saat meminta perbaikan atau menolak pengajuan.');
        }

        $warga = Warga::findOrFail($id);
        $oldStatus = $warga->status_verifikasi;

        $warga->status_verifikasi = $request->status_verifikasi;
        $warga->catatan_verifikasi = $request->catatan_verifikasi;
        $warga->tanggal_verifikasi = now();
        $warga->verifikator_id = $user->id;
        $warga->save();

        // Audit Logging
        $eventMap = [
            'terverifikasi' => 'verify',
            'ditolak' => 'reject',
            'perbaikan' => 'revise',
        ];
        $event = $eventMap[$request->status_verifikasi] ?? 'verify';

        AuditLogger::log(
            $event,
            'Verifikasi Warga',
            $warga->id,
            "Pengajuan verifikasi kependudukan '{$warga->nama}' (RT {$warga->rt_number}) diproses dengan status " . strtoupper($request->status_verifikasi) . " oleh Admin RW {$user->name}. Catatan: " . ($request->catatan_verifikasi ?: '-'),
            ['status_verifikasi' => $oldStatus],
            ['status_verifikasi' => $warga->status_verifikasi, 'catatan' => $warga->catatan_verifikasi],
            $warga->rt_number
        );

        return redirect()->back()->with('success', "Keputusan verifikasi untuk warga '{$warga->nama}' berhasil disimpan (Status: " . strtoupper($request->status_verifikasi) . ").");
    }

    /**
     * Endpoint API JSON Pencarian Global Warga Lintas RT.
     */
    public function globalSearch(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga') {
            return response()->json([], 403);
        }

        $keyword = trim($request->query('q', ''));
        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $query = Warga::with('user');
        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        }

        $results = $query->where(function($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('nik', 'like', "%{$keyword}%")
              ->orWhere('no_kk', 'like', "%{$keyword}%")
              ->orWhere('alamat', 'like', "%{$keyword}%")
              ->orWhere('blok_rumah', 'like', "%{$keyword}%")
              ->orWhereHas('user', function($uq) use ($keyword) {
                  $uq->where('email', 'like', "%{$keyword}%");
              });
        })
        ->orderBy('nama')
        ->take(15)
        ->get()
        ->map(function($w) {
            $maskedNik = $w->nik ? (strlen($w->nik) >= 10 ? substr($w->nik, 0, 6) . '******' . substr($w->nik, -4) : $w->nik) : '-';
            $maskedKk = $w->no_kk ? (strlen($w->no_kk) >= 10 ? substr($w->no_kk, 0, 6) . '******' . substr($w->no_kk, -4) : $w->no_kk) : '-';

            return [
                'id' => $w->id,
                'nama' => $w->nama,
                'email' => $w->user ? $w->user->email : null,
                'nik_masked' => $maskedNik,
                'no_kk_masked' => $maskedKk,
                'rt_number' => $w->rt_number,
                'blok_rumah' => $w->blok_rumah,
                'gender' => $w->gender,
                'umur_formatted' => $w->umur_formatted,
                'status_verifikasi' => $w->status_verifikasi,
                'status_warga' => $w->status_warga,
                'profile_photo_url' => $w->profile_photo_url,
                'detail_url' => route('warga.show', $w->id),
            ];
        });

        return response()->json($results);
    }

    /**
     * Ekspor Data Kependudukan ke format CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $query = Warga::query()->where('status_warga', '!=', 'nonaktif');
        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt')) {
            $query->where('rt_number', str_pad($request->rt, 3, '0', STR_PAD_LEFT));
        }

        $filename = 'database_warga_' . ($request->filled('rt') ? 'rt_' . $request->rt . '_' : 'rw018_') . date('Ymd_His') . '.csv';
        $wargas = $query->orderBy('rt_number', 'asc')->orderBy('nama', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function() use ($wargas) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Nama Lengkap', 'NIK', 'No. KK', 'RT', 'RW', 'Blok Rumah',
                'Jenis Kelamin', 'Tanggal Lahir', 'Usia', 'Status Hubungan',
                'No. HP', 'Status Verifikasi', 'Status Kependudukan', 'Alamat Lengkap'
            ]);

            $no = 1;
            foreach ($wargas as $w) {
                fputcsv($handle, [
                    $no++,
                    $w->nama,
                    $w->nik ?: '-',
                    $w->no_kk ?: '-',
                    $w->rt_number,
                    $w->rw_number ?? '018',
                    $w->blok_rumah ?: '-',
                    $w->gender == 'L' ? 'Laki-laki' : ($w->gender == 'P' ? 'Perempuan' : '-'),
                    $w->tanggal_lahir ? Carbon::parse($w->tanggal_lahir)->format('Y-m-d') : '-',
                    $w->umur_formatted,
                    $w->status_hubungan,
                    $w->nomor_hp ?: '-',
                    ucfirst($w->status_verifikasi),
                    ucfirst($w->status_warga),
                    $w->alamat ?: '-',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $query = Warga::where(function($q) {
            $q->where('is_kk', true)
              ->orWhere('is_kk', 1)
              ->orWhere('is_kk', '1');
        });

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        }

        $kepalaKeluargas = $query->orderBy('nama', 'asc')->get();

        return view('warga.create', compact('kepalaKeluargas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw_number') && $request->rw_number != '') {
            $request->merge(['rw_number' => str_pad($request->rw_number, 3, '0', STR_PAD_LEFT)]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                'unique:users,email',
            ],
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'rfid_uid' => 'nullable|string|max:64|unique:wargas,rfid_uid',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'alamat' => 'required|string',
        ], [
            'email.regex' => 'Alamat email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar oleh pengguna lain.',
        ]);

        if ($user->isAdminRt() && $request->rt_number !== $user->rt_number) {
            return redirect()->back()->withErrors(['rt_number' => 'Anda hanya bisa menambahkan warga ke RT Anda.']);
        }

        $data = $request->only([
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'rfid_uid', 'gender',
            'tanggal_lahir', 'tempat_lahir', 'agama', 'status_perkawinan', 'pekerjaan',
            'rt_number', 'rw_number', 'is_kk', 'alamat'
        ]);

        $data['kk_id'] = $request->is_kk ? null : $request->kk_id;
        $data['status_verifikasi'] = $user->isAdminRw() ? 'terverifikasi' : 'menunggu';
        $data['status_warga'] = 'aktif';

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        $warga = Warga::create($data);

        // Jika email diisi, buat akun pengguna terkait
        if ($request->filled('email')) {
            $userAccount = User::create([
                'name' => $warga->nama,
                'email' => strtolower(trim($request->email)),
                'password' => bcrypt('password123'),
                'role' => 'warga',
                'rt_number' => $warga->rt_number,
                'rw_number' => $warga->rw_number ?? '018',
                'phone' => $warga->nomor_hp,
                'nik' => $warga->nik,
                'no_kk' => $warga->no_kk,
                'address' => $warga->alamat,
                'warga_id' => $warga->id,
            ]);
            $warga->update(['user_id' => $userAccount->id]);
        }

        // Audit Logging
        AuditLogger::log(
            'create',
            'Warga',
            $warga->id,
            "Pendaftaran warga baru '{$warga->nama}' (RT {$warga->rt_number}) berhasil disimpan oleh {$user->name}.",
            null,
            $warga->toArray(),
            $warga->rt_number
        );

        if ($user->isAdminRw()) {
            return redirect()->route('warga.rt', $warga->rt_number)->with('success', 'Data warga berhasil ditambahkan.');
        }

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $warga = Warga::findOrFail($id);
        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

        $query = Warga::where(function($q) {
            $q->where('is_kk', true)
              ->orWhere('is_kk', 1)
              ->orWhere('is_kk', '1');
        })->where('id', '!=', $id);

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        }

        $kepalaKeluargas = $query->orderBy('nama', 'asc')->get();

        return view('warga.edit', compact('warga', 'kepalaKeluargas'));
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $warga = Warga::findOrFail($id);
        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw_number') && $request->rw_number != '') {
            $request->merge(['rw_number' => str_pad($request->rw_number, 3, '0', STR_PAD_LEFT)]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $existingUserId = $warga->user_id ?: ($warga->user?->id ?? null);
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                Rule::unique('users', 'email')->ignore($existingUserId),
            ],
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'rfid_uid' => ['nullable', 'string', 'max:64', Rule::unique('wargas', 'rfid_uid')->ignore($warga->id)],
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'alamat' => 'required|string',
        ], [
            'email.regex' => 'Alamat email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
        ]);

        if ($user->isAdminRt() && $request->rt_number !== $user->rt_number) {
            return redirect()->back()->withErrors(['rt_number' => 'Anda tidak bisa mengubah RT ke luar wewenang Anda.']);
        }

        $oldValues = $warga->toArray();

        $data = $request->only([
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'rfid_uid', 'gender',
            'tanggal_lahir', 'tempat_lahir', 'agama', 'status_perkawinan', 'pekerjaan',
            'rt_number', 'rw_number', 'is_kk', 'alamat', 'status_warga'
        ]);

        $data['kk_id'] = $request->is_kk ? null : $request->kk_id;

        if ($request->hasFile('profile_photo')) {
            if ($warga->profile_photo && Storage::disk('public')->exists($warga->profile_photo)) {
                Storage::disk('public')->delete($warga->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        $warga->update($data);

        // Jika email diisi, perbarui atau buat akun pengguna terkait
        if ($request->filled('email')) {
            $emailClean = strtolower(trim($request->email));
            if ($warga->user) {
                $warga->user->update([
                    'email' => $emailClean,
                    'name' => $warga->nama,
                    'phone' => $warga->nomor_hp,
                    'rt_number' => $warga->rt_number,
                    'no_kk' => $warga->no_kk,
                    'nik' => $warga->nik,
                    'address' => $warga->alamat,
                ]);
            } else {
                $userAccount = User::create([
                    'name' => $warga->nama,
                    'email' => $emailClean,
                    'password' => bcrypt('password123'),
                    'role' => 'warga',
                    'rt_number' => $warga->rt_number,
                    'rw_number' => $warga->rw_number ?? '018',
                    'phone' => $warga->nomor_hp,
                    'nik' => $warga->nik,
                    'no_kk' => $warga->no_kk,
                    'address' => $warga->alamat,
                    'warga_id' => $warga->id,
                ]);
                $warga->update(['user_id' => $userAccount->id]);
            }
        }

        // Audit Logging
        AuditLogger::log(
            'update',
            'Warga',
            $warga->id,
            "Pembaruan data kependudukan warga '{$warga->nama}' (RT {$warga->rt_number}) dilakukan oleh {$user->name}.",
            $oldValues,
            $warga->toArray(),
            $warga->rt_number
        );

        return redirect()->route('warga.show', $warga->id)->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $warga = Warga::findOrFail($id);
        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

        // Arsipkan data warga tanpa menghapus riwayat kependudukan permanen
        $warga->status_warga = 'nonaktif';
        $warga->save();

        AuditLogger::log(
            'delete',
            'Warga',
            $warga->id,
            "Data warga '{$warga->nama}' (RT {$warga->rt_number}) diarsipkan/dinonaktifkan oleh {$user->name}.",
            null,
            ['status_warga' => 'nonaktif'],
            $warga->rt_number
        );

        if ($user->isAdminRw()) {
            return redirect()->route('warga.rt', $warga->rt_number)->with('success', "Data warga '{$warga->nama}' berhasil diarsipkan.");
        }

        return redirect()->route('warga.index')->with('success', "Data warga '{$warga->nama}' berhasil diarsipkan.");
    }

    /**
     * Endpoint untuk menerima kiriman data UID E-KTP dari NodeMCU ESP32 + RFID RC522.
     * Mendukung POST maupun GET: /api/rfid/scan?uid=A1B2C3D4
     */
    public function receiveRfidScan(Request $request)
    {
        $rawUid = $request->input('uid') ?? $request->input('rfid_uid') ?? $request->input('card_uid');

        if (!$rawUid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter uid wajib dikirim oleh NodeMCU ESP32.',
            ], 422);
        }

        // Normalisasi UID (huruf besar, hanya karakter hex/alphanumeric tanpa spasi/titik dua)
        $uid = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($rawUid)));

        $existingWarga = Warga::where('rfid_uid', $uid)->first();

        $scanPayload = [
            'uid' => $uid,
            'device_id' => $request->input('device_id', 'ESP32-RC522'),
            'scanned_at' => now()->toIso8601String(),
            'timestamp_ms' => round(microtime(true) * 1000),
            'registered_warga' => $existingWarga ? [
                'id' => $existingWarga->id,
                'nama' => $existingWarga->nama,
                'rt_number' => $existingWarga->rt_number,
            ] : null,
        ];

        // Simpan di cache selama 5 menit agar modal admin dapat mengambil hasil tap terbaru
        Cache::put('rfid_last_scan', $scanPayload, now()->addMinutes(5));

        return response()->json([
            'status' => 'success',
            'message' => 'UID E-KTP berhasil diterima dari RC522.',
            'data' => $scanPayload,
        ]);
    }

    /**
     * Endpoint polling untuk modal Rekam E-KTP di halaman Admin Data Warga.
     */
    public function getLatestRfidScan(Request $request)
    {
        if (Auth::user()->role === 'warga') {
            return response()->json(['status' => 'forbidden'], 403);
        }

        $scan = Cache::get('rfid_last_scan');
        $since = (float) $request->query('since', 0);

        if (!$scan || ($since > 0 && $scan['timestamp_ms'] <= $since)) {
            return response()->json([
                'status' => 'waiting',
                'data' => null,
            ]);
        }

        return response()->json([
            'status' => 'detected',
            'data' => $scan,
        ]);
    }

    /**
     * Menyimpan / mengaitkan UID E-KTP hasil scan RC522 ke data Warga tertentu.
     */
    public function storeRfidKtp(Request $request, string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $warga = Warga::findOrFail($id);

        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak: Warga berada di luar RT Anda.');
        }

        $rawUid = $request->input('rfid_uid', '');
        $normalizedUid = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($rawUid)));
        $request->merge(['rfid_uid' => $normalizedUid]);

        $request->validate([
            'rfid_uid' => [
                'required',
                'string',
                'min:4',
                'max:64',
                Rule::unique('wargas', 'rfid_uid')->ignore($warga->id),
            ],
            'nik' => 'nullable|string|max:25',
        ], [
            'rfid_uid.required' => 'UID E-KTP belum terbaca. Silakan tempelkan E-KTP pada reader RFID RC522.',
            'rfid_uid.unique' => 'Kartu E-KTP (UID) ini sudah terdaftar pada warga lain.',
        ]);

        $updateData = ['rfid_uid' => $normalizedUid];
        if ($request->filled('nik')) {
            $updateData['nik'] = $request->nik;
        }

        $warga->update($updateData);

        // Bersihkan cache scan terakhir setelah berhasil direkam
        Cache::forget('rfid_last_scan');

        return redirect()->back()->with('success', "E-KTP (UID: {$normalizedUid}) berhasil direkam untuk {$warga->nama}.");
    }
}
