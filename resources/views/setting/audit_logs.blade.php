<x-app>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">
        <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
            Audit Trail / Log Aktivitas Sistem
        </h5>

        <!-- Filter Form -->
        <form action="{{ route('audit_logs.index') }}" method="GET" class="row g-2 mb-4 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label small fw-bold">Cari Aktivitas/Aksi</label>
                <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Kata kunci..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3">
                <label for="user_id" class="form-label small fw-bold">Pelaku (User)</label>
                <select name="user_id" id="user_id" class="form-select form-select-sm select2-default">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                            {{ $u->name }} ({{ $u->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="action_type" class="form-label small fw-bold">Tipe Aksi</label>
                <select name="action_type" id="action_type" class="form-select form-select-sm select2-default">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" @selected(request('action_type') == $a)>
                            {{ $a }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('audit_logs.index') }}" class="btn btn-secondary btn-sm flex-fill">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead>
                    <tr class="text-center">
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">Waktu</th>
                        <th style="width: 15%;">Pelaku</th>
                        <th style="width: 12%;">Aksi</th>
                        <th style="width: 28%;">Deskripsi</th>
                        <th style="width: 10%;">IP / User Agent</th>
                        <th style="width: 15%;">Payload (Detail)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-center small">{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                            <td class="text-center small">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->name }}</strong><br>
                                    <span class="badge bg-secondary extra-small">{{ $log->user->role }}</span>
                                @else
                                    <span class="text-muted small">System / Guest</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark small">{{ $log->action }}</span>
                            </td>
                            <td class="small">{{ $log->description }}</td>
                            <td class="small">
                                <span class="badge bg-light text-dark font-monospace extra-small">{{ $log->ip_address }}</span><br>
                                <span class="text-muted extra-small" title="{{ $log->user_agent }}" style="font-size: 0.7rem; display: inline-block; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $log->user_agent }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($log->payload_before || $log->payload_after)
                                    <button type="button" class="btn btn-outline-info btn-xs btn-sm" data-bs-toggle="modal" data-bs-target="#payloadModal{{ $log->id }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>

                                    <!-- Payload Details Modal -->
                                    <div class="modal fade" id="payloadModal{{ $log->id }}" tabindex="-1" aria-labelledby="payloadModalLabel{{ $log->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title fw-bold" id="payloadModalLabel{{ $log->id }}">Detail Payload Log #{{ $log->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="row">
                                                        <div class="col-md-6 border-end">
                                                            <h6 class="fw-bold border-bottom pb-1 text-danger">Keadaan Sebelum (Before)</h6>
                                                            <pre class="bg-light p-2 rounded small text-dark" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->payload_before, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold border-bottom pb-1 text-success">Keadaan Sesudah (After)</h6>
                                                            <pre class="bg-light p-2 rounded small text-dark" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->payload_after, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Tidak ditemukan log aktivitas yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $logs->links() }}
        </div>
    </div>
</x-app>
