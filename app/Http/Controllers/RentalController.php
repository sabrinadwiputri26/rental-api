<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Exception;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search_supir;
        $limit = $request->limit;
        $rentals = Rental::where('supir', 'LIKE', '&'. $search.'&')->limit($limit)->get();

        if ($rentals) {
            return ApiFormatter::createAPI(200, 'success', $rentals);
        }else {
            return ApiFormatter::createAPI(400, 'failed');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'alamat' => 'required',
                'type' => 'required',
                'waktu_jam' => 'required',
                'jam_mulai' => 'required',
                'supir' => 'required',
            ]);

            $rentals= Rental::create([
                'nama' =>$request->nama,
                'alamat' =>$request->alamat,
                'type' =>$request->type,
                'total_harga' =>$request->waktu_jam*150000,
                'jam_mulai' =>$request->jam_mulai,
                'waktu_jam'=>$request->waktu_jam,
                'supir' =>$request->supir,
                'jam_selesai' => NULL,
                'tempat_tujuan' => NULL,
                'riwayat_perjalanan' => NULL,
                'status' => "proses",
            ]);

            $hasilTambahData = Rental::where('id', $rentals->id)->first();
            if ($hasilTambahData) {
                return ApiFormatter::createAPI(200, 'success', $rentals);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch(Exception $error) {
            return ApiFormatter::createAPI(400, 'error',$error->getMessage());
        }
    }

    public function createToken()
    {
        return csrf_token();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $rental = Rental::find($id);
            if ($rental) {
                return ApiFormatter::createAPI(200, 'success', $rental);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch(Exception $error) {
            return ApiFormatter::createAPI(400, 'error',$error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'jam_selesai' => 'required',
                'tempat_tujuan' => 'required',
            ]);
    
            $rentals = Rental::find($id);
                $rentals->update([
                    'jam_selesai' => $request->jam_selesai,
                    'tempat_tujuan' => $request->tempat_tujuan,
                    'riwayat_perjalanan' => "Dimulai pada saat jam $rentals->jam_mulai dengan titik awal berada di $rentals->alamat, dan diakhiri pada jam $rentals->jam_selesai dengan tempat tujuan di $rentals->tempat_tujuan",
                    'status' => "selesai",
            ]);
    
            $dataTerbaru = Rental::where('id', $rentals->id)->first();
            if ($dataTerbaru) {
                return ApiFormatter::createAPI(200, 'success', $rentals);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch(Exception $error) {
            return ApiFormatter::createAPI(400, 'error',$error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $rental = Rental::find($id);
            $cekBerhasil = $rental->delete();

            if ($cekBerhasil) {
                return ApiFormatter::createAPI(200, 'success', 'Data terhapus');
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function trash()
    {
        try {
            $rentals = Rental::onlyTrashed()->get();

            if ($rentals) {
                return ApiFormatter::createAPI(200, 'success', $rentals);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $rental = Rental::onlyTrashed('id', $id);
            $rental->restore();
            $dataKembali = Rental::where('id', $id)->first();

            if ($dataKembali) {
                return ApiFormatter::createAPI(200, 'success', $rental);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function permanentDelete($id)
    {
        try {
            $rental = Rental::onlyTrashed()->where('id', $id);
            $proses = $rental->forceDelete();
                return ApiFormatter::createAPI(200, 'success', 'Berhasil hapus permanent!');
        }catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }
}