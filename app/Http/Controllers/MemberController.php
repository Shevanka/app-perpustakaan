<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private array $members = [
        [
            'id' => 1,
            'nama' => 'Ahmad Fauzi',
            'nim' => '2103191001',
            'email' => 'ahmad.fauzi@pens.ac.id',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Raya ITS No. 1, Surabaya',
            'status' => 'aktif',
        ],
        [
            'id' => 2,
            'nama' => 'Siti Nurhaliza',
            'nim' => '2103191002',
            'email' => 'siti.nurhaliza@pens.ac.id',
            'nomor_telepon' => '081234567891',
            'alamat' => 'Jl. Gebang Wetan No. 15, Surabaya',
            'status' => 'aktif',
        ],
        [
            'id' => 3,
            'nama' => 'Budi Santoso',
            'nim' => '2103191003',
            'email' => 'budi.santoso@pens.ac.id',
            'nomor_telepon' => '081234567892',
            'alamat' => 'Jl. Keputih Perintis No. 8, Surabaya',
            'status' => 'nonaktif',
        ],
    ];

    public function index()
    {
        $members = $this->members;

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        return redirect()->route('members.index')
            ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan (data dummy, belum tersimpan ke database).");
    }

    public function show(string $id)
    {
        return "MemberController@show, id: {$id}";
    }

    public function edit(string $id)
    {
        return "MemberController@edit, id: {$id}";
    }

    public function update(Request $request, string $id)
    {
        return "MemberController@update, id: {$id}";
    }

    public function destroy(string $id)
    {
        return redirect()->route('members.index')
            ->with('success', "Anggota dengan id {$id} berhasil dihapus (data dummy, belum tersimpan ke database).");
    }
}
