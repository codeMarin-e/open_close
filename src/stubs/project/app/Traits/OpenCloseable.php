<?php
    namespace App\Traits;

    use App\Models\OpenClose;

    trait OpenCloseable {

        public static function bootOpenCloseable() {
            static::deleting( static::class.'@onDeleting_open_closes' );
        }

        public function open_closes() {
            return $this->morphMany( OpenClose::class, 'owner');
        }
        public function open_closes_week() {
            return $this->morphMany( OpenClose::class, 'owner')->where('type', 'week');
        }

        public function open_closes_special() {
            return $this->morphMany( OpenClose::class, 'owner')->where('type', 'special');
        }

        public function onDeleting_open_closes($model) {
            $model->loadMissing('open_closes');
            foreach($model->open_closes as $open_close) {
                $open_close->delete();
            }
        }

        public function getWeekDaysOpenClose($dayIndex = null, $bldQry = null) {
            $bldQry = $bldQry? clone $bldQry : $this->open_closes();
            $bldQry = $bldQry->where('type', 'week');
            if($dayIndex) {
                return $bldQry->where('type_value', $dayIndex)->first();
            }
            return $bldQry->get();
        }

        public function getSpecialDayOpenClose(\DateTime $day = null, $bldQry = null) {
            $bldQry = $bldQry? clone $bldQry : $this->open_closes();
            $bldQry = $bldQry->where('type', 'special');
            if($day) {
                $day = \DateTime::createFromFormat('d.m.Y H:i:s', $day->format('d.m.Y').' 00:00:00');
                return $bldQry->where('type_value', $day->getTimestamp())->first();
            }
            return $bldQry->get();
        }

        public function isOpen(\DateTime $checkTime = null) {
            $checkTime = $checkTime? clone $checkTime : new \DateTime();
            //SPECIAL DATE
            if($todaySpecialDay = $this->getSpecialDayOpenClose( $checkTime ) ) {
                $openTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkTime->format('d.m.Y').' 00:00:00');
                $closeTodayDT = clone $openTodayDT;
                $openTodayDT->add( new \DateInterval($todaySpecialDay->open_at) );
                if($todaySpecialDay->close_next_day)
                    $closeTodayDT->add( new \DateInterval('P1D') );
                $closeTodayDT->add( new \DateInterval($todaySpecialDay->close_at) );
                if($checkTime > $openTodayDT && $checkTime < $closeTodayDT)
                    return true;
                return false;
            }
            $yesterdayCheckDay = clone $checkTime;
            $yesterdayCheckDay->sub( new \DateInterval('P1D') );
            if($yesterdaySpecialDay = $this->getSpecialDayOpenClose(
                $yesterdayCheckDay,
                $this->open_closes_special()->where('close_next_day', true)
            )) {
                $closeTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkTime->format('d.m.Y').' 00:00:00');
                $closeTodayDT->add( new \DateInterval($yesterdaySpecialDay->close_at) );
                if($checkTime < $closeTodayDT)
                    return true;
            }

            //WEEK DAY
            $weekDayIndex = $checkTime->format('N');
            $todayOpenClose = $this->getWeekDaysOpenClose($weekDayIndex);

            if($todayOpenClose) {
                $openTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkTime->format('d.m.Y').' 00:00:00');
                $closeTodayDT = clone $openTodayDT;

                $openTodayDT->add( new \DateInterval($todayOpenClose->open_at) );
                if($todayOpenClose->close_next_day)
                    $closeTodayDT->add( new \DateInterval('P1D') );
                $closeTodayDT->add( new \DateInterval($todayOpenClose->close_at) );
                if($checkTime > $openTodayDT && $checkTime < $closeTodayDT)
                    return true;
            }

            $yesterdayWeekDayIndex = (!($weekDayIndex-1))? 7 : $weekDayIndex-1;
            $yesterdayOpenClose = $this->getWeekDaysOpenClose(
                $yesterdayWeekDayIndex,
                $this->open_closes()->where('close_next_day', true));
            if($yesterdayOpenClose) {
                $closeTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkTime->format('d.m.Y').' 00:00:00');
                $closeTodayDT->add( new \DateInterval($yesterdayOpenClose->close_at) );
                if($checkTime < $closeTodayDT)
                    return true;
            }
            return false;
        }

        public function getOpenClosePeriods($checkDate = null) {
            $return = [];
            $checkDate = $checkDate? clone $checkDate : new \DateTime();
            $weekDayIndex = $checkDate->format('N');

            //SPECIAL DATE - YESTERDAY
            $yesterdayCheckDay = clone $checkDate;
            $yesterdayCheckDay->sub( new \DateInterval('P1D') );
            if($yesterdaySpecialDay = $this->getSpecialDayOpenClose(
                $yesterdayCheckDay,
                $this->open_closes_special()->where('close_next_day', true)
            )) {
                $openYesterdayCheckDT = \DateTime::createFromFormat('d.m.Y H:i:s', $yesterdayCheckDay->format('d.m.Y').' 00:00:00');
                $openYesterdayCheckDT->add( new \DateInterval($yesterdaySpecialDay->open_at) );
                $closeYesterdayCheckDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkDate->format('d.m.Y').' 00:00:00');
                $closeYesterdayCheckDT->add( new \DateInterval($yesterdaySpecialDay->close_at) );

                $return[] = [
                    'open_at' => $openYesterdayCheckDT,
                    'close_at' => $closeYesterdayCheckDT
                ];
            } else {
                //WEEK DAY - YESTERDAY
                $yesterdayWeekDayIndex = (!($weekDayIndex-1))? 7 : $weekDayIndex-1;
                $yesterdayOpenClose = $this->getWeekDaysOpenClose(
                    $yesterdayWeekDayIndex,
                    $this->open_closes()->where('close_next_day', true));
                if($yesterdayOpenClose) {
                    $openTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $yesterdayCheckDay->format('d.m.Y').' 00:00:00');
                    $openTodayDT->add( new \DateInterval($yesterdayOpenClose->open_at) );

                    $closeTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkDate->format('d.m.Y').' 00:00:00');
                    $closeTodayDT->add( new \DateInterval($yesterdayOpenClose->close_at) );

                    $return[] = [
                        'open_at' => $openTodayDT,
                        'close_at' => $closeTodayDT
                    ];
                }
            }

            //SPECIAL DATE
            if($todaySpecialDay = $this->getSpecialDayOpenClose( $checkDate )) {
                $openTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkDate->format('d.m.Y').' 00:00:00');
                $closeTodayDT = clone $openTodayDT;
                $openTodayDT->add( new \DateInterval($todaySpecialDay->open_at) );
                if($todaySpecialDay->close_next_day)
                    $closeTodayDT->add( new \DateInterval('P1D') );
                $closeTodayDT->add( new \DateInterval($todaySpecialDay->close_at) );
                $return[] = [
                    'open_at' => $openTodayDT,
                    'close_at' => $closeTodayDT
                ];
                return $return;
            }

            //WEEK DAY
            $todayOpenClose = $this->getWeekDaysOpenClose($weekDayIndex);

            if($todayOpenClose) {
                $openTodayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkDate->format('d.m.Y').' 00:00:00');
                $closeTodayDT = clone $openTodayDT;

                $openTodayDT->add( new \DateInterval($todayOpenClose->open_at) );
                if($todayOpenClose->close_next_day)
                    $closeTodayDT->add( new \DateInterval('P1D') );
                $closeTodayDT->add( new \DateInterval($todayOpenClose->close_at) );
                $return[] = [
                    'open_at' => $openTodayDT,
                    'close_at' => $closeTodayDT
                ];
                return $return;
            }

            return $return;
        }

        public function getLastClose_at(\DateTime $checkTimeDT = null) {
            $checkTimeDT = ($checkTimeDT)? clone $checkTimeDT : new \DateTime();
            $return = false;
            foreach($this->getOpenClosePeriods($checkTimeDT) as $period) {
                if(!$return) {
                    $return = $period['close_at'];
                }
                $return = $return < $period['close_at']? $period['close_at'] : $return;
            }
            return $return;
        }

        public function nextFreePeriod(\DateTime $checkTimeDT = null, \DateTime $checkToDT = null) {
            $nowDT = new \DateTime();
            $checkTimeDT = ($checkTimeDT && $checkTimeDT >= $nowDT)? clone $checkTimeDT : new \DateTime();
            $checkDayDT = \DateTime::createFromFormat('d.m.Y H:i:s', $checkTimeDT->format('d.m.Y').' 00:00:00');
            $hasSpecDay = false;
            $checkDayTS = $checkDayDT;
            //check for special day
            if($specialOpenClose = $this->open_closes_special()->where('type_value', '=', $checkDayTS)->first()) {
                $hasSpecDay = true;
                $todayCheckCloseDT = clone $checkDayDT;
                $todayCheckCloseDT->add( new \DateInterval($specialOpenClose->close_at) );
                if($specialOpenClose->close_next_day) {
                    $todayCheckCloseDT->add( new \DateInterval('P1D') );
                }
                if($checkTimeDT > $todayCheckCloseDT) //if already closed
                    $specialOpenClose = null;
            }


            //check for yesterday special day with closing on next day
            if(!$specialOpenClose) {
                $yesterdayCheckDayDT = clone $checkDayDT;
                $yesterdayCheckDayDT->sub(new \DateInterval('P1D'));
                $yesterdayCheckDayTS = $yesterdayCheckDayDT->getTimestamp();
                if ($specialOpenClose = $this->open_closes_special()->where([
                    ['type_value', '=', $yesterdayCheckDayTS],
                    ['close_next_day', '=', 1],
                ])->first()) {
                    $yesterdayCheckCloseDT = clone $checkDayDT;
                    $yesterdayCheckCloseDT->add(new \DateInterval($specialOpenClose->close_at));
                    if ($checkTimeDT > $yesterdayCheckCloseDT)
                        $specialOpenClose = null;
                }
            }
            //find future special day
            if(!$specialOpenClose) {
                $specialOpenClose = $this->open_closes_special();
                if($checkToDT) {
                    $specialOpenClose = $specialOpenClose->where('type_value', '<=', $checkToDT->getTimestamp());
                }
                $specialOpenClose = $specialOpenClose->where('type_value', '>', $checkDayTS)->first();
            }

            //check if there is set week day
            if(!$this->open_closes_week()->first()) {
                if($specialOpenClose) {
                    return $specialOpenClose->getPeriod(); //return the found special day
                }
                return false;
            }
            //if there was found correct yesterday special day return it
            if($specialOpenClose) {
                $specialOpenCloseDateDT = $specialOpenClose->getSpecialDay();
                if($specialOpenCloseDateDT <= $checkDayDT) {
                    return $specialOpenClose->getPeriod();
                }
            }

            $bufDay = clone $checkDayDT;
            $yesterdayWeekDay = false;
            if($hasSpecDay) {
                //if there is today special day but it wasn't correct - go for next
                $bufDay->add( new \DateInterval('P1D') );
                $yesterdayWeekDay = true;
            }

            //loop for days to find future special or week day
            while(true) {
                if($checkToDT && $bufDay > $checkToDT) {
                    return false;
                }
                //if was found next future special day
                if($specialOpenClose && $specialOpenCloseDateDT == $bufDay) {
                    return $specialOpenClose->getPeriod();
                }

                //if yesterday week day is found
                if(!$yesterdayWeekDay) {
                    $yesterdayWeekDay = clone $checkDayDT;
                    $yesterdayWeekDay->sub(new \DateInterval('P1D'));
                    $weekDayIndex = $yesterdayWeekDay->format('N');
                    if($weekDayOpenClose = $this->getWeekDaysOpenClose($weekDayIndex, $this->open_closes()->where('close_next_day', '=', 1) )) {
                        $period = $weekDayOpenClose->getPeriod($yesterdayWeekDay);
                        if($period['close_at'] > $checkTimeDT) {
                            return $period;
                        }
                    }
                    $yesterdayWeekDay = true;
                }

                //if week day is found
                $weekDayIndex = $bufDay->format('N');
                if($weekDayOpenClose = $this->getWeekDaysOpenClose($weekDayIndex)) {
                    $period = $weekDayOpenClose->getPeriod($bufDay);
                    if($period['close_at'] > $checkTimeDT) {
                        return $period;
                    }
                }
                $bufDay->add( new \DateInterval('P1D') );
            }
        }
    }
