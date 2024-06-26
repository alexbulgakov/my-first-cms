<?php

require("config.php");
session_start();
$action = isset($_GET['action']) ? $_GET['action'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

if ($action != "login" && $action != "logout" && !$username) {
	login();
	exit;
}

switch ($action) {
	case 'login':
		login();
		break;
	case 'logout':
		logout();
		break;
	case 'newArticle':
		newArticle();
		break;
	case 'editArticle':
		editArticle();
		break;
	case 'deleteArticle':
		deleteArticle();
		break;
	case 'listCategories':
		listCategories();
		break;
	case 'newCategory':
		newCategory();
		break;
	case 'editCategory':
		editCategory();
		break;
	case 'deleteCategory':
		deleteCategory();
		break;
	case 'listUsers':
		listUsers();
		break;
	case 'newUser':
		newUser();
		break;
	case 'editUser':
		editUser();
		break;
	case 'deleteUser':
		deleteUser();
	case 'listSubcategories':
		listSubcategories();
		break;
	case 'newSubcategory':
		newSubcategory();
		break;
	case 'editSubcategory':
		editSubcategory();
		break;
	case 'deleteSubcategory':
		deleteSubcategory();
	default:
		listArticles();
}

/**
 * Авторизация пользователя (админа) -- установка значения в сессию
 */
function login() {
	$results = array();
	$results['pageTitle'] = "Admin Login | Widget News";

	if (isset($_POST['login'])) {
		// Администраторский вход
		if ($_POST['username'] == ADMIN_USERNAME && $_POST['password'] == ADMIN_PASSWORD) {
			$_SESSION['username'] = ADMIN_USERNAME;
			header("Location: admin.php");
		} else {
			// Проверка логина/пароля через сущность User
			$user = User::getByUsername($_POST['username']);
			if ($user && $_POST['password'] == $user->password) {
				// Проверка активности пользователя
				if ($user->isActive) {
					$_SESSION['username'] = $user->login;
					header("Location: admin.php");
				} else {
					$results['errorMessage'] = "Ваш аккаунт не активен.";
					require(TEMPLATE_PATH . "/admin/loginForm.php");
				}
			} else {
				$results['errorMessage'] = "Неправильный логин или пароль. Попробуйте ещё раз.";
				require(TEMPLATE_PATH . "/admin/loginForm.php");
			}
		}
	} else {
		// Показать форму входа, если не была отправлена
		require(TEMPLATE_PATH . "/admin/loginForm.php");
	}
}

function logout() {
	unset($_SESSION['username']);
	header("Location: admin.php");
}

function newArticle() {

	$results = array();
	$results['pageTitle'] = "New Article";
	$results['formAction'] = "newArticle";

	if (isset($_POST['saveChanges'])) {
//            echo "<pre>";
//            print_r($results);
//            print_r($_POST);
//            echo "<pre>";
//            В $_POST данные о статье сохраняются корректно
		// Пользователь получает форму редактирования статьи: сохраняем новую статью
		$article = new Article();
		$article->storeFormValues($_POST);
//            echo "<pre>";
//            print_r($article);
//            echo "<pre>";
//            А здесь данные массива $article уже неполные(есть только Число от даты, категория и полный текст статьи)          
		$article->insert();
		header("Location: admin.php?status=changesSaved");
	} elseif (isset($_POST['cancel'])) {

		// Пользователь сбросил результаты редактирования: возвращаемся к списку статей
		header("Location: admin.php");
	} else {

		// Пользователь еще не получил форму редактирования: выводим форму
		$results['article'] = new Article;
		$data = Category::getList();
		$results['categories'] = $data['results'];
		require( TEMPLATE_PATH . "/admin/editArticle.php" );
	}
}

/**
 * Редактирование статьи
 * 
 * @return null
 */
function editArticle() {

	$results = array();
	$results['pageTitle'] = "Edit Article";
	$results['formAction'] = "editArticle";

	if (isset($_POST['saveChanges'])) {

		// Пользователь получил форму редактирования статьи: сохраняем изменения
		if (!$article = Article::getById((int) $_POST['articleId'])) {
			header("Location: admin.php?error=articleNotFound");
			return;
		}

		$article->storeFormValues($_POST);
		$article->update();
		header("Location: admin.php?status=changesSaved");
	} elseif (isset($_POST['cancel'])) {

		// Пользователь отказался от результатов редактирования: возвращаемся к списку статей
		header("Location: admin.php");
	} else {

		// Пользвоатель еще не получил форму редактирования: выводим форму
		$results['article'] = Article::getById((int) $_GET['articleId']);
		$data = Category::getList();
		$results['categories'] = $data['results'];
		require(TEMPLATE_PATH . "/admin/editArticle.php");
	}
}

function deleteArticle() {

	if (!$article = Article::getById((int) $_GET['articleId'])) {
		header("Location: admin.php?error=articleNotFound");
		return;
	}

	$article->delete();
	header("Location: admin.php?status=articleDeleted");
}

function listArticles() {
	$results = array();

	$data = Article::getList();
	$results['articles'] = $data['results'];
	$results['totalRows'] = $data['totalRows'];

	$data = Category::getList();
	$results['categories'] = array();
	foreach ($data['results'] as $category) {
		$results['categories'][$category->id] = $category;
	}

	$results['pageTitle'] = "Все статьи";

	if (isset($_GET['error'])) { // вывод сообщения об ошибке (если есть)
		if ($_GET['error'] == "articleNotFound")
			$results['errorMessage'] = "Error: Article not found.";
	}

	if (isset($_GET['status'])) { // вывод сообщения (если есть)
		if ($_GET['status'] == "changesSaved") {
			$results['statusMessage'] = "Your changes have been saved.";
		}
		if ($_GET['status'] == "articleDeleted") {
			$results['statusMessage'] = "Article deleted.";
		}
	}

	require(TEMPLATE_PATH . "/admin/listArticles.php" );
}

function listCategories() {
	$results = array();
	$data = Category::getList();
	$results['categories'] = $data['results'];
	$results['totalRows'] = $data['totalRows'];
	$results['pageTitle'] = "Article Categories";

	if (isset($_GET['error'])) {
		if ($_GET['error'] == "categoryNotFound")
			$results['errorMessage'] = "Error: Category not found.";
		if ($_GET['error'] == "categoryContainsArticles")
			$results['errorMessage'] = "Error: Category contains articles. Delete the articles, or assign them to another category, before deleting this category.";
	}

	if (isset($_GET['status'])) {
		if ($_GET['status'] == "changesSaved")
			$results['statusMessage'] = "Your changes have been saved.";
		if ($_GET['status'] == "categoryDeleted")
			$results['statusMessage'] = "Category deleted.";
	}

	require( TEMPLATE_PATH . "/admin/listCategories.php" );
}

function newCategory() {

	$results = array();
	$results['pageTitle'] = "New Article Category";
	$results['formAction'] = "newCategory";

	if (isset($_POST['saveChanges'])) {

		// User has posted the category edit form: save the new category
		$category = new Category;
		$category->storeFormValues($_POST);
		$category->insert();
		header("Location: admin.php?action=listCategories&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {

		// User has cancelled their edits: return to the category list
		header("Location: admin.php?action=listCategories");
	} else {

		// User has not posted the category edit form yet: display the form
		$results['category'] = new Category;
		require( TEMPLATE_PATH . "/admin/editCategory.php" );
	}
}

function editCategory() {

	$results = array();
	$results['pageTitle'] = "Edit Article Category";
	$results['formAction'] = "editCategory";

	if (isset($_POST['saveChanges'])) {

		// User has posted the category edit form: save the category changes

		if (!$category = Category::getById((int) $_POST['categoryId'])) {
			header("Location: admin.php?action=listCategories&error=categoryNotFound");
			return;
		}

		$category->storeFormValues($_POST);
		$category->update();
		header("Location: admin.php?action=listCategories&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {

		// User has cancelled their edits: return to the category list
		header("Location: admin.php?action=listCategories");
	} else {

		// User has not posted the category edit form yet: display the form
		$results['category'] = Category::getById((int) $_GET['categoryId']);
		require( TEMPLATE_PATH . "/admin/editCategory.php" );
	}
}

function deleteCategory() {

	if (!$category = Category::getById((int) $_GET['categoryId'])) {
		header("Location: admin.php?action=listCategories&error=categoryNotFound");
		return;
	}

	$articles = Article::getList(1000000, $category->id);

	if ($articles['totalRows'] > 0) {
		header("Location: admin.php?action=listCategories&error=categoryContainsArticles");
		return;
	}

	$category->delete();
	header("Location: admin.php?action=listCategories&status=categoryDeleted");
}

function listUsers() {
	$results = array();
	$data = User::getList();
	$results['users'] = $data;
	$results['pageTitle'] = "Управление пользователями";

	require(TEMPLATE_PATH . "/admin/listUsers.php");
}

function newUser() {
	$results = array();
	$results['pageTitle'] = "Новый пользователь";
	$results['formAction'] = "newUser";

	if (isset($_POST['saveChanges'])) {
		// Создание нового пользователя
		$user = new User;
		$user->storeFormValues($_POST);
		$user->insert();
		header("Location: admin.php?action=listUsers&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {
		header("Location: admin.php?action=listUsers");
	} else {
		$results['user'] = new User();
		require(TEMPLATE_PATH . "/admin/editUser.php");
	}
}

function editUser() {
	$results = array();
	$results['pageTitle'] = "Редактировать пользователя";
	$results['formAction'] = "editUser";

	if (isset($_POST['saveChanges'])) {
		if (!$user = User::getById((int) $_POST['userId'])) {
			header("Location: admin.php?action=listUsers&error=userNotFound");
			return;
		}

		// Сохраняем текущий пароль пользователя
		$currentPassword = $user->password;

		$user->storeFormValues($_POST);

		// Восстанавливаем старый пароль, если новый не был предоставлен
		if (empty($_POST['password'])) {
			$user->password = $currentPassword;
		} else {
			$user->password = $_POST['password'];
		}

		$user->update();
		header("Location: admin.php?action=listUsers&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {
		header("Location: admin.php?action=listUsers");
	} else {
		$results['user'] = User::getById((int) $_GET['userId']);
		require(TEMPLATE_PATH . "/admin/editUser.php");
	}
}

function deleteUser() {
	if (!$user = User::getById((int) $_GET['userId'])) {
		header("Location: admin.php?action=listUsers&error=userNotFound");
		return;
	}

	$user->delete();
	header("Location: admin.php?action=listUsers&status=userDeleted");
}

function listSubcategories() {
	$results = array();
	$data = Subcategory::getList();
	$results['subcategories'] = $data['results'];
	$results['totalRows'] = $data['totalRows'];
	$results['pageTitle'] = "Subcategories";

	require(TEMPLATE_PATH . "/admin/listSubcategories.php");
}

function newSubcategory() {
	$results = array();
	$results['pageTitle'] = "Новая подкатегория";
	$results['formAction'] = "newSubcategory";

	if (isset($_POST['saveChanges'])) {
		$subcategory = new Subcategory;
		$subcategory->storeFormValues($_POST);
		$subcategory->insert();
		header("Location: admin.php?action=listSubcategories&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {
		header("Location: admin.php?action=listSubcategories");
	} else {
		$results['subcategory'] = new Subcategory;

		$data = Category::getList();
		$results['categories'] = $data['results'];

		require(TEMPLATE_PATH . "/admin/editSubcategory.php");
	}
}

function editSubcategory() {
	$results = array();
	$results['pageTitle'] = "Редактировать подкатегорию";
	$results['formAction'] = "editSubcategory";

	if (isset($_POST['saveChanges'])) {
		if (!$subcategory = Subcategory::getById((int) $_POST['subcategoryId'])) {
			header("Location: admin.php?action=listSubcategories&error=subcategoryNotFound");
			return;
		}

		$subcategory->storeFormValues($_POST);
		$subcategory->update();
		header("Location: admin.php?action=listSubcategories&status=changesSaved");
	} elseif (isset($_POST['cancel'])) {
		header("Location: admin.php?action=listSubcategories");
	} else {
		$results['subcategory'] = Subcategory::getById((int) $_GET['subcategoryId']);
		// Загрузка всех категорий для списка в форме
		$data = Category::getList();
		$results['categories'] = $data['results'];  // Сохраняем список категорий
		require(TEMPLATE_PATH . "/admin/editSubcategory.php");
	}
}

function deleteSubcategory() {
	if (!$subcategory = Subcategory::getById((int) $_GET['subcategoryId'])) {
		header("Location: admin.php?action=listSubcategories&error=subcategoryNotFound");
		return;
	}

	$subcategory->delete();
	header("Location: admin.php?action=listSubcategories&status=subcategoryDeleted");
}
