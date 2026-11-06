@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
    <h1>Daftar Anggota</h1>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('members.create') }}" class="btn">+ Tambah Anggota</a>

        <form action="{{ route('members.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" placeholder="🔍 Cari nama anggota..." value="{{ request('search') }}" style="width: 240px; margin: 0; padding: 10px 16px;">
            <button type="submit" class="btn" style="padding: 10px 18px;">Cari</button>
            @if (request('search'))
                <a href="{{ route('members.index') }}" class="btn" style="background: linear-gradient(135deg, #94a3b8, #64748b); box-shadow: 4px 4px 10px rgba(148, 163, 184, 0.4), -4px -4px 10px #ffffff; padding: 10px 16px;">Reset</a>
            @endif
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Email</th>
                <th>No. Telepon</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ $member['id'] }}</td>
                    <td>{{ $member['nama'] }}</td>
                    <td>{{ $member['nim'] }}</td>
                    <td>{{ $member['email'] }}</td>
                    <td>{{ $member['nomor_telepon'] }}</td>
                    <td>{{ ucfirst($member['status']) }}</td>
                    <td>
                        <a href="{{ route('members.show', $member['id']) }}">Detail</a>
                        <a href="{{ route('members.edit', $member['id']) }}">Edit</a>
                        <form class="inline" action="{{ route('members.destroy', $member['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data anggota.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $members->appends(request()->query())->links() }}
@endsection
