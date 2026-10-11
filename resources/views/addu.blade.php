<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tambah / Edit Plant</title>
  <style>

    .form { max-width: 700px; margin: 24px auto; background:#fff; padding:18px; border-radius:8px; color:#222;}
    label { display:block; margin-bottom:6px; font-weight:bold;}
    input[type="text"], input[type="number"] { width:100%; padding:8px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px;}
    .btn { padding:8px 12px; border-radius:4px; text-decoration:none; }
  </style>
</head>
<body>
  <div class="form">
    @if(isset($plant))
      <h2>Edit Plant #{{ $plant->id }}</h2>
      <form action="{{ route('plant.update', $plant->id) }}" method="POST">
      @method('PUT')
    @else
      <h2>Tambah Pemilik</h2>
      <form action="{{ route('plantd.store') }}" method="POST">
    @endif

      @csrf

      <label for="Pemilik_lahan">Pemilik Lahan</label>
      <input type="text" name="Pemilik_lahan" id="Pemilik_lahan" value="{{ old('Pemilik_lahan', $plant->Pemilik_lahan ?? '') }}">
      @error('Pemilik_lahan')
        <div style="color:red">{{ $message }}</div>
      @enderror

      <label for="Luas_lahan">Luas Lahan (m²)</label>
      <input type="number" step="0.01" name="Luas_lahan" id="Luas_lahan" value="{{ old('Luas_lahan', $plant->Luas_lahan ?? '') }}">
      @error('Luas_lahan')
        <div style="color:red">{{ $message }}</div>
      @enderror

      <div style="margin-top:12px">
        <button type="submit" class="btn btn-primary">
          {{ isset($plant) ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('plants.index') }}" class="btn btn-reset">Batal</a>
      </div>
    </form>
  </div>
</body>
</html>
