<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<h1>Все пользователи</h1>

<?php if (isset($results['errorMessage'])) { ?>
    <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

<?php if (isset($results['statusMessage'])) { ?>
    <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
<?php } ?>

<table>
    <tr>
        <th>ID</th>
        <th>Логин</th>
        <th>Активность</th>
    </tr>

    <?php foreach ($results['users'] as $user) { ?>

        <tr onclick="location='admin.php?action=editUser&amp;userId=<?php echo $user->id ?>'">
            <td><?php echo $user->id ?></td>
            <td><?php echo htmlspecialchars($user->login) ?></td>
            <td><?php echo $user->isActive ? 'Активен' : 'Не активен'; ?></td>
        </tr>

    <?php } ?>

</table>

<p><a href="admin.php?action=newUser">Добавить нового пользователя</a></p>

<?php include "templates/include/footer.php" ?>
