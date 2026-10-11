<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Plant Care Guide - Data pemilik lahan</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap');

    * { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: "Irish Grover", system-ui;
      background: url("Images/plbg.png") center/cover no-repeat;
      background-color: green;
      min-height:100vh;
      color: white;
      position:relative;
      padding-bottom:40px;
    }

    nav { 
      display:flex; 
      justify-content:center; 
      gap:12px; padding:16px; 
      background:rgba(0,0,0,0.4); 
    }
    nav a { 
      background:#689f38; 
      color:#fff; 
      padding:8px 16px; 
      border-radius:5px; 
      text-decoration:none; 
      font-weight:bold; 
    }
    .logout { 
      position:absolute; 
      top:16px; 
      right:16px; 
    }
    .logout a { 
      background:#c62828; 
      color:#fff; 
      padding:8px 12px; 
      border-radius:5px; 
      text-decoration:none; 
    }

    .content { 
      text-align:center; 
      padding:28px 20px 10px; 
      text-shadow:1px 1px 3px #000; 
    }
    .content h1{ 
      font-size:2rem; 
      margin-bottom:6px; 
    }
    .content p{ 
      max-width:700px; 
      margin:0 auto 12px; 
      color:#fff; 
      opacity:.95; 
    }

    .card-wrap {
      display:flex; 
      justify-content:center; 
    }
    .card {
      width: 95%;
      max-width:1100px;
      margin: 18px auto;
      background:#fff;
      color:#111;
      border-radius:10px;
      padding:18px;
      box-shadow:0 6px 18px rgba(0,0,0,0.15);
    }

    /* fixed display typo below */
    .card .top {
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:12px;
      gap:12px;
    }
    .card h2 { 
      margin:0; 
      color:#0b5136; 
      font-size:1.3rem; 
    }
    .small { 
      color:#666; 
      font-size:0.95rem; 
    }

    .controls{ 
      display:flex; 
      gap:12px; 
      align-items:flex-end; 
      flex-wrap:wrap; 
      margin-bottom:12px; 
    }
    .controls label{ 
      display:block; 
      margin-bottom:6px; 
      color:#333; 
      font-size:0.9rem; 
    }
    select,input[type="text"]{ 
      padding:8px; 
      border:1px solid #ccc; 
      border-radius:4px; 
    }
    .btn { 
      font-family: "Irish Grover", system-ui;
      padding:8px 12px; 
      border:none; 
      background:#1976d2; 
      color:#fff; 
      border-radius:4px; 
      cursor:pointer; 
      text-decoration:none; 
    }
    .btn-reset { 
      background:#eee; 
      color:#333; 
      padding:8px 10px; 
      border-radius:4px; 
      text-decoration:none; 
    }
    .btn-danger {
      background:#c62828;
      color:#fff;
      padding:6px 10px;
      border-radius:4px;
      border: none;
      cursor: pointer;
    }
    .btn-small { padding:6px 10px; font-size:.9rem; }

    table{ 
      width:100%; 
      border-collapse:collapse; 
      margin-top:6px; 
    }
    thead th{ 
      background:#f7f7f9; 
      color:#333; 
      padding:10px; 
      border:1px solid #e6e9ec; 
      text-align:left; 
    }
    tbody td{ 
      padding:10px; 
      border:1px solid #e6e9ec; 
      text-align:left; 
      vertical-align:middle; 
      color:#222; 
    }
    .no-data { 
      text-align:center; 
      padding:18px; 
      color:#666; 
      background:#fff; 
    }

    .panel-right { 
      width:260px; 
      margin-left:20px; 
    }
    .right-grid { 
      display:flex; 
      gap:18px; 
      align-items:flex-start; 
    }
    .latest { 
      background:#fff; 
      border:1px solid #e6e9ec; 
      padding:12px; 
      border-radius:6px; 
    }
    .stats{ 
      display:flex; 
      flex-direction:column; 
      gap:10px; 
      margin-top:12px; 
    }
    .stat{ 
      background:#fff; 
      border:1px solid #e6e9ec; 
      padding:10px; 
      border-radius:6px; 
      text-align:left; 
    }

    .stats-under {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 16px;
    }
    .stats-under .stat {
      flex: 1 1 180px;
      background: #fff;
      border: 1px solid #e6e9ec;
      padding: 10px;
      border-radius: 6px;
      text-align: left;
      color: #111;
    }

    @media(max-width:980px){
      .right-grid{ flex-direction:column; }
      .panel-right{ width:100%; margin-left:0; }
    }
  </style>
