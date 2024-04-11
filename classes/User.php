<?php

/**
 * Классдля работы с пользователями
 */
class User {

	/**
	 * @var int $id ID пользователя
	 */
	public $id = null;

	/**
	 * @var string $login Логин пользователя
	 */
	public $login = null;

	/**
	 * @var string $password Пароль пользователя
	 */
	public $password = null;

	/**
	 * @var bool $isActive Флаг активности пользователя
	 */
	public $isActive = null;

	/**
	 * Конструктор для объекта User
	 *
	 * @param array $data Массив свойств пользователя
	 */
	public function __construct($data = array()) {
		if (isset($data['id']))
			$this->id = (int) $data['id'];
		if (isset($data['login']))
			$this->login = $data['login'];
		if (isset($data['password']))
			$this->password = $data['password'];
		if (isset($data['isActive']))
			$this->isActive = $data['isActive'];
	}

	/**
	 * Устанавливаем свойства объекта User с помощью значений из формы
	 *
	 * @param array $params Массив параметров
	 */
	public function storeFormValues($params) {
// Store all the parameters
		$this->__construct($params);
	}

	/**
	 * Возвращаем объект User, соответствующи заданному id
	 *
	 * @param int $id ID пользователя
	 * @return User|false Возвращает объект User или false, если запись не найдена или возникли проблемы
	 */
	public static function getById($id) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT * FROM users WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();

		$row = $st->fetch();
		$conn = null;
		if ($row)
			return new User($row);
	}

	/**
	 * Возвращает все объекты User из БД
	 *
	 * @return Array Массив объектов User
	 */
	public static function getList() {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT * FROM users";

		$st = $conn->query($sql);
		$list = array();

		while ($row = $st->fetch()) {
			$user = new User($row);
			$list[] = $user;
		}

		$conn = null;
		return $list;
	}

	/**
	 * Вставляем текущий объект User в базу данных
	 */
	public function insert() {
		if (!is_null($this->id))
			trigger_error("User::insert(): Attempt to insert a User object that already has its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "INSERT INTO users (login, password, isActive) VALUES (:login, :password, :isActive)";
		$st = $conn->prepare($sql);
		$st->bindValue(":login", $this->login, PDO::PARAM_STR);
		$st->bindValue(":password", $this->password, PDO::PARAM_STR);
		$st->bindValue(":isActive", isset($this->isActive) ? $this->isActive : 0, PDO::PARAM_INT);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	/**
	 * Обновляем текущий объект User в базе данных
	 */
	public function update() {
		if (is_null($this->id))
			trigger_error("User::update(): Attempt to update a User object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "UPDATE users SET login = :login, password = :password, isActive = :isActive WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":login", $this->login, PDO::PARAM_STR);
		$st->bindValue(":password", $this->password, PDO::PARAM_STR);
		$st->bindValue(":isActive", $this->isActive, PDO::PARAM_INT);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	/**
	 * Удаляем текущий объект User из БД
	 */
	public function delete() {
		if (is_null($this->id))
			trigger_error("User::delete(): Attempt to delete a User object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$st = $conn->prepare("DELETE FROM users WHERE id = :id LIMIT 1");
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	/**
	 * Возвращаем User, соответствуйющий username
	 */
	public static function getByUsername($username) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT * FROM users WHERE login = :username";
		$st = $conn->prepare($sql);
		$st->bindValue(":username", $username, PDO::PARAM_STR);
		$st->execute();

		$row = $st->fetch();
		$conn = null;
		if ($row)
			return new User($row);
		return null;
	}
}
