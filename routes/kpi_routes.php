
// ===== MODUL KPI CORPORATE =====
use App\Http\Controllers\KpiMasterController;
use App\Http\Controllers\KpiManajemenController;
use App\Http\Controllers\KpiPenilaianController;
use App\Http\Controllers\KpiApprovalController;
use App\Http\Controllers\KpiDashboardController;

Route::prefix('kpi')->middleware('auth')->group(function () {
    // Dashboard KPI
    Route::get('/dashboard', [KpiDashboardController::class, 'index']);

    // Master Data (Hanya Admin / HRD)
    Route::middleware('role:admin|hrd|general_manager')->group(function () {
        // Kategori KPI
        Route::get('/kategori', [KpiMasterController::class, 'indexKategori']);
        Route::post('/kategori', [KpiMasterController::class, 'storeKategori']);
        Route::put('/kategori/{id}', [KpiMasterController::class, 'updateKategori']);
        Route::delete('/kategori/{id}', [KpiMasterController::class, 'deleteKategori']);
        
        // Periode KPI
        Route::get('/periode', [KpiMasterController::class, 'indexPeriode']);
        Route::post('/periode', [KpiMasterController::class, 'storePeriode']);
        Route::put('/periode/{id}', [KpiMasterController::class, 'updatePeriode']);
        Route::delete('/periode/{id}', [KpiMasterController::class, 'deletePeriode']);

        // Manajemen KPI & Penugasan
        Route::get('/manajemen', [KpiManajemenController::class, 'index']);
        Route::post('/manajemen', [KpiManajemenController::class, 'store']);
        Route::put('/manajemen/{id}', [KpiManajemenController::class, 'update']);
        Route::delete('/manajemen/{id}', [KpiManajemenController::class, 'delete']);
        
        Route::get('/manajemen/{kpi_id}/assign', [KpiManajemenController::class, 'assignIndex']);
        Route::post('/manajemen/{kpi_id}/assign', [KpiManajemenController::class, 'assignStore']);
        Route::delete('/manajemen/assign/{assignment_id}', [KpiManajemenController::class, 'assignDelete']);
    });

    // Penilaian KPI (Semua Karyawan yang di-assign)
    Route::get('/penilaian', [KpiPenilaianController::class, 'index']);
    Route::post('/penilaian/submit', [KpiPenilaianController::class, 'submit']);
    
    // Approval KPI (Untuk Atasan / HRD)
    Route::middleware('role:admin|hrd|general_manager')->group(function () {
        Route::get('/approval', [KpiApprovalController::class, 'index']);
        Route::post('/approval/{id}/approve', [KpiApprovalController::class, 'approve']);
        Route::post('/approval/{id}/reject', [KpiApprovalController::class, 'reject']);
    });
});
