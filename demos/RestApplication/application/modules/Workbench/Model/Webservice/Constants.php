<?php
class Workbench_Model_Webservice_Constants
{
    const HTTP_HEADER_APPLICATION_TYPE  = "application/vnd.nl.mainflow";

    // Types to be set for authorization requests
    const AUTHTYPE_PASSWORD = "PASSWORD_REQUEST";
    const AUTHTYPE_ACCOUNT  = "ACCOUNT_REQUEST";
    const AUTHTYPE_LOGIN    = "LOGIN_REQUEST";

    // Account status types
    const ACCOUNT_STATUS_PENDING  = "Pending";
    const ACCOUNT_STATUS_ACTIVE   = "Active";
    const ACCOUNT_STATUS_INACTIVE = "Inactive";
    const ACCOUNT_STATUS_DELETED  = "Deleted";

    // HTTP status codes
    const HTTP_STATUS_OK                   = 200;
    const HTTP_STATUS_CREATED              = 201;
    const HTTP_STATUS_NOCONTENT            = 204;

    const HTTP_STATUS_BADREQUEST           = 400;
    const HTTP_STATUS_UNAUTHORIZED         = 401;
    const HTTP_STATUS_FORBIDDEN            = 403;
    const HTTP_STATUS_NOTFOUND             = 404;
    const HTTP_STATUS_CONFLICT             = 409;
    const HTTP_STATUS_UNSUPPORTERMEDIATYPE = 415;

    const HTTP_STATUS_INTERNALSERVERERROR  = 500;
    const HTTP_STATUS_NOTIMPLEMENTED       = 501;
    const HTTP_STATUS_SERVICEUNAVAILABLE   = 503;

    const MSG_FORMDATA            = 1000;
    const MSG_CONFLICT            = 1001;
    const MSG_NOTFOUND            = 1002;
    const MSG_NOTIMPLEMENTED      = 1003;
    const MSG_NO_ACCOUNTREQUEST   = 2000;
    const MSG_NO_ACCOUNT          = 2001;
    const MSG_NO_SESSION          = 2002;
    const MSG_AUTHORIZATION       = 2003;
    const MSG_ETAG_NOTFOUND       = 2004;
    const MSG_SAVE_ACCOUNT        = 3001;
    const MSG_SAVE_ACCOUNTREQUEST = 3002;
    const MSG_SAVE_SESSION        = 3003;
    const MSG_OAUTH_REQUIRED      = 4001;
    const MSG_OAUTH_ENCRYPTION    = 4002;
    const MSG_OAUTH_FAILURE       = 4003;
    const MSG_OAUTH_VARNOTFOUND   = 4004;

    static $messages = array (
        self::MSG_FORMDATA => "Formdata could not be validated correctly.",
        self::MSG_CONFLICT => "Conflict occured. Please fetch the latest resource and try again.",
        self::MSG_NOTFOUND => "The specified resource could not be found.",
        self::MSG_NOTIMPLEMENTED => "This call is not implemented.",
        self::MSG_NO_ACCOUNTREQUEST => "Cannot find the specified account request.",
        self::MSG_NO_ACCOUNT => "Cannot find the specified account.",
        self::MSG_NO_SESSION => "Cannot find the specified session.",
        self::MSG_AUTHORIZATION => "Website is not authorized to operate on this resource.",
        self::MSG_ETAG_NOTFOUND => "Etag is not checked with the If-None-Match flag.",
        self::MSG_SAVE_ACCOUNT => "Error while saving account data.",
        self::MSG_SAVE_ACCOUNTREQUEST => "Error while saving account request data.",
        self::MSG_SAVE_SESSION => "Error while saving session data.",
        self::MSG_OAUTH_REQUIRED => "OAuth authentication is required",
        self::MSG_OAUTH_ENCRYPTION => "OAuth encryption is not supported",
        self::MSG_OAUTH_FAILURE => "OAuth authentication failed",
        self::MSG_OAUTH_VARNOTFOUND => "Not all mandatory OAuth fields are found");

}