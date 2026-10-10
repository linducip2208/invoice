# AI Features — Sprint 5

> **PRINSIP UTAMA:** ikuti pattern dynamic provider — JANGAN hardcode OpenAI/Anthropic/Gemini di code. User input API key sendiri di admin UI.
> Reference: `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`

---

## 5.0 AI Provider Foundation (prerequisite)

**Status:** ❌ | **Priority:** 🔥 Critical (prerequisite untuk semua AI features) | **Effort:** 2 hari

### Why
Tanpa provider abstraction, tiap fitur AI berakhir hardcode key/model. Saat OpenAI naikin harga atau user mau switch ke Groq/DeepSeek/Anthropic — pain. Bikin foundation 1x, semua fitur AI tinggal pakai.

### Database Schema
**Tabel BARU `ai_providers`** (lihat 01-DATABASE-RELATIONS Section 11).
**Tabel BARU `ai_usage_logs`** (Section 12).

**Tambahan tabel `ai_feature_mapping`:**
```sql
id BIGINT PK
workspace_id BIGINT FK
feature VARCHAR(50)  -- 'description_generator','ocr','translate','predict','risk_score','autocomplete'
provider_id BIGINT FK→ai_providers ON DELETE SET NULL
model VARCHAR(100) NULL  -- override default model
is_enabled BOOLEAN DEFAULT TRUE
config JSON NULL  -- per-feature param (mis. temperature, max_tokens)
UNIQUE(workspace_id, feature)
INDEX(workspace_id)
```

**Workspace settings:**
- `ai_monthly_budget_idr DECIMAL(10,2) NULL` — soft cap, alert kalau lewat

### Routes
```
GET    /ai/providers                       ai.providers.index
POST   /ai/providers                       ai.providers.store
PUT    /ai/providers/{provider}            ai.providers.update
DEL    /ai/providers/{provider}            ai.providers.destroy
POST   /ai/providers/{provider}/test       ai.providers.test         (verify API key)
POST   /ai/providers/{provider}/fetch-models  ai.providers.fetch-models  (call /v1/models)
GET    /ai/feature-mapping                 ai.feature-mapping.index
PUT    /ai/feature-mapping/{feature}       ai.feature-mapping.update
GET    /ai/usage                           ai.usage.index             (cost & token tracking)
```

### Controllers
- `App\Http\Controllers\AiProviderController`
- `App\Http\Controllers\AiFeatureMappingController`
- `App\Http\Controllers\AiUsageController`

### Service / Adapter Architecture
```
App\Services\Ai\
├── Contracts\
│   ├── ChatAdapter.php          (interface)
│   ├── EmbeddingAdapter.php
│   └── VisionAdapter.php
├── Adapters\
│   ├── OpenAiCompatibleAdapter.php   (OpenAI, DeepSeek, Groq, Mistral, Together, Fireworks, OpenRouter, xAI, Ollama, LM Studio, Cerebras, Anyscale)
│   ├── AnthropicFormatAdapter.php
│   └── GeminiFormatAdapter.php
├── AdapterFactory.php
├── AiClient.php                       (high-level wrapper)
└── UsageTracker.php
```

**Naming rule:** TIDAK ADA class dengan nama vendor (`OpenAiAdapter`, `GroqAdapter`). Hanya **format-based**.

**Resolve flow:**
```php
$adapter = AdapterFactory::resolve($workspace, 'description_generator');
// 1. cek ai_feature_mapping ws+feature → provider_id
// 2. load ai_providers → api_format
// 3. instantiate adapter sesuai format
// 4. inject base_url, api_key (decrypted), default_model

$response = $adapter->chat([
    'messages' => [...],
    'model' => $model,
    'max_tokens' => 500,
]);
// AdapterFactory wraps with UsageTracker untuk insert ke ai_usage_logs
```

