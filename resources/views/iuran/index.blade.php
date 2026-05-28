@extends('layouts.app')

@section('title', 'Data Iuran')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Iuran</h5>
        @if(Auth::user()->role != 'warga')
        <a href="{{ route('iuran.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Tambah Iuran
        </a>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Iuran</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        @if(Auth::user()->role != 'warga')
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($iurans as $iuran)
                    <tr>
                        <td>{{ $iuran->name }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($iuran->type) }}</span></td>
                        <td>Rp {{ number_format($iuran->amount, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($iuran->due_date)->format('d M Y') }}</td>
                        <td>
                            @if($iuran->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        @if(Auth::user()->role != 'warga')
                        <td>
                            <a href="{{ route('iuran.edit', $iuran) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('iuran.destroy', $iuran) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data iuran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $iurans->links() }}
    </div>
</div>
@endsection
