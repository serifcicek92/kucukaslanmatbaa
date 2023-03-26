<?php

namespace App\System;



use App\System\Application;



abstract class Model extends DataBase

{

    private bool $error = false;
    private array $errors = [];

    private string $errorMessage = "";

    public const RULE_REQUIRED = 'required';

    public const RULE_EMAIL = 'email';

    public const RULE_MIN = 'min';

    public const RULE_MAX = 'max';

    public const RULE_MATCH = 'match';

    public const RULE_UNIQUE = 'unique';

    public const RULE_CHECHED = 'on';

  
    abstract public function rules():array;

    public function labels():array

    {

        return [];

    }

    public function getLabel($attribute)

    {

        return $this->labels()[$attribute] ?? $attribute;

    }

    public function loadData($data)

    {

        foreach ($data as $key => $value) {

            if (property_exists($this,$key)) {

                $this->{$key} = $value;

            }

        }

    }

    public function validate()

    {

        foreach ($this->rules() as $attribute => $rules) {

            if (!isset($this->{$attribute})) {

                continue;

            }

            $value = $this->{$attribute};

            foreach ($rules as $rule) {

                $ruleName = $rule;

                if (!is_string($ruleName)) {

                    $ruleName = $rule[0];

                }

                if ($ruleName===self::RULE_REQUIRED && !$value) {

                    $this->addErrorForRule($attribute,self::RULE_REQUIRED);

                }

                if ($ruleName===self::RULE_EMAIL && !filter_var($value,FILTER_VALIDATE_EMAIL)) 

                {

                    $this->addErrorForRule($attribute,self::RULE_EMAIL);

                }

                if ($ruleName===self::RULE_MIN && strlen($value) < $rule['min']) 

                {

                    $this->addErrorForRule($attribute,self::RULE_MIN,$rule);

                }

                if ($ruleName===self::RULE_MAX && strlen($value) > $rule['max']) 

                {

                    $this->addErrorForRule($attribute,self::RULE_MAX,$rule);

                }

                if ($ruleName===self::RULE_MATCH && $value !== $this->{$rule['match']}) 

                {

                    $rule['match'] = $this->getLabel($rule['match']);

                    $this->addErrorForRule($attribute,self::RULE_MATCH,$rule);

                }

                if ($ruleName===self::RULE_CHECHED && !$value) {

                    $this->addErrorForRule($attribute,self::RULE_CHECHED);

                }

                if ($ruleName == self::RULE_UNIQUE) {

                    $className = $rule['class'];

                    $uniqueAttr = $rule['attribute'] ?? $attribute;

                    $tableName = $className::tableName();

                    $statement = $this->db->prepare("SELECT * FROM $tableName where $uniqueAttr = :attr");

                    $statement->bindValue(":attr",$value);

                    $statement->execute();

                     $record = $statement->fetchObject();

                     if ($record) {

                         $this->addErrorForRule($attribute,self::RULE_UNIQUE,['field'=>$this->getLabel($attribute)]);

                     }

                }



            }

        }

        return empty($this->errors);

    }

    private function addErrorForRule(string $attribute,string $rule,$params = [])

    {

        $message = $this->errorMessage()[$rule] ?? '';

        foreach ($params as $key => $value) {

            $message = str_replace("{{$key}}",$value,$message);

        }

        $this->errors[$attribute][] = $message;

    }



    public function addError(string $attribute,string $message)

    {

        $this->errors[$attribute][] = $message;

    }



    public function errorMessage()

    {

        return [

        self::RULE_REQUIRED => 'Bu alan gereklidir.',

        self::RULE_EMAIL => 'bu alan doğrulanmış bir email adresi olmalıdır.',

        self::RULE_MIN => 'Minimum {min} karakter olmalıdır.',

        self::RULE_MAX => 'Maksimum {max} karakter olmalıdır.',

        self::RULE_MATCH => 'bu alan {match} ile aynı olmalıdır.',

        self::RULE_UNIQUE => '{field} isimli kayıt zaten mevcut',

        self::RULE_CHECHED => 'Bu Alanı Checklemelisiniz'

        ];

    }



    public function hasError($attribute)

    {

     

        return $this->errors[$attribute] ?? false;

    }



    public function getFirstError($attribute)

    {

        return $this->errors[$attribute][0] ?? false;

    }



    public function setError($state,$message = null)

    {

        $this->error =$state;

        $this->errorMessage = $message;

    }

    public function getError()

    {

        return $this->error;

    }
    public function getErrors()

    {

        return $this->errors;

    }

    public function getErrorMessage()

    {

        return $this->errorMessage;

    }

}