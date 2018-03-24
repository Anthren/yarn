<?php

namespace app\components\widgets\datecontrol;

use \kartik\datecontrol\DateControl as BaseDateControl;

class TimeControl extends BaseDateControl
{

    public $type = self::FORMAT_TIME;
    public $ajaxConversion = true;
    public $displayTimezone = 'UTC';
    public $saveTimezone = 'UTC';

}
