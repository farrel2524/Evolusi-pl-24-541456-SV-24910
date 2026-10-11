

@section('content')
<div class="container">
    <h4>Tambah pemilik</h4>
    <form action="{{ route('plant.store') }}">
        @csrf
        <div>Pemilik_lahan<input type="text" name="Pemilik"></div>
        <div>Luas_lahan<input type="text" name="Luas"></div>
        <div>created_at <input type="date" name="created"></div>
        <div>updated_at <input type="date" name="updated"></div>
        <button type="submit">Simpan</button>
        <a href="{{ '/abt' }}">Kembali</a>
    </form>
</div>

@endsection