### View
- `resources/views/ai/providers/index.blade.php` — table providers + add new modal
- `resources/views/ai/providers/form.blade.php` — name, format, base_url, api_key, default_model, pricing
- `resources/views/ai/feature-mapping.blade.php` — tabel feature × provider dropdown
- `resources/views/ai/usage.blade.php` — chart pemakaian per feature × bulan, total cost estimate

### Preset Templates (autofill convenience)
File JSON di `storage/app/ai-presets/`:
- `openai.json`: `{"name":"OpenAI","base_url":"https://api.openai.com/v1","models":["gpt-4o-mini","gpt-4o"]}`
- `groq.json`: `{"name":"Groq","base_url":"https://api.groq.com/openai/v1","models":["llama-3.3-70b","mixtral-8x7b"]}`
- dst.

UI: tombol "Pilih Preset" → autofill, user **boleh edit** sebelum simpan, **boleh skip** preset entirely. Code TIDAK pernah reference preset file saat runtime.

### Edge Cases
- Provider API down → fallback ke provider kedua kalau ada (configurable via `ai_feature_mapping.fallback_provider_id` — phase 2)
- API key invalid → error handling rapi, user dapat notif "API key gagal di provider X, fix di settings"
- Rate limit → respect `Retry-After` header, queue retry

### Acceptance Criteria
- [ ] Tambah provider via UI dengan format-based adapter
- [ ] Test connection berhasil sebelum simpan
- [ ] Map feature ke provider via feature-mapping page
- [ ] Usage tracking per feature × hari
- [ ] Soft budget alert kalau lewat batas

---

## 5.1 Smart Description Generator ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
User ngetik "design landing page" — AI expand jadi: *"Desain UI/UX landing page (5 section: hero, fitur, testimoni, pricing, footer), 2x revisi major, file source dalam Figma + export PNG/JPG."*. Ngirit waktu bikin invoice profesional.

### Database Schema
Tidak ada tabel baru. Track usage di `ai_usage_logs` (feature='description_generator').

### Routes
```
POST /api/ai/description/expand            api.ai.description.expand
```

### Controller
- `App\Http\Controllers\Api\AiDescriptionController::expand`

### Service
- `App\Services\Ai\Features\DescriptionGenerator`
  - Input: `keyword`, `context` (mis. industri user dari workspace)
  - Output: `expanded_description`, `tokens_used`
  - Prompt template:
    ```
    Anda penulis invoice profesional. User input: "{{keyword}}".
    Industri: {{workspace_industry}}.
    Buat deskripsi 1-2 kalimat untuk line item invoice yang:
    - Spesifik (sebut deliverable)
    - Tidak ambigu
    - Bahasa Indonesia formal
    Output: hanya deskripsi tanpa penjelasan tambahan.
    ```

### UI
Di tabel item form (create & edit invoice):
- Setiap row, di samping field Description ada tombol **✨ AI Expand**
- Klik → dropdown loading → field terisi otomatis (user boleh edit)

### Edge Cases
- AI provider belum di-set → tombol disabled + tooltip "Set up AI provider dulu"
- Output >500 char → trim, warning user
- Rate limit → cool-down 5 detik per click

### Acceptance Criteria
- [ ] Tombol AI Expand di setiap row item
- [ ] Hasil expand fit di description field
- [ ] Track usage per call
- [ ] Audit log per expand action

---

## 5.2 OCR Receipt → Expense / Invoice ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 2-3 hari

### Why
Power user feature. Foto struk → AI extract: nama merchant, tanggal, total, items. Auto-create expense atau line items invoice. Cuts 80% data entry.

### Database Schema
Modifikasi `expenses`:
- `ocr_status ENUM('not_ocr','processing','done','failed') NULL`
- `ocr_extracted JSON NULL`
- `ocr_provider_id BIGINT NULL FK→ai_providers`

(Kalau OCR untuk supplier invoice → buat tabel `inbox_invoices` mirror dengan ocr_status — phase 2)

