# 🗄️ Baseline Skema Basis Data — KONEKSA Platform

Dokumen ini memuat perancangan struktur tabel basis data relasional MySQL 8 untuk mendukung 21 fitur operasional KONEKSA sesuai proposal pengembangan produk.

---

## 1. Diagram Relasi Entitas (Entity-Relationship Diagram)

```mermaid
erDiagram
    users ||--o| businesses : "owns as UMKM"
    users ||--o| partner_profiles : "owns as Mitra"
    users ||--o{ programs : "creates as Pemda"
    users ||--o{ verification_requests : "reviewed by Pemda"

    businesses ||--o{ products : "sells"
    businesses ||--o{ transactions : "records"
    businesses ||--o{ program_applications : "registers"
    businesses ||--o{ collaboration_requests : "participates"
    businesses ||--o{ verification_requests : "requests"

    partner_profiles ||--o{ collaboration_requests : "sends/receives"
    partner_profiles ||--o{ digital_offerings : "publishes"

    products ||--o{ transaction_items : "included in"
    transactions ||--o{ transaction_items : "contains"

    programs ||--o{ program_applications : "receives"

    users {
        bigint id PK
        string name
        string email UK
        string password
        string role "umkm, partner, government, admin"
        string status "active, inactive"
        timestamp created_at
    }

    businesses {
        bigint id PK
        bigint user_id FK, UK
        string business_name
        string slug UK
        string category "kuliner, fashion, kriya, jasa, agribisnis, dll"
        string address
        string phone
        string legal_type "perorangan, cv, pt, belum_ada"
        string verification_status "unverified, pending, verified, rejected"
        text business_needs
        boolean is_published
        timestamp created_at
    }

    partner_profiles {
        bigint id PK
        bigint user_id FK, UK
        string organization_name
        string organization_type "korporasi, bumn, startup, komunitas, edukasi"
        string website
        text focus_areas
        string contact_person
        timestamp created_at
    }

    products {
        bigint id PK
        bigint business_id FK
        string name
        string sku
        decimal price
        int stock
        int minimum_stock_alert
        string image_url
        boolean is_active
        timestamp created_at
    }

    transactions {
        bigint id PK
        bigint business_id FK
        string invoice_number UK
        decimal total_amount
        string payment_method "tunai, transfer, qris"
        timestamp transaction_date
        timestamp created_at
    }

    transaction_items {
        bigint id PK
        bigint transaction_id FK
        bigint product_id FK
        int quantity
        decimal unit_price
        decimal subtotal
    }

    collaboration_requests {
        bigint id PK
        bigint business_id FK
        bigint partner_profile_id FK
        string title
        text proposal_description
        string sender_role "partner, umkm"
        string status "pending, accepted, rejected, in_progress, completed"
        timestamp created_at
    }

    programs {
        bigint id PK
        bigint created_by_user_id FK
        string title
        text description
        date registration_start
        date registration_end
        int quota
        string status "draft, open, closed, completed"
        timestamp created_at
    }

    program_applications {
        bigint id PK
        bigint program_id FK
        bigint business_id FK
        string status "pending, accepted, rejected"
        text notes
        timestamp created_at
    }

    digital_offerings {
        bigint id PK
        bigint partner_profile_id FK
        string title
        text description
        string solution_category
        string status "active, archived"
        timestamp created_at
    }

    verification_requests {
        bigint id PK
        bigint business_id FK
        bigint reviewed_by_user_id FK
        string document_path
        string status "pending, approved, rejected"
        text reviewer_notes
        timestamp reviewed_at
        timestamp created_at
    }
```

---

## 2. Aturan Transaksi & Integritas Basis Data
1. **Pencatatan Transaksi & Penurunan Stok (F06 & F07)**:
   - Wajib dijalankan dalam transaksi atomik (`DB::transaction(...)`).
   - Jika stok barang tidak mencukupi untuk jumlah yang dibeli, rollback transaksi dan kembalikan galat 422.
2. **Pencegahan Over-Quota pada Seleksi Program (F15)**:
   - Saat petugas menyetujui pendaftar program, sistem wajib memeriksa `accepted_count < quota` dengan *row-level locking* (`lockForUpdate()`) agar kuota program tidak pernah terlampaui.
3. **Pemberian Nilai Default & Kolom Nullable**:
   - Seluruh perubahan skema pada tabel eksisting harus mematuhi panduan non-destruktif di `docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md`.

