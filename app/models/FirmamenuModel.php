<?php
namespace App\Models;
use App\System\Model;

class FirmaMenu extends Model{
    
    public ?int $id;
    public ?string $firmaadi;
    public ?string $dosyayolu;
    public ?string $linki;

    

    public function insert()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("INSERT INTO firmamenuleri (firmaadi,dosyayolu,linki) values (:firmaadi,:dosyayolu,:linki)");
            $status->bindParam(':firmaadi',$this->firmaadi,($this->firmaadi == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':dosyayolu',$this->dosyayolu,($this->dosyayolu == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':linki',$this->linki,($this->linki == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->execute();
            $this->db->commit();
            $this->id = $this->db->lastInsertId();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
        
    }

    public function update()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("UPDATE firmamenuleri set firmaadi = :firmaadi ,dosyayolu = :dosyayolu, linki = :linki WHERE id = :id");
            $status->bindParam(':firmaadi',$this->firmaadi,($this->firmaadi == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':dosyayolu',$this->dosyayolu,($this->dosyayolu == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':linki',$this->linki,($this->linki == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':id',$this->id,\PDO::PARAM_INT);
            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }

    public function del()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("DELETE from firmamenuleri where id = :id");
            $status->bindParam(':id',$this->id, \PDO::PARAM_INT);
            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }

    public function get():array
    {
        $status = $this->db->prepare("SELECT * from firmamenuleri WHERE (:id is null or id = :id) and (:firmaadi is null or firmaadi = :firmaadi) and (:linki is null or linki = :linki)");
        $status->bindParam(':id',$this->id,($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
        $status->bindParam(':firmaadi',$this->firmaadi,($this->firmaadi == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->bindParam(':linki',$this->linki,($this->linki == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getList():array
    {
        $status = $this->db->prepare("SELECT * from firmamenuleri");
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_OBJ);
    }

	/**
	 * @return array
	 */
	public function rules(): array {
        return [];
	}


}