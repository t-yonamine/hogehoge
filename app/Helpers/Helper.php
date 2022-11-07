<?php

namespace App\Helpers;

use App\Enums\Degree;
use App\Enums\SchoolStaffRole;

class Helper
{
    /*
        @author ThuongNV-VNEXT
        Description: Get list role
    */
    public static function getListRole($number, $array)
    {
        $roleSelected = in_array($number, $array);
        if ($roleSelected) {
            return [$number];
        }
        // Create array empty
        $filter = [];
        // Filter array role and get value less number role
        foreach ($array as $value) {
            if ($value < $number) {
                // Push value in filter
                $filter[] = $value;
            }
        }
        // Create array empty whose task will contain selected elements
        $arrSelected = [];
        // Count array filter role
        $length = count($filter) - 1;
        // Iterates the elements so that the number passed is equal to the filtered elements
        while ($number > 0 && $length >= 0) {
            if ($number - $filter[$length] >= 0) {
                $number = $number - $filter[$length];
                $arrSelected[] = $filter[$length];
            }
            $length--;
        }
        return $arrSelected;
    }

    /*
        @author ThuongNV-VNEXT
        Description: Show name after checking which role belongs to
    */
    public static function getRoleName($number)
    {
        $arrayRole = SchoolStaffRole::getValues();
        $roleSelected = in_array($number, $arrayRole);

        if ($roleSelected) {
            return SchoolStaffRole::getDescription($number);
        } else {
            $arrSelected = static::getListRole($number, $arrayRole);
            $result = [];
            foreach ($arrSelected as $item) {
                $result[] = SchoolStaffRole::getDescription($item);
            }
            return join('、', $result);
        }
    }

    /*
        @author ThuongNV-VNEXT
        Description: Check the list of roles that have any roles in the system admin
    */
    public static function checkRole($number)
    {
        $arrayRole = SchoolStaffRole::getValues();
        $roleSelected = in_array($number, $arrayRole);

        if ($roleSelected) {
            if ($number == SchoolStaffRole::SYS_ADMINISTRATOR) {
                return $roleSelected;
            } else {
                return abort(403);
            }
        } else {
            $arrSelected = static::getListRole($number, $arrayRole);
            $findSysAdmin = in_array(SchoolStaffRole::SYS_ADMINISTRATOR, $arrSelected);
            if ($findSysAdmin) {
                return $findSysAdmin;
            } else {
                return abort(403);
            }
        }
    }
    /* 
        @author HoangNH-VNEXT
        Description: Get list Degree
    */
    public static function Degree($number)
    {
        $arrDegree = Degree::getValues();
        $roleSelected = in_array($number, $arrDegree);
        if ($roleSelected) {
            return [$number];
        } else {
            return static::getListRole($number, $arrDegree);
        }
    }

}
