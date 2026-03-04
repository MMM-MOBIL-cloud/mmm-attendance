<!DOCTYPE html>
<html>
<head>
    <title>Buat Akun Absensi MMM MOBIL</title>
</head>
<body>

<h2>Buat Akun Absensi MMM MOBIL</h2>

<form method="POST">
    @csrf

    <div>
        <label>Nama</label>
        <input type="text" name="name" required>
    </div>

    <br>

    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <br>

    <div>
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <br>

    <button type="submit">Buat Akun</button>

</form>

</body>
</html>
