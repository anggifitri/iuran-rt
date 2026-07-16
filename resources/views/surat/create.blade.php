@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Ajukan Surat Digital NexaNest</h4>
    <form method="POST" action="{{ route('surat.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Jenis Surat</label>
            <select name="jenis_surat" class="form-select" required>
                <option value="Domisili">Surat Domisili</option>
                <option value="SKTM">Surat Keterangan Tidak Mampu</option>
                <option value="Pengantar Nikah">Surat Pengantar Nikah</option>
                <option value="Keterangan Usaha">Surat Keterangan Usaha</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Keperluan</label>
            <textarea name="keperluan" class="form-control" rows="4" placeholder="Tuliskan tujuan dan kebutuhan surat Anda." required></textarea>
        </div>
        <button class="btn btn-primary">Kirim Permintaan</button>
    </form>
</div>
@endsection
