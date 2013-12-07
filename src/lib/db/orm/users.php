<?php
final class Users {
    const TABLE='users';
    const COL_USER_ID='user_id';
    const COL_EMAIL='email';
    const COL_PASSWORD='password';
    const COL_DOB='dob';
    const COL_DATE_REGISTERED='date_registered';
    const COL_IS_ACTIVE='is_active';
    const COL_LAST_LOGIN='last_login';
    const COL_LOGIN_ATTEMPT_NUMBER='login_attempt_number';
    const COL_IS_LOCKED_OUT='is_locked_out';
    const COL_USER_NAME='user_name';
    const COL_ROLE_ID='role_id';

    private function __construct(){}
} 