@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
<div class="row min-vh-100 align-items-center">
    <div class="col-lg-6 mx-auto text-center">
        <div class="welcome-section">
            <i class="fas fa-home fa-4x mb-4 text-primary"></i>
            <h1 class="display-4 fw-bold mb-3">NexaNest</h1>
            <p class="lead mb-4">Solusi pintar untuk mengelola iuran warga secara digital</p>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-animated px-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg btn-animated px-4">
                    <i class="fas fa-user-plus me-2"></i>Register
                </a>
            </div>
        </div>

        <div class="row mt-5 g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-chart-line fa-3x text-primary"></i>
                    <h5 class="mt-3">Mudah Digunakan</h5>
                    <p class="text-muted">Antarmuka sederhana & intuitif</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    <h5 class="mt-3">Aman & Terpercaya</h5>
                    <p class="text-muted">Data tersimpan dengan aman</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-clock fa-3x text-primary"></i>
                    <h5 class="mt-3">Real-time</h5>
                    <p class="text-muted">Update data langsung</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
