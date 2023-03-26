<?php
namespace App\Models;
use App\System\Model;

class Billing extends Model{
    


public ?int $id;
public ?int $orderid;
public ?string $billingnumber;
public ?string $billingdate;
public ?int $billingstatus;
public ?string $waybillnumber;
public ?string $waybilldate;
public ?int $paymentoption;
public ?float $withouttax;
public ?float $taxtotal;
public ?float $amounttotal;
public ?int $installments;
public ?string $orderdate;
public ?int $addid;
public ?string $addtime;
public ?int $updateid;
public ?string $updatetime;
public ?int $active;


	/**
	 * @return array
	 */
	public function rules(): array {
       return [];
	}

}