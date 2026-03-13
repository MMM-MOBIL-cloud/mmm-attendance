<x-app-layout>

<div class="p-6">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
📅 Kelola Libur Nasional & Cuti Bersama
</h1>

    {{-- FORM TAMBAH --}}
    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="POST" action="{{ route('admin.holidays.store') }}">
            @csrf

            <div class="bg-white p-6 rounded-xl shadow mb-6 max-w-xl">

<form method="POST" action="{{ route('admin.holidays.store') }}"
      class="flex flex-col gap-3 max-w-md">
    @csrf

    <div>
        <label class="block text-sm font-semibold mb-1">
            Nama Libur
        </label>
        <input type="text"
               name="title"
               required
               class="w-full border rounded-lg px-3 py-2"
               placeholder="Contoh: Cuti Bersama">
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">
            Tanggal
        </label>
        <input type="date"
               name="date"
               required
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white
                   px-4 py-2 rounded-lg shadow font-semibold">
        Simpan
    </button>

</form>

</div>

    {{-- LIST LIBUR --}}
    <div class="bg-white p-4 rounded shadow">

        <table class="w-full">

            <thead>
                <tr class="border-b">
                    <th class="p-2 text-left">Tanggal</th>
                    <th class="p-2 text-left">Nama</th>
                    <th class="p-2 text-left">Action</th>
                </tr>
            </thead>

            <tbody>

            @foreach($holidays as $h)
                <tr class="border-b">
                    <td class="p-2">
                        {{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}
                    </td>

                    <td class="p-2">
                        {{ $h->title }}
                    </td>

                    <td class="p-2">

                        <form method="POST"
                              action="{{ route('admin.holidays.delete',$h->id) }}">
                            @csrf
                            @method('DELETE')

                            <button class="bg-red-500 text-white px-3 py-1 rounded">
                                Hapus
                            </button>
                        </form>

                    </td>
                </tr>
            @endforeach

            </tbody>

        </table>

    </div>

</div>

</x-app-layout>
