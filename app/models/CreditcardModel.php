<?php
namespace app\Models;
use App\System\Model;

class Creditcard extends Model{
    
    public ?string $cardfullname;
    public ?string $cardnumber;
    public ?string $cardexpiry;
    public ?string $cardcvc;

	/**
	 * @return array
	 */
	public function rules(): array {
        return [
            'cardfullname'=> [self::RULE_REQUIRED,[]],
            'cardnumber' => [self::RULE_REQUIRED,[]],
            'cardexpiry' => [self::RULE_REQUIRED,[]],
            'cardcvc' => [self::RULE_REQUIRED,[]]
        ];
	}
}