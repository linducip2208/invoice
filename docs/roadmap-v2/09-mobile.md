# Mobile / On-the-Go — Sprint 6

> Mobile-first untuk freelancer & small business owner yang sering kerja dari HP. PWA, quick capture, audit responsive.

---

## 9.1 Mobile UI Audit ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1.5 hari

### Why
Saat ini banyak page tested cuma di desktop. Coba buka `/invoices/create` di iPhone 12 — kemungkinan beberapa elemen overflow, button kekecilan, sidebar nutupin content.

### Scope Audit
Page utama yang harus 100% mobile-friendly:
1. `/dashboard`
2. `/invoices` (index)
3. `/invoices/create` (form panjang)
4. `/invoices/{id}` (show)
5. `/clients` (index, create, show)
6. `/products`
7. `/payments`
8. `/quotes/*`
9. `/i/{token}` (public invoice — paling sering dilihat di mobile)
10. `/q/{token}` (public quote)
11. `/login`, `/register`
12. `/docs`

### Methodology
- Test di breakpoint: 320px (iPhone SE), 375px (iPhone 12), 414px (iPhone 12 Pro Max), 768px (iPad portrait), 1024px (iPad landscape)
- Test browser: Safari iOS, Chrome Android
- Use Lighthouse mobile audit, target score: Performance >85, Accessibility >95

### Common Issues to Fix
- [ ] Tabel wide tanpa horizontal scroll → wrap dalam `<div class="overflow-x-auto">`
- [ ] Button touch target <44px → minimum 44px height
- [ ] Sticky/fixed nav nutupin content → padding top yang cukup
- [ ] Sidebar fixed di mobile → toggle drawer
- [ ] Form 2-column → stack 1-column di mobile
- [ ] Modal terlalu kecil/besar → fluid sizing
- [ ] Font size <14px untuk body → minimum 14px (16px recommended)
- [ ] Input zoom on focus (iOS) → set `font-size: 16px` untuk inputs

### Approach
1. Inventory issues pakai checklist di atas
2. Fix per page (commit per page biar reviewable)
3. Re-test pakai Chrome DevTools device emulator
4. Run Lighthouse audit, fix critical/serious

### Files Affected
- Banyak — kemungkinan setiap blade view di `resources/views/`
- Tailwind config: pastikan breakpoint sm/md/lg/xl tepat
- Custom CSS: cari `min-w-` yang bikin overflow di mobile

### Acceptance Criteria
- [ ] Setiap page utama lulus audit di 5 breakpoint
- [ ] Lighthouse mobile score >85 untuk page penting
- [ ] No horizontal scrolling di body level
- [ ] Touch target minimum 44px

---

## 9.2 PWA (Progressive Web App) ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 2 hari

### Why
User install Invocely ke home screen iPhone/Android → buka kayak app native. Push notification, offline access.

### Database Schema
Tabel BARU `push_subscriptions`:
```sql
id BIGINT PK
user_id BIGINT FK ON DELETE CASCADE
workspace_id BIGINT NULL FK ON DELETE SET NULL
endpoint VARCHAR(500)
keys_p256dh VARCHAR(255)
keys_auth VARCHAR(255)
device_label VARCHAR(120) NULL  -- "Chrome on Pixel 7"
ua VARCHAR(500) NULL
last_used_at TIMESTAMP NULL
created_at, updated_at
INDEX(user_id)
```

### Files to Create
- `public/manifest.webmanifest`
- `public/sw.js` (service worker)
- `resources/views/components/pwa-meta.blade.php` — `<link rel="manifest">`, theme-color, apple-touch-icon
- `app/Services/Push/PushNotificationDispatcher.php`
- `app/Http/Controllers/PushSubscriptionController.php`

### Manifest Content
```json
{
  "name": "Invocely",
  "short_name": "Invocely",
  "description": "Invoicing ajaib",
  "start_url": "/dashboard",
  "display": "standalone",
  "background_color": "#FFF8FE",
  "theme_color": "#A78BFA",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/icons/icon-maskable-512.png", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
  ]
}
```

### Service Worker (offline-first cache strategy)
- Cache static assets aggressive (CSS, JS, images, fonts)
- API requests: network-first, fallback to cached "last successful response"
- Pages: stale-while-revalidate

### Push Notification Flow
1. User accept browser permission prompt
2. Browser generate subscription endpoint
3. POST ke `/api/push-subscriptions` dengan keys
4. Backend store di `push_subscriptions`
5. Saat event terjadi (lihat 6.6 notification triggers), juga dispatch web push

### Library
- Composer: `minishlink/web-push` untuk dispatch
- Frontend: vanilla JS (service worker API)

### Routes
```
POST   /api/push-subscriptions          api.push-subscriptions.store
DELETE /api/push-subscriptions          api.push-subscriptions.destroy
GET    /workspace/devices               devices.index
DELETE /workspace/devices/{sub}         devices.destroy
```

### Edge Cases
- Browser tidak support → degrade gracefully, sembunyiin tombol "Enable Notifications"
- Permission denied → kasih panduan untuk re-enable di browser settings
- Endpoint stale (HTTP 410 saat push) → auto-delete subscription

### Acceptance Criteria
- [ ] Install ke home screen
- [ ] Logo & splash screen tampil
- [ ] Offline read previously cached pages
- [ ] Push notification berjalan (test via "send invoice paid" event)
- [ ] User bisa lihat & remove device

---

## 9.3 Quick Capture (Mobile-Optimized Flows) ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
3 flow yang paling sering dipakai dari mobile:
1. Foto struk → expense (dengan OCR)
2. Voice-to-text quick invoice draft
3. Quick payment record (saat klien transfer in person)

### Files to Build
- `resources/views/mobile/quick-expense.blade.php` — full-screen camera + 1 tap save
- `resources/views/mobile/quick-invoice.blade.php` — minimal form (klien + 1 item + total + send)
- `resources/views/mobile/quick-payment.blade.php` — pilih invoice + nominal + done

### Routes
```
GET  /m/quick/expense                  mobile.quick.expense
POST /m/quick/expense                  mobile.quick.expense.store
GET  /m/quick/invoice                  mobile.quick.invoice
POST /m/quick/invoice                  mobile.quick.invoice.store
GET  /m/quick/payment                  mobile.quick.payment
POST /m/quick/payment                  mobile.quick.payment.store
```

### UX Principle
- Minimum field, sane defaults
- Big buttons, swipe gestures
- Auto-suggest klien yang baru saja di-create
- Voice-to-text via Web Speech API (browser native)

### Acceptance Criteria
- [ ] Camera capture works on iOS Safari + Android Chrome
- [ ] Voice input untuk description
- [ ] One-tap save flow
- [ ] Linkable from PWA shortcut menu

---

## 9.4 Mobile-Specific Bottom Nav ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1 hari

### Why
Mobile pattern: bottom navigation (Home / Invoices / +Create / Clients / Profile). Lebih ergonomis dibanding hamburger menu.

### Files
- `resources/views/components/mobile-bottom-nav.blade.php`
- Inject di layout `resources/views/layouts/app.blade.php` dengan `lg:hidden`

### Items
- 🏠 Home → /dashboard
- 📄 Invoices → /invoices
- ➕ Create (FAB center, big) → /invoices/create
- 👥 Clients → /clients
- 👤 Me → /profile

### Acceptance Criteria
- [ ] Tampil hanya di mobile (<lg)
- [ ] Active state highlight current route
- [ ] FAB elevated above other items
- [ ] Aman dengan iOS safe-area-inset
