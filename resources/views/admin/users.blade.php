<x-app-layout>
<div class="p-6">

<h1 class="text-2xl font-bold mb-6">Manajemen User</h1>

<form method="POST" action="{{ route('admin.users.store') }}" class="bg-white p-6 rounded shadow mb-8">
@csrf

<div class="grid grid-cols-2 gap-4">
    <input type="text" name="name" placeholder="Nama" class="border p-2 rounded" required>
    <input type="email" name="email" placeholder="Email" class="border p-2 rounded" required>
    <input type="password" name="password" placeholder="Password" class="border p-2 rounded" required>

    <select name="role" class="border p-2 rounded">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>

    <input type="time" name="shift_start" class="border p-2 rounded">
    <input type="time" name="shift_end" class="border p-2 rounded">
</div>

<button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
Tambah User
</button>

</form>

</div>
</x-app-layout>
