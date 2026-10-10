# Assignment Checklist – PLcare Laravel + Vue 3 + Frontend CI/CD

## 1. Laravel JSON API endpoint (CRUD table: `plantd`)

- **Model**: `App\Models\Plantd` (table `plantd`)
- **Endpoint**: `GET /api/plants` and alias `GET /api/tugas`
- **Controller method**: `PlantController@apiIndex` returns JSON
- **CORS**: already open for `api/*` in `config/cors.php` so the local Vue app can call it

### Example response
```json
{
  "success": true,
  "data": [ { "id": 1, "Pemilik_lahan": "...", "Luas_lahan": 100, ... } ],
  "meta": { "total": 20, "total_luas": 1234 }
}
```

### Test the API locally
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
# then open http://localhost:8000/api/plants
```

---

## 2. Vue 3 application in `frontend/`

- Vue 3 + Vue Router + Vite
- **Two routed pages**:
  - `/` – Home (shows `VITE_API_URL`)
  - `/plants` – Fetches and displays data from Laravel API
- **API URL** is read from `import.meta.env.VITE_API_URL` (see `src/utils/api.js`) – never hardcoded

### Run frontend locally
```bash
cd frontend
cp .env.example .env          # set VITE_API_URL=http://localhost:8000
npm install                   # generates package-lock.json (required for npm ci)
npm run dev                   # http://localhost:5173
```

---

## 3–5. Frontend GitHub Actions workflow

File: `.github/workflows/frontend.yml`

**Four jobs chained with `needs:`**:

| Job    | needs   | Purpose                                      |
|--------|---------|----------------------------------------------|
| lint   | –       | `npm ci` + ESLint                            |
| test   | lint    | `npm ci` + Vitest                            |
| build  | test    | `npm ci` + `npm run build` → upload artifact |
| deploy | build   | download artifact, list `dist/` (no rebuild) |

- Uses `actions/setup-node` with `cache: 'npm'` and `cache-dependency-path: frontend/package-lock.json`
- Uses `npm ci` in every job
- **Deploy only runs on `main` push** (`if: github.ref == 'refs/heads/main' && github.event_name == 'push'`)
- Pull Requests still run lint → test → build; deploy is skipped

### Important: commit `package-lock.json`
```bash
cd frontend && npm install && cd ..
git add frontend/package-lock.json
```

---

## 6. Vitest unit test (no Laravel backend required)

File: `frontend/src/utils/__tests__/api.spec.js`

Tests the pure helper `formatPlantLabel()` and that `getApiBaseUrl()` does not hardcode a URL.

```bash
cd frontend
npm test
```

These tests use no network and pass in GitHub Actions without the Laravel backend running.

---

## 7. Demonstration steps

### A. Open a Pull Request → deployment is skipped
```bash
git checkout -b feature/demo-pr
# make a small change, e.g. edit a comment
git add . && git commit -m "demo: open PR"
git push -u origin feature/demo-pr
# Open PR on GitHub targeting main
```
→ Workflow runs: lint → test → build. **deploy job is skipped**.

### B. Fail a test → pipeline turns red, then fix it
1. Edit `frontend/src/utils/__tests__/api.spec.js` and change one expectation, e.g.:
   ```js
   expect(formatPlantLabel(plant)).toBe('WRONG')
   ```
2. Commit & push → **test job fails** (red). build/deploy never run.
3. Revert the expectation to the correct value, push again → green.

---

## Existing Laravel CI

The original Laravel pipeline remains in `.github/workflows/ci.yml` (build → test → staging → production on main). It is independent of the frontend workflow.

---

## Quick start (full local stack)
```bash
# Terminal 1 – Laravel
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve

# Terminal 2 – Vue
cd frontend
npm install
cp .env.example .env   # VITE_API_URL=http://localhost:8000
npm run dev
```
