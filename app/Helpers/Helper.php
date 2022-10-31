<?php

namespace App\Helpers;

use App\Enums\Role;

class Helper
{
    /* 
        @author ThuongNV-VNEXT
        Description: List Role
    */
    private const ARR_ROLE = [
        Role::SYS_ADMINISTRATOR,
        Role::CLERK_1,
        Role::CLERK_2,
        Role::APTITUDE_TESTER,
        Role::INSTRUCTOR,
        Role::EXAMINER,
        Role::SUB_ADMINISTRATOR,
        Role::ADMINISTRATOR
    ];

    /* 
        @author ThuongNV-VNEXT
        Description: Get list role
    */
    public static function getListRole($number, $array)
    {
        // Create array empty
        $filter = [];
        // Filter array role and get value less number role
        foreach ($array as $value) {
            if ($value < $number) {
                // Push value in filter 
                array_push($filter, $value);
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
                array_push($arrSelected, $filter[$length]);
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
        $arrayRole = self::ARR_ROLE;
        $roleSelected = in_array($number, $arrayRole);

        if ($roleSelected) {
            return $roleSelected;
        } else {
            $arrSelected = static::getListRole($number, $arrayRole);
            $result = [];
            foreach ($arrSelected as $item) {
                array_push($result, Role::getRole($item));
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
        $arrayRole = self::ARR_ROLE;
        $roleSelected = in_array($number, $arrayRole);

        if ($roleSelected) {
            if ($number == Role::SYS_ADMINISTRATOR) {
                return $roleSelected;
            } else {
                return abort(403);
            }
        } else {
            $arrSelected = static::getListRole($number, $arrayRole);
            $findSysAdmin = in_array(Role::SYS_ADMINISTRATOR, $arrSelected);
            if ($findSysAdmin) {
                return $findSysAdmin;
            } else {
                return abort(403);
            }
        }
    }
}
