<?php

namespace App;


interface AccountableInterface
{
    public function getAccountId();

    public function getAccountName();

    public function getAccountType();
}