<div class="w-64 bg-gray-800 h-screen fixed top-0 left-0 text-white">
    <div class="p-4 border-b border-gray-700">
        <h3 class="text-lg font-semibold">Admin Panel</h3>
    </div>
    <ul class="mt-4">
        <li class="px-4 py-3 hover:bg-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="block">Dashboard</a>
        </li>
        <li class="px-4 py-3 hover:bg-gray-700">
            <a href="{{ route('admin.users') }}" class="block">Users</a>
        </li>
        <li class="px-4 py-3 hover:bg-gray-700">
            <a href="{{ route('admin.settings') }}" class="block">Settings</a>
        </li>
        <li class="px-4 py-3 hover:bg-red-600">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left">Logout</button>
            </form>
        </li>
    </ul>
</div>
