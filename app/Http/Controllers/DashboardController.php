<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalUsers = \App\Models\User::count();
        $superadminCount = \App\Models\User::where('role', 'Superadmin')->count();
        $adminCount = \App\Models\User::where('role', 'Admin')->count();

        // Admin/Superadmin Analytics
        $cohortIpkData = [];
        $highestPassRates = [];
        $lowestPassRates = [];
        $activeStudentsCount = 0;
        $activeTeachersCount = 0;
        $averageInstitutionIpk = 0.00;
        $currentClassesCount = 0;

        if ($user->role === 'Superadmin' || $user->role === 'Admin') {
            $activeStudentsCount = \App\Models\Student::where('status', 'active')->count();
            $activeTeachersCount = \App\Models\Teacher::count();

            // Rata-rata IPK institusi
            $allStudents = \App\Models\Student::all();
            $totalIpkSum = 0;
            $studentsCount = 0;
            foreach ($allStudents as $st) {
                $totalIpkSum += $st->calculateIPK();
                $studentsCount++;
            }
            $averageInstitutionIpk = $studentsCount > 0 ? round($totalIpkSum / $studentsCount, 2) : 0.00;

            // Jumlah kelas berjalan
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $currentClassesCount = $activeYear 
                ? \App\Models\AcademicClass::where('academic_year_id', $activeYear->id)->count() 
                : 0;

            // IPK per angkatan
            $cohorts = \App\Models\Student::select('angkatan')->distinct()->pluck('angkatan')->toArray();
            sort($cohorts);
            foreach ($cohorts as $cohort) {
                $students = \App\Models\Student::where('angkatan', $cohort)->get();
                $totalIpk = 0;
                $count = 0;
                foreach ($students as $student) {
                    $totalIpk += $student->calculateIPK();
                    $count++;
                }
                $cohortIpkData[$cohort] = $count > 0 ? round($totalIpk / $count, 2) : 0.00;
            }

            // Kelulusan MK
            $courses = \App\Models\Course::with('classes.enrollments.grade')->get();
            $coursePassRates = [];
            foreach ($courses as $course) {
                $totalLocked = 0;
                $totalPassed = 0;
                foreach ($course->classes as $class) {
                    foreach ($class->enrollments as $enrollment) {
                        if ($enrollment->grade && $enrollment->grade->is_locked) {
                            $totalLocked++;
                            if ($enrollment->grade->quality_point > 0) {
                                $totalPassed++;
                            }
                        }
                    }
                }
                if ($totalLocked > 0) {
                    $passRate = ($totalPassed / $totalLocked) * 100;
                    $coursePassRates[] = [
                        'code' => $course->code,
                        'name' => $course->name,
                        'pass_rate' => round($passRate, 2),
                        'total_students' => $totalLocked,
                    ];
                }
            }
            
            usort($coursePassRates, function($a, $b) {
                return $b['pass_rate'] <=> $a['pass_rate'];
            });
            $highestPassRates = array_slice($coursePassRates, 0, 5);
            $lowestPassRates = array_slice(array_reverse($coursePassRates), 0, 5);
        }

        // Dosen Analytics
        $totalClasses = 0;
        $totalEnrolledStudents = 0;
        $dosenGradeCounts = [
            'A' => 0, 'A-' => 0, 'B+' => 0, 'B' => 0, 'B-' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'E' => 0
        ];
        $dosenClassesList = collect();
        $pendingInputClasses = collect();
        $mahasiswaBimbinganCount = 0;

        if ($user->role === 'Dosen') {
            $teacher = $user->teacher;
            if ($teacher) {
                $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

                $dosenClasses = \App\Models\AcademicClass::where('teacher_id', $teacher->id)
                    ->with(['course', 'academicYear', 'enrollments.grade'])
                    ->latest()
                    ->get();

                $totalClasses = $dosenClasses->count();
                $totalEnrolledStudents = $dosenClasses->sum(function($c) {
                    return $c->enrollments->count();
                });

                $mahasiswaBimbinganCount = \App\Models\Enrollment::whereIn('class_id', $dosenClasses->pluck('id'))
                    ->distinct('student_id')
                    ->count('student_id');

                $dosenClassesList = $activeYear
                    ? $dosenClasses->where('academic_year_id', $activeYear->id)
                    : collect();

                foreach ($dosenClasses as $c) {
                    $classLocked = $c->enrollments->contains(function ($e) {
                        return $e->grade?->is_locked;
                    });
                    if (!$classLocked && $c->enrollments->count() > 0) {
                        $pendingInputClasses->push($c);
                    }
                }

                foreach ($dosenClasses as $class) {
                    foreach ($class->enrollments as $enrollment) {
                        if ($enrollment->grade && $enrollment->grade->is_locked) {
                            $letter = $enrollment->grade->grade_letter;
                            if (array_key_exists($letter, $dosenGradeCounts)) {
                                $dosenGradeCounts[$letter]++;
                            }
                        }
                    }
                }
            }
        }

        // Mahasiswa Analytics
        $ipsTrend = [];
        $mahasiswaIpk = 0.00;
        $mahasiswaSks = 0;
        $mahasiswaIpsLast = 0.00;

        if ($user->role === 'Mahasiswa') {
            $student = $user->student;
            if ($student) {
                $mahasiswaIpk = $student->calculateIPK();
                $mahasiswaSks = $student->calculateTotalSksLulus();

                $years = \App\Models\AcademicYear::whereHas('classes.enrollments', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereHas('grade', function($g) {
                          $g->where('is_locked', true);
                      });
                })->orderBy('year', 'asc')->orderBy('semester', 'asc')->get();

                foreach ($years as $year) {
                    $ips = $student->calculateIPS($year->id);
                    $ipsTrend[] = [
                        'semester' => $year->year . ' (' . $year->semester . ')',
                        'ips' => $ips,
                    ];
                    $mahasiswaIpsLast = $ips;
                }
            }
        }

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'totalUsers' => $totalUsers,
            'superadminCount' => $superadminCount,
            'adminCount' => $adminCount,
            'cohortIpkData' => $cohortIpkData,
            'highestPassRates' => $highestPassRates,
            'lowestPassRates' => $lowestPassRates,
            'activeStudentsCount' => $activeStudentsCount,
            'activeTeachersCount' => $activeTeachersCount,
            'averageInstitutionIpk' => $averageInstitutionIpk,
            'currentClassesCount' => $currentClassesCount,
            'totalClasses' => $totalClasses,
            'totalEnrolledStudents' => $totalEnrolledStudents,
            'dosenGradeCounts' => $dosenGradeCounts,
            'ipsTrend' => $ipsTrend,
            'mahasiswaIpk' => $mahasiswaIpk,
            'mahasiswaSks' => $mahasiswaSks,
            'mahasiswaIpsLast' => $mahasiswaIpsLast,
            'dosenClassesList' => $dosenClassesList,
            'pendingInputClasses' => $pendingInputClasses,
            'mahasiswaBimbinganCount' => $mahasiswaBimbinganCount,
        ]);
    }

    public function show()
    {
        return view('dashboard.show', [
            'title' => 'My Profile',
            'user' => Auth::user()
        ]);
    }

    public function edit()
    {
        return view('dashboard.edit', [
            'title' => 'Edit Profile',
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $validate = $request->validate([
                'name' => 'required',
                'password' => 'nullable|min:8',
                'passwordconfirm' => 'nullable|same:password',
                'email' => 'required|email|lowercase|unique:users,email,' . $user->id,
                'avatar' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:512'
            ], [
                'name.required' => 'Nama wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'passwordconfirm.same' => 'Konfirmasi password tidak cocok',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'avatar.image' => 'File avatar harus berupa gambar',
                'avatar.mimes' => 'Format avatar harus png, jpg, jpeg, atau svg',
                'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 512 KB',
            ]);

            if ($request->file('avatar')) {
                $validate['avatar'] = $request->file('avatar')->store('img', 'public');
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            if ($request->password) {
                $validate['password'] = bcrypt($request->password);
            } else {
                unset($validate['password']);
            }
            $user->update($validate);

            DB::commit();
            return to_route('dashboard.show')->withSuccess('Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('dashboard.edit')->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }
}
