# ✉️ PENERBITAN_SURAT

> Pengajuan surat keterangan secara online dengan persetujuan berjenjang **RT → RW** dan **Tanda Tangan Elektronik (TTE)** berbasis hash SHA-256.

## 1. Ringkasan
Warga mengajukan surat (domisili, SKU, SKTM, pengantar SKCK, pengantar nikah, dll). Admin RT memverifikasi, lalu Admin RW memberikan TTE dan menerbitkan. Surat yang terbit dapat dilihat/dicetak dalam format resmi (kop RW, data pemohon, blok TTE + hash).

Admin juga dapat **membuat surat atas nama warga**:
- Dibuat Admin RT → langsung `pending_rw`
- Dibuat Admin RW → langsung `selesai` + TTE

## 2. Aktor & Hak Akses
| Aktor | Ajukan/Buat | Lihat Daftar | Setujui RT | TTE & Terbitkan | Lihat PDF |
|---|---|---|---|---|---|
| Warga / Bendahara | ✅ untuk diri sendiri → `pending_rt` | Miliknya | ❌ | ❌ | ✅ jika `selesai` |
| Admin RT | ✅ untuk warga RT-nya → `pending_rw` | Surat RT-nya | ✅ | ❌ | ✅ |
| Admin RW | ✅ untuk warga mana pun → `selesai` | Semua RT | ❌ | ✅ | ✅ |

### Tab Filter di Halaman Daftar
| Role | Tab | Status yang ditampilkan |
|---|---|---|
| Admin RW | Semua · **Menunggu Disetujui (RW)** · Sudah Selesai | all · `pending_rw` · `selesai` |
| Admin RT | Semua · **Menunggu Disetujui (RT)** · Menunggu RW · Sudah Selesai | all · `pending_rt` · `pending_rw` · `selesai` |
| Warga | (tanpa tab) | semua miliknya |

## 3. File Terkait
| Jenis | Path |
|---|---|
| Controller | `app/Http/Controllers/SuratPengajuanController.php` |
| Model | `app/Models/SuratPengajuan.php` (tabel `surat_pengajuans`) |
| View | `resources/views/surat/index.blade.php`, `create.blade.php`, `pdf.blade.php` |
| Seeder | `database/seeders/SuratPengajuanSeeder.php` |
| Legacy (jangan dipakai) | `SuratController`, model `Surat`, tabel `surats` |

## 4. Route
| Method | URI | Name | Aksi |
|---|---|---|---|
| GET | `/surat?filter=all\|pending\|pending_rw\|selesai` | `surat.index` | Daftar + tab |
| GET | `/surat/create` | `surat.create` | Form |
| POST | `/surat` | `surat.store` | Simpan pengajuan |
| POST | `/surat/{surat}/approve-rt` | `surat.approve_rt` | Admin RT setujui → `pending_rw` |
| POST | `/surat/{surat}/approve-rw` | `surat.approve_rw` | Admin RW TTE → `selesai` |
| GET | `/surat/{surat}/pdf` | `surat.pdf` | Tampilan surat resmi (status `selesai`) |

## 5. Aturan Bisnis
1. **Jenis surat** (dropdown): Surat Keterangan Domisili, Surat Keterangan Usaha (SKU), Surat Keterangan Tidak Mampu (SKTM), Surat Pengantar Kelakuan Baik (SKCK), Surat Pengantar Pernikahan, dan lainnya sesuai `create.blade.php`.
2. `rt_number` surat = RT pemohon (target user saat dibuat admin; fallback `'006'`).
3. **TTE Hash**:
   - Approve RW: `sha256(id | jenis_surat | now())`
   - Dibuat langsung Admin RW: `sha256(random(10) | jenis_surat | now())`
4. Setelah TTE: `pdf_path = route('surat.pdf', id)`.
5. `approveRt` → `abort(403)` jika bukan admin atau Admin RW. `approveRw` → `abort(403)` jika bukan Admin RW.
6. `downloadPdf` → `abort(403)` jika status ≠ `selesai`.
7. Warga dapat mengajukan hanya jika akun terhubung dengan data `Warga` (tombol submit di-disable bila `$warga` null).

## 6. Use Case Diagram
```mermaid
flowchart LR
    W(["👤 Warga"])
    RT(["👤 Admin RT"])
    RW(["👤 Admin RW"])
    UC1(("Ajukan surat"))
    UC2(("Lihat status surat"))
    UC3(("Verifikasi & setujui (RT)"))
    UC4(("Buat surat untuk warga"))
    UC5(("TTE & terbitkan (RW)"))
    UC6(("Lihat / cetak surat resmi"))
    UC7(("Filter tab status"))
    W --> UC1
    W --> UC2
    W --> UC6
    RT --> UC3
    RT --> UC4
    RT --> UC7
    RW --> UC5
    RW --> UC4
    RW --> UC7
    RW --> UC6
    UC5 -. include .-> UC6
```

