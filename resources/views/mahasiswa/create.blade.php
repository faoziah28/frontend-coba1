@extends('layouts.app')

@section('title', 'Tambah mahasiswa Baru')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah mahasiswa Baru</h1>

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

    <form action="{{ route('mahasiswa.store') }}" method="POST" class="space-y-4">
        @csrf {{-- Token CSRF untuk keamanan Laravel --}}

        <div>
            <label for="npm" class="block text-sm font-medium text-gray-700">NPM</label>
            <input type="text" name="npm" id="npm" value="{{ old('npm') }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                   required maxlength="60">
            @error('npm')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama_mahasiswa" class="block text-sm font-medium text-gray-700">Nama mahasiswa</label>
            <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                   required maxlength="40">
            @error('nama_mahasiswa')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
        <label class="block mb-2">Pilih User</label>
            <select name="id_user" 
                    class="w-full p-2 border rounded mb-4 @error('id_user') border-red-500 @enderror" 
                    required>
                <option value="">-- Pilih User --</option>
                @foreach($users as $u)
                    <option value="{{ $u['id_user'] }}" 
                        {{ old('id_user') == $u['id_user'] ? 'selected' : '' }}>
                        {{ $u['username'] }}
                    </option>
                @endforeach
            </select>
            @error('id_user')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            </div>

        <div>
            <label class="block mb-2">Pilih Kode Kelas</label>
            <select name="kode_kelas" 
                    class="w-full p-2 border rounded mb-4 @error('kode_kelas') border-red-500 @enderror" 
                    required>
                <option value="">-- Pilih Kode Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k['kode_kelas'] }}" 
                        {{ old('kode_kelas') == $k['kode_kelas'] ? 'selected' : '' }}>
                        {{ $k['kode_kelas'] }}
                    </option>
                @endforeach
            </select>
            @error('kode_kelas')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('mahasiswa.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200 ease-in-out">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 ease-in-out transform hover:scale-105">
                Simpan mahasiswa
            </button>
        </div>
    </form>
</div>
@endsection