</head>
<body>

  <nav>
    <a href="{{ route('abt.index') }}">Data user</a>
    <a href="{{ route('abt.index') }}">My Plants</a>
    <a href="#">Species</a>
    <a href="#">Maintenance</a>
    <a href="#">Care Guide</a>
  </nav>

  <div class="logout"><a href="#">Logout</a></div>

  <div class="content">
    <h1>Your place for your plant’s care guide</h1>
    <p>Get help for your plant's every needs — document growth, problems, and care tips.</p>
  </div>

  <div style="max-width:1100px; margin: 10px auto;">
    @if(session('success'))
      <div style="background:#dff0d8; color:#3c763d; padding:8px 12px; border-radius:6px; margin-bottom:10px;">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="background:#f2dede; color:#a94442; padding:8px 12px; border-radius:6px; margin-bottom:10px;">
        {{ session('error') }}
      </div>
    @endif
  </div>

  @php
    $plantd = isset($plantd) ? $plantd : collect();
    if (!isset($pemilikList)) {
        $pemilikList = $plantd->pluck('Pemilik_lahan')->filter()->unique()->sort()->values();
    }
    if (!isset($latest5)) {
        $latest5 = $plantd->sortByDesc('created_at')->take(5);
    }
    $totalCount = isset($totalCount) ? $totalCount : $plantd->count();
    $totalLuas = isset($totalLuas) ? $totalLuas : ($plantd->sum('Luas_lahan') ?? 0);
    $maxLuas = isset($maxLuas) ? $maxLuas : ($plantd->max('Luas_lahan') ?? 0);
    $minLuas = isset($minLuas) ? $minLuas : ($plantd->min('Luas_lahan') ?? 0);
  @endphp

  <div class="card-wrap">
    <div class="card">
      <div class="top">
        <div>
          <h2>Data Pemilik lahan</h2>
          <div class="small">Menampilkan <strong>{{ $totalCount }}</strong> hasil</div>
        </div>

        {{-- Add / create button (uses route 'addu' per your controller routes) --}}
        <div>
          <a href="{{ route('addu') }}" class="btn">Tambah pemilik</a>
        </div>
      </div>

      <form method="GET" action="{{ route('abt.index') }}">
        <div class="controls">
          <div>
            <label for="pemilik">Filter Pemilik</label>
            <select name="pemilik" id="pemilik" onchange="this.form.submit()">
              <option value="semua" {{ request('pemilik','semua') === 'semua' ? 'selected' : '' }}>Semua Pemilik</option>
              @foreach($pemilikList as $p)
                <option value="{{ $p }}" {{ request('pemilik') === $p ? 'selected' : '' }}>{{ $p }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="search">Cari Pemilik</label>
            <input type="text" name="search" id="search" placeholder="Masukkan nama pemilik" value="{{ request('search') }}">
          </div>

          <div>
            <label style="visibility:hidden">x</label>
            <div>
              <button type="submit" class="btn">Cari</button>
              <a href="{{ route('abt.index') }}" class="btn-reset">Reset</a>
            </div>
          </div>
        </div>
      </form>

      <div class="right-grid">
        <div style="flex:1;">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Pemilik_lahan</th>
                <th>Luas_lahan</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @if($plantd->count() > 0)
                @foreach($plantd as $row)
                
                  <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->Pemilik_lahan }}</td>
                    <td>{{ function_exists('formatLuas') ? formatLuas($row->Luas_lahan) : ($row->Luas_lahan ?? '') . ' m²' }}</td>
                    <td>{{ optional($row->created_at)->format('Y-m-d') ?? $row->created_at }}</td>
                    <td>{{ optional($row->updated_at)->format('Y-m-d') ?? $row->updated_at }}</td>
                    <td>
                    <a href="{{ route('plantd.edit', $row->id) }}" class="btn btn-small" style="margin-right:6px;">Edit</a>

                    <form action="{{ route('plantd.destroy', $row->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger btn-small" onclick="return confirm('Yakin ingin menghapus data ini?')">
                            Hapus
                        </button>
                    </form>
                  </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" class="no-data">Tidak ditemukan data sesuai kriteria.</td>
                </tr>
              @endif
            </tbody>
          </table>

          {{-- if you paginate, show links: --}}
          {{-- {{ $plantd->links() }} --}}
          
          <div class="stats-under">
            <div class="stat"><small>Total Lahan</small><div style="font-weight:bold">{{ $totalCount }}</div></div>
            <div class="stat"><small>Total Luas</small><div style="font-weight:bold">{{ function_exists('formatLuas') ? formatLuas($totalLuas) : $totalLuas . ' m²' }}</div></div>
            <div class="stat"><small>Luas Terbesar</small><div style="font-weight:bold">{{ function_exists('formatLuas') ? formatLuas($maxLuas) : $maxLuas . ' m²' }}</div></div>
            <div class="stat"><small>Luas Terkecil</small><div style="font-weight:bold">{{ function_exists('formatLuas') ? formatLuas($minLuas) : $minLuas . ' m²' }}</div></div>
          </div>
        </div>

        <div class="panel-right">
          <div class="latest">
            <strong>5 Lahan Terbaru</strong>
            <ul style="margin:8px 0 0 16px; padding:0;">
              @forelse($latest5 as $l)
                <li style="margin-bottom:6px">{{ $l->Pemilik_lahan }} — 
                  <small>{{ function_exists('formatLuas') ? formatLuas($l->Luas_lahan) : ($l->Luas_lahan ?? '') . ' m²' }}</small>
                </li>
              @empty
                <li style="color:#666">Belum ada data</li>
              @endforelse
            </ul>
          </div>
        </div>

      </div> {{-- right-grid end --}}
    </div> {{-- card end --}}
  </div> {{-- card-wrap end --}}

</body>
</html>
