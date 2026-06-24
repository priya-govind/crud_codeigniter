<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="max-w-md mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

    <?php if(isset($validation)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/attemptLogin" class="space-y-4">
        <input type="email" name="email" placeholder="Email"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300" required>

        <input type="password" name="password" placeholder="Password"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300" required>

        <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
            Login
        </button>
    </form>
</div>
<?= $this->endSection() ?>
