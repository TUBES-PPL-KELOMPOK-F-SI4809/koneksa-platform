# 🔌 Panduan Spesifikasi REST API — KONEKSA Platform

Dokumen ini mendefinisikan standar kontrak pertukaran data API, format envelope respon JSON, kode status HTTP, dan konvensi penanganan error pada **KONEKSA Platform**.

---

## 1. Konvensi Endpoint & HTTP Verbs

* **Bentuk Dasar URL**: `/api/v1/{resource}`
* **Format Pengenal Resource**: Gunakan kata benda plural (`/api/v1/initiatives`, `/api/v1/products`).

| Method | Endpoint | Fungsi | Status Sukses |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/businesses` | Mengambil daftar UMKM (filter kategori & status) | `200 OK` |
| `GET` | `/api/v1/businesses/{id}` | Mengambil detail profil UMKM publik | `200 OK` |
| `POST` | `/api/v1/transactions` | Mencatat transaksi penjualan kasir (atomik) | `201 Created` |
| `POST` | `/api/v1/collaboration-requests`| Mengirimkan proposal kerja sama | `201 Created` |
| `PATCH` | `/api/v1/verification-requests/{id}` | Menyetujui/menolak verifikasi administratif | `200 OK` |

---

## 2. Standar JSON Envelope Respon

Seluruh API internal maupun integrasi wajib mengembalikan format struktur terbungkus (*envelope*) seragam:

### 2.1 Respon Sukses Tunggal:
```json
{
    "success": true,
    "message": "Data transaksi berhasil disimpan",
    "data": {
        "id": 104,
        "invoice_number": "INV-202611-00104",
        "total_amount": 250000.00,
        "transaction_date": "2026-11-06T10:00:00Z"
    }
}
```

### 2.2 Respon Error Validasi (`422 Unprocessable Content`):
```json
{
    "success": false,
    "message": "Data formulir tidak valid",
    "errors": {
        "business_name": ["Nama usaha wajib diisi."],
        "category": ["Kategori yang dipilih tidak valid."]
    }
}
```

