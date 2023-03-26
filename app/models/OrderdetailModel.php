<?php

namespace App\Models;

use App\System\Model;

class Orderdetail extends Model
{

    public ?int $id;
    public ?int $orderid;
    public ?int $productid;
    public ?float $amount;
    public ?float $unitprice;
    public ?float $taxtotal;
    public ?float $amounttotal;
    public ?int $addid;
    public ?string $addtime;
    public ?int $updateid;
    public ?string $updatetime;
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
			$status = $this->db->prepare("INSERT INTO orderdetails (orderid, productid, amount, unitprice, taxtotal, amounttotal, addid, addtime, updateid, updatetime, active)
					VALUES (:orderid, :productid, :amount, :unitprice, :taxtotal, :amounttotal, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1)");
			$status->bindParam(':orderid', $this->orderid, ($this->orderid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':amount', $this->amount, ($this->amount == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':unitprice', $this->unitprice, ($this->unitprice == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':taxtotal', $this->taxtotal, ($this->taxtotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':amounttotal', $this->amounttotal, ($this->amounttotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
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
                "UPDATE orderdetails 
                 SET status = :status, 
                     orderid = :orderid, 
                     productid = :productid,
                     amount = :amount, 
                     unitprice = :unitprice, 
                     taxtotal = :taxtotal, 
                     amounttotal = :amounttotal,
                     updateid = :updateid,
                     current_timestamp()
				where id = :id");
			$status->bindParam(':orderid', $this->orderid, ($this->orderid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':amount', $this->amount, ($this->amount == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':unitprice', $this->unitprice, ($this->unitprice == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':taxtotal', $this->taxtotal, ($this->taxtotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':amounttotal', $this->amounttotal, ($this->amounttotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':updateid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
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
			$status = $this->db->prepare("UPDATE orderdetails set active = 0 where id = :id");
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
		("	SELECT * FROM orderdetails o
			WHERE (:id is null or o.id = :id) and
				(:orderid is null or o.orderid = :orderid) and
                active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':orderid', $this->orderid, ($this->orderid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

    public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from orderdetails");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  * 
			 from orderdetails 
			 limit :llimit OFFSET :loffset
			");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

}