### Routes
```
POST /expenses/ocr-upload              expenses.ocr.upload      (single image upload + OCR)
GET  /expenses/{expense}/ocr-status    expenses.ocr.status      (poll while processing)
POST /expenses/{expense}/ocr-confirm   expenses.ocr.confirm     (user review → save)
```

### Controller
- `App\Http\Controllers\ExpenseOcrController`

### Service
- `App\Services\Ai\Features\OcrReceipt`
  - `extract(string $imagePath): array`
  - Pakai vision-capable model (GPT-4o, Claude Sonnet 3.5+, Gemini Pro Vision)
  - Prompt:
    ```
    Foto berikut adalah struk/receipt. Extract:
    - merchant_name
    - date (YYYY-MM-DD)
    - subtotal
    - tax
    - total
    - currency (IDR/USD)
    - items: [{description, qty, unit_price, total}]
    Output JSON. Kalau tidak ada → null.
    ```

### Flow
1. User upload foto struk via mobile (atau drag-drop di desktop)
2. Backend save image → trigger job `OcrReceiptJob`
3. Job call AI vision adapter → parse JSON
4. Update expense.ocr_extracted + ocr_status='done'
5. User redirect ke review form, edit kalau ada salah, simpan

### Edge Cases
- Image tidak terbaca / blur → status='failed', kasih retry button
- Extract data invalid (mis. total>9 digit) → flag for manual review
- Multiple receipt dalam 1 image → reject, suggest 1 image per receipt
- Provider tidak support vision → block + suggest provider lain (e.g., Gemini Flash, Claude 3.5 Sonnet, GPT-4o)

### Acceptance Criteria
- [ ] Upload struk → 5-15 detik dapat hasil extract
- [ ] User review form pre-filled, editable
- [ ] Save → expense record created
- [ ] Image tetap tersimpan di expense.receipt_path
- [ ] Audit log "expense created via OCR"

---

## 5.3 Predict Payment Date ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1.5 hari

### Why
"Klien ini biasanya bayar H+12 (rata-rata 6 bulan terakhir)" — actionable insight saat bikin invoice. Bantu user set jatuh tempo realistis & forecast cash flow.

### Database Schema
Tidak butuh AI provider. Pure statistical query.

Modifikasi `clients`:
- `avg_payment_days INT NULL` — di-update via observer payment
- `payment_reliability ENUM('excellent','good','fair','poor') NULL`
- `total_invoices_count INT DEFAULT 0`
- `total_paid_count INT DEFAULT 0`

### Service
- `App\Services\Analytics\ClientPaymentBehavior`
  - `recompute(Client $c)` — call setiap kali payment di-create
    - Avg days = avg(`payments.created_at - invoices.issue_date`) untuk paid invoices
    - Reliability:
      - excellent: avg ≤ due_date AND on_time_rate ≥ 95%
      - good: avg ≤ due_date+3 AND on_time_rate ≥ 80%
      - fair: avg ≤ due_date+10 AND on_time_rate ≥ 60%
      - poor: lainnya

### UI
Saat create invoice, setelah pilih client tampil card insight:
```
👤 Klien ini bayar rata-rata H+12
✓ Reliability: GOOD (12 invoice, 92% on-time)
💡 Saran due date: 14 hari (otomatis)
```

### Edge Cases
- Klien baru (<3 paid invoice) → tampil "Data belum cukup, default 30 hari"
- Outlier 1 invoice yg bayar 100 hari telat → exclude top 5% saat hitung avg

### Acceptance Criteria
- [ ] Auto-update saat payment created
- [ ] Tampil di create invoice form
- [ ] Reliability badge di client list
- [ ] Sorting client list by reliability

---

## 5.4 Late Risk Score ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
Saat user mau kirim invoice, sistem warn: "Invoice ini 73% kemungkinan telat" — based on historical pattern. User bisa proactive (collect deposit, atau pre-call klien).

