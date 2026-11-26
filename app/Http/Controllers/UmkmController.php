<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmFavorit;
use App\Models\UmkmProduk;
use App\Models\UmkmUlasan;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function getUmkmCategories(): array
    {
        return [
            'Kuliner',
            'Makanan & Minuman',
            'Kerajinan',
            'Jasa/Service',
            'Jasa',
            'Perdagangan',
        ];
    }

    public function getUmkmCategoryImages(): array
    {
        return [
            'jasa' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80',
            'kerajinan' => 'https://images.unsplash.com/photo-1477867082705-47a1d8d462f8?auto=format&fit=crop&w=900&q=80',
            'makanan & minuman' => 'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=900&q=80',
            'jasa/service' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80',
            'kuliner' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80',
            'perdagangan' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
            'default' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=900&q=80',
        ];
    }

    public function resolveCoverImage(?string $coverImage, ?string $category): string
    {
        if (!empty($coverImage)) {
            if (preg_match('/^https?:\/\//', $coverImage)) {
                return $coverImage;
            }
            return asset('storage/' . $coverImage);
        }

        $normalized = strtolower(trim($category ?? ''));
        if (str_contains($normalized, 'jasa')) {
            return 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80';
        }
        if (str_contains($normalized, 'kerajinan')) {
            return 'https://images.unsplash.com/photo-1477867082705-47a1d8d462f8?auto=format&fit=crop&w=900&q=80';
        }
        if (str_contains($normalized, 'makan') || str_contains($normalized, 'kuliner')) {
            return 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80';
        }
        if (str_contains($normalized, 'dagang') || str_contains($normalized, 'perdagangan')) {
            return 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80';
        }

        return 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=900&q=80';
    }

    /**
     * Display a listing of UMKM directory.
     */
    public function index(Request $request)
    {
        $query = Umkm::with(['produks', 'visibleUlasans', 'user']);

        // 1. Search Query
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($b) use ($search) {
                $b->where('nama', 'like', "%{$search}%")
                  ->orWhere('pemilik', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('produks', function ($pq) use ($search) {
                      $pq->where('nama_produk', 'like', "%{$search}%")
                         ->orWhere('deskripsi', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 3. Filter Wilayah RT
        if ($request->filled('rt_number')) {
            $query->where('rt_number', $request->rt_number);
        }

        // 4. Filter Status Verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // 5. Filter Hanya Favorit
        if ($request->filled('favorit') && Auth::check()) {
            $query->whereHas('favorits', function ($fq) {
                $fq->where('user_id', Auth::id());
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'terbaru');
        match ($sortBy) {
            'rating' => $query->orderBy('rating_avg', 'desc')->orderBy('rating_count', 'desc'),
            'nama' => $query->orderBy('nama', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $usahas = $query->paginate(9)->withQueryString();
        $categories = $this->getUmkmCategories();
        $categoryImages = $this->getUmkmCategoryImages();
        $rtList = ['006', '007', '008', '009', '010'];

        // Metrics (Database-driven)
        $totalUsaha = Umkm::count();
        $usahaTerverifikasi = Umkm::where('status_verifikasi', 'terverifikasi')->count();
        $totalProduk = UmkmProduk::count();
        $totalUlasan = UmkmUlasan::where('is_hidden', false)->count();

        // Featured recommendation items (verified with highest rating)
        $featuredItems = Umkm::where('status_verifikasi', 'terverifikasi')
            ->orderBy('rating_avg', 'desc')
            ->orderBy('rating_count', 'desc')
            ->take(5)
            ->get();

        return view('umkm.index', compact(
            'usahas',
            'categories',
            'categoryImages',
            'rtList',
            'totalUsaha',
            'usahaTerverifikasi',
            'totalProduk',
            'totalUlasan',
            'featuredItems'
        ));
    }

    /**
     * Show detailed profile, products, reviews, and owner dashboard for UMKM.
     */
    public function show(Request $request, Umkm $umkm)
    {
        $umkm->load([
            'produks',
            'visibleUlasans.user',
            'ulasans.user',
            'user',
            'verifier',
        ]);

        $categoryImages = $this->getUmkmCategoryImages();
        $currentUser = Auth::user();

        $isOwner = $currentUser && (
            $umkm->user_id === $currentUser->id ||
            strtolower(trim($currentUser->name)) === strtolower(trim($umkm->pemilik))
        );

        $isAdmin = $currentUser && ($currentUser->isRw() || $currentUser->isAdminRt());
        $isAdminRw = $currentUser && $currentUser->isRw();

        // User's own review if already submitted
        $myReview = $currentUser ? $umkm->ulasans->where('user_id', $currentUser->id)->first() : null;

        // Rating breakdown stats
        $allVisibleReviews = $umkm->visibleUlasans;
        $totalReviewCount = $allVisibleReviews->count();
        $breakdown = [
            5 => $allVisibleReviews->where('rating', 5)->count(),
            4 => $allVisibleReviews->where('rating', 4)->count(),
            3 => $allVisibleReviews->where('rating', 3)->count(),
            2 => $allVisibleReviews->where('rating', 2)->count(),
            1 => $allVisibleReviews->where('rating', 1)->count(),
        ];

        // Reported reviews count for Admin RW
        $reportedReviews = $isAdminRw ? $umkm->ulasans->where('is_reported', true) : collect();

        $whatsappUrl = $umkm->whatsapp_url;

        if ($request->filled('embed') || $request->ajax()) {
            return view('umkm._embed', compact(
                'umkm',
                'categoryImages',
                'whatsappUrl',
                'isOwner',
                'isAdmin'
            ));
        }

        return view('umkm.show', compact(
            'umkm',
            'categoryImages',
            'whatsappUrl',
            'isOwner',
            'isAdmin',
            'isAdminRw',
            'myReview',
            'breakdown',
            'totalReviewCount',
            'reportedReviews'
        ));
    }

    /**
     * Show form to register new UMKM.
     */
    public function create()
    {
        $categories = $this->getUmkmCategories();
        $rtList = ['006', '007', '008', '009', '010'];
        return view('umkm.create', compact('categories', 'rtList'));
    }

    /**
     * Return JSON categories.
     */
    public function categories()
    {
        return response()->json($this->getUmkmCategories());
    }

    /**
     * Store newly registered UMKM.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string',
            'telepon' => 'required|string|max:50',
            'jam_operasional' => 'nullable|string|max:100',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'rt_number' => 'required|string|in:006,007,008,009,010',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $coverImage = null;
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image')->store('umkm_covers', 'public');
        } else {
            $coverImage = $this->resolveCoverImage(null, $validated['kategori']);
        }

        // Auto-verify if created by Admin RW or Admin RT
        $isPrivileged = $user && ($user->isRw() || $user->isAdminRt());
        $statusVerifikasi = $isPrivileged ? 'terverifikasi' : 'menunggu';
        $verifiedAt = $isPrivileged ? now() : null;
        $verifiedBy = $isPrivileged ? $user->id : null;

        $umkm = Umkm::create([
            'user_id' => $user ? $user->id : null,
            'nama' => $validated['nama'],
            'pemilik' => $validated['pemilik'],
            'kategori' => $validated['kategori'],
            'telepon' => $validated['telepon'],
            'jam_operasional' => $validated['jam_operasional'] ?: '08:00 - 21:00 WIB',
            'alamat' => $validated['alamat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'rt_number' => $validated['rt_number'],
            'cover_image' => $coverImage,
            'status_verifikasi' => $statusVerifikasi,
            'verified_at' => $verifiedAt,
            'verified_by' => $verifiedBy,
            'rating_avg' => 0.00,
            'rating_count' => 0,
        ]);

        AuditLogger::log(
            'create',
            'Direktori UMKM',
            $umkm->id,
            "Pendaftaran usaha baru '{$umkm->nama}' oleh {$validated['pemilik']} (RT {$umkm->rt_number}). Status: " . strtoupper($statusVerifikasi),
            null,
            $umkm->toArray(),
            $umkm->rt_number
        );

        $msg = $isPrivileged
            ? "Usaha UMKM '{$umkm->nama}' berhasil didaftarkan dan langsung terverifikasi!"
            : "Usaha UMKM '{$umkm->nama}' berhasil didaftarkan. Pengurus RT/RW akan memverifikasi usaha Anda.";

        return redirect()->route('umkm.show', $umkm->id)->with('success', $msg);
    }

    /**
     * Update UMKM profile.
     */
    public function update(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdmin = $user && ($user->isRw() || $user->isAdminRt());

        if (!$isOwner && !$isAdmin) {
            return redirect()->back()->with('error', 'Akses ditolak: Anda bukan pemilik usaha ini.');
        }

        if (!$isOwner && $user->isAdminRt() && $umkm->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang mengedit UMKM di luar RT Anda.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'kategori' => 'required|string',
            'telepon' => 'required|string|max:50',
            'jam_operasional' => 'nullable|string|max:100',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'rt_number' => 'required|string|in:006,007,008,009,010',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($umkm->cover_image && !preg_match('/^https?:\/\//', $umkm->cover_image)) {
                Storage::disk('public')->delete($umkm->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('umkm_covers', 'public');
        }

        $oldValues = $umkm->toArray();
        $umkm->update($validated);

        AuditLogger::log(
            'update',
            'Direktori UMKM',
            $umkm->id,
            "Profil usaha '{$umkm->nama}' diperbarui oleh {$user->name}.",
            $oldValues,
            $umkm->toArray(),
            $umkm->rt_number
        );

        return redirect()->route('umkm.show', $umkm->id)->with('success', 'Informasi profil usaha berhasil diperbarui.');
    }

    /**
     * Toggle or update verification status by Admin RW / Admin RT.
     */
    public function toggleVerifikasi(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        if (!$user || (!$user->isRw() && !$user->isAdminRt())) {
            return redirect()->back()->with('error', 'Akses dibatasi hanya untuk Pengurus RT dan RW.');
        }

        if ($user->isAdminRt() && $umkm->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: UMKM berada di luar wilayah RT ' . $user->rt_number . '.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:terverifikasi,menunggu,ditolak',
        ]);

        $newStatus = $request->status_verifikasi;
        $umkm->status_verifikasi = $newStatus;
        $umkm->verified_at = $newStatus === 'terverifikasi' ? now() : null;
        $umkm->verified_by = $newStatus === 'terverifikasi' ? $user->id : null;
        $umkm->save();

        AuditLogger::log(
            'verification',
            'Direktori UMKM',
            $umkm->id,
            "Status verifikasi usaha '{$umkm->nama}' diubah menjadi " . strtoupper($newStatus) . " oleh {$user->name}.",
            null,
            ['status_verifikasi' => $newStatus],
            $umkm->rt_number
        );

        return redirect()->route('umkm.show', $umkm->id)->with('success', "Status verifikasi UMKM berhasil diperbarui menjadi: " . strtoupper($newStatus));
    }

    /**
     * Toggle bookmark favorite by authenticated resident.
     */
    public function toggleFavorit(Umkm $umkm)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $existing = UmkmFavorit::where('umkm_id', $umkm->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $msg = "Usaha '{$umkm->nama}' dihapus dari daftar favorit Anda.";
        } else {
            UmkmFavorit::create([
                'umkm_id' => $umkm->id,
                'user_id' => $userId,
            ]);
            $msg = "Usaha '{$umkm->nama}' ditambahkan ke daftar favorit Anda.";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Add new product to UMKM catalog.
     */
    public function storeProduk(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdmin = $user && ($user->isRw() || $user->isAdminRt());

        if (!$isOwner && !$isAdmin) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_tersedia' => 'nullable|boolean',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('umkm_produks', 'public');
        }

        UmkmProduk::create([
            'umkm_id' => $umkm->id,
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
            'is_tersedia' => $request->boolean('is_tersedia', true),
        ]);

        return redirect()->route('umkm.show', $umkm->id . '#tab-produk')->with('success', "Produk '{$request->nama_produk}' berhasil ditambahkan ke katalog!");
    }

    /**
     * Update product details.
     */
    public function updateProduk(Request $request, Umkm $umkm, UmkmProduk $produk)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdmin = $user && ($user->isRw() || $user->isAdminRt());

        if (!$isOwner && !$isAdmin) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_tersedia' => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            if ($produk->foto && !preg_match('/^https?:\/\//', $produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $produk->foto = $request->file('foto')->store('umkm_produks', 'public');
        }

        $produk->nama_produk = $request->nama_produk;
        $produk->harga = $request->harga;
        $produk->deskripsi = $request->deskripsi;
        $produk->is_tersedia = $request->boolean('is_tersedia', true);
        $produk->save();

        return redirect()->route('umkm.show', $umkm->id . '#tab-produk')->with('success', "Produk '{$produk->nama_produk}' berhasil diperbarui.");
    }

    /**
     * Delete product.
     */
    public function destroyProduk(Umkm $umkm, UmkmProduk $produk)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdmin = $user && ($user->isRw() || $user->isAdminRt());

        if (!$isOwner && !$isAdmin) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if ($produk->foto && !preg_match('/^https?:\/\//', $produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }
        $nama = $produk->nama_produk;
        $produk->delete();

        return redirect()->route('umkm.show', $umkm->id . '#tab-produk')->with('success', "Produk '{$nama}' berhasil dihapus dari katalog.");
    }

    /**
     * Submit customer review & rating.
     */
    public function storeUlasan(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Owner cannot review their own store
        if ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik))) {
            return redirect()->back()->with('error', 'Anda tidak dapat memberikan ulasan pada usaha milik sendiri.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:5|max:1000',
        ]);

        // Each resident can create or update their single review
        $ulasan = UmkmUlasan::updateOrCreate(
            [
                'umkm_id' => $umkm->id,
                'user_id' => $user->id,
            ],
            [
                'nama_pembeli' => $user->name,
                'rating' => $request->rating,
                'komentar' => $request->komentar,
                'is_verified_purchase' => true,
            ]
        );

        $umkm->refreshRating();

        return redirect()->route('umkm.show', $umkm->id . '#tab-ulasan')->with('success', 'Ulasan dan rating Anda berhasil dipublikasikan. Terima kasih telah mendukung UMKM warga!');
    }

    /**
     * Edit review by buyer.
     */
    public function updateUlasan(Request $request, Umkm $umkm, UmkmUlasan $ulasan)
    {
        $user = Auth::user();
        if (!$user || $ulasan->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Akses ditolak: Anda hanya dapat mengedit ulasan milik sendiri.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:5|max:1000',
        ]);

        $ulasan->rating = $request->rating;
        $ulasan->komentar = $request->komentar;
        $ulasan->save();

        $umkm->refreshRating();

        return redirect()->route('umkm.show', $umkm->id . '#tab-ulasan')->with('success', 'Perubahan ulasan Anda berhasil disimpan.');
    }

    /**
     * Reply to buyer review by store owner.
     */
    public function replyUlasan(Request $request, Umkm $umkm, UmkmUlasan $ulasan)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdmin = $user && ($user->isRw() || $user->isAdminRt());

        if (!$isOwner && !$isAdmin) {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya pemilik usaha yang dapat membalas ulasan.');
        }

        $request->validate([
            'balasan_penjual' => 'required|string|max:1000',
        ]);

        $ulasan->balasan_penjual = $request->balasan_penjual;
        $ulasan->balasan_at = now();
        $ulasan->save();

        return redirect()->route('umkm.show', $umkm->id . '#tab-ulasan')->with('success', 'Balasan tanggapan penjual berhasil dikirim!');
    }

    /**
     * Report an inappropriate review.
     */
    public function reportUlasan(Request $request, Umkm $umkm, UmkmUlasan $ulasan)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'report_reason' => 'required|string|max:255',
        ]);

        $ulasan->is_reported = true;
        $ulasan->report_reason = $request->report_reason;
        $ulasan->save();

        return redirect()->route('umkm.show', $umkm->id . '#tab-ulasan')->with('success', 'Laporan ulasan telah diterima dan diteruskan kepada Pengurus RW untuk dimoderasi.');
    }

    /**
     * Moderate reported review (Admin RW only).
     */
    public function moderateUlasan(Request $request, Umkm $umkm, UmkmUlasan $ulasan)
    {
        $user = Auth::user();
        if (!$user || !$user->isRw()) {
            return redirect()->back()->with('error', 'Akses dibatasi hanya untuk Admin RW.');
        }

        $request->validate([
            'action' => 'required|in:hide,unhide,dismiss,delete',
        ]);

        $action = $request->action;

        if ($action === 'hide') {
            $ulasan->is_hidden = true;
            $ulasan->is_reported = false;
            $ulasan->save();
            $msg = 'Ulasan berhasil disembunyikan dari publik.';
        } elseif ($action === 'unhide') {
            $ulasan->is_hidden = false;
            $ulasan->save();
            $msg = 'Ulasan kembali ditampilkan ke publik.';
        } elseif ($action === 'dismiss') {
            $ulasan->is_reported = false;
            $ulasan->report_reason = null;
            $ulasan->save();
            $msg = 'Laporan ulasan ditolak dan ulasan tetap dipertahankan.';
        } elseif ($action === 'delete') {
            $ulasan->delete();
            $msg = 'Ulasan yang melanggar telah dihapus permanen oleh Admin RW.';
        }

        $umkm->refreshRating();

        return redirect()->route('umkm.show', $umkm->id . '#tab-ulasan')->with('success', $msg);
    }

    /**
     * Delete UMKM.
     */
    public function destroy(Umkm $umkm)
    {
        $user = Auth::user();
        $isOwner = $user && ($umkm->user_id === $user->id || strtolower(trim($user->name)) === strtolower(trim($umkm->pemilik)));
        $isAdminRw = $user && $user->isRw();
        $isAdminRt = $user && $user->isAdminRt();

        if (!$isOwner && !$isAdminRw && !($isAdminRt && $umkm->rt_number === $user->rt_number)) {
            abort(403, 'Akses ditolak.');
        }

        $nama = $umkm->nama;
        $rt = $umkm->rt_number;

        if ($umkm->cover_image && !preg_match('/^https?:\/\//', $umkm->cover_image)) {
            Storage::disk('public')->delete($umkm->cover_image);
        }

        $umkm->delete();

        AuditLogger::log(
            'delete',
            'Direktori UMKM',
            $umkm->id,
            "Usaha UMKM '{$nama}' dihapus dari direktori oleh {$user->name}.",
            ['nama' => $nama],
            null,
            $rt
        );

        return redirect()->route('umkm.index')->with('success', "Usaha '{$nama}' telah dihapus dari direktori UMKM.");
    }
}
