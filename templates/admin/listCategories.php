<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<h1>Категории</h1>

<?php if (isset($results['errorMessage'])) { ?>
	<div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>


<?php if (isset($results['statusMessage'])) { ?>
	<div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
<?php } ?>

<table>
	<tr>
		<th>Категория</th>
	</tr>

	<?php foreach ($results['categories'] as $category) { ?>

		<tr onclick="location = 'admin.php?action=editCategory&amp;categoryId=<?php echo $category->id ?>'">
			<td>
				<?php echo $category->name ?>
			</td>
		</tr>

	<?php } ?>

</table>

<p><a href="admin.php?action=newCategory">Добавить категорию</a></p>

<?php include "templates/include/footer.php" ?>
