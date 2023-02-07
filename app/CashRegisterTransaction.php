<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashRegisterTransaction extends Model
{
    protected $table = "cash_register_transactions";
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
