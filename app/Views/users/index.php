<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div class="w-full min-h-screen bg-gray-50 p-6">
    <h2 class="text-2xl font-bold mb-6">User List</h2>

    <table id="usersTable" class="min-w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/users/getUsers',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: null, render: function(data, type, row) {
                return `
                    <a href="/users/show/${row.id}">View</a> |
                    <a href="/users/edit/${row.id}">Edit</a> |
                    <a href="/users/delete/${row.id}" onclick="return confirm('Delete user?')">Delete</a>
                `;
            }}
        ]
    });
});
</script>
<?= $this->endSection() ?>