## 7. Flowchart — Alur End-to-End
```mermaid
flowchart TD
    A["Warga isi form: jenis surat, keperluan"] --> B["status = pending_rt"]
    B --> C["Admin RT buka tab Menunggu Disetujui (RT)"]
    C --> D{"Data valid?"}
    D -- Tidak --> E["(Belum ada fitur tolak) Hubungi warga"]
    D -- Ya --> F["Klik Setujui, status = pending_rw"]
    F --> G["Admin RW buka tab Menunggu Disetujui (RW)"]
    G --> H["Klik TTE & Terbitkan"]
    H --> I["Generate SHA-256 tte_hash, set pdf_path"]
    I --> J["status = selesai"]
    J --> K["Warga klik Unduh/Lihat Surat"]
    K --> L["Render surat/pdf.blade.php dengan blok TTE + hash"]

    AR["Admin RT buat surat untuk warga"] --> F
    AW["Admin RW buat surat untuk warga"] --> I
```

## 8. Sequence Diagram — Approve RW (TTE)
```mermaid
sequenceDiagram
    actor RW as Admin RW
    participant V as surat/index.blade.php
    participant SC as SuratPengajuanController
    participant S as SuratPengajuan
    participant DB as SQLite

    RW->>V: GET /surat?filter=pending
    V->>SC: index(filter=pending)
    SC->>S: where status pending_rw, with user.warga, latest
    S->>DB: SELECT
    DB-->>SC: surats
    SC-->>RW: daftar menunggu TTE
    RW->>SC: POST /surat/{id}/approve-rw
    SC->>SC: cek isAdmin dan rt_number = 000, else 403
    SC->>SC: tteHash = sha256(id, jenis, now)
    SC->>S: update status selesai, tte_hash, pdf_path
    S->>DB: UPDATE
    SC-->>RW: redirect surat.index + flash TTE berhasil
```

## 9. Sequence Diagram — Pengajuan oleh Warga
```mermaid
sequenceDiagram
    actor W as Warga
    participant SC as SuratPengajuanController
    participant S as SuratPengajuan
    W->>SC: GET /surat/create
    SC-->>W: form (data warga terisi otomatis)
    W->>SC: POST /surat jenis_surat, keperluan
    SC->>SC: validate
    SC->>S: create user_id, rt_number, status pending_rt
    SC-->>W: redirect + flash dikirim ke RT
```

## 10. State Diagram
```mermaid
stateDiagram-v2
    [*] --> pending_rt : Warga mengajukan
    [*] --> pending_rw : Admin RT membuat
    [*] --> selesai : Admin RW membuat (langsung TTE)
    pending_rt --> pending_rw : Admin RT approveRt
    pending_rw --> selesai : Admin RW approveRw + TTE SHA-256
    selesai --> [*]
```

## 11. Class Diagram
```mermaid
classDiagram
    class SuratPengajuanController {
        +index(Request) View
        +create() View
        +store(Request) RedirectResponse
        +approveRt(SuratPengajuan) RedirectResponse
        +approveRw(SuratPengajuan) RedirectResponse
        +downloadPdf(SuratPengajuan) View
    }
    class SuratPengajuan {
        +int user_id
        +string rt_number
        +string jenis_surat
        +text keperluan
        +string status
        +string tte_hash
        +string pdf_path
        +user() BelongsTo
    }
    class User {
        +warga() HasOne
        +isAdminRt() bool
        +isAdminRw() bool
    }
    class Warga
    SuratPengajuanController ..> SuratPengajuan
    SuratPengajuan "0..*" --> "1" User
    User "1" --> "0..1" Warga
```

## 12. Validasi
| Konteks | Field | Rule |
|---|---|---|
| Admin | target_user_id | required, exists:users,id |
| Semua | jenis_surat | required, max 255 |
| Semua | keperluan | required, max 2000 |

## 13. Known Issues & Pengembangan Lanjutan
| # | Isu | Rekomendasi |
|---|---|---|
| 🔴 1 | `downloadPdf` tanpa cek kepemilikan | Izinkan hanya pemilik, Admin RT dengan RT sama, Admin RW |
| 🟠 2 | `approveRt` tidak cek `surat.rt_number == user.rt_number` & status `pending_rt` | Tambah guard |
| 🟠 3 | `approveRw` tidak cek status `pending_rw` (bisa TTE ulang surat selesai → hash berubah) | Tambah guard |
| 🟡 4 | Tidak ada status **ditolak** + alasan | Tambah `ditolak_rt`, `ditolak_rw`, kolom `catatan` |
| 🟡 5 | Admin memilih `target_user_id` dari daftar Warga — warga tanpa akun user tidak bisa dipilih secara valid | Sinkronkan relasi user–warga atau izinkan `warga_id` |
| 🟡 6 | "PDF" masih berupa halaman HTML + QR mock | dompdf + QR asli berisi URL verifikasi publik `/verifikasi/{hash}` |
| 🟢 7 | Nomor surat resmi belum ada | Generate `nomor_surat` berurutan per tahun |
