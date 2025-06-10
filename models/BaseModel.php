<?php

/**
 * Model base para todos os outros models
 */

require_once __DIR__ . '/../config/config.php';

abstract class BaseModel
{
  protected $db;
  protected $table;

  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  /**
   * Buscar todos os registros
   */
  public function findAll()
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
  }

  /**
   * Buscar registro por ID
   */
  public function findById($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  /**
   * Buscar registro por campo específico
   */
  public function findBy($field, $value)
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
    $stmt->execute([$value]);
    return $stmt->fetch();
  }

  /**
   * Buscar múltiplos registros por campo específico
   */
  public function findAllBy($field, $value)
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ? ORDER BY id DESC");
    $stmt->execute([$value]);
    return $stmt->fetchAll();
  }

  /**
   * Criar novo registro
   */
  public function create($data)
  {
    $fields = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})");

    foreach ($data as $field => $value) {
      $stmt->bindValue(":{$field}", $value);
    }

    return $stmt->execute();
  }

  /**
   * Atualizar registro
   */
  public function update($id, $data)
  {
    $fields = [];
    foreach (array_keys($data) as $field) {
      $fields[] = "{$field} = :{$field}";
    }
    $fieldsString = implode(', ', $fields);

    $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fieldsString} WHERE id = :id");

    foreach ($data as $field => $value) {
      $stmt->bindValue(":{$field}", $value);
    }
    $stmt->bindValue(':id', $id);

    return $stmt->execute();
  }

  /**
   * Deletar registro
   */
  public function delete($id)
  {
    $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
    return $stmt->execute([$id]);
  }

  /**
   * Obter último ID inserido
   */
  public function getLastInsertId()
  {
    return $this->db->lastInsertId();
  }
}
