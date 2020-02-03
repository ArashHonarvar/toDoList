<?php

namespace App\Tools;

use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{

    const TYPE_VALIDATION_ERROR = "validation_error";
    const TYPE_INVALID_REQUEST_BODY_FORMAT = "invalid_body_format";
    const TYPE_USER_EXISTED = "user_existed";
    const TYPE_AUTH_HEADERS_MISSING = "auth_headers_missing";
    const TYPE_AUTH_TOKEN_EXPIRED = "auth_token_expired";
    const TYPE_AUTH_TOKEN_INVALID = "auth_token_invalid";
    const TYPE_AUTH_REQUIRED = "auth_required";

    private static $titles = [
        self::TYPE_VALIDATION_ERROR => "There was a validation error",
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => "Invalid JSON format sent",
        self::TYPE_USER_EXISTED => "User with this email/username existed",
        self::TYPE_AUTH_HEADERS_MISSING => "Authentication Headers are missing",
        self::TYPE_AUTH_TOKEN_EXPIRED => "ApiToken is expired",
        self::TYPE_AUTH_TOKEN_INVALID => "ApiToken is invalid",
        self::TYPE_AUTH_REQUIRED => "Authentication is Required",
    ];

    private $statusCode;

    private $type;

    private $title;

    private $extraData = array();

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;

        if ($type === null) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : 'Unknown status code :(';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException("No title for type " . $type);
            }
            $title = self::$titles[$type];
        }
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            array(
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            )
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }


}
