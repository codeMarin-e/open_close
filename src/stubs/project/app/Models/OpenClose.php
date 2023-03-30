<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MacroableModel;

class OpenClose extends Model
{
    protected $guarded = [];

    use MacroableModel;

    // @HOOK_TRAITS

    public function owner() {
        return $this->morphTo('owner');
    }

    public function getPeriod(\DateTime $checkDayDT = null) {
        $dateDT = $checkDayDT? clone $checkDayDT : new \DateTime();
        if($this->type == 'special') {
            $dateDT->setTimestamp( $this->type_value );
        }
        $openAtDT = clone $dateDT;
        $openAtDT->add( new \DateInterval($this->open_at) );

        $closeAtDt = clone $dateDT;
        if($this->close_next_day) {
            $closeAtDt->add( new \DateInterval('P1D') );
        }
        $closeAtDt->add( new \DateInterval($this->close_at) );
        return [
            'open_at' => $openAtDT,
            'close_at' => $closeAtDt,
        ];
    }

    public function getSpecialDay() {
        if($this->type != 'special') {
            return false;
        }
        $dt = new \DateTime();
        $dt->setTimestamp( $this->type_value );
        return $dt;
    }

}
