<?php

namespace Productreview\Reviews\Model;

final class ModuleDetails
{
    const MODULE_NAME = 'Productreview_Reviews';

    const CONNECTION_STATUS_SUCCESS = 'success';
    const CONNECTION_STATUS_FAIL = 'fail'; // Network Failure
    const CONNECTION_STATUS_INVALID = 'invalid_credentials';
    const CONNECTION_STATUS_NO_CREDENTIALS = 'no_credentials';
}
