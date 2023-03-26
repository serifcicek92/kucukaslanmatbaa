<?php

namespace App\Models;

use App\System\Model;

class Shipping extends Model
{

    public ?int $id; 
    public ?string $name;
    public ?string $explain; 
    public ?string $logo;
    public ?int $addid;
    public ?int $updateid;
    public ?int $active;
    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    public function insert()
    {
        try {
			$this->db->beginTransaction();
			$status = $this->db->prepare("INSERT INTO shippings ( name, explain, logo, addid, addtime, updateid, updatetime, active )
					VALUES (:name, :explain, :logo, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1)");
			$status->bindParam(':name', $this->name, ($this->name == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':explain', $this->explain, ($this->explain == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':logo', $this->logo, ($this->logo == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->execute();
			$lastInsertId = $this->db->lastInsertId();
			$this->id = $lastInsertId;
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
    }

    public function update()
    {
        try {
			$this->db->beginTransaction();
			$status = $this->db->prepare(
                "UPDATE shippings 
                 SET status = :status, 
                    name = :name, 
                    explain = :explain, 
                    logo = :logo,
                     updateid = :updateid,
                     current_timestamp())
					VALUES (:name, :explain, :logo, :updateid, CURRENT_TIMESTAMP())");
			$status->bindParam(':name', $this->name, ($this->name == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':explain', $this->explain, ($this->explain == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':logo', $this->logo, ($this->logo == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':updateid', $this->updateid, ($this->updateid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->execute();
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
    }

    public function delete()
    {
        $this->db->beginTransaction();
		try {
			$status = $this->db->prepare("UPDATE shippings set active = 0 where id = :id");
			$status->bindParam(":id", $this->id, \PDO::PARAM_INT);
			$status->execute();
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
    }

    public function get()
	{
		$status = $this->db->prepare
		("	SELECT * FROM shippings o
			WHERE (:id is null or o.id = :id) and
                active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

    public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from shippings");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  * 
			 from shippings 
			 limit :llimit OFFSET :loffset
			");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

}
