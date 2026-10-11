# Docker assignment – PLcare (Laravel 9)

Files added for this assignment:

| File | Purpose |
|---|---|
| `Dockerfile` | Image definition (cache-friendly layer order) |
| `.dockerignore` | Keeps `node_modules`, `vendor`, `.git`, `.env` (and more) out of the image |
| `docker/entrypoint.sh` | Runtime setup: APP_KEY, SQLite DB, migrate, seed, then start server |
| `artisan`, `app/Http/Controllers/Controller.php` | **Repaired** – both were saved as UTF-16 (PHP cannot read that) and `artisan` was truncated to its first line |

---

## Step 0 – Generate `composer.lock` (one time, REQUIRED)

The repo has no `composer.lock`, and the Dockerfile copies it on purpose (requirement 3), so the build
fails until it exists. Pick ONE:

**A. You have PHP + Composer installed**
```
composer update --no-scripts
```

**B. No PHP/Composer – use Docker (run in the project root)**

PowerShell:
```powershell
docker run --rm -v "${PWD}:/app" -w /app composer:2 composer update --no-install --no-scripts --ignore-platform-reqs --no-interaction
```
bash / zsh:
```bash
docker run --rm -v "$PWD:/app" -w /app composer:2 composer update --no-install --no-scripts --ignore-platform-reqs --no-interaction
```

Then commit `composer.lock`.

---

## Step 4 – Prove the cache works

Run from the project root. Three builds, with the timing printed after each one.

### PowerShell

```powershell
$r = @()

# Build 1 - cold start, nothing cached
$t = Measure-Command { docker build --no-cache -t plcare:latest . | Out-Host }
$r += ("Build 1 (cold, --no-cache)        : {0,7:N1} s" -f $t.TotalSeconds)

# Build 2 - identical, nothing changed
$t = Measure-Command { docker build -t plcare:latest . | Out-Host }
$r += ("Build 2 (no changes)              : {0,7:N1} s" -f $t.TotalSeconds)

# >>> Now change ONE character in your code (e.g. a letter in a string in routes/web.php) and SAVE.
Read-Host "Edit one character, save the file, then press Enter"

# Build 3 - one-character code change
$t = Measure-Command { docker build -t plcare:latest . | Out-Host }
$r += ("Build 3 (1 character changed)     : {0,7:N1} s" -f $t.TotalSeconds)

""; "===== BUILD TIMINGS ====="; $r
```

### bash / zsh

```bash
TIMEFORMAT='>>> BUILD TIME: %R seconds'

echo "### Build 1 - cold";            time docker build --no-cache -t plcare:latest .
echo "### Build 2 - no changes";      time docker build -t plcare:latest .

# >>> Now change ONE character in your code (e.g. in routes/web.php) and SAVE, then:
echo "### Build 3 - 1 char changed";  time docker build -t plcare:latest .
```

### What to expect (use this to check your own output)

* **Build 2**: every step says `CACHED`; finishes in about a second.
* **Build 3**: the steps up to and including `composer install` say `CACHED`; only
  `COPY . .` and the short finishing `RUN` re-execute.
* **Build 1** is the slow one (installing PHP extensions + all Composer packages – typically minutes);
  **Build 3** should be a small fraction of that (typically seconds). Exact numbers depend on your machine and network.

**Screenshot:** the terminal showing the three timings (the `BUILD TIMINGS` summary in PowerShell,
or the three `BUILD TIME` lines in bash). If it does not fit on one screen, scroll to the end and capture the
summary – and, ideally, also a shot of Build 3's output showing the `CACHED` lines.

> Why `--no-cache` on build 1? If you built before, Docker would reuse old layers and the "first" build would
> look fast. `--no-cache` guarantees an honest cold baseline.

---

## Step 5 – Run the container, open it in a browser, show `docker ps`

```
docker run -d --name plcare -p 8000:8000 plcare:latest
docker ps
```

Give it ~5–10 seconds on first start (it creates the SQLite DB, migrates and seeds 20 rows).
`docker logs plcare` should show `Server running on [http://0.0.0.0:8000]`.

Open in a browser:

* `http://localhost:8000/` – Laravel welcome page
* `http://localhost:8000/plants` – the plants table page (reads from the DB)

**Screenshot:** browser (address bar visible) **and** the terminal with the `docker ps` output in the same
picture. The `PORTS` column should read `0.0.0.0:8000->8000/tcp`.

---

## Step 6 – Call the API from outside the container with Postman

1. New request → method **GET**
2. URL: `http://localhost:8000/api/plants`
3. Header (optional but good practice): `Accept: application/json`
4. **Send**

Expected: status **200 OK** and a body shaped like the controller's response (20 seeded rows;
`Luas_lahan` is between 185 and 250 because of the factory):

```json
{
  "success": true,
  "data": [ { "id": 1, "Pemilik_lahan": "...", "Luas_lahan": 2xx, "created_at": "...", "updated_at": "..." } ],
  "meta": { "total": 20, "total_luas": 4xxx }
}
```

**Screenshot:** Postman showing the request URL, the `200 OK` status, and the JSON body.

(This is also the exact URL the Vue app uses: `VITE_API_URL=http://localhost:8000` + `/api/plants`.)

---

## Useful commands / options

```
docker logs plcare                 # errors go to stderr -> visible here
docker stop plcare && docker rm plcare
docker run -d --name plcare -p 8080:8000 plcare:latest      # host port 8080 if 8000 is busy
docker run -d --name plcare -p 8000:8000 -e APP_KEY=base64:... plcare:latest   # fixed APP_KEY
docker run -d --name plcare -p 8000:8000 -e DB_SEED=false plcare:latest        # no sample data
```

Use MySQL instead of the built-in SQLite:
```
docker run -d --name plcare -p 8000:8000 \
  -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 \
  -e DB_DATABASE=laravel -e DB_USERNAME=root -e DB_PASSWORD=secret plcare:latest
```

## Troubleshooting

| Symptom | Cause / fix |
|---|---|
| `"/composer.lock": not found` during build | Do Step 0 |
| `exec ... docker/entrypoint.sh: no such file or directory` | Windows CRLF line endings. The Dockerfile strips them; if you still see it, rebuild with `--no-cache` |
| Browser/Postman: connection refused / empty reply | Container not running (`docker ps -a`, `docker logs plcare`) or port 8000 already taken – use `-p 8080:8000` |
| 500 error | `docker logs plcare` – `LOG_CHANNEL=stderr` prints the exception there |

## Design notes

* `php artisan serve --host=0.0.0.0` – binding to `0.0.0.0` is what makes the API reachable from outside the
  container; the default `127.0.0.1` would only listen *inside* it. `artisan serve` is a development server – for
  real production you would put Apache/Nginx + PHP-FPM in front of the app.
* `.env` is not in the image (secrets don't belong in image layers). Configuration comes from environment
  variables; if `APP_KEY` is not supplied the entrypoint generates a temporary one.
* Dev dependencies are installed on purpose: the seeder uses Faker (a `require-dev` package).
