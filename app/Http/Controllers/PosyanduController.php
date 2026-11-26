<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\PosyanduAnak;
use App\Models\PosyanduAnakPemeriksaan;
use App\Models\PosyanduAnakImunisasi;
use App\Models\PosyanduBumil;
use App\Models\PosyanduBumilPemeriksaan;
use App\Models\PosyanduPetugas;
use App\Models\Warga;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use App\Services\WhoGrowthStandardsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosyanduController extends Controller
{
    /**
     * Helper to verify if user has administrative or health worker rights.
     */
    protected function isOfficer(?string $rt = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->isWarga()) return false;
        return $user->isPosyanduOfficer($rt);
    }

    /**
     * Resolve family warga IDs for current authenticated user.
     */
    protected function getFamilyWargaIds(): array
    {
        $user = Auth::user();
        if (!$user) return [];
        return $user->familyWargaIds();
    }

    /**
     * Main Dashboard: Statistics, Activity Calendar, Balita & Bumil Summaries.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isWarga = $user && $user->isWarga();
        $isAdminRw = $user && $user->isAdminRw();
        $isAdminRt = $user && $user->isAdminRt();
        $isKader = !$isWarga && $user && $user->isPosyanduKader();
        $isNakes = !$isWarga && $user && $user->isPosyanduNakes();
        $isOfficer = $isAdminRw || $isKader || $isNakes;
        $familyWargaIds = $this->getFamilyWargaIds();

        // 1. Dashboard Metrics (Database Driven)
        $totalBalita = PosyanduAnak::count();
        $totalBumil = PosyanduBumil::count();

        $curMonth = now()->month;
        $curYear = now()->year;
        $pemeriksaanBulanIni = PosyanduAnakPemeriksaan::whereMonth('tanggal_pemeriksaan', $curMonth)
            ->whereYear('tanggal_pemeriksaan', $curYear)->count()
            + PosyanduBumilPemeriksaan::whereMonth('tanggal_pemeriksaan', $curMonth)
            ->whereYear('tanggal_pemeriksaan', $curYear)->count();

        $totalImunisasiDiberikan = PosyanduAnakImunisasi::where('status', 'Sudah Diberikan')->count();
        $jadwalMendatang = Posyandu::whereDate('tanggal', '>=', now())
            ->where('status', '!=', 'dibatalkan')->count();

        $balitaTindakLanjutQuery = PosyanduAnak::where('perlu_tindak_lanjut', true)
            ->with('latestPemeriksaan');

        $bumilTindakLanjutQuery = PosyanduBumil::where('perlu_tindak_lanjut', true)
            ->with('latestPemeriksaan');

        if ($isWarga) {
            $userWarga = $user ? $user->resolveWarga() : null;
            $userNoKk = $userWarga ? $userWarga->no_kk : ($user->no_kk ?? null);

            if (!empty($userNoKk) && !empty(trim($userNoKk)) && !empty($familyWargaIds)) {
                $balitaTindakLanjutQuery->whereIn('warga_id', $familyWargaIds);
                $bumilTindakLanjutQuery->whereIn('warga_id', $familyWargaIds);
            } else {
                $balitaTindakLanjutQuery->whereRaw('1 = 0');
                $bumilTindakLanjutQuery->whereRaw('1 = 0');
            }
        }

        $balitaTindakLanjut = $balitaTindakLanjutQuery->orderBy('updated_at', 'desc')->get();
        $bumilTindakLanjut = $bumilTindakLanjutQuery->orderBy('updated_at', 'desc')->get();

        $perluTindakLanjutCount = $balitaTindakLanjut->count() + $bumilTindakLanjut->count();

        // 2. Activity Calendar Query with filters & sorting
        $jadwalQuery = Posyandu::query();

        if ($request->filled('status_jadwal') && $request->status_jadwal !== 'semua') {
            $jadwalQuery->where('status', $request->status_jadwal);
        }

        if ($request->filled('search_jadwal')) {
            $s = $request->search_jadwal;
            $jadwalQuery->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('lokasi', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        $jadwal = $jadwalQuery->orderBy('tanggal', 'asc')->get();

        // 3. Balita Query
        $balitaQuery = PosyanduAnak::with(['latestPemeriksaan.verifiedNakes']);

        // Data Privacy: Jika user adalah Warga, kunci ketat HANYA balita dalam 1 KK yang sama
        if ($isWarga) {
            $userWarga = $user ? $user->resolveWarga() : null;
            $userNoKk = $userWarga ? $userWarga->no_kk : ($user->no_kk ?? null);

            if (!empty($userNoKk) && !empty(trim($userNoKk)) && !empty($familyWargaIds)) {
                $balitaQuery->whereIn('warga_id', $familyWargaIds);
            } else {
                $balitaQuery->whereRaw('1 = 0');
            }
        } elseif ($request->filled('rt_anak') && $request->rt_anak !== 'semua') {
            $balitaQuery->where('rt_number', $request->rt_anak);
        }

        if ($request->filled('status_anak') && $request->status_anak !== 'semua') {
            if ($request->status_anak === 'tindak_lanjut') {
                $balitaQuery->where('perlu_tindak_lanjut', true);
            } else {
                $balitaQuery->where('perlu_tindak_lanjut', false);
            }
        }

        if (!$isWarga && $request->filled('search_anak')) {
            $s = $request->search_anak;
            $balitaQuery->where(function ($q) use ($s) {
                $q->where('nama_anak', 'like', "%{$s}%")
                  ->orWhere('nama_ibu', 'like', "%{$s}%")
                  ->orWhere('nama_ayah', 'like', "%{$s}%");
            });
        }

        $anak = $balitaQuery->orderBy('nama_anak', 'asc')->get();

        // 4. Ibu Hamil Query
        $bumilQuery = PosyanduBumil::with(['latestPemeriksaan.verifiedNakes']);

        // Data Privacy: Jika user adalah Warga, kunci ketat HANYA ibu hamil dalam 1 KK yang sama
        if ($isWarga) {
            $userWarga = $user ? $user->resolveWarga() : null;
            $userNoKk = $userWarga ? $userWarga->no_kk : ($user->no_kk ?? null);

            if (!empty($userNoKk) && !empty(trim($userNoKk)) && !empty($familyWargaIds)) {
                $bumilQuery->whereIn('warga_id', $familyWargaIds);
            } else {
                $bumilQuery->whereRaw('1 = 0');
            }
        } elseif ($request->filled('rt_bumil') && $request->rt_bumil !== 'semua') {
            $bumilQuery->where('rt_number', $request->rt_bumil);
        }

        if ($request->filled('status_bumil') && $request->status_bumil !== 'semua') {
            if ($request->status_bumil === 'tindak_lanjut') {
                $bumilQuery->where('perlu_tindak_lanjut', true);
            } else {
                $bumilQuery->where('perlu_tindak_lanjut', false);
            }
        }

        if (!$isWarga && $request->filled('search_bumil')) {
            $s = $request->search_bumil;
            $bumilQuery->where(function ($q) use ($s) {
                $q->where('nama_ibu', 'like', "%{$s}%")
                  ->orWhere('nama_suami', 'like', "%{$s}%");
            });
        }

        $bumils = $bumilQuery->orderBy('nama_ibu', 'asc')->get();

        // 5. Warga Personal Family Lists (Privacy-safe)
        $myBalitas = collect();
        $myBumils = collect();
        if ($isWarga) {
            $userWarga = $user ? $user->resolveWarga() : null;
            $userNoKk = $userWarga ? $userWarga->no_kk : ($user->no_kk ?? null);
            if (!empty($userNoKk) && !empty(trim($userNoKk)) && !empty($familyWargaIds)) {
                $myBalitas = PosyanduAnak::whereIn('warga_id', $familyWargaIds)->get();
                $myBumils = PosyanduBumil::whereIn('warga_id', $familyWargaIds)->get();
            }
        }

        // Active Petugas Count (for Admin RW badge)
        $totalPetugasAktif = PosyanduPetugas::aktifValid()->count();

        $rtList = ['006', '007', '008', '009', '010'];

        return view('posyandu.index', compact(
            'totalBalita',
            'totalBumil',
            'pemeriksaanBulanIni',
            'totalImunisasiDiberikan',
            'jadwalMendatang',
            'perluTindakLanjutCount',
            'balitaTindakLanjut',
            'bumilTindakLanjut',
            'jadwal',
            'anak',
            'bumils',
            'myBalitas',
            'myBumils',
            'isOfficer',
            'isAdminRw',
            'isAdminRt',
            'isKader',
            'isNakes',
            'isWarga',
            'totalPetugasAktif',
            'familyWargaIds',
            'rtList'
        ));
    }

    /**
     * Individual Child (Balita) Health Profile & Interactive KMS Digital.
     */
    public function showBalita(Request $request, PosyanduAnak $anak)
    {
        $user = Auth::user();
        $isWarga = $user && $user->isWarga();
        $isAdminRw = $user && $user->isAdminRw();
        $isKader = !$isWarga && $user && $user->isPosyanduKader($anak->rt_number);
        $isNakes = !$isWarga && $user && $user->isPosyanduNakes($anak->rt_number);
        $isOfficer = $isAdminRw || $isKader || $isNakes;
        $familyWargaIds = $this->getFamilyWargaIds();

        // RBAC Access Control check:
        // 1. Admin RW: Full access
        // 2. Tenaga Kesehatan / Kader Posyandu assigned to this RT or RW: Full access
        // 3. Warga / Parents: Allowed ONLY if child is in their family KK
        // 4. Admin RT: NOT allowed individual medical access unless child is in their own family!
        if (!$isOfficer) {
            $isFamily = ($anak->warga_id && in_array($anak->warga_id, $familyWargaIds));
            if (!$isFamily) {
                if ($user->isAdminRt()) {
                    abort(403, 'Akses rekam medis individual balita dibatasi khusus untuk Petugas Posyandu / Tenaga Kesehatan berwenang atau orang tua anak demi menjaga kerahasiaan data kesehatan warga RT.');
                }
                abort(403, 'Anda tidak memiliki hak akses untuk melihat rekam medis balita ini demi menjaga privasi dan kerahasiaan data kesehatan anak.');
            }
        }

        $anak->load([
            'pemeriksaans.verifiedNakes',
            'imunisasis' => function ($q) {
                $q->orderBy('id', 'asc');
            },
            'warga',
        ]);

        $pemeriksaansAsc = $anak->pemeriksaans->sortBy('tanggal_pemeriksaan');
        $pemeriksaansDesc = $pemeriksaansAsc->sortByDesc('tanggal_pemeriksaan');
        $latestPem = $pemeriksaansDesc->first();

        // Prepare points for Chart.js
        $chartPointsBbU = [];
        $chartPointsTbU = [];
        $chartPointsBbTb = [];
        $chartPointsImtU = [];

        foreach ($pemeriksaansAsc as $p) {
            $chartPointsBbU[] = [
                'x' => (float) $p->umur_bulan,
                'y' => (float) $p->berat_badan,
                'date' => $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d M Y') : '-',
                'status' => $p->status_bb_u,
            ];
            $chartPointsTbU[] = [
                'x' => (float) $p->umur_bulan,
                'y' => (float) $p->tinggi_badan,
                'date' => $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d M Y') : '-',
                'status' => $p->status_tb_u,
            ];
            $chartPointsBbTb[] = [
                'x' => (float) $p->tinggi_badan,
                'y' => (float) $p->berat_badan,
                'date' => $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d M Y') : '-',
                'status' => $p->status_bb_tb,
            ];
            $chartPointsImtU[] = [
                'x' => (float) $p->umur_bulan,
                'y' => (float) ($p->imt ?: round($p->berat_badan / (($p->tinggi_badan/100)*($p->tinggi_badan/100)), 2)),
                'date' => $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d M Y') : '-',
                'status' => $p->status_imt_u,
            ];
        }

        // WHO Curves reference for this child's gender
        $whoBbU = WhoGrowthStandardsService::getChartJsReference($anak->jenis_kelamin, 'bb_u');
        $whoTbU = WhoGrowthStandardsService::getChartJsReference($anak->jenis_kelamin, 'tb_u');
        $whoImtU = WhoGrowthStandardsService::getChartJsReference($anak->jenis_kelamin, 'imt_u');

        // Audit logs relating to this child
        $auditLogs = AuditLog::where(function ($q) use ($anak) {
            $q->where(function ($sq) use ($anak) {
                $sq->where('module', 'like', '%Posyandu%')
                   ->where('record_id', (string)$anak->id);
            })->orWhere('description', 'like', "%{$anak->nama_anak}%");
        })->orderBy('created_at', 'desc')->take(10)->get();

        return view('posyandu.balita_show', compact(
            'anak',
            'pemeriksaansAsc',
            'pemeriksaansDesc',
            'latestPem',
            'chartPointsBbU',
            'chartPointsTbU',
            'chartPointsBbTb',
            'chartPointsImtU',
            'whoBbU',
            'whoTbU',
            'whoImtU',
            'isOfficer',
            'isKader',
            'isNakes',
            'isAdminRw',
            'auditLogs'
        ));
    }

    /**
     * Individual Pregnant Mother (Ibu Hamil) Profile & ANC Timeline.
     */
    public function showBumil(Request $request, PosyanduBumil $bumil)
    {
        $user = Auth::user();
        $isWarga = $user && $user->isWarga();
        $isAdminRw = $user && $user->isAdminRw();
        $isKader = !$isWarga && $user && $user->isPosyanduKader($bumil->rt_number);
        $isNakes = !$isWarga && $user && $user->isPosyanduNakes($bumil->rt_number);
        $isOfficer = $isAdminRw || $isKader || $isNakes;
        $familyWargaIds = $this->getFamilyWargaIds();

        // RBAC Access Control check:
        if (!$isOfficer) {
            $isFamily = ($bumil->warga_id && in_array($bumil->warga_id, $familyWargaIds));
            if (!$isFamily) {
                if ($user->isAdminRt()) {
                    abort(403, 'Akses rekam medis kehamilan individual dibatasi khusus untuk Petugas Posyandu / Bidan atau ibu hamil dan suami yang bersangkutan.');
                }
                abort(403, 'Anda tidak memiliki hak akses untuk melihat rekam medis ibu hamil ini demi menjaga privasi dan kerahasiaan data kesehatan keluarga.');
            }
        }

        $bumil->load([
            'pemeriksaans.verifiedNakes',
            'warga',
        ]);

        $ancTimeline = $bumil->pemeriksaans->sortByDesc('tanggal_pemeriksaan');
        $latestPem = $ancTimeline->first();
        $totalTtd = $ancTimeline->sum('pemberian_ttd');

        // Audit logs relating to this bumil
        $auditLogs = AuditLog::where(function ($q) use ($bumil) {
            $q->where(function ($sq) use ($bumil) {
                $sq->where('module', 'like', '%Posyandu%')
                   ->where('record_id', (string)$bumil->id);
            })->orWhere('description', 'like', "%{$bumil->nama_ibu}%");
        })->orderBy('created_at', 'desc')->take(10)->get();

        return view('posyandu.bumil_show', compact(
            'bumil',
            'ancTimeline',
            'latestPem',
            'totalTtd',
            'isOfficer',
            'isKader',
            'isNakes',
            'isAdminRw',
            'auditLogs'
        ));
    }

    /**
     * Medical Validation & Verification by Healthcare Worker (Tenaga Kesehatan / Bidan).
     */
    public function verifyNakesBalita(Request $request, PosyanduAnak $anak, PosyanduAnakPemeriksaan $pemeriksaan)
    {
        $user = Auth::user();
        if (!$user->isAdminRw() && !$user->isPosyanduNakes()) {
            abort(403, 'Hanya Tenaga Kesehatan terverifikasi atau Bidan yang berwenang melakukan validasi medis.');
        }

        $validated = $request->validate([
            'status_tumbuh'        => 'required|string|max:100',
            'catatan_nakes'        => 'required|string|max:2000',
            'rekomendasi_rujukan'  => 'nullable|string|max:255',
        ]);

        $pemeriksaan->update([
            'status_tumbuh'        => $validated['status_tumbuh'],
            'is_verified_nakes'    => true,
            'verified_by_nakes_id' => $user->id,
            'verified_nakes_at'    => now(),
            'catatan_nakes'        => $validated['catatan_nakes'],
            'rekomendasi_rujukan'  => $validated['rekomendasi_rujukan'] ?? null,
        ]);

        $anak->syncLatestExamination();

        AuditLogger::log(
            'verify',
            'Posyandu Medis',
            $anak->id,
            "Tenaga Kesehatan {$user->name} memverifikasi rekam pemeriksaan balita '{$anak->nama_anak}' (Diagnosa: {$validated['status_tumbuh']}, Rujukan: " . ($validated['rekomendasi_rujukan'] ?: 'Pemantauan Posyandu') . ")."
        );

        return redirect()->route('posyandu.balita.show', $anak->id . '#tab-pemeriksaan')
            ->with('success', 'Hasil pemeriksaan balita berhasil diverifikasi secara medis oleh Tenaga Kesehatan.');
    }

    /**
     * Medical Validation & Verification for Pregnant Mother by Healthcare Worker (Nakes/Bidan).
     */
    public function verifyNakesBumil(Request $request, PosyanduBumil $bumil, PosyanduBumilPemeriksaan $pemeriksaan)
    {
        $user = Auth::user();
        if (!$user->isAdminRw() && !$user->isPosyanduNakes()) {
            abort(403, 'Hanya Tenaga Kesehatan terverifikasi atau Bidan yang berwenang melakukan validasi medis.');
        }

        $validated = $request->validate([
            'status_kesehatan'     => 'required|string|max:100',
            'catatan_nakes'        => 'required|string|max:2000',
            'rekomendasi_rujukan'  => 'nullable|string|max:255',
        ]);

        $pemeriksaan->update([
            'status_kesehatan'     => $validated['status_kesehatan'],
            'is_verified_nakes'    => true,
            'verified_by_nakes_id' => $user->id,
            'verified_nakes_at'    => now(),
            'catatan_nakes'        => $validated['catatan_nakes'],
            'rekomendasi_rujukan'  => $validated['rekomendasi_rujukan'] ?? null,
        ]);

        $bumil->syncLatestExamination();

        AuditLogger::log(
            'verify',
            'Posyandu Medis',
            $bumil->id,
            "Tenaga Kesehatan {$user->name} memverifikasi rekam medis ANC ibu hamil '{$bumil->nama_ibu}' (Diagnosa: {$validated['status_kesehatan']}, Rujukan: " . ($validated['rekomendasi_rujukan'] ?: 'Pemantauan Posyandu') . ")."
        );

        return redirect()->route('posyandu.bumil.show', $bumil->id . '#tab-anc')
            ->with('success', 'Hasil pemeriksaan ANC berhasil diverifikasi secara medis oleh Tenaga Kesehatan.');
    }

    /**
     * Store new measurement examination for Balita.
     */
    public function storePemeriksaanBalita(Request $request, PosyanduAnak $anak)
    {
        $user = Auth::user();
        if (!$user->isPosyanduOfficer($anak->rt_number)) {
            abort(403, 'Hanya petugas posyandu atau tenaga kesehatan yang berwenang mencatat pemeriksaan kesehatan.');
        }

        $validated = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'umur_bulan'          => 'required|numeric|min:0|max:120',
            'berat_badan'         => 'required|numeric|min:1|max:40',
            'tinggi_badan'        => 'required|numeric|min:35|max:160',
            'lingkar_kepala'      => 'nullable|numeric|min:20|max:65',
            'lingkar_lengan'      => 'nullable|numeric|min:8|max:30',
            'asi_eksklusif'       => 'nullable|string|in:Ya,Tidak,Selesai',
            'vitamin_a'           => 'nullable|boolean',
            'obat_cacing'         => 'nullable|boolean',
            'catatan_petugas'     => 'nullable|string|max:2000',
        ]);

        $gender = $anak->jenis_kelamin ?? 'L';
        $age = (float) $validated['umur_bulan'];
        $bb = (float) $validated['berat_badan'];
        $tb = (float) $validated['tinggi_badan'];

        $clsBb = WhoGrowthStandardsService::classifyBbU($gender, $age, $bb);
        $clsTb = WhoGrowthStandardsService::classifyTbU($gender, $age, $tb);
        $bmi = round($bb / (($tb / 100.0) * ($tb / 100.0)), 2);
        $clsImt = WhoGrowthStandardsService::classifyImtU($gender, $age, $bmi);

        // Standard interpretation
        $statusTumbuh = 'Normal';
        if (str_contains(strtolower($clsTb['status']), 'pendek')) {
            $statusTumbuh = $clsTb['status'];
        } elseif (str_contains(strtolower($clsBb['status']), 'kurang') || str_contains(strtolower($clsBb['status']), 'buruk')) {
            $statusTumbuh = $clsBb['status'];
        } elseif (str_contains(strtolower($clsImt['status']), 'lebih') || str_contains(strtolower($clsImt['status']), 'obes')) {
            $statusTumbuh = $clsImt['status'];
        }

        $pem = PosyanduAnakPemeriksaan::create([
            'posyandu_anak_id'    => $anak->id,
            'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
            'umur_bulan'          => $age,
            'berat_badan'         => $bb,
            'tinggi_badan'        => $tb,
            'lingkar_kepala'      => $validated['lingkar_kepala'] ?? null,
            'lingkar_lengan'      => $validated['lingkar_lengan'] ?? null,
            'imt'                 => $bmi,
            'status_bb_u'         => $clsBb['status'],
            'status_tb_u'         => $clsTb['status'],
            'status_bb_tb'        => $clsImt['status'],
            'status_imt_u'        => $clsImt['status'],
            'status_tumbuh'       => $statusTumbuh,
            'asi_eksklusif'       => $validated['asi_eksklusif'] ?? null,
            'vitamin_a'           => $request->boolean('vitamin_a'),
            'obat_cacing'         => $request->boolean('obat_cacing'),
            'catatan_petugas'     => $validated['catatan_petugas'] ?? null,
            'petugas_nama'        => Auth::user()->name,
            'is_verified_nakes'   => $user->isPosyanduNakes(), // Auto-verified if inputted by Nakes
            'verified_by_nakes_id'=> $user->isPosyanduNakes() ? $user->id : null,
            'verified_nakes_at'   => $user->isPosyanduNakes() ? now() : null,
            'created_by'          => Auth::id(),
        ]);

        $anak->syncLatestExamination();

        AuditLogger::log(
            'create',
            'Posyandu',
            $anak->id,
            "Mencatat pemeriksaan berkala balita '{$anak->nama_anak}' (BB: {$bb}kg, TB: {$tb}cm, Status: {$statusTumbuh}) oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id . '#tab-pemeriksaan')
            ->with('success', 'Hasil pemeriksaan balita berhasil disimpan dan kurva KMS otomatis diperbarui.');
    }

    /**
     * Delete an individual measurement of balita.
     */
    public function destroyPemeriksaanBalita(PosyanduAnak $anak, PosyanduAnakPemeriksaan $pemeriksaan)
    {
        $user = Auth::user();
        // Kader cannot delete verified medical records without correction procedure
        if (!$user->isAdminRw() && !$user->isPosyanduNakes() && $pemeriksaan->is_verified_nakes) {
            abort(403, 'Hasil pemeriksaan yang sudah diverifikasi Tenaga Kesehatan tidak dapat dihapus oleh kader tanpa persetujuan Tenaga Kesehatan / Admin RW.');
        }

        if (!$user->isPosyanduOfficer($anak->rt_number)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $info = "Pemeriksaan tanggal " . $pemeriksaan->tanggal_pemeriksaan->format('d/m/Y');
        $pemeriksaan->delete();
        $anak->syncLatestExamination();

        AuditLogger::log(
            'delete',
            'Posyandu',
            $anak->id,
            "Menghapus data {$info} untuk balita '{$anak->nama_anak}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id . '#tab-pemeriksaan')
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }

    /**
     * Record new vaccination for Balita.
     */
    public function storeImunisasiBalita(Request $request, PosyanduAnak $anak)
    {
        if (!Auth::user()->isPosyanduOfficer($anak->rt_number)) {
            abort(403, 'Hanya petugas berwenang yang dapat mencatat imunisasi.');
        }

        $validated = $request->validate([
            'vaksin_nama'       => 'required|string|max:255',
            'tanggal_pemberian' => 'nullable|date',
            'status'            => 'required|string|in:Sudah Diberikan,Terjadwal,Terlewat',
            'jadwal_berikutnya' => 'nullable|date',
            'batch_nomor'       => 'nullable|string|max:100',
            'catatan_petugas'   => 'nullable|string|max:1000',
        ]);

        PosyanduAnakImunisasi::create([
            'posyandu_anak_id'  => $anak->id,
            'vaksin_nama'       => $validated['vaksin_nama'],
            'tanggal_pemberian' => $validated['tanggal_pemberian'] ?? null,
            'status'            => $validated['status'],
            'jadwal_berikutnya' => $validated['jadwal_berikutnya'] ?? null,
            'batch_nomor'       => $validated['batch_nomor'] ?? null,
            'petugas_nama'      => Auth::user()->name,
            'catatan_petugas'   => $validated['catatan_petugas'] ?? null,
        ]);

        AuditLogger::log(
            'create',
            'Posyandu',
            $anak->id,
            "Mencatat imunisasi '{$validated['vaksin_nama']}' ({$validated['status']}) untuk balita '{$anak->nama_anak}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id . '#tab-imunisasi')
            ->with('success', 'Pencatatan vaksinasi imunisasi berhasil disimpan.');
    }

    /**
     * Update existing vaccination status for Balita.
     */
    public function updateImunisasiBalita(Request $request, PosyanduAnak $anak, PosyanduAnakImunisasi $imunisasi)
    {
        if (!Auth::user()->isPosyanduOfficer($anak->rt_number)) {
            abort(403, 'Hanya petugas berwenang yang dapat mengubah status imunisasi.');
        }

        $validated = $request->validate([
            'status'            => 'required|string|in:Sudah Diberikan,Terjadwal,Terlewat',
            'tanggal_pemberian' => 'nullable|date',
            'batch_nomor'       => 'nullable|string|max:100',
            'catatan_petugas'   => 'nullable|string|max:1000',
        ]);

        $imunisasi->update([
            'status'            => $validated['status'],
            'tanggal_pemberian' => $validated['status'] === 'Sudah Diberikan' ? ($validated['tanggal_pemberian'] ?? now()->format('Y-m-d')) : null,
            'batch_nomor'       => $validated['batch_nomor'] ?? $imunisasi->batch_nomor,
            'petugas_nama'      => Auth::user()->name,
            'catatan_petugas'   => $validated['catatan_petugas'] ?? $imunisasi->catatan_petugas,
        ]);

        AuditLogger::log(
            'update',
            'Posyandu',
            $anak->id,
            "Memperbarui status vaksin '{$imunisasi->vaksin_nama}' menjadi '{$validated['status']}' untuk balita '{$anak->nama_anak}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id . '#tab-imunisasi')
            ->with('success', 'Status imunisasi berhasil diperbarui.');
    }

    /**
     * Store new ANC check for Ibu Hamil.
     */
    public function storePemeriksaanBumil(Request $request, PosyanduBumil $bumil)
    {
        $user = Auth::user();
        if (!$user->isPosyanduOfficer($bumil->rt_number)) {
            abort(403, 'Hanya petugas posyandu atau tenaga kesehatan yang berwenang mencatat pemeriksaan kesehatan.');
        }

        $validated = $request->validate([
            'tanggal_pemeriksaan'   => 'required|date',
            'usia_kehamilan_minggu' => 'required|integer|min:1|max:45',
            'berat_badan'           => 'required|numeric|min:30|max:150',
            'tekanan_darah'         => 'required|string|max:20',
            'lila'                  => 'required|numeric|min:15|max:40',
            'tinggi_fundus'         => 'nullable|numeric|min:5|max:45',
            'detak_jantung_janin'   => 'nullable|string|max:50',
            'keluhan'               => 'nullable|string|max:2000',
            'pemberian_ttd'         => 'nullable|integer|min:0|max:180',
            'catatan_petugas'       => 'nullable|string|max:2000',
            'jadwal_berikutnya'     => 'nullable|date',
        ]);

        $statusKesehatan = 'Kondisi Baik / Normal';
        $lila = (float) $validated['lila'];
        $td = trim($validated['tekanan_darah']);

        if ($lila < 23.5) {
            $statusKesehatan = 'Kekurangan Energi Kronis (KEK)';
        }

        if (preg_match('/(\d+)\s*\/\s*(\d+)/', $td, $m)) {
            $sys = (int) $m[1];
            $dia = (int) $m[2];
            if ($sys >= 140 || $dia >= 90) {
                $statusKesehatan = 'Hipertensi Gestasional';
            }
        }

        PosyanduBumilPemeriksaan::create([
            'posyandu_bumil_id'     => $bumil->id,
            'tanggal_pemeriksaan'   => $validated['tanggal_pemeriksaan'],
            'usia_kehamilan_minggu' => $validated['usia_kehamilan_minggu'],
            'berat_badan'           => $validated['berat_badan'],
            'tekanan_darah'         => $validated['tekanan_darah'],
            'tinggi_fundus'         => $validated['tinggi_fundus'] ?? null,
            'lila'                  => $validated['lila'],
            'detak_jantung_janin'   => $validated['detak_jantung_janin'] ?? null,
            'keluhan'               => $validated['keluhan'] ?? null,
            'pemberian_ttd'         => $validated['pemberian_ttd'] ?? 30,
            'status_kesehatan'      => $statusKesehatan,
            'catatan_petugas'       => $validated['catatan_petugas'] ?? null,
            'jadwal_berikutnya'     => $validated['jadwal_berikutnya'] ?? null,
            'petugas_nama'          => Auth::user()->name,
            'is_verified_nakes'     => $user->isPosyanduNakes(),
            'verified_by_nakes_id'  => $user->isPosyanduNakes() ? $user->id : null,
            'verified_nakes_at'     => $user->isPosyanduNakes() ? now() : null,
            'created_by'            => Auth::id(),
        ]);

        $bumil->syncLatestExamination();

        AuditLogger::log(
            'create',
            'Posyandu',
            $bumil->id,
            "Mencatat pemeriksaan ANC usia kehamilan {$validated['usia_kehamilan_minggu']} minggu untuk '{$bumil->nama_ibu}' (TD: {$validated['tekanan_darah']}, LiLA: {$validated['lila']}cm, Status: {$statusKesehatan}) oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.bumil.show', $bumil->id . '#tab-anc')
            ->with('success', 'Catatan pemeriksaan ANC ibu hamil berhasil disimpan.');
    }

    /**
     * Delete an individual ANC check of pregnant mother.
     */
    public function destroyPemeriksaanBumil(PosyanduBumil $bumil, PosyanduBumilPemeriksaan $pemeriksaan)
    {
        $user = Auth::user();
        if (!$user->isAdminRw() && !$user->isPosyanduNakes() && $pemeriksaan->is_verified_nakes) {
            abort(403, 'Hasil pemeriksaan yang sudah diverifikasi Tenaga Kesehatan tidak dapat dihapus oleh kader.');
        }

        if (!$user->isPosyanduOfficer($bumil->rt_number)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $info = "Pemeriksaan tanggal " . $pemeriksaan->tanggal_pemeriksaan->format('d/m/Y');
        $pemeriksaan->delete();
        $bumil->syncLatestExamination();

        AuditLogger::log(
            'delete',
            'Posyandu',
            $bumil->id,
            "Menghapus data {$info} untuk ibu hamil '{$bumil->nama_ibu}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.bumil.show', $bumil->id . '#tab-anc')
            ->with('success', 'Data pemeriksaan ANC berhasil dihapus.');
    }

    /**
     * Create form dispatcher.
     */
    public function create()
    {
        $batasBalita = Carbon::now()->subYears(5)->format('Y-m-d');
        $targetAnak = Warga::where('tanggal_lahir', '>=', $batasBalita)
            ->orderBy('nama', 'asc')->get();

        $minimalUmur = Carbon::now()->subYears(18)->format('Y-m-d');
        $maksimalUmur = Carbon::now()->subYears(45)->format('Y-m-d');
        $targetBumil = Warga::where('gender', 'P')
            ->whereBetween('tanggal_lahir', [$maksimalUmur, $minimalUmur])
            ->orderBy('nama', 'asc')->get();

        $rtList = ['006', '007', '008', '009', '010'];

        return view('posyandu.create', compact('targetAnak', 'targetBumil', 'rtList'));
    }

    /**
     * Store dispatcher with automatic resident lookup enrichment.
     */
    public function store(Request $request)
    {
        $type = $request->input('type');
        if ($type === 'anak') {
            $merged = [];
            $w = Warga::where('nama', 'like', '%' . trim($request->nama_anak) . '%')->first();
            if ($w) {
                if (!$request->filled('warga_id')) $merged['warga_id'] = $w->id;
                if (!$request->filled('jenis_kelamin')) $merged['jenis_kelamin'] = $w->gender ?? 'L';
                if (!$request->filled('tanggal_lahir')) $merged['tanggal_lahir'] = $w->tanggal_lahir ? Carbon::parse($w->tanggal_lahir)->format('Y-m-d') : now()->subMonths(round($request->umur_bulan ?? 12))->format('Y-m-d');
                if (!$request->filled('rt_number')) $merged['rt_number'] = $w->rt_number ?? '007';
                if (!$request->filled('alamat')) $merged['alamat'] = $w->alamat ?? "Lingkungan RT {$w->rt_number}";
            } else {
                if (!$request->filled('jenis_kelamin')) $merged['jenis_kelamin'] = 'L';
                if (!$request->filled('tanggal_lahir')) $merged['tanggal_lahir'] = now()->subMonths(round($request->umur_bulan ?? 12))->format('Y-m-d');
                if (!$request->filled('rt_number')) $merged['rt_number'] = '007';
            }
            if (!empty($merged)) {
                $request->merge($merged);
            }
            return $this->storeBalita($request);
        } elseif ($type === 'bumil') {
            $merged = [];
            $w = Warga::where('nama', 'like', '%' . trim($request->nama_ibu) . '%')->first();
            if ($w) {
                if (!$request->filled('warga_id')) $merged['warga_id'] = $w->id;
                if (!$request->filled('rt_number')) $merged['rt_number'] = $w->rt_number ?? '008';
                if (!$request->filled('alamat')) $merged['alamat'] = $w->alamat ?? "Lingkungan RT {$w->rt_number}";
            } else {
                if (!$request->filled('rt_number')) $merged['rt_number'] = '008';
            }
            if (!empty($merged)) {
                $request->merge($merged);
            }
            return $this->storeBumil($request);
        } else {
            return $this->storeJadwal($request);
        }
    }

    /**
     * Store new Activity Schedule.
     */
    public function storeJadwal(Request $request)
    {
        $user = Auth::user();
        if (!$user->isPosyanduOfficer()) {
            abort(403, 'Hanya petugas posyandu atau pengurus RW yang berwenang menjadwalkan kegiatan.');
        }

        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'tanggal'          => 'required|date',
            'lokasi'           => 'nullable|string|max:255',
            'kategori'         => 'nullable|string|max:100',
            'jam_mulai'        => 'nullable|string|max:20',
            'jam_selesai'      => 'nullable|string|max:20',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string|max:2000',
        ]);

        $status = Carbon::parse($validated['tanggal'])->isPast() ? 'selesai' : 'terjadwal';

        $pos = Posyandu::create([
            'nama'             => $validated['nama'],
            'tanggal'          => $validated['tanggal'],
            'lokasi'           => $validated['lokasi'] ?? 'Balai Warga Utama RW 018',
            'kategori'         => $validated['kategori'] ?? 'Balita',
            'jam_mulai'        => $validated['jam_mulai'] ?? '08:30',
            'jam_selesai'      => $validated['jam_selesai'] ?? '11:30',
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? Auth::user()->name,
            'keterangan'       => $validated['keterangan'] ?? null,
            'status'           => $status,
        ]);

        AuditLogger::log(
            'create',
            'Posyandu',
            $pos->id,
            "Menjadwalkan posyandu baru '{$pos->nama}' pada " . Carbon::parse($pos->tanggal)->format('d/m/Y') . " oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.index')->with('success', 'Jadwal kegiatan posyandu berhasil ditambahkan.');
    }

    /**
     * Update Activity Schedule Status / Details.
     */
    public function updateJadwal(Request $request, Posyandu $posyandu)
    {
        if (!Auth::user()->isPosyanduOfficer()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'tanggal'          => 'required|date',
            'lokasi'           => 'nullable|string|max:255',
            'status'           => 'required|string|in:terjadwal,selesai,dibatalkan',
            'kategori'         => 'nullable|string|max:100',
            'jam_mulai'        => 'nullable|string|max:20',
            'jam_selesai'      => 'nullable|string|max:20',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string|max:2000',
        ]);

        $oldStatus = $posyandu->status;
        $posyandu->update($validated);

        AuditLogger::log(
            'update',
            'Posyandu',
            $posyandu->id,
            "Memperbarui jadwal posyandu '{$posyandu->nama}' (Status: {$oldStatus} -> {$posyandu->status}) oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.index')->with('success', 'Jadwal posyandu berhasil diperbarui.');
    }

    /**
     * Delete Activity Schedule.
     */
    public function destroyJadwal(Posyandu $posyandu)
    {
        if (!Auth::user()->isAdminRw()) {
            abort(403, 'Hanya Admin RW yang dapat menghapus jadwal posyandu.');
        }

        $nama = $posyandu->nama;
        $posyandu->delete();

        AuditLogger::log(
            'delete',
            'Posyandu',
            null,
            "Menghapus jadwal posyandu '{$nama}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.index')->with('success', 'Jadwal posyandu berhasil dihapus.');
    }

    /**
     * Register new Balita profile.
     */
    public function storeBalita(Request $request)
    {
        if (!Auth::user()->isPosyanduOfficer($request->rt_number)) {
            abort(403, 'Hanya petugas posyandu berwenang yang dapat mendaftarkan balita baru.');
        }

        $validated = $request->validate([
            'nama_anak'      => 'required|string|max:255',
            'warga_id'       => 'nullable|exists:wargas,id',
            'nik'            => 'nullable|string|max:30',
            'jenis_kelamin'  => 'required|string|in:L,P',
            'tanggal_lahir'  => 'required|date',
            'nama_ibu'       => 'nullable|string|max:255',
            'nama_ayah'      => 'nullable|string|max:255',
            'rt_number'      => 'required|string|max:10',
            'alamat'         => 'nullable|string|max:255',
            'no_telepon'     => 'nullable|string|max:30',
            'berat_lahir'    => 'nullable|numeric|min:1|max:10',
            'tinggi_lahir'   => 'nullable|numeric|min:20|max:70',
            'golongan_darah' => 'nullable|string|max:5',
            'berat_badan'    => 'required|numeric|min:1|max:40',
            'tinggi_badan'   => 'required|numeric|min:35|max:160',
            'lingkar_kepala' => 'nullable|numeric|min:20|max:65',
            'catatan_khusus' => 'nullable|string|max:2000',
        ]);

        $birthDate = Carbon::parse($validated['tanggal_lahir']);
        $ageMonths = round($birthDate->diffInMonths(now()), 1);

        $clsBb = WhoGrowthStandardsService::classifyBbU($validated['jenis_kelamin'], $ageMonths, (float)$validated['berat_badan']);
        $clsTb = WhoGrowthStandardsService::classifyTbU($validated['jenis_kelamin'], $ageMonths, (float)$validated['tinggi_badan']);

        $needFollowUp = (
            str_contains(strtolower($clsBb['status']), 'kurang') ||
            str_contains(strtolower($clsTb['status']), 'pendek')
        );

        $anak = PosyanduAnak::create([
            'warga_id'            => $validated['warga_id'] ?? null,
            'nama_anak'           => $validated['nama_anak'],
            'nik'                 => $validated['nik'] ?? null,
            'jenis_kelamin'       => $validated['jenis_kelamin'],
            'tanggal_lahir'       => $validated['tanggal_lahir'],
            'nama_ibu'            => $validated['nama_ibu'] ?? null,
            'nama_ayah'           => $validated['nama_ayah'] ?? null,
            'rt_number'           => $validated['rt_number'],
            'alamat'              => $validated['alamat'] ?? null,
            'no_telepon'          => $validated['no_telepon'] ?? null,
            'berat_lahir'         => $validated['berat_lahir'] ?? null,
            'tinggi_lahir'        => $validated['tinggi_lahir'] ?? null,
            'golongan_darah'      => $validated['golongan_darah'] ?? null,
            'umur_bulan'          => $ageMonths,
            'berat_badan'         => $validated['berat_badan'],
            'tinggi_badan'        => $validated['tinggi_badan'],
            'status_tumbuh'       => $clsBb['status'],
            'perlu_tindak_lanjut' => $needFollowUp,
            'alasan_tindak_lanjut'=> $needFollowUp ? $clsBb['status'] : null,
            'catatan_khusus'      => $validated['catatan_khusus'] ?? null,
        ]);

        // Create initial examination entry
        PosyanduAnakPemeriksaan::create([
            'posyandu_anak_id'    => $anak->id,
            'tanggal_pemeriksaan' => now()->format('Y-m-d'),
            'umur_bulan'          => $ageMonths,
            'berat_badan'         => $validated['berat_badan'],
            'tinggi_badan'        => $validated['tinggi_badan'],
            'lingkar_kepala'      => $validated['lingkar_kepala'] ?? null,
            'imt'                 => round($validated['berat_badan'] / (($validated['tinggi_badan']/100)*($validated['tinggi_badan']/100)), 2),
            'status_bb_u'         => $clsBb['status'],
            'status_tb_u'         => $clsTb['status'],
            'status_bb_tb'        => $clsBb['status'],
            'status_imt_u'        => $clsBb['status'],
            'status_tumbuh'       => $clsBb['status'],
            'catatan_petugas'     => 'Pemeriksaan awal saat pendaftaran balita baru di posyandu.',
            'petugas_nama'        => Auth::user()->name,
            'created_by'          => Auth::id(),
        ]);

        AuditLogger::log(
            'create',
            'Posyandu',
            $anak->id,
            "Mendaftarkan profil balita baru '{$anak->nama_anak}' di RT {$anak->rt_number} oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id)
            ->with('success', 'Profil balita berhasil didaftarkan dan kartu KMS digital siap digunakan.');
    }

    /**
     * Update Balita profile information.
     */
    public function updateBalita(Request $request, PosyanduAnak $anak)
    {
        if (!Auth::user()->isPosyanduOfficer($anak->rt_number)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama_anak'      => 'required|string|max:255',
            'nik'            => 'nullable|string|max:30',
            'jenis_kelamin'  => 'required|string|in:L,P',
            'tanggal_lahir'  => 'required|date',
            'nama_ibu'       => 'nullable|string|max:255',
            'nama_ayah'      => 'nullable|string|max:255',
            'rt_number'      => 'required|string|max:10',
            'alamat'         => 'nullable|string|max:255',
            'no_telepon'     => 'nullable|string|max:30',
            'berat_lahir'    => 'nullable|numeric|min:1|max:10',
            'tinggi_lahir'   => 'nullable|numeric|min:20|max:70',
            'golongan_darah' => 'nullable|string|max:5',
            'catatan_khusus' => 'nullable|string|max:2000',
        ]);

        $anak->update($validated);

        AuditLogger::log(
            'update',
            'Posyandu',
            $anak->id,
            "Memperbarui data identitas balita '{$anak->nama_anak}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.balita.show', $anak->id)
            ->with('success', 'Data identitas balita berhasil diperbarui.');
    }

    /**
     * Register new Ibu Hamil profile.
     */
    public function storeBumil(Request $request)
    {
        if (!Auth::user()->isPosyanduOfficer($request->rt_number)) {
            abort(403, 'Hanya petugas posyandu berwenang yang dapat mendaftarkan ibu hamil baru.');
        }

        $validated = $request->validate([
            'nama_ibu'              => 'required|string|max:255',
            'warga_id'              => 'nullable|exists:wargas,id',
            'nik'                   => 'nullable|string|max:30',
            'nama_suami'            => 'nullable|string|max:255',
            'rt_number'             => 'required|string|max:10',
            'alamat'                => 'nullable|string|max:255',
            'no_telepon'            => 'nullable|string|max:30',
            'usia_kehamilan_minggu' => 'required|integer|min:1|max:45',
            'hamil_ke'              => 'nullable|integer|min:1|max:15',
            'golongan_darah'        => 'nullable|string|max:5',
            'berat_badan'           => 'required|numeric|min:30|max:150',
            'tekanan_darah'         => 'required|string|max:20',
            'lila'                  => 'required|numeric|min:15|max:40',
            'hpht'                  => 'nullable|date',
            'hpl'                   => 'nullable|date',
            'solusi'                => 'nullable|string|max:2000',
        ]);

        $hpht = $validated['hpht'] ?? now()->subWeeks($validated['usia_kehamilan_minggu'])->format('Y-m-d');
        $hpl = $validated['hpl'] ?? Carbon::parse($hpht)->addDays(280)->format('Y-m-d');

        $statusKesehatan = 'Kondisi Baik / Normal';
        $lila = (float) $validated['lila'];
        if ($lila < 23.5) {
            $statusKesehatan = 'Kekurangan Energi Kronis (KEK)';
        }

        $bumil = PosyanduBumil::create([
            'warga_id'              => $validated['warga_id'] ?? null,
            'nama_ibu'              => $validated['nama_ibu'],
            'nik'                   => $validated['nik'] ?? null,
            'nama_suami'            => $validated['nama_suami'] ?? null,
            'rt_number'             => $validated['rt_number'],
            'alamat'                => $validated['alamat'] ?? null,
            'no_telepon'            => $validated['no_telepon'] ?? null,
            'usia_kehamilan_minggu' => $validated['usia_kehamilan_minggu'],
            'hamil_ke'              => $validated['hamil_ke'] ?? 1,
            'golongan_darah'        => $validated['golongan_darah'] ?? null,
            'hpht'                  => $hpht,
            'hpl'                   => $hpl,
            'berat_badan'           => $validated['berat_badan'],
            'tekanan_darah'         => $validated['tekanan_darah'],
            'lila'                  => $validated['lila'],
            'status_kesehatan'      => $statusKesehatan,
            'perlu_tindak_lanjut'   => $lila < 23.5,
            'alasan_tindak_lanjut'  => $lila < 23.5 ? 'Risiko KEK (LiLA < 23.5 cm)' : null,
            'solusi'                => $validated['solusi'] ?? null,
        ]);

        // Create initial examination entry
        PosyanduBumilPemeriksaan::create([
            'posyandu_bumil_id'     => $bumil->id,
            'tanggal_pemeriksaan'   => now()->format('Y-m-d'),
            'usia_kehamilan_minggu' => $validated['usia_kehamilan_minggu'],
            'berat_badan'           => $validated['berat_badan'],
            'tekanan_darah'         => $validated['tekanan_darah'],
            'lila'                  => $validated['lila'],
            'status_kesehatan'      => $statusKesehatan,
            'pemberian_ttd'         => 30,
            'catatan_petugas'       => 'Pemeriksaan awal saat pendaftaran ibu hamil baru di posyandu.',
            'petugas_nama'          => Auth::user()->name,
            'created_by'            => Auth::id(),
        ]);

        AuditLogger::log(
            'create',
            'Posyandu',
            $bumil->id,
            "Mendaftarkan profil ibu hamil baru '{$bumil->nama_ibu}' di RT {$bumil->rt_number} oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.bumil.show', $bumil->id)
            ->with('success', 'Profil ibu hamil berhasil didaftarkan dan timeline ANC siap dipantau.');
    }

    /**
     * Update Ibu Hamil profile information.
     */
    public function updateBumil(Request $request, PosyanduBumil $bumil)
    {
        if (!Auth::user()->isPosyanduOfficer($bumil->rt_number)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama_ibu'         => 'required|string|max:255',
            'nik'              => 'nullable|string|max:30',
            'nama_suami'       => 'nullable|string|max:255',
            'rt_number'        => 'required|string|max:10',
            'alamat'           => 'nullable|string|max:255',
            'no_telepon'       => 'nullable|string|max:30',
            'hpht'             => 'nullable|date',
            'hpl'              => 'nullable|date',
            'hamil_ke'         => 'nullable|integer|min:1|max:15',
            'golongan_darah'   => 'nullable|string|max:5',
            'status_kehamilan' => 'required|string|in:Aktif Hamil,Sudah Melahirkan,Gugur',
        ]);

        $bumil->update($validated);

        AuditLogger::log(
            'update',
            'Posyandu',
            $bumil->id,
            "Memperbarui data identitas ibu hamil '{$bumil->nama_ibu}' oleh " . Auth::user()->name . "."
        );

        return redirect()->route('posyandu.bumil.show', $bumil->id)
            ->with('success', 'Data ibu hamil berhasil diperbarui.');
    }

    // =========================================================================
    // MODUL MANAJEMEN PETUGAS POSYANDU (ADMIN RW ONLY)
    // =========================================================================

    /**
     * List all assignments, active cadres, and healthcare workers.
     */
    public function petugasIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Hanya Admin RW yang memiliki hak akses untuk mengelola penugasan petugas Posyandu.');
        }

        $query = PosyanduPetugas::with(['user', 'assigner', 'revoker']);

        if ($request->filled('tipe') && $request->tipe !== 'semua') {
            $query->where('tipe_petugas', $request->tipe);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('jabatan', 'like', "%{$s}%")
                  ->orWhere('nomor_sk_str', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        $petugasList = $query->orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();

        // Eligible users to be assigned (excluding Admin RW itself)
        $eligibleUsers = User::where('id', '!=', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        // Statistics
        $totalPetugas = PosyanduPetugas::count();
        $totalAktif = PosyanduPetugas::aktifValid()->count();
        $totalKader = PosyanduPetugas::aktifValid()->kader()->count();
        $totalNakes = PosyanduPetugas::aktifValid()->nakes()->count();

        // Assignment Audit Logs
        $assignmentLogs = AuditLog::where('module', 'like', '%Posyandu Petugas%')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $rtList = ['006', '007', '008', '009', '010'];

        return view('posyandu.petugas_index', compact(
            'petugasList',
            'eligibleUsers',
            'totalPetugas',
            'totalAktif',
            'totalKader',
            'totalNakes',
            'assignmentLogs',
            'rtList'
        ));
    }

    /**
     * Store new officer assignment.
     */
    public function storePetugas(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'tipe_petugas'        => 'required|string|in:kader,tenaga_kesehatan',
            'jabatan'             => 'required|string|max:255',
            'nomor_sk_str'        => 'nullable|string|max:100',
            'instansi'            => 'nullable|string|max:255',
            'posyandu_nama'       => 'nullable|string|max:255',
            'wilayah_rt'          => 'required|string|max:10',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'kualifikasi_dokumen' => 'nullable|string|max:2000',
            'catatan_penugasan'   => 'nullable|string|max:2000',
        ]);

        $assignedUser = User::findOrFail($validated['user_id']);

        $petugas = PosyanduPetugas::create([
            'user_id'             => $validated['user_id'],
            'tipe_petugas'        => $validated['tipe_petugas'],
            'jabatan'             => $validated['jabatan'],
            'nomor_sk_str'        => $validated['nomor_sk_str'] ?? null,
            'instansi'            => $validated['instansi'] ?? 'Posyandu Melati Mandiri RW 018',
            'posyandu_nama'       => $validated['posyandu_nama'] ?? 'Posyandu Melati RW 018',
            'wilayah_rt'          => $validated['wilayah_rt'],
            'tanggal_mulai'       => $validated['tanggal_mulai'],
            'tanggal_selesai'     => $validated['tanggal_selesai'] ?? null,
            'status'              => 'aktif',
            'status_verifikasi'   => 'terverifikasi',
            'kualifikasi_dokumen' => $validated['kualifikasi_dokumen'] ?? null,
            'catatan_penugasan'   => $validated['catatan_penugasan'] ?? null,
            'catatan_verifikasi'  => 'Diverifikasi langsung oleh Admin RW ' . $user->name,
            'assigned_by'         => $user->id,
        ]);

        $tipeLabel = $petugas->tipe_petugas === 'tenaga_kesehatan' ? 'Tenaga Kesehatan' : 'Kader Posyandu';

        AuditLogger::log(
            'assign',
            'Posyandu Petugas',
            $petugas->id,
            "Admin RW {$user->name} menetapkan penugasan baru kepada '{$assignedUser->name}' sebagai {$tipeLabel} ({$petugas->jabatan}) untuk {$petugas->wilayah_teks}."
        );

        return redirect()->route('posyandu.petugas.index')
            ->with('success', "Penugasan resmi untuk {$assignedUser->name} sebagai {$tipeLabel} berhasil diterbitkan.");
    }

    /**
     * Update officer assignment details.
     */
    public function updatePetugas(Request $request, PosyanduPetugas $petugas)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'jabatan'             => 'required|string|max:255',
            'nomor_sk_str'        => 'nullable|string|max:100',
            'instansi'            => 'nullable|string|max:255',
            'posyandu_nama'       => 'nullable|string|max:255',
            'wilayah_rt'          => 'required|string|max:10',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'status'              => 'required|string|in:aktif,nonaktif,dicabut',
            'kualifikasi_dokumen' => 'nullable|string|max:2000',
            'catatan_penugasan'   => 'nullable|string|max:2000',
        ]);

        $petugas->update($validated);

        AuditLogger::log(
            'update',
            'Posyandu Petugas',
            $petugas->id,
            "Admin RW {$user->name} memperbarui data penugasan '{$petugas->user->name}' ({$petugas->jabatan}, Status: {$petugas->status})."
        );

        return redirect()->route('posyandu.petugas.index')
            ->with('success', 'Data penugasan petugas posyandu berhasil diperbarui.');
    }

    /**
     * Verify credentials/qualification of officer.
     */
    public function verifyPetugas(Request $request, PosyanduPetugas $petugas)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'status_verifikasi'  => 'required|string|in:terverifikasi,ditolak',
            'catatan_verifikasi' => 'required|string|max:1000',
        ]);

        $petugas->update([
            'status_verifikasi'  => $validated['status_verifikasi'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        AuditLogger::log(
            'verify',
            'Posyandu Petugas',
            $petugas->id,
            "Admin RW {$user->name} memverifikasi kualifikasi penugasan '{$petugas->user->name}' dengan status " . strtoupper($validated['status_verifikasi']) . ". Catatan: {$validated['catatan_verifikasi']}"
        );

        return redirect()->route('posyandu.petugas.index')
            ->with('success', 'Status verifikasi kualifikasi petugas berhasil disimpan.');
    }

    /**
     * Revoke officer assignment immediately (Pencabutan Hak Akses Langsung).
     */
    public function revokePetugas(Request $request, PosyanduPetugas $petugas)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'revocation_reason' => 'required|string|max:500',
        ]);

        $targetName = $petugas->user ? $petugas->user->name : 'Petugas';

        $petugas->update([
            'status'            => 'dicabut',
            'revoked_at'        => now(),
            'revoked_by'        => $user->id,
            'revocation_reason' => $validated['revocation_reason'],
        ]);

        AuditLogger::log(
            'revoke',
            'Posyandu Petugas',
            $petugas->id,
            "Admin RW {$user->name} mencabut hak akses penugasan Posyandu '{$targetName}' ({$petugas->jabatan}). Alasan: {$validated['revocation_reason']}"
        );

        return redirect()->route('posyandu.petugas.index')
            ->with('success', "Hak akses penugasan posyandu untuk {$targetName} telah berhasil dicabut secara langsung.");
    }

    /**
     * Delete assignment record.
     */
    public function destroyPetugas(PosyanduPetugas $petugas)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $targetName = $petugas->user ? $petugas->user->name : 'Petugas';
        $petugas->delete();

        AuditLogger::log(
            'delete',
            'Posyandu Petugas',
            null,
            "Admin RW {$user->name} menghapus arsip penugasan posyandu '{$targetName}'."
        );

        return redirect()->route('posyandu.petugas.index')
            ->with('success', 'Arsip penugasan petugas berhasil dihapus.');
    }
}
