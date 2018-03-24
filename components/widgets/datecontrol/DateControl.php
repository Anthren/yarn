<?php

namespace app\components\widgets\datecontrol;

use \kartik\datecontrol\DateControl as BaseDateControl;

class DateControl extends BaseDateControl
{
    public $type = self::FORMAT_DATE;
    public $ajaxConversion = false;
    public $displayTimezone = null;
    public $saveTimezone = null;
    public $language = 'ru';
}
