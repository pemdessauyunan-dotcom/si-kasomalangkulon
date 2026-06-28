<?php

namespace App\Http\Controllers;

use App\Models\ProgramBansos;
use App\Models\PenerimaBansos;
use App\Models\Warga;
use Illuminate\Http\Request;

class BansosController extends Controller
{
    /**
     * Get daftar program bansos
     */
    public function programs(Request $request)
    {
        $query = ProgramBansos::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $programs = $query->withCount(['penerimaBansos' => function ($q) {
            $q->where('status', 'disetujui');
        }])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Get detail program bansos
     */
    public function showProgram(ProgramBansos $program)
    {
        $program->load(['penerimaBansos.warga.rt']);

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    /**
     * Get daftar penerima bansos
     */
    public function recipients(Request $request)
    {
        $user = $request->user();

        $query = PenerimaBansos::with(['warga.rt', 'programBansos', 'pengusul', 'verifikator']);

        // Filter berdasarkan role
        if ($user->role === 'rt') {
            // RT hanya lihat usulan di wilayahnya
            $query->whereHas('warga', function ($q) use ($user) {
                $q->where('rt_id', $user->rt_id);
            });
        }

        // Filter tambahan
        if ($request->has('program_id')) {
            $query->where('program_bansos_id', $request->program_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $recipients = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $recipients,
        ]);
    }

    /**
     * Usulkan penerima bansos baru (RT)
     */
    public function propose(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['rt', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only RT or Admin can propose beneficiaries.',
            ], 403);
        }

        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'program_bansos_id' => 'required|exists:program_bansos,id',
            'alasan_pengajuan' => 'required|string|max:500',
        ]);

        // Cek apakah warga sudah diusulkan untuk program ini
        $existing = PenerimaBansos::where('warga_id', $validated['warga_id'])
            ->where('program_bansos_id', $validated['program_bansos_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Warga ini sudah terdaftar dalam program ini.',
            ], 422);
        }

        // Jika RT, cek apakah warga ada di wilayahnya
        if ($user->role === 'rt') {
            $warga = Warga::find($validated['warga_id']);
            if ($warga->rt_id !== $user->rt_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa mengusulkan warga di wilayah RT Anda.',
                ], 403);
            }
        }

        $penerima = PenerimaBansos::create([
            'warga_id' => $validated['warga_id'],
            'program_bansos_id' => $validated['program_bansos_id'],
            'status' => PenerimaBansos::STATUS_DIUSULKAN,
            'alasan_pengajuan' => $validated['alasan_pengajuan'],
            'pengusul_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $penerima->load(['warga', 'programBansos']),
            'message' => 'Usulan berhasil dikirim. Menunggu verifikasi admin.',
        ], 201);
    }

    /**
     * Approve usulan bansos (Admin)
     */
    public function approve(Request $request, PenerimaBansos $penerima)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can approve beneficiaries.',
            ], 403);
        }

        $penerima->approve($user->id);

        return response()->json([
            'success' => true,
            'data' => $penerima->fresh(),
            'message' => 'Usulan berhasil disetujui.',
        ]);
    }

    /**
     * Reject usulan bansos (Admin)
     */
    public function reject(Request $request, PenerimaBansos $penerima)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can reject beneficiaries.',
            ], 403);
        }

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $penerima->reject($user->id, $validated['catatan_penolakan']);

        return response()->json([
            'success' => true,
            'data' => $penerima->fresh(),
            'message' => 'Usulan ditolak.',
        ]);
    }

    /**
     * Cek status penerima bansos publik (by NIK)
     */
    public function checkPublic(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|min:16|max:16',
        ]);

        $warga = Warga::where('nik', $validated['nik'])->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        $penerimaBansos = PenerimaBansos::with('programBansos')
            ->where('warga_id', $warga->id)
            ->whereIn('status', ['diusulkan', 'disetujui'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $warga->nama_lengkap,
                'rt_rw' => 'RT ' . $warga->rt->rt . ' / RW ' . $warga->rt->rw,
                'bansos' => $penerimaBansos->map(function ($p) {
                    return [
                        'program' => $p->programBansos->nama_program,
                        'status' => $p->getStatusBadgeInfo()['label'],
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get statistik bansos
     */
    public function statistics(Request $request)
    {
        $stats = ProgramBansos::aktif()
            ->withCount(['penerimaBansos' => function ($q) {
                $q->where('status', 'disetujui');
            }])
            ->get()
            ->map(function ($program) {
                return [
                    'program' => $program->nama_program,
                    'jumlah_penerima' => $program->penerima_bansos_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
