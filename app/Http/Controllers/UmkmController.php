<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    protected function getUmkmCategories(): array
    {
        return [
            'Jasa',
            'Kerajinan',
            'Makanan & Minuman',
            'Jasa/Service',
            'Kuliner',
            'Perdagangan',
        ];
    }

    protected function getUmkmCategoryImages(): array
    {
        return [
            'jasa' => 'https://images.unsplash.com/photo-1515165562835-cf2d1d5113d6?auto=format&fit=crop&w=900&q=80',
            'kerajinan' => 'https://images.unsplash.com/photo-1477867082705-47a1d8d462f8?auto=format&fit=crop&w=900&q=80',
            'makanan & minuman' => 'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=900&q=80',
            'jasa/service' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80',
            'kuliner' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=80',
            'perdagangan' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
            'default' => 'images/umkm/default.svg',
        ];
    }

    public function index(Request $request)
    {
        $query = Umkm::query();

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                        ->orWhere('pemilik', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $usahas = $query->orderBy('nama', 'asc')->get();
        $categories = $this->getUmkmCategories();
        $categoryImages = $this->getUmkmCategoryImages();

        $totalUsaha = $usahas->count();
        $totalKategori = count($categories);
        $featuredUsaha = Umkm::all()->count();
        $featuredItems = Umkm::orderBy('created_at', 'desc')->take(6)->get();

        return view('umkm.index', compact('usahas', 'categories', 'totalUsaha', 'totalKategori', 'featuredUsaha', 'featuredItems', 'categoryImages'));
    }

    public function show(Request $request, Umkm $umkm)
    {
        $categoryImages = $this->getUmkmCategoryImages();
        $menuItems = $this->getUmkmMenuItems($umkm);
        $whatsappUrl = $this->buildWhatsappUrl(
            $umkm->telepon,
            "Halo%2C%20saya%20ingin%20order%20dari%20usaha%20{$umkm->nama}%20(%20Kategori%20{$umkm->kategori}%20)."
        );

        if ($request->filled('embed') || $request->ajax()) {
            return view('umkm._embed', compact('umkm', 'categoryImages', 'menuItems', 'whatsappUrl'));
        }

        return view('umkm.show', compact('umkm', 'categoryImages', 'menuItems', 'whatsappUrl'));
    }

    public function create()
    {
        return view('umkm.create');
    }

    public function categories()
    {
        return response()->json($this->getUmkmCategories());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pemilik' => 'nullable|string|max:255',
            'kategori' => 'required|string|in:Jasa,Kerajinan,Makanan & Minuman,Jasa/Service,Kuliner,Perdagangan',
            'telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        Umkm::create([
            'nama' => $validated['nama'],
            'pemilik' => $validated['pemilik'] ?? null,
            'kategori' => $validated['kategori'],
            'telepon' => $validated['telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil disimpan.');
    }

    protected function getUmkmMenuItems(Umkm $umkm): array
    {
        $defaultImage = 'images/umkm/product.svg';
        $itemsByCategory = [
            'jasa' => [
                ['nama' => 'Konsultasi Branding', 'deskripsi' => 'Sesi strategi brand dan visual identity.', 'harga' => 'Rp 250.000', 'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Desain Konten Sosial', 'deskripsi' => 'Konten promosi Instagram dan Facebook.', 'harga' => 'Rp 175.000', 'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Foto Produk', 'deskripsi' => 'Foto produk aesthetic untuk katalog online.', 'harga' => 'Rp 150.000', 'image' => 'https://images.unsplash.com/photo-1513865307998-0a263f588b83?auto=format&fit=crop&w=400&q=80'],
            ],
            'kerajinan' => [
                ['nama' => 'Tas Anyaman', 'deskripsi' => 'Handmade dengan bahan lokal berkualitas.', 'harga' => 'Rp 120.000', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Gelombang Lilin Aromaterapi', 'deskripsi' => 'Set lilin wangi untuk rumah dan hadiah.', 'harga' => 'Rp 65.000', 'image' => 'https://images.unsplash.com/photo-1509460913899-1b97f687de65?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Gelang Kulit', 'deskripsi' => 'Aksesoris stylish untuk sehari-hari.', 'harga' => 'Rp 75.000', 'image' => 'https://images.unsplash.com/photo-1535702351475-1f4eeddc25a2?auto=format&fit=crop&w=400&q=80'],
            ],
            'makanan & minuman' => [
                ['nama' => 'Es Kopi', 'deskripsi' => 'Kopi susu dingin dengan shot ekstra.', 'harga' => 'Rp 12.000', 'image' => 'es-kopi.svg'],
                ['nama' => 'Kue Lapis', 'deskripsi' => 'Kue lapis lembut manis cocok untuk camilan.', 'harga' => 'Rp 10.000', 'image' => 'kue-lapis.svg'],
                ['nama' => 'Sate Taichan', 'deskripsi' => 'Sate pedas kekinian dengan sambal special.', 'harga' => 'Rp 20.000', 'image' => 'sate-taichan.svg'],
            ],
            'jasa/service' => [
                ['nama' => 'Servis Elektronik', 'deskripsi' => 'Perbaikan cepat untuk alat rumah tangga.', 'harga' => 'Rp 120.000', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Jasa Cuci Motor', 'deskripsi' => 'Cuci kilat motor dengan detailing sederhana.', 'harga' => 'Rp 35.000', 'image' => 'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Cetak Spanduk', 'deskripsi' => 'Spanduk promosi ukuran custom.', 'harga' => 'Rp 90.000', 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=400&q=80'],
            ],
            'kuliner' => [
                ['nama' => 'Nasi Goreng Spesial', 'deskripsi' => 'Nasi goreng dengan topping ayam dan telur.', 'harga' => 'Rp 22.000', 'image' => 'nasi-goreng-spesial.svg'],
                ['nama' => 'Dessert Box', 'deskripsi' => 'Kotak dessert berbagai rasa untuk 2 orang.', 'harga' => 'Rp 55.000', 'image' => 'dessert-box.svg'],
                ['nama' => 'Teh Tarik', 'deskripsi' => 'Minuman hangat manis khas warung kopi.', 'harga' => 'Rp 12.000', 'image' => 'teh-tarik.svg'],
            ],
            'perdagangan' => [
                ['nama' => 'Paket Hemat', 'deskripsi' => 'Pilihan kebutuhan sehari-hari lengkap.', 'harga' => 'Rp 80.000', 'image' => 'https://images.unsplash.com/photo-1542831371-d531d36971e6?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Souvenir Lokal', 'deskripsi' => 'Pilihan souvenir unik untuk oleh-oleh.', 'harga' => 'Rp 40.000', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80'],
                ['nama' => 'Aksesoris Rumah', 'deskripsi' => 'Pajangan dekoratif untuk interior.', 'harga' => 'Rp 65.000', 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=400&q=80'],
            ],
        ];

        $categoryKey = strtolower(trim($umkm->kategori ?? 'default'));

        return $itemsByCategory[$categoryKey] ?? [
            ['nama' => 'Produk Pilihan', 'deskripsi' => 'Menu unggulan sesuai usaha.', 'harga' => 'Rp 25.000', 'image' => 'produk-pilihan.svg'],
            ['nama' => 'Layanan Pesan', 'deskripsi' => 'Pesan cepat lewat WhatsApp.', 'harga' => 'Rp 0', 'image' => 'layanan-pesan.svg'],
        ];
    }

    protected function buildWhatsappUrl(?string $phone, string $text = null): string
    {
        $clean = $phone ? preg_replace('/[^0-9+]/', '', $phone) : '';
        $clean = preg_replace('/^\+/', '', $clean);

        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        if ($clean === '') {
            return 'https://wa.me/';
        }

        $url = 'https://wa.me/'.$clean;

        if ($text) {
            $url .= '?text='.urlencode($text);
        }

        return $url;
    }
}
