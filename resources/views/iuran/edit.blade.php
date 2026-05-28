@extends('layouts.app')

@section('title', 'Edit Iuran')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Iuran</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('iuran.update', $iuran) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama Iuran</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ $iuran->name }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Jenis Iuran</label>
                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="wajib" {{ $iuran->type == 'wajib' ? 'selected' : '' }}>Wajib</option>
                    <option value="sukarela" {{ $iuran->type == 'sukarela' ? 'selected' : '' }}>Sukarela</option>
                    <option value="kebersihan" {{ $iuran->type == 'kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                    <option value="keamanan" {{ $iuran->type == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                       id="amount" name="amount" value="{{ $iuran->amount }}" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Jatuh Tempo</label>
                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                       id="due_date" name="due_date" value="{{ $iuran->due_date }}" required>
                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $iuran->description }}</textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ $iuran->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update
            </button>
            <a href="{{ route('iuran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
