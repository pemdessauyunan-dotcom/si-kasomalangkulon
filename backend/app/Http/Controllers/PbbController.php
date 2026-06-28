<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPbb;
use App\Models\WajibPajak;
use App\Models\Warga;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PbbController extends Controller
{
    /**
     * Get daftar pembayaran PBB (dengan filter berdasarkan role)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = PembayaranPbb::with(['wajibPajak.warga.rt', 'kolektor', 'verifikator']);

        // Filter berdasarkan role
        if ($user->role === 'kolektor') {
            // Kolektor hanya lihat data yang diinput sendiri
            $query->where('kolektor_id', $user->id);
        } elseif ($user->role === 'rt') {
            // RT hanya lihat data di wilayahnya
            $query->whereHas('wajibPajak.warga', function ($q) use ($user) {
                $q->where('rt_id', $user->rt_id);
            });
        }
        // Admin bisa lihat semua data

        // Filter tambahan
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tahun')) {
            $query->where('tahun_pajak', $request->tahun);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('wajibPajak', function ($q) use ($search) {
                $q->where('nop', 'like', "%{$search}%")
                  ->orWhereHas('warga', function ($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 15);
        $pembayaran = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }

    /**
     * Get detail pembayaran PBB
     */
    public function show(PembayaranPbb $pembayaran)
    {
        $pembayaran->load(['wajibPajak.warga.rt', 'kolektor', 'verifikator']);

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }

    /**
     * Input pembayaran PBB baru (Kolektor)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validasi
        $validated = $request->validate([
            'wajib_pajak_id' => 'required|exists:wajib_pajak,id',
            'tahun_pajak' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'jumlah_bayar' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'bukti_sppt' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120', // Max 5MB
        ]);

        // Cek apakah wajib pajak ada di wilayah kolektor
        if ($user->role === 'kolektor') {
            $wajibPajak = WajibPajak::with('warga')->find($validated['wajib_pajak_id']);
            
            // Cek apakah kolektor punya akses ke RT ini
            // (Logic ini bisa disesuaikan dengan relasi kolektor-RT)
        }

        // Cek duplikasi tahun untuk wajib pajak yang sama
        $existing = PembayaranPbb::where('wajib_pajak_id', $validated['wajib_pajak_id'])
            ->where('tahun_pajak', $validated['tahun_pajak'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran untuk tahun ini sudah tercatat.',
            ], 422);
        }

        // Upload bukti SPPT
        $buktiPath = null;
        if ($request->hasFile('bukti_sppt')) {
            $file = $request->file('bukti_sppt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('bukti_pbb', $filename, 'public');
        }

        // Create pembayaran dengan status pending
        $pembayaran = PembayaranPbb::create([
            'wajib_pajak_id' => $validated['wajib_pajak_id'],
            'tahun_pajak' => $validated['tahun_pajak'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'status' => PembayaranPbb::STATUS_PENDING,
            'bukti_sppt_path' => $buktiPath,
            'kolektor_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $pembayaran->load(['wajibPajak.warga']),
            'message' => 'Pembayaran berhasil diinput. Menunggu verifikasi admin.',
        ], 201);
    }

    /**
     * Approve pembayaran PBB (Admin)
     */
    public function approve(Request $request, PembayaranPbb $pembayaran)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can approve payments.',
            ], 403);
        }

        $pembayaran->approve($user->id);

        return response()->json([
            'success' => true,
            'data' => $pembayaran->fresh(),
            'message' => 'Pembayaran berhasil disetujui.',
        ]);
    }

    /**
     * Reject pembayaran PBB (Admin)
     */
    public function reject(Request $request, PembayaranPbb $pembayaran)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can reject payments.',
            ], 403);
        }

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $pembayaran->reject($user->id, $validated['catatan_penolakan']);

        return response()->json([
            'success' => true,
            'data' => $pembayaran->fresh(),
            'message' => 'Pembayaran ditolak.',
        ]);
    }

    /**
     * Get statistik PBB
     */
    public function statistics(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $user = $request->user();

        $query = WajibPajak::with(['pembayaranPbb' => function ($q) use ($tahun) {
            $q->where('tahun_pajak', $tahun);
        }]);

        // Filter by RT jika user adalah RT atau Kolektor
        if ($user->role === 'rt') {
            $query->whereHas('warga', function ($q) use ($user) {
                $q->where('rt_id', $user->rt_id);
            });
        }

        $totalWajibPajak = $query->count();
        $sudahLunas = PembayaranPbb::where('tahun_pajak', $tahun)
            ->where('status', PembayaranPbb::STATUS_APPROVED)
            ->count();
        $pending = PembayaranPbb::where('tahun_pajak', $tahun)
            ->where('status', PembayaranPbb::STATUS_PENDING)
            ->count();
        $belumBayar = $totalWajibPajak - $sudahLunas - $pending;

        $totalRealisasi = PembayaranPbb::where('tahun_pajak', $tahun)
            ->where('status', PembayaranPbb::STATUS_APPROVED)
            ->sum('jumlah_bayar');

        return response()->json([
            'success' => true,
            'data' => [
                'tahun' => $tahun,
                'total_wajib_pajak' => $totalWajibPajak,
                'sudah_lunas' => $sudahLunas,
                'pending' => $pending,
                'belum_bayar' => $belumBayar,
                'total_realisasi' => $totalRealisasi,
                'persentase_realisasi' => $totalWajibPajak > 0 
                    ? round(($sudahLunas / $totalWajibPajak) * 100, 2) 
                    : 0,
            ],
        ]);
    }

    /**
     * Cek status PBB publik (by NOP atau nama)
     */
    public function checkPublic(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|min:3',
        ]);

        $wajibPajak = WajibPajak::with(['warga', 'pembayaranPbb' => function ($q) {
            $q->orderBy('tahun_pajak', 'desc');
        }])
        ->where(function ($q) use ($validated) {
            $q->where('nop', 'like', "%{$validated['keyword']}%")
              ->orWhereHas('warga', function ($q) use ($validated) {
                  $q->where('nama_lengkap', 'like', "%{$validated['keyword']}%");
              });
        })
        ->first();

        if (!$wajibPajak) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        // Hanya tampilkan info yang aman untuk publik
        return response()->json([
            'success' => true,
            'data' => [
                'nop' => substr($wajibPajak->nop, 0, 4) . '...' . substr($wajibPajak->nop, -4),
                'nama' => $wajibPajak->warga->nama_lengkap,
                'rt_rw' => 'RT ' . $wajibPajak->warga->rt->rt . ' / RW ' . $wajibPajak->warga->rt->rw,
                'pembayaran' => $wajibPajak->pembayaranPbb->map(function ($p) {
                    return [
                        'tahun' => $p->tahun_pajak,
                        'status' => $p->getStatusBadgeInfo(),
                    ];
                }),
            ],
        ]);
    }
}