### Database Schema
Modifikasi `invoices`:
- `late_risk_score TINYINT NULL` (0-100)
- `late_risk_factors JSON NULL` — explain kenapa

### Service
- `App\Services\Analytics\LateRiskScorer::score(Invoice $i): array`
  - Pakai composite weighted formula (BUKAN AI):
    - 40% × client.payment_reliability poor=80, fair=50, good=20, excellent=5
    - 20% × invoice.amount > avg client invoice amount × 2 → +30
    - 15% × due_date < 14 days → +20 (terlalu cepat)
    - 15% × workspace recent late_rate per quarter
    - 10% × bulan tertentu (akhir kuartal cenderung delay) → +10
  - Output `score` 0-100 + array `factors[]` sebagai explanation

### UI
- Saat invoice show → badge risk
  - Hijau (0-30): "Aman"
  - Kuning (31-60): "Perhatian"
  - Merah (61-100): "Risiko Tinggi"
- Tooltip explain: "Faktor: klien 3 invoice terakhir telat avg 18 hari, jumlah lebih besar dari biasanya."

### Edge Cases
- Klien baru tanpa history → score 50 default + flag "data tidak cukup"
- Score harusnya recompute setiap perubahan invoice (amount, due_date, status)

### Acceptance Criteria
- [ ] Auto-score saat invoice create/update
- [ ] Badge tampil di invoice list & show
- [ ] Tooltip jelas explain alasan
- [ ] Sorting invoice list by risk

---

## 5.5 Auto-Translate Invoice ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1 hari

### Why
Klien luar negeri perlu invoice English. Native multi-language tanpa user retype.

### Database Schema
Tabel BARU `invoice_translations`:
```sql
id BIGINT PK
invoice_id BIGINT FK ON DELETE CASCADE
locale VARCHAR(10)  -- 'en', 'zh', 'ja', 'es'
fields JSON  -- {description, notes, terms, ...} translated
generated_by_provider_id BIGINT NULL FK→ai_providers
translated_at TIMESTAMP
UNIQUE(invoice_id, locale)
```

### Routes
```
POST /invoices/{invoice}/translate/{locale}     invoices.translate
GET  /invoices/{invoice}/pdf?locale=en          invoices.pdf (extend existing)
```

### Service
- `App\Services\Ai\Features\InvoiceTranslator::translate(Invoice $i, string $locale)`
  - Translate fields: item descriptions, notes, terms
  - Date format → locale (en: "May 8, 2026", id: "8 Mei 2026")
  - Currency label tetap tapi number formatting per locale

### UI
- Dropdown locale di invoice show: 🇮🇩 ID / 🇺🇸 EN / 🇯🇵 JP / 🇨🇳 ZH
- "Generate Translation" button untuk locale baru

### Edge Cases
- Translation gagal → tampilkan original + warning
- Cache result, jangan re-translate kalau sudah ada
- Re-translate kalau invoice items diubah → invalidate cache

### Acceptance Criteria
- [ ] Tombol translate per locale
- [ ] PDF render in selected locale
- [ ] Public view bisa pilih locale

---

## 5.6 Auto-Categorize Expenses ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1 hari

### Why
User upload struk, AI tebak kategori (Office Supplies / Travel / Meals / Software / dll). Bantu reporting.

### Database Schema
Modifikasi `expenses`:
- `ai_category_suggested VARCHAR(50) NULL`
- `ai_category_confidence DECIMAL(4,2) NULL`

Tabel `expense_categories` (workspace-scoped, user-defined):
```sql
id, workspace_id, name, icon, color, is_default
```

### Service
- `App\Services\Ai\Features\ExpenseCategorizer`
  - Input: description + amount + merchant_name
  - Match against workspace's categories
  - Output: `category_id` + confidence

### UI
Saat upload expense, suggested category tampil di-highlight, user 1-klik confirm.

### Acceptance Criteria
- [ ] Suggest category saat create expense
- [ ] User confirm/override → train future suggestions
- [ ] Reporting per kategori
