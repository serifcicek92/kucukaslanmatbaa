<?php
namespace App\Models;
use App\System\Model;

class Billingdetail extends Model{
    
    public ?int $id;
    public ?int $billingid;
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
	public function rules(): array {
        return [];
	}
}