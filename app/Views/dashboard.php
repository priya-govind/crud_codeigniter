<div class="max-w-2xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6 text-center">
    <h1 class="text-3xl font-bold mb-4">Welcome, <?= session()->get('user_name'); ?></h1>
    <a href="/logout"
       class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
       Logout
    </a>
</div>
