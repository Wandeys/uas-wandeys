<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dosen.index', [
            'title' => 'Dosen',
            'teachers' => Teacher::with('user')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dosen.create', [
            'title' => 'Tambah Dosen',
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
            'nip' => 'required|unique:teachers,nip',
            'nidn' => 'required|unique:teachers,nidn',
            'gelar' => 'nullable',
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
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nidn.required' => 'NIDN wajib diisi',
            'nidn.unique' => 'NIDN sudah terdaftar',
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
                'role' => 'Dosen',
                'email_verified_at' => now(),
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => $validate['nip'],
                'nidn' => $validate['nidn'],
                'gelar' => $validate['gelar'],
            ]);

            DB::commit();
            return to_route('dosen.index')->withSuccess('Data Dosen berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('dosen.create')->withError('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $dosen)
    {
        // Load user relationship
        $dosen->load('user');
        return view('dosen.show', [
            'title' => 'Detail Dosen',
            'dosen' => $dosen,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $dosen)
    {
        $dosen->load('user');
        return view('dosen.edit', [
            'title' => 'Edit Dosen',
            'dosen' => $dosen,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $dosen)
    {
        $user = $dosen->user;

        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|email|lowercase|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'passwordconfirm' => 'nullable|same:password',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:512',
            'nip' => 'required|unique:teachers,nip,' . $dosen->id,
            'nidn' => 'required|unique:teachers,nidn,' . $dosen->id,
            'gelar' => 'nullable',
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
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nidn.required' => 'NIDN wajib diisi',
            'nidn.unique' => 'NIDN sudah terdaftar',
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

            $dosen->update([
                'nip' => $validate['nip'],
                'nidn' => $validate['nidn'],
                'gelar' => $validate['gelar'],
            ]);

            DB::commit();
            return to_route('dosen.index')->withSuccess('Data Dosen berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('dosen.edit', $dosen)->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $dosen)
    {
        DB::beginTransaction();

        try {
            $user = $dosen->user;
            $avatar = $user ? $user->avatar : null;

            // Delete teacher first
            $dosen->delete();

            // Delete user if exists (this will cascade, but we do it manually to clean users table)
            if ($user) {
                $user->delete();
            }

            if ($avatar && Storage::disk('public')->exists($avatar)) {
                Storage::disk('public')->delete($avatar);
            }

            DB::commit();
            return to_route('dosen.index')->withSuccess('Data Dosen berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('dosen.index')->withError('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
