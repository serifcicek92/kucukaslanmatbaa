<?php

namespace App\Models;

use App\System\Model;

class Basketdetail extends Model
{

    public ?int $id; 
    public ?int $basketid;
    public ?int $productid;
    public ?int $miktar;
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
			$status = $this->db->prepare("INSERT INTO basketdetail ( basketid, productid, miktar, addid, addtime, updateid, updatetime, active )
					VALUES (:basketid, :productid, :miktar, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1)");
			$status->bindParam(':basketid', $this->basketid, ($this->basketid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':miktar', $this->miktar, ($this->miktar == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->execute();
			$lastInsertId = $this->db->lastInsertId();
			$this->id = $lastInsertId;
			$this->db->commit();
			return true;
		} catch (\Throwable $th) {
			$this->db->rollBack();
			return false;
		}
    }

    public function update()
    {
        try {
			$this->db->beginTransaction();
			$status = $this->db->prepare(
                "UPDATE basketdetail 
                 SET basketid = coalesce(:basketid,basketid), 
                 productid = coalesce(:productid,productid),
                 miktar = coalesce(:miktar,miktar),
                 updateid = coalesce(:updateid,updateid),
				 updatetime = current_timestamp()
				 WHERE id = :id");
			$status->bindParam(':basketid', $this->basketid, ($this->basketid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':miktar', $this->miktar, ($this->miktar == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':updateid', $this->updateid, ($this->updateid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':id',$this->id,\PDO::PARAM_INT);
			$status->execute();
			$this->db->commit();
			return true;
		} catch (\Throwable $th) {
			$this->db->rollBack();
			return false;
		}
    }

    public function delete()
    {
        $this->db->beginTransaction();
		try {
			$status = $this->db->prepare("UPDATE basketdetail set active = 0 where id = :id");
			$status->bindParam(":id", $this->id, \PDO::PARAM_INT);
			$status->execute();
			$this->db->commit();
			return true;
		} catch (\Throwable $th) {
			$this->db->rollBack();
			return false;
		}
    }

    public function get()
	{
		$status = $this->db->prepare
		("	SELECT * FROM basketdetail o
			WHERE (:id is null or o.id = :id) and
                  (:basketid is null or basketid = :basketid) and
				  (:productid is null or productid = :productid) and
                active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':basketid', $this->basketid, ($this->basketid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':productid',$this->productid,($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

    public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from basketdetail");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  * 
			 from basketdetail 
			 limit :llimit OFFSET :loffset
			");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

}
