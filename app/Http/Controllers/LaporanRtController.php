<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\LaporanRt;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\SuratPengajuan;
use App\Models\Warga;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanRtController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $query = LaporanRt::with(['pengirim', 'pemeriksa'])->orderByDesc('tahun')->orderByDesc('bulan');

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number')) {
            $query->where('rt_number', $request->rt_number);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $laporans = $query->paginate(15)->withQueryString();

        // Metric counts
        $mQuery = LaporanRt::query();
        if ($user->isAdminRt()) {
            $mQuery->where('rt_number', $user->rt_number);
        }
        $countDraft = (clone $mQuery)->where('status', 'draft')->count();
        $countMenunggu = (clone $mQuery)->where('status', 'menunggu')->count();
        $countDiterima = (clone $mQuery)->where('status', 'diterima')->count();
        $countRevisi = (clone $mQuery)->where('status', 'perlu_revisi')->count();

        return view('laporan_rt.index', compact(
            'laporans',
            'countDraft',
            'countMenunggu',
            'countDiterima',
            'countRevisi'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRt() && !$user->isAdminRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $rtNumber = $user->isAdminRt() ? $user->rt_number : ($request->get('rt_number', '006'));
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Check if report already exists
        $existing = LaporanRt::where('rt_number', $rtNumber)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($existing) {
            return redirect()->route('laporan_rt.show', $existing->id)->with('info', 'Laporan untuk periode ini sudah dibuat. Anda dapat melihat atau memperbarui laporannya.');
        }

        // Live calculation of operational metrics for this RT and month
        $metrics = $this->calculateLiveSnapshot($rtNumber, $bulan, $tahun);

        return view('laporan_rt.create', compact('rtNumber', 'bulan', 'tahun', 'metrics'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRt() && !$user->isAdminRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $request->validate([
            'rt_number' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'evaluasi_kegiatan' => 'nullable|string',
            'kendala' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
            'action_type' => 'required|in:draft,submit',
        ]);

        $snapshot = $this->calculateLiveSnapshot($request->rt_number, $request->bulan, $request->tahun);
        $status = $request->action_type === 'submit' ? 'menunggu' : 'draft';

        $laporan = LaporanRt::create([
            'rt_number' => $request->rt_number,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'pengirim_id' => $user->id,
            'status' => $status,
            'snapshot_data' => $snapshot,
            'evaluasi_kegiatan' => $request->evaluasi_kegiatan,
            'kendala' => $request->kendala,
            'catatan_tambahan' => $request->catatan_tambahan,
            'version' => 1,
        ]);

        AuditLogger::log(
            $status === 'menunggu' ? 'submit' : 'create',
            'Laporan RT',
            $laporan->id,
            "Laporan RT {$request->rt_number} periode {$request->bulan}/{$request->tahun} disimpan sebagai " . strtoupper($status) . " oleh {$user->name}.",
            null,
            $laporan->toArray(),
            $request->rt_number
        );

        $msg = $status === 'menunggu'
            ? "Laporan RT {$request->rt_number} berhasil diserahkan kepada Admin RW untuk diperiksa."
            : "Draft laporan RT {$request->rt_number} berhasil disimpan.";

        return redirect()->route('laporan_rt.show', $laporan->id)->with('success', $msg);
    }

    public function show($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $laporan = LaporanRt::with(['pengirim', 'pemeriksa'])->findOrFail($id);

        if ($user->isAdminRt() && $laporan->rt_number !== $user->rt_number) {
            return redirect()->route('laporan_rt.index')->with('error', 'Akses ditolak untuk laporan RT lain.');
        }

        return view('laporan_rt.show', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }
        $laporan = LaporanRt::findOrFail($id);

        if ($user->isAdminRt() && $laporan->rt_number !== $user->rt_number) {
            return redirect()->route('laporan_rt.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'evaluasi_kegiatan' => 'nullable|string',
            'kendala' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
            'action_type' => 'required|in:draft,submit',
        ]);

        $status = $request->action_type === 'submit' ? 'menunggu' : 'draft';
        $oldSnapshot = $laporan->snapshot_data;
        $newSnapshot = $this->calculateLiveSnapshot($laporan->rt_number, $laporan->bulan, $laporan->tahun);

        $revisiHistory = $laporan->riwayat_revisi ?: [];
        $revisiHistory[] = [
            'version' => $laporan->version,
            'updated_by' => $user->name,
            'updated_at' => now()->toDateTimeString(),
            'status_before' => $laporan->status,
        ];

        $laporan->evaluasi_kegiatan = $request->evaluasi_kegiatan;
        $laporan->kendala = $request->kendala;
        $laporan->catatan_tambahan = $request->catatan_tambahan;
        $laporan->status = $status;
        $laporan->snapshot_data = $newSnapshot;
        $laporan->version += 1;
        $laporan->riwayat_revisi = $revisiHistory;
        $laporan->save();

        AuditLogger::log(
            'update',
            'Laporan RT',
            $laporan->id,
            "Laporan RT {$laporan->rt_number} periode {$laporan->bulan}/{$laporan->tahun} diperbarui dan dikirim kembali sebagai " . strtoupper($status) . " oleh {$user->name}.",
            ['version' => $laporan->version - 1],
            ['version' => $laporan->version, 'status' => $status],
            $laporan->rt_number
        );

        return redirect()->route('laporan_rt.show', $laporan->id)->with('success', 'Laporan RT berhasil diperbarui!');
    }

    public function review(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Hanya Admin RW yang berhak memeriksa laporan RT.');
        }

        $request->validate([
            'status' => 'required|in:diterima,perlu_revisi',
            'catatan_pemeriksaan_rw' => 'required|string',
        ]);

        $laporan = LaporanRt::findOrFail($id);
        $oldStatus = $laporan->status;

        $revisiHistory = $laporan->riwayat_revisi ?: [];
        $revisiHistory[] = [
            'action' => 'review_' . $request->status,
            'reviewer' => $user->name,
            'reviewed_at' => now()->toDateTimeString(),
            'notes' => $request->catatan_pemeriksaan_rw,
        ];

        $laporan->status = $request->status;
        $laporan->catatan_pemeriksaan_rw = $request->catatan_pemeriksaan_rw;
        $laporan->pemeriksa_id = $user->id;
        $laporan->waktu_pemeriksaan = now();
        $laporan->riwayat_revisi = $revisiHistory;
        $laporan->save();

        AuditLogger::log(
            $request->status === 'diterima' ? 'approve' : 'revise',
            'Pemeriksaan Laporan RT',
            $laporan->id,
            "Laporan RT {$laporan->rt_number} ({$laporan->bulan}/{$laporan->tahun}) diperiksa oleh Admin RW {$user->name} dengan hasil: " . strtoupper($request->status) . ". Catatan: {$request->catatan_pemeriksaan_rw}",
            ['status' => $oldStatus],
            ['status' => $request->status, 'catatan_rw' => $request->catatan_pemeriksaan_rw],
            $laporan->rt_number
        );

        $msg = $request->status === 'diterima'
            ? "Laporan RT {$laporan->rt_number} telah DITERIMA dan disahkan oleh Admin RW."
            : "Laporan RT {$laporan->rt_number} dikembalikan untuk PERBAIKAN / REVISI.";

        return redirect()->route('laporan_rt.show', $laporan->id)->with('success', $msg);
    }

    private function calculateLiveSnapshot($rtNumber, $bulan, $tahun): array
    {
        $wargas = Warga::where('rt_number', $rtNumber)->where('status_warga', '!=', 'pindah')->get();
        $totalWarga = $wargas->count();
        $totalKk = $wargas->where('is_kk', true)->count();
        $male = $wargas->where('gender', 'L')->count();
        $female = $wargas->where('gender', 'P')->count();

        // Keuangan periode
        $kasMasuk = Pembayaran::where('rt_number', $rtNumber)
            ->where('tipe', 'masuk')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $kasKeluar = Pembayaran::where('rt_number', $rtNumber)
            ->where('tipe', 'keluar')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // Total Kas akumulasi
        $totalKasMasuk = Pembayaran::where('rt_number', $rtNumber)->where('tipe', 'masuk')->sum('jumlah');
        $totalKasKeluar = Pembayaran::where('rt_number', $rtNumber)->where('tipe', 'keluar')->sum('jumlah');
        $saldoKasSaatIni = $totalKasMasuk - $totalKasKeluar;

        // Iuran
        $wargaBayarIuran = Pembayaran::where('rt_number', $rtNumber)
            ->where('tipe', 'masuk')
            ->where('kategori', 'like', '%Iuran%')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();

        // Surat
        $suratSelesai = SuratPengajuan::where('rt_number', $rtNumber)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->count();

        // Pengaduan
        $pengaduanMasuk = Pengaduan::where('rt_number', $rtNumber)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        $pengaduanSelesai = Pengaduan::where('rt_number', $rtNumber)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->count();

        // Bank Sampah
        $sampahKg = BankSampah::where('rt_number', $rtNumber)
            ->whereMonth('tanggal_setor', $bulan)
            ->whereYear('tanggal_setor', $tahun)
            ->sum('berat_kg');

        return [
            'demografi' => [
                'total_warga' => $totalWarga,
                'total_kk' => $totalKk,
                'laki_laki' => $male,
                'perempuan' => $female,
            ],
            'keuangan' => [
                'pemasukan_bulan_ini' => $kasMasuk,
                'pengeluaran_bulan_ini' => $kasKeluar,
                'saldo_kas' => $saldoKasSaatIni,
            ],
            'iuran' => [
                'kk_wajib_iuran' => $totalKk,
                'kk_lunas' => $wargaBayarIuran,
                'persentase_kepatuhan' => $totalKk > 0 ? round(($wargaBayarIuran / $totalKk) * 100) : 0,
            ],
            'pelayanan' => [
                'surat_diterbitkan' => $suratSelesai,
                'pengaduan_masuk' => $pengaduanMasuk,
                'pengaduan_selesai' => $pengaduanSelesai,
            ],
            'lingkungan' => [
                'sampah_terkumpul_kg' => $sampahKg,
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
