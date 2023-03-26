<?php

namespace App\Models;

use App\System\Application;
use App\System\Model;
use DateTime;

class Combovalues extends Model
{
    public int $id; 
    public string $name; 
    public int $code; 
    public int $visibleorder; 
    public string $value1; 
    public string $value2; 
    public string $value3; 
    public int $addid; 
    public DateTime $addtime; 
    public int $updateid; 
    public DateTime $updatetime;
    public int $active;

    public function get($name)
    {
        $status = $this->db->prepare("select * from combovalues where name = :name");
        $status->bindParam(':name', $name, ($name == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }


	/**
	 * @return array
	 */
	public function rules(): array {
        return [];
	}
}