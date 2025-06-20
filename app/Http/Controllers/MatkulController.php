<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User; // Pastikan model User ada
use App\Models\Matkul; // Impor model matkul Anda, sesuaikan jika nama modelnya berbeda
use Illuminate\Support\Facades\Log; // Untuk logging error

use Barryvdh\DomPDF\Facade\Pdf; // tambahkan di atas

class MatkulController extends Controller
{
    protected $apiUrl = 'http://localhost:8000/api'; // Contoh: ganti ini dengan API URL Anda
    protected $endpoint = 'http://localhost:8080/matkul';

    public function index()
    {
        $matkul = []; // Initialize as an empty array
        $apiErrors = []; // Initialize for API errors

        try {
            $response = Http::get($this->endpoint);

            if ($response->successful()) {
                $matkul = $response->json(); // Get JSON data from the response
                // Assuming the API returns a direct array of class objects, e.g., [...]
                // If your API returns {'data': [...]}, you might need: $matkul = $response->json()['data'];
            } else {
                // Handle error if API does not return a successful status
                $errorMessage = $response->json()['message'] ?? 'Failed to retrieve class data from API.';
                $apiErrors['api_error'] = $errorMessage;
            }
        } catch (\Exception $e) {
                // Handle exception for connection issues
            $apiErrors['connection_error'] = 'Could not connect to the class API: ' . $e->getMessage();
        }

        // Pass the normalized class data and API errors to the view
        return view('matkul.index', compact('matkul', 'apiErrors'));
    }

    /**
     * Show the form for creating a new class.
     * This method is required by Route::resource for the GET /matkul/create route.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('matkul.create');
    }

    public function store(Request $request)
    {
        // KEMBALIKAN VALIDASI LARAVEL
        $request->validate([
        'kode_matkul' => 'required|string|max:6',
        'nama_matkul' => 'required|string|max:40',
        'sks' => 'required|string|max:40',

    ]);

        try {
            $response = Http::asForm()->post($this->endpoint, $request->all());

            if ($response->successful()) {
                return redirect()->route('matkul.index')->with('success', 'Class data successfully added!');
            } else {
                // Perbaiki penanganan pesan error dari API CodeIgniter
                $apiResponse = $response->json();
                $errorMessage = 'Failed to add class data.';

                if (isset($apiResponse['messages'])) {
                    // Jika ada array 'messages' (misal dari fail($this->model->errors()))
                    if (is_array($apiResponse['messages'])) {
                        $errorMessage = implode(', ', array_values($apiResponse['messages']));
                    } else {
                        $errorMessage = $apiResponse['messages'];
                    }
                } elseif (isset($apiResponse['message'])) {
                    // Jika ada kunci 'message'
                    $errorMessage = $apiResponse['message'];
                }

                return redirect()->back()->withInput()->withErrors(['api_error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['connection_error' => 'Could not connect to API: ' . $e->getMessage()]);
        }
    }

    // public function store(Request $request)
    // {
    //     // KEMBALIKAN VALIDASI LARAVEL
    //     $request->validate([
    //         'kode_matkul' => 'required|string|max:6',
    //         'nama_matkul' => 'required|string|max:40',
    //         'sks' => 'required|string|max:40',

    //     ]);

    //     try {
    //         // PENTING: Hapus asForm() karena CI backend sekarang menerima JSON
    //         $response = Http::post($this->endpoint, $request->all());

    //         if ($response->successful()) {
    //             return redirect()->route('matkul.index')->with('success', 'Class data successfully added!');
    //         } else {
    //             // Perbaiki penanganan pesan error dari API CodeIgniter
    //             $apiResponse = $response->json();
    //             $errorMessage = 'Failed to add class data.';

    //             if (isset($apiResponse['messages'])) {
    //                 // Jika ada array 'messages' (misal dari fail($this->model->errors()))
    //                 if (is_array($apiResponse['messages'])) {
    //                     $errorMessage = implode(', ', array_values($apiResponse['messages']));
    //                 } else {
    //                     $errorMessage = $apiResponse['messages'];
    //                 }
    //             } elseif (isset($apiResponse['message'])) {
    //                 // Jika ada kunci 'message'
    //                 $errorMessage = $apiResponse['message'];
    //             }

    //             return redirect()->back()->withInput()->withErrors(['api_error' => $errorMessage]);
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->back()->withInput()->withErrors(['connection_error' => 'Could not connect to API: ' . $e->getMessage()]);
    //     }
    // }


    public function show($id)
    {
        try {
            $matkul = Http::get("{$this->endpoint}/{$id}")->json();
            return view('matkul.show', compact('matkul'));
        } catch (\Exception $e) {
            return redirect()->route('matkul.index')->with('error', 'Could not connect to API: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $matkul = Http::get("{$this->endpoint}/{$id}")->json();
            return view('matkul.edit', compact('matkul'));
        } catch (\Exception $e) {
            return redirect()->route('matkul.index')->with('error', 'Could not connect to API: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_matkul' => 'required|string|max:40',
            'sks' => 'required|string|max:40',
        ]);

        try {
            $response = Http::put("{$this->endpoint}/{$id}", $request->except('kode_matkul'));

            if ($response->successful()) {
                return redirect()->route('matkul.index')->with('success', 'Class data successfully updated!');
            } else {
                $apiResponse = $response->json();
                $errorMessage = 'Failed to update class data.';

                if (isset($apiResponse['messages'])) {
                    if (is_array($apiResponse['messages'])) {
                        $errorMessage = implode(', ', array_values($apiResponse['messages']));
                    } else {
                        $errorMessage = $apiResponse['messages'];
                    }
                } elseif (isset($apiResponse['message'])) {
                    $errorMessage = $apiResponse['message'];
                }
                return redirect()->back()->withInput()->withErrors(['api_error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['connection_error' => 'Could not connect to API: ' . $e->getMessage()]);
        }
    }

     public function unduhMatkul($kode_matkul)
    {
        // 1. Ambil detail matkul dari database lokal Anda berdasarkan $kode_matkul
        $unduhMatkul = Matkul::where('kode_matkul', $kode_matkul)->first();

        // Jika matkul tidak ditemukan, kembalikan error
        if (!$unduhMatkul) {
            return back()->with('error', 'matkul dengan kode ' . $kode_matkul . ' tidak ditemukan.');
        }

        // 2. Buat view untuk PDF. Contoh: resources/views/pdfs/detail_matkul.blade.php
        $pdf = PDF::loadView('matkul.cetak', compact('unduhMatkul'));

        // 3. Kembalikan PDF sebagai unduhan
        return $pdf->download('matkul.cetak_' . $kode_matkul . '.pdf');
    }


    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->endpoint}/{$id}");

            if ($response->successful()) {
                return redirect()->route('matkul.index')->with('success', 'Class data successfully deleted!');
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to delete class data.';
                return redirect()->route('matkul.index')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->route('matkul.index')->with('error', 'Could not connect to API: ' . $e->getMessage());
        }
    }
}
