<?php

namespace App\Models;

use App\System\Model;

class Order extends Model
{

    public ?int $id;
    public ?int $userid;
    public ?int $status;
    public ?int $paymentoption;
    public ?int $installments;
    public ?string $orderdate;
    public ?int $shippingid;
    public ?string $shippingtrackingnumber;
    public ?string $calculateddeliverydate;
    public ?string $deliverydate;
    public ?int $addid;
    public ?string $addtime;
    public ?int $updateid;
    public ?string $updatetime;
    public ?int $active;
	public ?float $taxtotal;
    public ?float $amounttotal;
	public ?string $orderer;
	public ?string $adress;
	public ?string $phone;
	public ?string $zipcode;
	public ?string $city; 
	public ?string $district;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
			'orderer' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 80]],
			'adress' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
			'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 80]],
			'zipcode' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 15]],
			'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 80]],
			'district' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 80]]
		];
    }

    public function insert()
    {
        try {
			$this->db->beginTransaction();
			$status = $this->db->prepare("INSERT INTO orders (userid, status, paymentoption, installments, orderdate, shippingid, shippingtrackingnumber, calculateddeliverydate, addid, addtime, updateid, updatetime, active, taxtotal, amounttotal, orderer, adress, phone, zipcode, city, district)
					VALUES (:userid, :status, :paymentoption, :installments, current_timestamp(), :shippingid, :shippingtrackingnumber, :calculateddeliverydate, :addid, CURRENT_TIMESTAMP(), :addid, CURRENT_TIMESTAMP(), 1, :taxtotal, :amounttotal, :orderer, :adress, :phone, :zipcode, :city, :district)");
			$status->bindParam(':userid', $this->userid, ($this->userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':status', $this->status, ($this->status == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':paymentoption', $this->paymentoption, ($this->paymentoption == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':installments', $this->installments, ($this->installments == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':shippingid', $this->shippingid, ($this->shippingid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':shippingtrackingnumber', $this->shippingtrackingnumber, ($this->shippingtrackingnumber == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':shippingtrackingnumber', $this->shippingtrackingnumber, ($this->shippingtrackingnumber == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':calculateddeliverydate', $this->calculateddeliverydate, ($this->calculateddeliverydate == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':taxtotal', $this->taxtotal, ($this->taxtotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':amounttotal', $this->amounttotal, ($this->amounttotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':orderer', $this->orderer, ($this->orderer == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':adress', $this->adress, ($this->adress == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':phone', $this->phone, ($this->phone == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':zipcode', $this->zipcode, ($this->amounttotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':city', $this->city, ($this->city == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':district', $this->district, ($this->district == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
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
                "UPDATE orders 
                 SET status = coalesce(:status,status), 
				 	 userid = coalesce(:userid,userid),
                     paymentoption = coalesce(:paymentoption,paymentoption), 
                     installments = coalesce(:installments,installments),
					 orderdate = coalesce(:orderdate,orderdate), 
                     shippingid = coalesce(:shippingid,shippingid),
                     shippingtrackingnumber = coalesce(:shippingtrackingnumber,shippingtrackingnumber),
                     calculateddeliverydate = coalesce(:calculateddeliverydate,calculateddeliverydate), 
                     deliverydate = coalesce(:deliverydate,deliverydate), 
                     updateid = :updateid,
                     updatetime = current_timestamp(), 
                     taxtotal = coalesce(:taxtotal,taxtotal), 
                     amounttotal = coalesce(:amounttotal,amounttotal),
					 orderer = coalesce(:orderer,orderer),
					 adress = coalesce(:adress,adress),
					 phone = coalesce(:phone,phone),
					 zipcode = coalesce(:zipcode,zipcode),
					 city = coalesce(:city,city),
					 district = coalesce(:district,district)
				WHERE id = :id
						");
			$status->bindParam(':status', $this->status, ($this->status == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':userid', $this->userid, ($this->userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':paymentoption', $this->paymentoption, ($this->paymentoption == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':installments', $this->installments, ($this->installments == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':orderdate', $this->orderdate, ($this->orderdate == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':shippingid', $this->shippingid, ($this->shippingid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':shippingtrackingnumber', $this->shippingtrackingnumber, ($this->shippingtrackingnumber == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':calculateddeliverydate', $this->calculateddeliverydate, ($this->calculateddeliverydate == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':deliverydate', $this->deliverydate, ($this->deliverydate == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':updateid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':taxtotal', $this->taxtotal, (strval($this->taxtotal) == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':amounttotal', $this->amounttotal, (strval($this->amounttotal) == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':orderer', $this->orderer, ($this->orderer == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':adress', $this->adress, ($this->adress == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':phone', $this->phone, ($this->phone == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':zipcode', $this->zipcode, ($this->amounttotal == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':city', $this->city, ($this->city == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':district', $this->district, ($this->district == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
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
			$status = $this->db->prepare("UPDATE orders set active = 0 where id = :id");
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
		("	SELECT * FROM orders o
			WHERE (:id is null or o.id = :id) and
				(:userid is null or o.userid = :userid) and
				(:status is null or o.status = :status) and
                active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':userid', $this->userid, ($this->userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':status', $this->status, ($this->status == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

    public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from orders");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  * 
			 from orders 
			 limit :llimit OFFSET :loffset
			");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getUserOrderList($userId)
	{
		$status = $this->db->prepare(
			"SELECT o.id,
					o.orderdate,
					(SELECT SUM(od.amount) from orderdetails od where od.orderid = o.id) as 'totalitem',
					o.amounttotal,
					o.status,
					(SELECT c.value1 from combovalues c where c.name='ORDERSTATUS' and c.code = o.status) as 'statusval',
					(SELECT c.value2 from combovalues c where c.name='ORDERSTATUS' and c.code = o.status) as 'statusval2'
			FROM orders o
			WHERE o.userid = :userid and
				  o.active = 1");
		$status->bindParam(":userid",$userId,\PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

}
