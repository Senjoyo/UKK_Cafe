<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Menu;
use App\Models\User;
use PDF;
use Illuminate\Support\Facades\File;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'kasir') {
            $transactions = Transaksi::with(['user', 'meja', 'menus'])
                ->orderBy('id')
                ->get();

            return response()->json($transactions);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'kasir') {
            $request->validate([
                'meja_id' => 'required|exists:mejas,id',
                'menu' => 'required|array',
                'menu.*.id' => 'required|exists:menus,id',
                'menu.*.jumlah' => 'required|integer',
            ]);

            $totalHarga = 0;
            foreach ($request->menu as $item) {
                $menu = Menu::find($item['id']);
                $totalHarga += $menu->harga * $item['jumlah'];
            }

            $transaksi = Transaksi::create([
                'user_id' => auth()->id(),
                'meja_id' => $request->meja_id,
                'total_harga' => $totalHarga,
            ]);

            foreach ($request->menu as $item) {
                $transaksi->menus()->attach($item['id'], ['jumlah' => $item['jumlah']]);
            }


            return response()->json([
                'message' => 'Transaksi berhasil',
                'data' => $transaksi,
            ]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    public function cetakNota($id, Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'kasir') {
            // Temukan transaksi berdasarkan ID
            $transaksi = Transaksi::with(['user', 'meja', 'menus'])->find($id);

            // Jika transaksi tidak ditemukan, kembalikan respon error
            if (!$transaksi) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Dapatkan data yang diperlukan untuk nota
            $namaCafe = "Cafe WikuHub"; // Ganti dengan nama cafe Anda
            $tanggalTransaksi = $transaksi->created_at->format('Y-m-d H:i:s');
            $namaKasir = $transaksi->user->name;
            $nomorMeja = $transaksi->meja->nomerMeja;

            // Data pemesanan (menu dan jumlahnya)
            $dataPemesanan = [];
            foreach ($transaksi->menus as $menu) {
                $dataPemesanan[] = [
                    'nama_menu' => $menu->namaMenu,
                    'jumlah' => $menu->pivot->jumlah,
                    'harga_satuan' => $menu->harga,
                    'total_harga' => $menu->harga * $menu->pivot->jumlah
                ];
            }

            $totalHarga = $transaksi->total_harga;

            // Data untuk ditampilkan di PDF
            $data = [
                'nama_cafe' => $namaCafe,
                'tanggal_transaksi' => $tanggalTransaksi,
                'nama_kasir' => $namaKasir,
                'nomor_meja' => $nomorMeja,
                'data_pemesanan' => $dataPemesanan,
                'total_harga' => $totalHarga
            ];

            // Buat PDF dari view (buat view 'nota-pdf.blade.php' untuk template PDF)
            $pdf = PDF::loadView('nota-pdf', $data);

            // Tentukan nama file PDF
            $fileName = 'nota_transaksi_' . $id . '.pdf';

            // Tentukan path ke folder public/nota
            $path = public_path('nota/' . $fileName);

            // Pastikan folder public/nota ada
            if (!File::isDirectory(public_path('nota'))) {
                File::makeDirectory(public_path('nota'), 0755, true); // Buat folder jika belum ada
            }

            // Simpan file PDF ke folder public/nota
            $pdf->save($path);

            // Unduh file PDF
            return $pdf->download('nota_transaksi_' . $id . '.pdf');
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    public function getTransaksiByKasir($id, Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'manajer') {
            // Cek apakah kasir (user) ada
            $kasir = User::find($id);
            if (!$kasir) {
                return response()->json(['message' => 'Kasir tidak ditemukan'], 404);
            }

            // Ambil semua transaksi yang dilakukan oleh kasir yang dipilih
            $transaksi = Transaksi::with(['user', 'meja', 'menus']) // Memuat relasi dengan user, meja, dan menus
                ->where('user_id', $id) // Memfilter transaksi berdasarkan kasir yang dipilih
                ->orderBy('created_at', 'desc') // Mengurutkan dari yang terbaru
                ->get();

            // Jika tidak ada transaksi
            if ($transaksi->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada transaksi yang ditemukan untuk kasir ini'
                ], 404);
            }

            // Kembalikan daftar transaksi
            return response()->json([
                'message' => 'Daftar transaksi berhasil diambil',
                'kasir' => $kasir->name, // Nama kasir
                'transaksi' => $transaksi
            ]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }


    public function getTransaksiByTanggal(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'manajer') {
            // Validasi input tanggal pertama dan kedua dari query string
            $request->validate([
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal', // Pastikan tanggal akhir tidak sebelum tanggal awal
            ]);

            // Ambil tanggal dari query string
            $tanggalAwal = $request->input('tanggal_awal');
            $tanggalAkhir = $request->input('tanggal_akhir');

            // Ambil semua transaksi yang terjadi dalam rentang tanggal yang dimasukkan
            $transaksi = Transaksi::with(['user', 'meja', 'menus']) // Memuat relasi dengan user, meja, dan menus
                ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]) // Memfilter berdasarkan rentang tanggal
                ->orderBy('created_at', 'desc') // Mengurutkan dari yang terbaru
                ->get();

            // Jika tidak ada transaksi dalam rentang tanggal tersebut
            if ($transaksi->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada transaksi yang ditemukan dalam rentang tanggal ini'
                ], 404);
            }

            // Kembalikan daftar transaksi
            return response()->json([
                'message' => 'Daftar transaksi berhasil diambil',
                'transaksi' => $transaksi
            ]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }
}
