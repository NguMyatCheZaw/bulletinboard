<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Error Messages
    |--------------------------------------------------------------------------
    |
     */

    'userrole' => [
        'admin' => '管理者',
        'member' => 'メンバー',
    ],

    'usertype' => [
        'admin' => 0,
        'member' => 1,
    ],

    'errors' => [
        'from_to' => '(から)日付は(に)日付より大きくなければなりません。',
        'password_fail' => 'Yor current password does not match.',
        'password_same' => 'Current and new password should not be same. Please choose a different password.',
        'password_confirm' => 'The new password confirmation does not match.',
    ],

    'profile-path' => 'storage/image/',

    'pagination' => 5,

    'exception' => [
        'code' => -9,
        'message' => 'Your action fails',
    ],

    'success' => [
        'code' => -1,
        'message' => 'Success',
    ],
];
