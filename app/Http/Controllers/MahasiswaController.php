<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;


class MahasiswaController extends Controller
{
protected $apiUrl = 'http://localhost:8000/api'; // Contoh: ganti ini dengan API URL Anda
    protected $endpoint = 'http://localhost:8080/mahasiswa';

    public function index()
    {
        $mahasiswa = []; // Initialize as an empty array
        $apiErrors = []; // Initialize for API errors

        try {
            $response = Http::get($this->endpoint);

            if ($response->successful()) {
                $mahasiswa = $response->json(); // Get JSON data from the response
                // Assuming the API returns a direct array of class objects, e.g., [...]
                // If your API returns {'data': [...]}, you might need: $mahasiswa = $response->json()['data'];
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
        return view('mahasiswa.index', compact('mahasiswa', 'apiErrors'));
    }

    /**
     * Show the form for creating a new class.
     * This method is required by Route::resource for the GET /mahasiswa/create route.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Ambil data user dan kelas dari API backend
        $responseUsers = Http::get('http://localhost:8080/user'); 
        $responseKelas = Http::get('http://localhost:8080/kelas'); 

        // Cek apakah kedua response API berhasil
        if ($responseUsers->successful() && $responseKelas->successful()) {
            $users = $responseUsers->json();
            $kelas = $responseKelas->json();
            return view('mahasiswa.create', compact('users', 'kelas'));
        }

        // Jika gagal, kembali ke halaman sebelumnya dengan pesan error
        return back()->withErrors(['msg' => 'Gagal mengambil data user atau kelas']);
    }

    // Menyimpan data mahasiswa baru ke backend API
    public function store(Request $request) 
    {
        // Validasi input
        $validatedData = $request->validate([
            'npm' => 'required|numeric',
            'nama_mahasiswa' => 'required|string',
            'email' => 'required|email',
            'id_user' => 'required|numeric|unique:mahasiswa,id_user',
            'kode_kelas' => 'required|string',
        ]);

        // Kirim data ke API 
        $response = Http::asForm()->post('http://localhost:8080/mahasiswa', $validatedData);

        // Cek respons dari backend
        if ($response->successful()) {
            return redirect()->route('admin.mhs.index')->with('success', 'Data mahasiswa berhasil ditambahkan');
        }

        // Ambil pesan error dari respons API jika ada
        $errorMessage = $response->json()['messages']['error'] ?? 'Gagal menambahkan data mahasiswa';
        return back()->with('error', $errorMessage)->withInput();
    }


    // Menampilkan form edit mahasiswa berdasarkan NPM
        public function edit($npm) {
        // Ambil data mahasiswa, user, dan kelas dari API backend
        $responseMahasiswa = Http::get("http://localhost:8080/mahasiswa/$npm");
        $responseUsers = Http::get('http://localhost:8080/user'); 
        $responseKelas = Http::get('http://localhost:8080/kelas'); 

        if (
            $responseMahasiswa->successful() &&
            $responseUsers->successful() &&
            $responseKelas->successful()
        ) {
            $mahasiswa = $responseMahasiswa->json();
            $users = $responseUsers->json();
            $kelas = $responseKelas->json();
            return view('mahasiswa.edit', compact('mahasiswa', 'users', 'kelas'));
        }

        return back()->withErrors(['msg' => 'Gagal mengambil data untuk edit mahasiswa']);
    }


    // Mengirim data update mahasiswa ke backend
    public function update(Request $request, $npm) {
        // Validasi input
        $request->validate([
            'npm' => 'required|numeric',
            'nama_mahasiswa' => 'required|string',
            'email' => 'required|email',
            'id_user' => [
            'required',
            'numeric',
            Rule::unique('mahasiswa', 'id_user')->ignore($npm, 'npm'), // abaikan data yang sedang diupdate
        ],
            'npm' => 'required|numeric',
        ]);

        // Kirim data ke API untuk update
        $response = Http::put("http://localhost:8080/mahasiswa/{$npm}", [
            'npm' => $request->npm,
            'nama_mahasiswa' => $request->nama_mahasiswa,
            'email' => $request->email,
            'id_user' => $request->id_user,
            'kode_kelas' => $request->kode_kelas,
        ]);

        // Cek hasil respons
        if ($response->successful()) {
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diupdate');
        } else {
            return back()->with('error', 'Gagal mengupdate data mahasiswa');
        }
    }

    // Menghapus data mahasiswa berdasarkan NPM
    public function destroy($npm)
    {
        try {
            $response = Http::delete("{$this->endpoint}/{$npm}");

            if ($response->successful()) {
                return redirect()->route('mahasiswa.index')->with('success', 'Class data successfully deleted!');
            } else {
                $errorMessage = $response->json()['message'] ?? 'Failed to delete class data.';
                return redirect()->route('mahasiswa.index')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->route('mahasiswa.index')->with('error', 'Could not connect to API: ' . $e->getMessage());
        }
    }
    
}
// }
