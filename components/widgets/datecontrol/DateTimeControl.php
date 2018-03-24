<?php

namespace app\components\widgets\datecontrol;

use \kartik\datecontrol\DateControl as BaseDateControl;

class DateTimeControl extends BaseDateControl
{
    public $type = self::FORMAT_DATETIME;
    public $ajaxConversion = false;
    public $displayTimezone = null;
    public $saveTimezone = null;
    public $language = 'ru';
}
