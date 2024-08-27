<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Client extends Model {
    protected $table = 'client';
    protected $columns = [
        'id',
        'email',
        'name',
        'company_name',
        'trading_name',
        'token',
        'active',
        'document',
        'rg_ins_state',
        'site',
        'phone_number',
        'zip_code',
        'address',
        'complement',
        'address_number',
        'zone',
        'city',
        'state',
        'birthday',
        'age',
        'obs',
        'created_at',
        'updated_at'
    ];

    //protected $guarded = ['id'];

    /*TODO: criar locale das chaves novas - ok */
    public $missingDataKeys = [
        ['name' => 'document', 'type' => 'tel', "required" => true, "mask" =>
            ["cpf" => "000.000.000-00",
            "cnpj" => "00.000.000/0000-00"]
        ],
        ['name' => 'rg_ins_state', 'type' => 'tel', "required" => false, "mask" => false],
        ['name' => 'birthday', 'type' => 'tel', "required" => false, "mask" => "00/00/0000"],
        ['name' => 'phone_number', 'type' => 'tel', "required" => true, "mask" => "(00) 00000-0000"],
        ['name' => 'zip_code', 'type' => 'tel', "required" => false, "mask" => "00000-000"],
        ['name' => 'email', 'type' => 'email', "required" => true, "mask" => false],
        ['name' => 'company_name', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'trading_name', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'name', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'address', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'address_number', 'type' => 'tel', "required" => true, "mask" => "00000000"],
        ['name' => 'zone', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'complement', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'city', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'state', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'obs', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'site', 'type' => 'url', "required" => false, "mask" => false],
    ];

    /**
     * @throws Exception
     */
    public function validate(): bool
    {

        if(empty($this->token)){
            $this->token = Uuid::uuid4();
        }
        return true;
    }

}
?>
