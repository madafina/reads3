<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PromotionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(PromotionDataTable $dataTable)
    {
        return $dataTable->render('admin.promotions.index');
    }

    public function promote(Resident $resident)
    {
        $currentStage = $resident->currentStage;
        $nextStage = Stage::where('order', '>', $currentStage->order)->orderBy('order')->first();

        if (!$nextStage) {
            return redirect()->back()->with('error', 'Tidak ada tahap selanjutnya.');
        }

        DB::transaction(function () use ($resident, $currentStage, $nextStage) {
            // 1. Update tahap saat ini di tabel resident
            $resident->update(['current_stage_id' => $nextStage->id]);

            // 2. Selesaikan tahap lama di tabel pivot
            DB::table('resident_stage')
                ->where('resident_id', $resident->id)
                ->where('stage_id', $currentStage->id)
                ->update([
                    'status' => 'completed',
                    'end_date' => now()
                ]);

            // 3. Tambahkan tahap baru di tabel pivot
            DB::table('resident_stage')->insert([
                'resident_id' => $resident->id,
                'stage_id' => $nextStage->id,
                'status' => 'active',
                'start_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.promotions.index')->with('success', $resident->user->name . ' berhasil dinaikkan ke ' . $nextStage->name);
    }
}