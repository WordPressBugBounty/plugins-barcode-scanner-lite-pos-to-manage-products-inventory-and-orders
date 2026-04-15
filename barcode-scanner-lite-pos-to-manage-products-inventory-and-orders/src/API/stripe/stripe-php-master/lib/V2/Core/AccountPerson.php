<?php


namespace Stripe\V2\Core;

class AccountPerson extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'v2.core.account_person';

    const LEGAL_GENDER_FEMALE = 'female';
    const LEGAL_GENDER_MALE = 'male';

    const POLITICAL_EXPOSURE_EXISTING = 'existing';
    const POLITICAL_EXPOSURE_NONE = 'none';
}
