<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('kode_produk', 'asc')->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;

        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('dashboard');
            }
        }
    }

    public function data()
    {
        try {
            $details = PenjualanDetail::with(['penjualan.member', 'penjualan.user', 'produk'])->get();

            return datatables()
                ->of($details)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($detail) {
                    return $detail->penjualan && $detail->penjualan->created_at
                        ? $detail->penjualan->created_at->format('d-m-Y')
                        : '-';
                })
                ->addColumn('kode_member', function ($detail) {
                    $member = $detail->penjualan->member->kode_member ?? null;
                    if ($member) {
                        return '<span class="label label-success">' . $member . '</span>';
                    } else {
                        return '<span class="label label-default">Tidak Menggunakan Member</span>';
                    }
                })
                ->addColumn('total_item', function ($detail) {
                    return $detail->penjualan->total_item ?? '-';
                })
                ->addColumn('total_harga', function ($detail) {
                    return 'Rp. ' . number_format($detail->penjualan->total_harga ?? 0, 0, ',', '.');
                })
                ->addColumn('diskon', function ($detail) {
                    return ($detail->penjualan->diskon ?? 0) . '%';
                })
                ->addColumn('bayar', function ($detail) {
                    return 'Rp. ' . number_format($detail->penjualan->bayar ?? 0, 0, ',', '.');
                })
                ->addColumn('kasir', function ($detail) {
                    return $detail->penjualan->user->name ?? '-';
                })
                ->addColumn('kode_produk', function ($detail) {
                    return $detail->produk->kode_produk ?? '-';
                })
                ->addColumn('produk', function ($detail) {
                    return $detail->produk->nama_produk ?? '-';
                })
                ->addColumn('aksi', function ($detail) {
                    return '
                        <div class="btn-group">
                            <button onclick="showDetail(`' . route('penjualan.show', $detail->id_penjualan) . '`)" class="btn btn-xs btn-info btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                          <button onclick="deleteData(`' . route('penjualan.destroy', $detail->id_penjualan_detail) . '`)" class="btn btn-xs btn-danger btn-flat">
                <i class="fa fa-trash"></i>
            </button>
                        </div>
                    ';
                })
                ->rawColumns(['kode_member', 'tanggal', 'total_item', 'kode_produk', 'total_harga', 'diskon', 'bayar', 'kasir', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function transaksiBaruData($id)
    {
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">' . $item->produk['kode_produk'] . '</span>';
            $row['nama_produk'] = $item->produk['nama_produk'];
            // Format harga jual dengan titik sebagai pemisah ribuan
            $row['harga_jual']  = 'Rp. ' . number_format($item->harga_jual, 0, ',', '.');
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="' . $item->id_penjualan_detail . '" value="' . $item->jumlah . '">';
            $row['diskon']      = $item->diskon . '%';
            // Format subtotal dengan titik sebagai pemisah ribuan
            $row['subtotal']    = 'Rp. ' . number_format($item->subtotal, 0, ',', '.');
            $row['aksi']        = '<div class="btn-group">
                                <button onclick="deleteData(`' . route('transaksi.destroy', $item->id_penjualan_detail) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                            </div>';
            $data[] = $row;

            $total += $item->harga_jual * $item->jumlah;
            $total_item += $item->jumlah;
        }

        $data[] = [
            'kode_produk' => '
            <div class="total hide">' . $total . '</div>
            <div class="total_item hide">' . $total_item . '</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah'])
            ->make(true);
    }


    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $produk = Produk::where('id_produk', $request->id_produk)->first();
            if (! $produk) {
                \DB::rollBack();
                return response()->json('Data gagal disimpan', 400);
            }

            $detail = new PenjualanDetail();
            $detail->id_penjualan = $request->id_penjualan;
            $detail->id_produk = $produk->id_produk;
            $detail->harga_jual = $produk->harga_jual;
            $detail->jumlah = 1;
            $detail->diskon = 0;
            $detail->subtotal = $produk->harga_jual;
            $detail->save();

            \DB::commit();
            return response()->json('Data berhasil disimpan', 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0)
    {
        $bayar   = $total - ($diskon / 100 * $total);
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar) . ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali) . ' Rupiah'),
        ];

        return response()->json($data);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('term');

        $result = Produk::where('nama_produk', 'LIKE', '%' . $search . '%')
            ->select('id_produk', 'kode_produk', 'nama_produk')
            ->limit(10)
            ->get();

        return response()->json($result->map(function ($produk) {
            return [
                'label' => $produk->nama_produk . ' (' . $produk->kode_produk . ')',
                'value' => $produk->nama_produk,
                'id_produk' => $produk->id_produk,
                'kode_produk' => $produk->kode_produk
            ];
        }));
    }
}
