<?php

class Subcategory {

	// Свойства
	public $id = null;
	public $name = null;
	public $categoryId = null;
	public $categoryName = null;

	/**
	 * Конструктор для класса Subcategory.
	 *
	 * @param array $data Инициализирующие данные
	 */
	public function __construct($data = array()) {
		if (isset($data['id']))
			$this->id = (int) $data['id'];
		if (isset($data['name']))
			$this->name = $data['name'];
		if (isset($data['category_id']))
			$this->categoryId = (int) $data['category_id'];
		if (isset($data['categoryName']))
			$this->categoryName = $data['categoryName'];
	}

	/**
	 * Устанавливаем свойства подкатегории из массива
	 *
	 * @param array $data Массив с данными подкатегории
	 */
	public function storeFormValues($data) {
		$this->__construct($data);
	}

	/**
	 * Возвращает все подкатегории
	 *
	 * @param int $limit Максимальное количество подкатегорий для возврата
	 * @param int $categoryId ID категории для фильтрации
	 * @return array Результаты запроса
	 */
	public static function getList($limit = 1000000, $categoryId = null) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT SQL_CALC_FOUND_ROWS subcategories.*, categories.name AS categoryName FROM subcategories ";
		$sql .= "LEFT JOIN categories ON subcategories.category_id = categories.id";
		$conditions = array();
		if ($categoryId) {
			$conditions[] = "subcategories.category_id = :categoryId";
		}
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(" AND ", $conditions);
		}
		$sql .= " ORDER BY subcategories.id DESC LIMIT :limit";

		$st = $conn->prepare($sql);
		$st->bindValue(":limit", $limit, PDO::PARAM_INT);
		if ($categoryId) {
			$st->bindValue(":categoryId", $categoryId, PDO::PARAM_INT);
		}
		$st->execute();
		$list = array();

		while ($row = $st->fetch()) {
			$subcategory = new Subcategory($row);
			$subcategory->categoryName = $row['categoryName']; // Добавляем имя категории к каждой подкатегории
			$list[] = $subcategory;
		}

		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		return array("results" => $list, "totalRows" => $totalRows[0]);
	}

	/**
	 * Возвращает подкатегорию по ID
	 *
	 * @param int $id ID подкатегории
	 * @return Subcategory Найденная подкатегория
	 */
	public static function getById($id) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT * FROM subcategories WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row)
			return new Subcategory($row);
		return null;
	}

	/**
	 * Вставляет подкатегорию в БД
	 */
	public function insert() {
		if (!is_null($this->id))
			trigger_error("Subcategory::insert(): Attempt to insert a Subcategory object that already has its ID set (to $this->id).", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "INSERT INTO subcategories (name, category_id) VALUES (:name, :categoryId)";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $this->name, PDO::PARAM_STR);
		$st->bindValue(":categoryId", $this->categoryId, PDO::PARAM_INT);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	/**
	 * Обновляет подкатегорию в БД
	 */
	public function update() {
		if (is_null($this->id))
			trigger_error("Subcategory::update(): Attempt to update a Subcategory object that does not have its ID set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "UPDATE subcategories SET name=:name, category_id=:categoryId WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $this->name, PDO::PARAM_STR);
		$st->bindValue(":categoryId", $this->categoryId, PDO::PARAM_INT);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	/**
	 * Удаляет подкатегорию из БД
	 */
	public function delete() {
		if (is_null($this->id))
			trigger_error("Subcategory::delete(): Attempt to delete a Subcategory object that does not have its ID set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$st = theconn->prepare("DELETE FROM subcategories WHERE id = :id LIMIT 1");
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}
}
