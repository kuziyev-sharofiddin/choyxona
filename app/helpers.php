<?php
if (!function_exists('getUzbekWeekday')) {
    function getUzbekWeekday($date) {
        $weekdays = [
            'Monday' => 'Dushanba',
            'Tuesday' => 'Seshanba', 
            'Wednesday' => 'Chorshanba',
            'Thursday' => 'Payshanba',
            'Friday' => 'Juma',
            'Saturday' => 'Shanba',
            'Sunday' => 'Yakshanba'
        ];
        
        $englishDay = $date->format('l');
        return $weekdays[$englishDay] ?? $englishDay;
    }
}