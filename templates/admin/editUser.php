<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<h1><?php echo $results['pageTitle'] ?></h1>

<form action="admin.php?action=<?php echo $results['formAction'] ?>" method="post">
    <input type="hidden" name="userId" value="<?php echo $results['user']->id ?>">

	<?php if (isset($results['errorMessage'])) { ?>
		<div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
	<?php } ?>

    <ul>
		<li>
			<label for="login">Логин</label>
			<input type="text" name="login" id="login" placeholder="Логин пользователя" required autofocus maxlength="255" value="<?php echo htmlspecialchars($results['user']->login) ?>" />
		</li>

		<li>
			<label for="password">Пароль</label>
			<input type="password" name="password" id="password" placeholder="Пароль пользователя" maxlength="255" value="" />
			<span>Оставьте пустым, чтобы не изменять пароль</span>
		</li>

		<li>
			<label for="isActive">Активность</label>
			<input type="hidden" name="isActive" value="0"/>
			<input type="checkbox" name="isActive" id="isActive" value="1" <?php echo ($results['user']->isActive) ? 'checked' : ''; ?>/>
		</li>
    </ul>

    <div class="buttons">
		<input type="submit" name="saveChanges" value="Сохранить изменения" />
		<input type="submit" formnovalidate name="cancel" value="Отмена" />
    </div>

</form>

<?php if ($results['user']->id) { ?>
	<p><a href="admin.php?action=deleteUser&amp;userId=<?php echo $results['user']->id ?>" onclick="return confirm('Удалить этого пользователя?')">
			Удалить этого пользователя
		</a>
	</p>
<?php } ?>

<?php include "templates/include/footer.php" ?>
