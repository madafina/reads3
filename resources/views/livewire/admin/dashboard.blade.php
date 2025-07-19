<div>
    @section('content_header')
        <h1 class="m-0 text-dark">Dashboard</h1>
    @stop

    @section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h4 class="alert-heading">Selamat Datang, {{ auth()->user()->name }}!</h4>
                <p>Ini adalah halaman dashboard Anda. Dari sini Anda bisa memantau aktivitas sistem.</p>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Box untuk Pending Submissions --}}
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingSubmissionsCount }}</h3>
                    <p>Ilmiah Menunggu Verifikasi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-hourglass-start"></i>
                </div>
                <a href="{{ route('admin.submissions.verify.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Box untuk Total Residen --}}
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalResidentsCount }}</h3>
                    <p>Total Residen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-user-graduate"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        {{-- Box untuk Total Dosen --}}
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalLecturersCount }}</h3>
                    <p>Total Dosen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-user-tie"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    @stop
</div>