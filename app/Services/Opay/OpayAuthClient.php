<?php

namespace App\Services\Opay;

final readonly class OpayAuthClient
{
    public const USER_LOGIN_PATH = '/api/users/userLoginV3';
    public const SWITCH_LOGIN_PATH = '/api/users/switch/login';
    public const GRAPHQL_PATH = '/graphql';

    public function __construct(
        private OpayEncryptedRequestFactory $encryptedRequestFactory = new OpayEncryptedRequestFactory(),
        private string $baseUrl = '',
        private string $publicKeyPem = '',
        private string $packageName = 'team.opay.pay',
        private string $versionCode = '8458272',
    ) {}

    public function prepareUserLogin(
        OpayTransportContext $context,
        array $payload,
        ?int $timestamp = null,
        ?string $aesKey = null,
    ): OpayPreparedRequest {
        $encrypted = $this->encryptedRequestFactory->make(
            payload: $payload,
            context: $context,
            publicKeyPem: $this->publicKeyPem,
            timestamp: $timestamp,
            aesKey: $aesKey,
        );

        return $this->preparedRequest(
            path: self::USER_LOGIN_PATH,
            context: $context,
            encrypted: $encrypted,
            extraHeaders: [
                'remove-token' => 'true',
            ],
        );
    }

    public function prepareSessionMetaQuery(
        OpayTransportContext $context,
        ?int $timestamp = null,
        ?string $aesKey = null,
    ): OpayPreparedRequest {
        $encrypted = $this->encryptedRequestFactory->make(
            payload: [
                'operationName' => 'SessionMetaQuery',
                'variables' => [],
                'query' => $this->sessionMetaQuery(),
            ],
            context: $context,
            publicKeyPem: $this->publicKeyPem,
            timestamp: $timestamp,
            aesKey: $aesKey,
        );

        return $this->preparedRequest(
            path: self::GRAPHQL_PATH,
            context: $context,
            encrypted: $encrypted,
            extraHeaders: [
                'Authorization' => 'Bearer '.$context->token,
            ],
        );
    }

    public function prepareSwitchLogin(
        OpayTransportContext $context,
        array $payload,
        ?int $timestamp = null,
        ?string $aesKey = null,
    ): OpayPreparedRequest {
        $encrypted = $this->encryptedRequestFactory->make(
            payload: $payload,
            context: $context,
            publicKeyPem: $this->publicKeyPem,
            timestamp: $timestamp,
            aesKey: $aesKey,
        );

        return $this->preparedRequest(
            path: self::SWITCH_LOGIN_PATH,
            context: $context,
            encrypted: $encrypted,
            extraHeaders: [
                'Authorization' => 'Bearer '.$context->token,
            ],
        );
    }

    private function preparedRequest(
        string $path,
        OpayTransportContext $context,
        OpayEncryptedRequest $encrypted,
        array $extraHeaders = [],
    ): OpayPreparedRequest {
        return new OpayPreparedRequest(
            method: 'POST',
            url: $this->normalizedBaseUrl().$path,
            path: $path,
            headers: [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'pn' => $this->packageName,
                'version_code' => $this->versionCode,
                'version_name' => $context->versionName,
                'token' => $context->token,
                'country' => 'NG',
                'role' => 'customer',
                'trace_id' => $context->deviceId,
                'gaid' => '',
                'model' => '',
                'dma' => '',
                'zone_offset' => (string) now()->getOffset(),
                'mcc' => '',
                'campaign' => '',
                'mediaSource' => '',
                'location' => 'null|null',
                'blackbox' => '',
                'appsflyerId' => '',
                'instanceId' => '',
                ...$extraHeaders,
                ...$encrypted->headers,
                ...$context->extraHeaders,
            ],
            body: $encrypted->body,
            canonicalJson: $encrypted->canonicalJson,
            aesKey: $encrypted->aesKey,
        );
    }

    private function normalizedBaseUrl(): string
    {
        $baseUrl = $this->baseUrl !== ''
            ? $this->baseUrl
            : (string) config('opay.auth_base_url', 'https://api.opayweb.com');

        return rtrim($baseUrl, '/');
    }

    private function sessionMetaQuery(): string
    {
        return <<<'GRAPHQL'
query SessionMetaQuery {
  countries {
    id
    selected
    name
    otpIssuedTime
  }
  currencies {
    isoCode
    string
    country
  }
  currency {
    isoCode
    string
  }
  currentUser {
    id
    phoneNumber
    phoneNumberSanitized
    firstName
    middleName
    surname
    gender
    dob
    state
    address
    email
    emailToVerify
    kycLevel
    role
    userToken
    emailVerified
    validID
    utilityBill
    picture
    referrerCode
    lastVisitTime
    avatarUrl
    avatarThumbnailUrl
    nickName
    city
    aggregatorId
    isSetPayPin
    createDate
    posLevelIconUrl
    posLevelFileIconUrl
    agentLevel
    bvn {
      number
    }
    isFinish
    stateCode
    lgaCode
    cityCode
    canUpdateName
    canUpdateDobAndGender
    facePicture
    isShowRegisterTask
    isNewOpayUser
  }
}
GRAPHQL;
    }
}
