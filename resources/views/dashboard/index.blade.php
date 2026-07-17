<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- Welcome Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">
                        <i class='bx bx-smile text-primary me-2'></i>
                        Selamat Datang, {{ Auth::user()->name }}!
                    </h3>
                    <p class="text-muted mb-0">
                        Anda login sebagai <span class="badge bg-primary">{{ Auth::user()->role }}</span>
                    </p>
                    <p class="text-muted mt-2">
                        <i class='bx bx-time-five me-1'></i>
                        {{ now()->isoFormat('dddd, D MMMM YYYY - HH:mm') }}
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('niceadmin/img/noprofil.png') }}"
                        alt="Avatar" class="img-fluid rounded-circle border border-3 border-primary"
                        style="max-width: 150px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards & Charts -->
    @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin')
        <!-- Institutional Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Mahasiswa Aktif</p>
                                <h4 class="fw-bold mb-0">{{ $activeStudentsCount }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-people fs-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Dosen Aktif</p>
                                <h4 class="fw-bold mb-0">{{ $activeTeachersCount }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-person-workspace fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Rata-rata IPK</p>
                                <h4 class="fw-bold mb-0">{{ number_format($averageInstitutionIpk, 2) }}</h4>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-award fs-4 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Kelas Semester Ini</p>
                                <h4 class="fw-bold mb-0">{{ $currentClassesCount }}</h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-journal-bookmark fs-4 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Users</p>
                                <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class='bx bx-user fs-2 text-primary'></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-primary bg-opacity-10 border-0 py-2">
                        <small class="text-primary fw-semibold">
                            <i class='bx bx-trending-up me-1'></i>
                            All registered users
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Superadmin</p>
                                <h2 class="fw-bold mb-0">{{ $superadminCount }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class='bx bx-shield fs-2 text-success'></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-success bg-opacity-10 border-0 py-2">
                        <small class="text-success fw-semibold">
                            <i class='bx bx-check-circle me-1'></i>
                            Full access users
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Admin</p>
                                <h2 class="fw-bold mb-0">{{ $adminCount }}</h2>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class='bx bx-user-check fs-2 text-info'></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-info bg-opacity-10 border-0 py-2">
                        <small class="text-info fw-semibold">
                            <i class='bx bx-user-circle me-1'></i>
                            Standard access users
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Visual Charts -->
        <div class="row g-4 mb-4">
            <!-- IPK Distribution Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Rata-rata IPK per Angkatan</h6>
                    </div>
                    <div class="card-body p-3">
                        <div style="height: 280px; position: relative;">
                            <canvas id="ipkAngkatanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pass Rates Tables -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Tingkat Kelulusan Mata Kuliah</h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="nav nav-tabs" id="passRateTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1" id="highest-tab" data-bs-toggle="tab" data-bs-target="#highest" type="button" role="tab">Tertinggi</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1" id="lowest-tab" data-bs-toggle="tab" data-bs-target="#lowest" type="button" role="tab">Terendah</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2" id="passRateTabsContent">
                            <div class="tab-pane fade show active" id="highest" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped m-0 small">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Mata Kuliah</th>
                                                <th class="text-center">Lulus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($highestPassRates as $rate)
                                                <tr>
                                                    <td class="font-monospace"><span class="badge bg-secondary">{{ $rate['code'] }}</span></td>
                                                    <td>{{ $rate['name'] }}</td>
                                                    <td class="text-center fw-bold text-success">{{ $rate['pass_rate'] }}%</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Belum ada data nilai finalized.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="lowest" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped m-0 small">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Mata Kuliah</th>
                                                <th class="text-center">Lulus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($lowestPassRates as $rate)
                                                <tr>
                                                    <td class="font-monospace"><span class="badge bg-secondary">{{ $rate['code'] }}</span></td>
                                                    <td>{{ $rate['name'] }}</td>
                                                    <td class="text-center fw-bold text-danger">{{ $rate['pass_rate'] }}%</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Belum ada data nilai finalized.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Dosen Statistics & Charts -->
    @if (Auth::user()->role == 'Dosen')
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Kelas Yang Diampu</p>
                                <h2 class="fw-bold mb-0">{{ $totalClasses }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class='bx bx-book-open fs-2 text-primary'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Mahasiswa Terdaftar</p>
                                <h2 class="fw-bold mb-0">{{ $totalEnrolledStudents }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class='bx bx-group fs-2 text-success'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Distribusi Nilai Mahasiswa (Semua Kelas)</h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 300px; position: relative;">
                    <canvas id="dosenSebaranChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Mahasiswa Charts -->
    @if (Auth::user()->role == 'Mahasiswa')
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2 text-primary"></i>Tren IPS (Indeks Prestasi Semester)</h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 300px; position: relative;">
                    <canvas id="mahasiswaIpsChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">
                <i class='bx bx-rocket me-2 text-primary'></i>
                Quick Actions
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mt-2 justify-content-center">
                @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin')
                    <div class="col-md-3">
                        <a href="{{ route('user.index') }}" class="text-decoration-none">
                            <div class="card border border-primary border-opacity-25 h-100 hover-shadow">
                                <div class="card-body text-center mt-4">
                                    <i class='bx bx-user-plus fs-1 text-primary mb-2'></i>
                                    <h6 class="mb-0">Manage Users</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('setting.index') }}" class="text-decoration-none">
                            <div class="card border border-success border-opacity-25 h-100 hover-shadow">
                                <div class="card-body text-center mt-4">
                                    <i class='bx bx-cog fs-1 text-success mb-2'></i>
                                    <h6 class="mb-0">Settings</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if (Auth::user()->role == 'Dosen')
                    <div class="col-md-3">
                        <a href="{{ route('dosen.kelas.index') }}" class="text-decoration-none">
                            <div class="card border border-primary border-opacity-25 h-100 hover-shadow">
                                <div class="card-body text-center mt-4">
                                    <i class='bx bx-book-open fs-1 text-primary mb-2'></i>
                                    <h6 class="mb-0">Kelas Saya</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if (Auth::user()->role == 'Mahasiswa')
                    <div class="col-md-3">
                        <a href="{{ route('khs.index') }}" class="text-decoration-none">
                            <div class="card border border-primary border-opacity-25 h-100 hover-shadow">
                                <div class="card-body text-center mt-4">
                                    <i class='bx bx-file fs-1 text-primary mb-2'></i>
                                    <h6 class="mb-0">Kartu Hasil Studi</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                <div class="col-md-3">
                    <a href="{{ route('dashboard.show') }}" class="text-decoration-none">
                        <div class="card border border-info border-opacity-25 h-100 hover-shadow">
                            <div class="card-body text-center mt-4">
                                <i class='bx bx-user-circle fs-1 text-info mb-2'></i>
                                <h6 class="mb-0">My Profile</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('dashboard.edit') }}" class="text-decoration-none">
                        <div class="card border border-warning border-opacity-25 h-100 hover-shadow">
                            <div class="card-body text-center mt-4">
                                <i class='bx bx-edit fs-1 text-warning mb-2'></i>
                                <h6 class="mb-0">Edit Profile</h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class='bx bx-info-circle me-2 text-primary'></i>
                        System Information
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 pt-4">
                        <li class="mb-2">
                            <i class='bx bx-check-circle text-success me-2'></i>
                            <strong>Laravel Version:</strong> {{ app()->version() }}
                        </li>
                        <li class="mb-2">
                            <i class='bx bx-check-circle text-success me-2'></i>
                            <strong>PHP Version:</strong> {{ PHP_VERSION }}
                        </li>
                        <li class="mb-2">
                            <i class='bx bx-check-circle text-success me-2'></i>
                            <strong>Environment:</strong> {{ config('app.env') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 pt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class='bx bx-user me-2 text-primary'></i>
                        Your Account
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class='bx bx-envelope text-primary me-2'></i>
                            <strong>Email:</strong> {{ Auth::user()->email }}
                        </li>
                        <li class="mb-2">
                            <i class='bx bx-calendar text-primary me-2'></i>
                            <strong>Member Since:</strong> {{ Auth::user()->created_at->format('d M Y') }}
                        </li>
                        <li class="mb-2">
                            <i class='bx bx-time text-primary me-2'></i>
                            <strong>Last Updated:</strong> {{ Auth::user()->updated_at->diffForHumans() }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    @push('modals')
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Admin/Superadmin Chart
            @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin')
                const ipkCtx = document.getElementById('ipkAngkatanChart').getContext('2d');
                new Chart(ipkCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($cohortIpkData)) !!},
                        datasets: [{
                            label: 'Rata-rata IPK',
                            data: {!! json_encode(array_values($cohortIpkData)) !!},
                            backgroundColor: 'rgba(0, 0, 128, 0.7)',
                            borderColor: 'rgba(0, 0, 128, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 4.0,
                                ticks: {
                                    stepSize: 0.5
                                }
                            }
                        }
                    }
                });
            @endif

            // 2. Dosen Chart
            @if (Auth::user()->role == 'Dosen')
                const dosenCtx = document.getElementById('dosenSebaranChart').getContext('2d');
                new Chart(dosenCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($dosenGradeCounts)) !!},
                        datasets: [{
                            label: 'Jumlah Mahasiswa',
                            data: {!! json_encode(array_values($dosenGradeCounts)) !!},
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.7)',   // A
                                'rgba(40, 167, 69, 0.5)',   // A-
                                'rgba(23, 162, 184, 0.7)',  // B+
                                'rgba(23, 162, 184, 0.5)',  // B
                                'rgba(23, 162, 184, 0.3)',  // B-
                                'rgba(255, 193, 7, 0.7)',   // C+
                                'rgba(255, 193, 7, 0.5)',   // C
                                'rgba(253, 126, 20, 0.7)',  // D
                                'rgba(220, 53, 69, 0.7)'    // E
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            @endif

            // 3. Mahasiswa Chart
            @if (Auth::user()->role == 'Mahasiswa')
                const mhsCtx = document.getElementById('mahasiswaIpsChart').getContext('2d');
                new Chart(mhsCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_column($ipsTrend, 'semester')) !!},
                        datasets: [{
                            label: 'Indeks Prestasi Semester (IPS)',
                            data: {!! json_encode(array_column($ipsTrend, 'ips')) !!},
                            borderColor: 'rgba(0, 0, 128, 1)',
                            backgroundColor: 'rgba(0, 0, 128, 0.1)',
                            fill: true,
                            tension: 0.1,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 4.0,
                                ticks: {
                                    stepSize: 0.5
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
    @endpush

</x-app>
