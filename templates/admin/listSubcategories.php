<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<h1>Subcategories</h1>

<?php if (isset($results['errorMessage'])) { ?>
	<div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

<?php if (isset($results['statusMessage'])) { ?>
	<div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
<?php } ?>

<table>
    <tr>
        <th>Название</th>
        <th>Категория</th>
    </tr>

	<?php foreach ($results['subcategories'] as $subcategory) { ?>
		<tr onclick="location = 'admin.php?action=editSubcategory&amp;subcategoryId=<?php echo $subcategory->id ?>'">
			<td><?php echo htmlspecialchars($subcategory->name ?? '') ?></td>
			<td><?php echo htmlspecialchars($subcategory->categoryName ?? '') ?></td>
		</tr>
	<?php } ?>
</table>

<p><a href="admin.php?action=newSubcategory">Добавить подкатегорию</a></p>

<?php include "templates/include/footer.php" ?>
