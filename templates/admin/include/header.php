<div id="adminHeader">
    <h2>Widget News Admin</h2>
    <p>You are logged in as <b><?php echo htmlspecialchars($_SESSION['username']) ?></b>.
        <a href="admin.php?action=listArticles">Редактировать статьи</a> 
        <a href="admin.php?action=listCategories">Редактировать категории</a>
		<a href="admin.php?action=listSubcategories">Редактировать подкатегории</a>
		<a href="admin.php?action=listUsers">Редактировать пользователей</a>
        <a href="admin.php?action=logout"?>Выйти</a>
    </p>
</div>
