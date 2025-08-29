<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;
class DateConv
{
    static function dateConvert($created_at, $date, $timezone)
    {
        $userTzNumber = Carbon::now($timezone)->offsetHours;
        $dateCreated = Carbon::parse($created_at);
        $dateCreatedToUserTz = $dateCreated->copy()->tz($timezone);

        $date = Carbon::parse($date);
        if ($userTzNumber < 0) {
            // UTC BIG
            if ($dateCreated->format("Y-m-d") === $dateCreatedToUserTz->format("Y-m-d")) {
                // USER 03/05 00:00
                // SERVER 03/05 03:00 -> Store 03/05 -> if Conv  (03/04 21:00) :: Ineed 03/05 00:00 from Time User !Slove >> Add(timezone User)
                $date->addHour($userTzNumber);
                // SELECT UserTimeZone(date_exp+$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
            } else {
                // USER 03/04 22:00 -> Expect 03/04 00:00 To UTC Expect 03/04 3:00
                // SERVER 03/05 01:00 -> Store 03/05 -> if Conv (03/04 21:00) :: Ineed 03/04 00:00 from Time User !Slove >> Add(timezone User)
                $date->subDay(1)->addHour($userTzNumber);
                // SELECT UserTimeZone((date_exp-1)+$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
            }
        } else if ($userTzNumber > 0) {
            // USER TIME BIG
            if ($dateCreated->format("Y-m-d") === $dateCreatedToUserTz->format("Y-m-d")) {
                // USER 03/05 03:00
                // SERVER 03/05 00:00 -> Store 03/05 -> if Conv  (03/05 3:00) :: Ineed 03/05 00:00 from Time User !Slove >> Sub(timezone User)
                $date->subHour($userTzNumber);

                // SELECT UserTimeZone(date_exp-$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
            } else {
                // USER 03/05 03:00 -> Expect 03/05 00:00 To UTC Expect 03/04 21:00
                // SERVER 03/04 21:00 -> Store 03/04 -> if Conv  (03/04 03:00) :: Ineed 03/05 00:00 from Time User !Slove >> Add(1 DAY)->Sub(timeZone)

                $date->addDay(1)->subHour($userTzNumber);
                // SELECT UserTimeZone((date_exp+1)-$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
            }
        }


        return $date->tz($timezone)->format("Y-m-d");
    }

    static function fieldQueryConv($created_at, $field, $timezone) {
        $userTzNumber = Carbon::now($timezone)->offsetHours;
        $dateCreated = Carbon::parse($created_at);
        $dateCreatedToUserTz = $dateCreated->copy()->tz($timezone);
        $padNumberTz = Str::padLeft(abs($userTzNumber),2,"0");

        if($userTzNumber < 0) {
            $padNumberTz = "-".$padNumberTz;
        } else {
            $padNumberTz = "+".$padNumberTz;
        }



        $query = "$field"; // date_exp

        // Lessons::where("date_exp","2024-01-01")->get();

        // $date = Carbon::parse($date);

        if ($userTzNumber < 0) {
            $userTzNumber = abs($userTzNumber);
            // UTC BIG
            if ($dateCreated->format("Y-m-d") === $dateCreatedToUserTz->format("Y-m-d")) {
                // SELECT UserTimeZone(date_exp+$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)

                $query = "CONVERT_TZ(DATE_ADD($field,INTERVAL $userTzNumber HOUR),'+00:00','$padNumberTz:00')";
            } else {

                // SELECT UserTimeZone((date_exp-1)+$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
                $query = "CONVERT_TZ(DATE_ADD(DATE_SUB($field,INTERVAL 1 DAY),INTERVAL $userTzNumber HOUR),'+00:00','$padNumberTz:00')";
                // $query = "UserTimeZone(($field-1)+$userTzNumber)";
            }
        } else if ($userTzNumber > 0) {
            if ($dateCreated->format("Y-m-d") === $dateCreatedToUserTz->format("Y-m-d")) {

                // SELECT UserTimeZone(date_exp-$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
                // $query = "UserTimeZone($field-$userTzNumber)";
                $query = "CONVERT_TZ(DATE_SUB($field,INTERVAL $userTzNumber HOUR),'+00:00','$padNumberTz:00')";
            } else {
                $query = "UserTimeZone(($field+1)-$userTzNumber)";
                // SELECT UserTimeZone((date_exp+1)-$userTzNumber) as date FROM lessons WHERE date === UserTimeZone(dateNow 00:00:00)
                $query = "CONVERT_TZ(DATE_SUB(DATE_ADD($field,INTERVAL 1 DAY),INTERVAL $userTzNumber HOUR),'+00:00','$padNumberTz:00')";
            }
        }
        return $query;
    }

    static function CONVERT_TZ($field, $timezone,$withTime = false) {
        $userTzNumber = Carbon::now($timezone)->offsetHours;
        $padNumberTz = Str::padLeft(abs($userTzNumber),2,"0");

        if($userTzNumber < 0) {
            $padNumberTz = "-".$padNumberTz;
        } else {
            $padNumberTz = "+".$padNumberTz;
        }

        $query = null;

        if($withTime) {
            $query = "CONVERT_TZ($field,'+00:00','$padNumberTz:00')";
        } else {
            $query = "DATE(CONVERT_TZ($field,'+00:00','$padNumberTz:00'))";
        }


        return $query;

    }

}




