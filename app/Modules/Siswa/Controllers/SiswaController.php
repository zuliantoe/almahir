<?php

namespace Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SiswaController
 * 
 * Handles all student-related operations within the Siswa module.
 * This is a sample controller demonstrating the modular architecture.
 * 
 * @author SIAKAD Development Team
 */
class SiswaController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): View
    {
        return view('siswa::index', [
            'title' => 'Data Siswa',
            'breadcrumb' => 'Siswa / Daftar',
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('siswa::create', [
            'title' => 'Tambah Siswa Baru',
            'breadcrumb' => 'Siswa / Tambah',
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'email' => 'required|email|unique:siswa,email',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
        ]);

        // TODO: Store the student data
        // Siswa::create($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified student.
     */
    public function show(string $id): View
    {
        // TODO: Fetch the student by ID
        return view('siswa::show', [
            'title' => 'Detail Siswa',
            'breadcrumb' => 'Siswa / Detail',
            'siswa' => null, // Replace with actual data
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(string $id): View
    {
        // TODO: Fetch the student by ID
        return view('siswa::edit', [
            'title' => 'Edit Siswa',
            'breadcrumb' => 'Siswa / Edit',
            'siswa' => null, // Replace with actual data
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20',
            'email' => 'required|email',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
        ]);

        // TODO: Update the student data
        
        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Delete the student
        
        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
