<?php

namespace App\Models;

use App\System\Model;
use DateTime;

class ProductPrices extends Model
{

    public int $id;
    public int $productid;
    public float $prices;
    public int $taxtype;
    public string $name;
    public int $addid;
    public int $updateid;
    public int $active;


    public function insert()
    {
        try {
            $this->db->beginTransaction();
            $status = $this->db->prepare("INSERT INTO productprices (productid, prices, taxtype, addid, addtime, updateid, updatetime, active)
					VALUES (:productid, :prices, :taxtype, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1)");

            $status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':prices', $this->prices, (strval($this->prices) == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':taxtype', $this->taxtype, ($this->taxtype == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->execute();
            $this->db->commit();
            $lastInsertId = $this->db->lastInsertId();
            $this->id = $lastInsertId;
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }


    public function update()
    {
        try {
            $this->db->beginTransaction();
            $status = $this->db->prepare("UPDATE FROM productprices SET
                productid = :productid, 
                prices = :prices, 
                taxtype = :taxtype,
                updateid = :updateid, 
                updatetime =CURRENT_TIMESTAMP())
            where id = :id");

            $status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':productid', $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':prices', $this->prices, (strval($this->prices) == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':name', $this->taxtype, ($this->taxtype == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':updateid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));

            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }

    public function get()
    {
        $status = $this->db->prepare("SELECT * FROM productprices where (:id is null or id = :id)");
        $status->bindValue(":id", $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEndPrices()
    {
        $status = $this->db->prepare("SELECT * FROM productprices where productid = :productid order by updatetime desc limit 1");
        $status->bindValue(":productid", $this->productid, ($this->productid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
