<?php
function getWeekDays(int $timestamp): array
{
    //Resolve back to the monday of the week
    $start = date('w', $timestamp) == 1 ? $timestamp : strtotime('last monday', $timestamp);
    $startDate = date('Y-m-d', $start);

    //Loop till 7 to build the days of the week
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $dayTimestamp = strtotime($startDate . "+$i days");
        //Build array keys that are relevant to use when someone calls this function
        $dates[] = [
            'timestamp' => $dayTimestamp,
            'fullDate' => date('Y-m-d', $dayTimestamp),
            'day' => date('D', $dayTimestamp),
            'dayNumber' => date('d', $dayTimestamp),
            'current' => date('d', $dayTimestamp) === date('d'),
            'month'=> date('M', $dayTimestamp)
        ];
    }
    return $dates;
}
?>
