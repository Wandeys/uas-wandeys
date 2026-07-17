<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mahasiswa.index', [
            'title' => 'Mahasiswa',
            'students' => Student::with('user')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mahasiswa.create', [
            'title' => 'Tambah Mahasiswa',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|email|lowercase|unique:users,email',
            'password' => 'required|min:8',
            'passwordconfirm' => 'required|same:password',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:512',
            'nim' => 'required|unique:students,nim',
            'angkatan' => 'required',
            'status' => 'required|in:active,inactive,graduated',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'passwordconfirm.required' => 'Konfirmasi password wajib diisi',
            'passwordconfirm.same' => 'Konfirmasi password tidak cocok',
            'avatar.image' => 'File avatar harus berupa gambar',
            'avatar.mimes' => 'Format avatar harus png, jpg, jpeg, atau svg',
            'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 512 KB',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'angkatan.required' => 'Angkatan wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ]);

        DB::beginTransaction();

        try {
            $avatarPath = null;
            if ($request->file('avatar')) {
                $avatarPath = $request->file('avatar')->store('img', 'public');
            }

            $user = User::create([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'password' => bcrypt($validate['password']),
                'avatar' => $avatarPath,
                'role' => 'Mahasiswa',
                'email_verified_at' => now(),
            ]);

            Student::create([
                'user_id' => $user->id,
                'nim' => $validate['nim'],
                'angkatan' => $validate['angkatan'],
                'status' => $validate['status'],
            ]);

            DB::commit();
            return to_route('mahasiswa.index')->withSuccess('Data Mahasiswa berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('mahasiswa.create')->withError('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $mahasiswa)
    {
        $mahasiswa->load('user');
        return view('mahasiswa.show', [
            'title' => 'Detail Mahasiswa',
            'mahasiswa' => $mahasiswa,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $mahasiswa)
    {
        $mahasiswa->load('user');
        return view('mahasiswa.edit', [
            'title' => 'Edit Mahasiswa',
            'mahasiswa' => $mahasiswa,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $mahasiswa)
    {
        $user = $mahasiswa->user;

        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|email|lowercase|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'passwordconfirm' => 'nullable|same:password',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:512',
            'nim' => 'required|unique:students,nim,' . $mahasiswa->id,
            'angkatan' => 'required',
            'status' => 'required|in:active,inactive,graduated',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'passwordconfirm.same' => 'Konfirmasi password tidak cocok',
            'avatar.image' => 'File avatar harus berupa gambar',
            'avatar.mimes' => 'Format avatar harus png, jpg, jpeg, atau svg',
            'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 512 KB',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'angkatan.required' => 'Angkatan wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid',
        ]);

        DB::beginTransaction();

        try {
            $avatarPath = $user->avatar;
            if ($request->file('avatar')) {
                $avatarPath = $request->file('avatar')->store('img', 'public');
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            $userUpdate = [
                'name' => $validate['name'],
                'email' => $validate['email'],
                'avatar' => $avatarPath,
            ];

            if ($request->password) {
                $userUpdate['password'] = bcrypt($request->password);
            }

            $user->update($userUpdate);

            $mahasiswa->update([
                'nim' => $validate['nim'],
                'angkatan' => $validate['angkatan'],
                'status' => $validate['status'],
            ]);

            DB::commit();
            return to_route('mahasiswa.index')->withSuccess('Data Mahasiswa berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('mahasiswa.edit', $mahasiswa)->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $mahasiswa)
    {
        DB::beginTransaction();

        try {
            $user = $mahasiswa->user;
            $avatar = $user ? $user->avatar : null;

            $mahasiswa->delete();

            if ($user) {
                $user->delete();
            }

            if ($avatar && Storage::disk('public')->exists($avatar)) {
                Storage::disk('public')->delete($avatar);
            }

            DB::commit();
            return to_route('mahasiswa.index')->withSuccess('Data Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('mahasiswa.index')->withError('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
