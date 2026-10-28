# 💰 TRANSAKSI_KAS

> Pencatatan pemasukan & pengeluaran kas dengan isolasi per RT dan agregasi gabungan untuk RW.

## 1. Ringkasan
Setiap uang masuk (iuran, donasi) dan keluar (kebersihan, listrik, perbaikan) dicatat di tabel **`transactions`** (model `Pembayaran`). Setiap baris wajib memiliki `rt_number` sehingga **tiap RT memiliki kas sendiri**, sedangkan **Admin RW melihat kas gabungan** seluruh RT. Halaman index menampilkan tabel transaksi (15/hal) + grafik 6 bulan.

## 2. Aktor & Hak Akses
| Aktor | Lihat | Tambah | Hapus | Cetak |
|---|---|---|---|---|
| Warga | ❌ redirect + error "hanya untuk pengurus" | ❌ | ❌ | ❌ |
| Admin RT | ✅ kas RT-nya | ✅ (warga RT-nya) | ✅ hanya transaksi RT-nya | ✅ RT-nya |
| Admin RW | ✅ gabungan | ✅ semua warga | ✅ semua | ✅ gabungan |
| Bendahara | ✅ gabungan | ✅ | ✅ | ✅ |

## 3. File Terkait
| Jenis | Path |
|---|---|
| Controller | `app/Http/Controllers/PembayaranController.php` (`index`, `create`, `store`, `destroy`, `cetakLaporan`) |
| Model | `app/Models/Pembayaran.php` → `$table = 'transactions'` |
| View | `resources/views/pembayaran/index.blade.php`, `create.blade.php`, `cetak.blade.php` |
| Migrasi | `2026_05_07_202901_create_transactions_table.php`, `2026_07_24_041417_add_rt_number_to_transactions_table.php` |

## 4. Route
| Method | URI | Name | Aksi |
|---|---|---|---|
| GET | `/pembayaran` | `pembayaran.index` | Tabel + grafik |
| GET | `/pembayaran/create` | `pembayaran.create` | Form |
| POST | `/pembayaran` | `pembayaran.store` | Simpan |
| DELETE | `/pembayaran/{id}` | `pembayaran.destroy` | Hapus |
| GET | `/laporan/cetak` | `laporan.cetak` | Cetak seluruh transaksi (filter role) |

## 5. Aturan Bisnis
1. **Penentuan `rt_number` saat simpan:**
   ```
   rt_number = isAdminRt ? user.rt_number : '000'
   jika warga_id diisi → rt_number = warga.rt_number
   ```
   `'000'` = **Kas Umum RW** (transaksi Admin RW tanpa warga).
2. `warga_id` opsional → kosong = "Kas Umum / Non-Warga".
3. Kategori dari dropdown:
   - Pemasukan: `Iuran Bulanan`, `Iuran Sukarela`, `Donasi Warga`
   - Pengeluaran: `Kebersihan`, `Keamanan`, `Listrik & Air`, `Perbaikan/Maintenance`, `Lain-lain`
4. Kategori yang mengandung kata **`Iuran`** dihitung sebagai pembayaran iuran di [LAPORAN_IURAN.md](./LAPORAN_IURAN.md) & dashboard.
5. Admin RT tidak boleh menghapus transaksi RT lain.
6. Grafik: template 6 bulan terakhir (nilai 0) di-merge dengan data yang di-`groupBy` per `Y-m` **di PHP** (portable).

## 6. Use Case Diagram
```mermaid
flowchart LR
    RT(["👤 Admin RT"])
    RW(["👤 Admin RW"])
    BD(["👤 Bendahara"])
    UC1(("Lihat transaksi kas"))
    UC2(("Catat pemasukan"))
    UC3(("Catat pengeluaran"))
    UC4(("Hapus transaksi"))
    UC5(("Lihat grafik 6 bulan"))
    UC6(("Cetak semua transaksi"))
    RT --> UC1
    RT --> UC2
    RT --> UC3
    RT --> UC4
    RT --> UC6
    RW --> UC1
    RW --> UC2
    RW --> UC3
    RW --> UC4
    RW --> UC6
    BD --> UC1
    BD --> UC2
    UC1 -. include .-> UC5
```

## 7. Flowchart — Simpan Transaksi
```mermaid
flowchart TD
    A["POST /pembayaran"] --> B{"Validasi lolos?"}
    B -- Tidak --> C["Kembali + error"]
    B -- Ya --> D{"isAdminRt?"}
    D -- Ya --> E["rt = user.rt_number"]
    D -- Tidak --> F["rt = 000 (Kas Umum RW)"]
    E --> G{"warga_id diisi?"}
    F --> G
    G -- Ya --> H["rt = warga.rt_number"]
    G -- Tidak --> I["pertahankan rt"]
    H --> J["Pembayaran::create"]
    I --> J
    J --> K["Redirect pembayaran.index + flash sukses"]
```

## 8. Sequence Diagram — Index + Grafik
```mermaid
sequenceDiagram
    actor A as Admin
    participant PC as PembayaranController
    participant P as Pembayaran
    participant V as pembayaran/index.blade.php
    participant CJ as Chart.js

    A->>PC: GET /pembayaran
    PC->>PC: role warga? redirect dashboard
    PC->>P: with warga, orderBy tanggal desc
    opt Admin RT
        PC->>P: where rt_number = user.rt_number
    end
    P-->>PC: paginate 15
    PC->>P: tanggal >= 5 bulan lalu (filter RT)
    P-->>PC: koleksi transaksi
    PC->>PC: groupBy Y-m, sum masuk dan keluar, merge template
    PC->>V: pembayarans, chartData
    V-->>A: tabel + canvas
    A->>CJ: render grafik
```

## 9. Class Diagram
```mermaid
classDiagram
    class PembayaranController {
        +index() View
        +create() View
        +store(Request) RedirectResponse
        +destroy(id) RedirectResponse
        +cetakLaporan() View
    }
    class Pembayaran {
        <<table transactions>>
        +int warga_id
        +string rt_number
        +enum tipe
        +bigint jumlah
        +string kategori
        +text keterangan
        +date tanggal
        +warga() BelongsTo
    }
    class Warga
    PembayaranController ..> Pembayaran
    Pembayaran "0..*" --> "0..1" Warga
```

## 10. Validasi
| Field | Rule |
|---|---|
| warga_id | nullable, exists:wargas,id |
| tipe | required, in masuk,keluar |
| jumlah | required, numeric |
| kategori | required, string |
| tanggal | required, date |
| keterangan | nullable |

## 11. Known Issues & Pengembangan Lanjutan
| # | Isu | Rekomendasi |
|---|---|---|
| 🟠 1 | `store` tidak menolak role warga secara eksplisit (hanya `create` yang redirect) | Tambah guard `isWarga()` di `store` |
| 🟠 2 | Admin RT bisa mengirim `warga_id` milik RT lain via request manipulasi → transaksi masuk ke RT lain | Validasi warga harus di RT admin |
| 🟡 3 | `jumlah` tidak dibatasi minimal > 0 | `numeric|min:1` |
| 🟡 4 | Tidak ada fitur edit transaksi (hanya hapus) | Tambah edit + audit log |
| 🟡 5 | Tidak ada bukti transfer/nota | Kolom `bukti` (upload) |
| 🟢 6 | Belum ada soft delete untuk jejak audit | `SoftDeletes` |
