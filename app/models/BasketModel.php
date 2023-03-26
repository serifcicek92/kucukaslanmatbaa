<?php

namespace App\Models;

use App\System\Model;

class Basket extends Model
{

    public ?int $id; 
    public ?string $userid;
    public ?int $addid;
    public ?int $updateid;
    public ?int $active;
	public ?string $guuid;
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
			$status = $this->db->prepare("INSERT INTO basket ( userid, addid, addtime, updateid, updatetime, active, guuid )
					VALUES (:userid, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1, :guuid)");
			$status->bindParam(':userid', $this->userid, ($this->userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':guuid', $this->guuid, ($this->guuid == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
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
                "UPDATE basket 
                 SET status = :status, 
                    userid = :userid, 
                    updateid = :updateid,
                     current_timestamp())
					VALUES (:userid, :updateid, CURRENT_TIMESTAMP())");
			$status->bindParam(':userid', $this->userid, ($this->userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':updateid', $this->updateid, ($this->updateid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
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
			$status = $this->db->prepare("UPDATE basket set active = 0 where id = :id");
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
		("	SELECT * FROM basket o
			WHERE (:id is null or o.id = :id) and
                active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

    public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from basket");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  * 
			 from basket 
			 limit :llimit OFFSET :loffset
			");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getUserActiveBasket(int $userid)
	{
		$status = $this->db->prepare("SELECT * FROM basket WHERE userid = :userid and active = 1");
		$status->bindParam(':userid', $userid, \PDO::PARAM_INT);
		$status->execute();
		// $status->setFetchMode(\PDO::FETCH_CLASS,'Basket');
		return $status->fetchAll(\PDO::FETCH_OBJ);
		// $this = $status->fetch();

	}

}
