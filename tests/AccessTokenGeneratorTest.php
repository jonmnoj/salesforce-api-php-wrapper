<?php

use \Mockery as m;

class AccessTokenGeneratorTest extends TestCase
{

    /** @test */
    public function token_gets_generated_from_json()
    {
        $jsonToken = json_encode([
            'id' => '',
            'dateIssued' => '',
            'dateExpires' => '',
            'scope' => '',
            'tokenType' => '',
            'refreshToken' => '',
            'signature' => '',
            'accessToken' => '',
            'apiUrl' => '',
        ]);
        $tokenGenerator = new \Crunch\Salesforce\AccessTokenGenerator();
        $token = $tokenGenerator->createFromJson($jsonToken);

        $this->assertInstanceOf(\Crunch\Salesforce\AccessToken::class, $token, 'Token generated not an instance of AccessToken');
    }

    /** @test */
    public function token_gets_generated_from_sf_response()
    {
        $responseData = [
            'id' => '',
            'issued_at' => '',
            'scope' => '',
            'token_type' => '',
            'refresh_token' => '',
            'signature' => '',
            'access_token' => '',
            'instance_url' => '',
        ];
        $tokenGenerator = new \Crunch\Salesforce\AccessTokenGenerator();
        $token = $tokenGenerator->createFromSalesforceResponse($responseData);

        $this->assertInstanceOf(\Crunch\Salesforce\AccessToken::class, $token, 'Token generated not an instance of AccessToken');
    }

    /** @test */
    public function token_gets_generated_from_limited_sf_response()
    {
        $responseData = [
            'id' => '',
            'issued_at' => '',
            'access_token' => '',
            'instance_url' => '',
        ];
        $tokenGenerator = new \Crunch\Salesforce\AccessTokenGenerator();
        $token = $tokenGenerator->createFromSalesforceResponse($responseData);

        $this->assertInstanceOf(\Crunch\Salesforce\AccessToken::class, $token, 'Token generated not an instance of AccessToken');
    }

    /** @test */
    public function token_date_generated_from_sf_response()
    {
        $time = time();
        $responseData = [
            'id' => '',
            'issued_at' => $time * 1000, //salesforce uses milliseconds
            'scope' => '',
            'token_type' => '',
            'refresh_token' => '',
            'signature' => '',
            'access_token' => '',
            'instance_url' => '',
        ];
        $tokenGenerator = new \Crunch\Salesforce\AccessTokenGenerator();
        $token = $tokenGenerator->createFromSalesforceResponse($responseData);

        $this->assertInstanceOf(DateTimeImmutable::class, $token->getDateIssued(), 'Token issued date not a DateTimeImmutable instance');
        $this->assertEquals($time, $token->getDateIssued()->getTimestamp(), 'Token issue timestamp doesnt match');
        $this->assertEquals($time + (60*55), $token->getDateExpires()->getTimestamp(), 'Token expiry time not 55 minutes after creation');
    }

    /** @test */
    public function token_date_generated_from_stored_json()
    {
        $issueDate = '2015-01-02 10:11:12';
        $expiryDate = '2015-01-02 11:11:12';
        $jsonToken = json_encode([
            'id' => '',
            'dateIssued' => $issueDate,
            'dateExpires' => $expiryDate,
            'scope' => '',
            'tokenType' => '',
            'refreshToken' => '',
            'signature' => '',
            'accessToken' => '',
            'apiUrl' => '',
        ]);
        $tokenGenerator = new \Crunch\Salesforce\AccessTokenGenerator();
        $token = $tokenGenerator->createFromJson($jsonToken);

        $this->assertInstanceOf(DateTime::class, $token->getDateIssued(), 'Token issued date not a DateTimeImmutable instance');
        $this->assertEquals($issueDate, $token->getDateIssued()->format('Y-m-d H:i:s'), 'Token issue timestamp doesnt match');
        $this->assertEquals($expiryDate, $token->getDateExpires()->format('Y-m-d H:i:s'), 'Token expiry time not 1 hour after creation');
    }

}