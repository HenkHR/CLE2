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

function getReservationCount(int $timestamp, array $reservations): int
{
    $result = 0;
    foreach($reservations as $reservation){
        if(strtotime($reservation['date_time']) == $timestamp){
            $result++;
        }
    }
    return $result;
}

function getAvailableSpots(int $timestamp, array $reservations): int
{
    $availableSpots = 0;
    $reservationAmount = getReservationCount($timestamp, $reservations);
    if($reservationAmount == 0){
        $availableSpots = 3;
    }
    elseif($reservationAmount == 1){
        $availableSpots = 2;
    }
    elseif ($reservationAmount == 2){
        $availableSpots = 1;
    }
    elseif ($reservationAmount == 3){
        $availableSpots = 0;
    }
    return $availableSpots;
}
?>
