@extends('layouts.app')

@section('title', 'Edit matkul')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit matkul: {{ $matkul['nama_matkul'] ?? '' }}</h1>

    {{-- Pesan Error dari Validasi atau API --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Terjadi Kesalahan!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pastikan $matkul ada dan bukan null sebelum mengakses elemennya --}}
    @if (isset($matkul))
        <form action="{{ route('matkul.update', $matkul['kode_matkul']) }}" method="POST" class="space-y-4">
            @csrf {{-- Token CSRF untuk keamanan Laravel --}}
            @method('PUT') {{-- Menggunakan method PUT untuk update --}}

            <div>
                <label for="kode_matkul" class="block text-sm font-medium text-gray-700">Kode matkul</label>
                <input type="text" id="kode_matkul" value="{{ $matkul['kode_matkul'] }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed sm:text-sm"
                       readonly disabled> {{-- Kode matkul biasanya tidak bisa diubah --}}
            </div>

            <div>
                <label for="nama_matkul" class="block text-sm font-medium text-gray-700">Nama matkul</label>
                <input type="text" name="nama_matkul" id="nama_matkul" value="{{ old('nama_matkul', $matkul['nama_matkul']) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       required maxlength="40">
                @error('nama_matkul')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sks" class="block text-sm font-medium text-gray-700">SKS</label>
                <input type="text" name="sks" id="sks" value="{{ old('sks', $matkul['sks']) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       required maxlength="40">
                @error('sks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('matkul.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200 ease-in-out">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 ease-in-out transform hover:scale-105">
                    Update matkul
                </button>
            </div>
        </form>
    @else
        <p class="text-red-500">Data matkul tidak ditemukan.</p>
    @endif
</div>
@endsection
