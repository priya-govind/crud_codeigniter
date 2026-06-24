<h2>Edit User</h2>
<form method="post" action="/users/update/<?= $user['id'] ?>">
    <input type="text" name="name" value="<?= $user['name'] ?>" required>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>
    <button type="submit">Update</button>
</form>