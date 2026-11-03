@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <h1>Profil Pengguna</h1>

    <div class="form-container">
        <table>
            <tr>
                <th style="width: 160px; background: #f3f4f6;">Nama Lengkap</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th style="width: 160px; background: #f3f4f6;">Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th style="width: 160px; background: #f3f4f6;">Role</th>
                <td>{{ ucfirst($user->role) }}</td>
            </tr>
        </table>

        <h2 style="margin-top: 30px;">Ganti Password</h2>

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <label for="current_password">Password Lama</label>
            <input type="password" name="current_password" id="current_password">
            @error('current_password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password Baru</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation">

            <br>
            <button type="submit" class="btn">Perbarui Password</button>
        </form>
    </div>
@endsection
