@extends('layouts.app')

@section('title', 'Tambah Iuran')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Iuran Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('iuran.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama Iuran</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Jenis Iuran</label>
                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="wajib">Wajib</option>
                    <option value="sukarela">Sukarela</option>
                    <option value="kebersihan">Kebersihan</option>
                    <option value="keamanan">Keamanan</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                       id="amount" name="amount" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Jatuh Tempo</label>
                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                       id="due_date" name="due_date" required>
                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Simpan
            </button>
            <a href="{{ route('iuran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
