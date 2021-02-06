<?php
/*
 * ██████╗ ███████╗██╗   ██╗███████╗████████╗ ██████╗ ██████╗ ███╗   ███╗
 * ██╔══██╗██╔════╝██║   ██║██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗████╗ ████║
 * ██║  ██║█████╗  ██║   ██║███████╗   ██║   ██║   ██║██████╔╝██╔████╔██║
 * ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║   ██║   ██║   ██║██╔══██╗██║╚██╔╝██║
 * ██████╔╝███████╗ ╚████╔╝ ███████║   ██║   ╚██████╔╝██║  ██║██║ ╚═╝ ██║
 * ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝
 * ______________________________________________________________________
 * | Author:    DevStorm Solutions - rplaner
 * | Project:   DS_Laravel_JWTToken
 * | File:      JWTToken.php
 * | Created:   02.12.2020
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevStorm\JWTToken\Http\Model;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DevStorm\Response\Response;
use DevStorm\Response\ResponseServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use hisorange\BrowserDetect\Parser as Browser;

class JWTToken
{
    private Configuration $config;
    private string $issuedBy;
    private string $permittedFor;
    private string $identifiedBy;
    private CarbonImmutable $issuedAt;
    private CarbonImmutable $canOnlyBeUsedAfter;
    private CarbonImmutable $expiresAt;
    private Model $model;
    private string $access_token;

    function __construct()
    {
        $this->issuedBy = env("APP_URL");
        $this->permittedFor = $this->issuedBy;
        $this->identifiedBy = uniqid("DS_").Carbon::now()->unix();
        $this->issuedAt = CarbonImmutable::now();
        $this->canOnlyBeUsedAfter = $this->issuedAt->modify("+1 seconds");
        $this->expiresAt = $this->issuedAt->addDays(1);

        $this->config = Configuration::forSymmetricSigner(
            new Sha512(),
            InMemory::plainText(env("APP_KEY"))
        );
    }

    /**
     * setIssuedBy
     * @param string $issuedBy Default ist env("APP_URL")
     * @return JWTToken;
     */
    public function setIssuedBy(string $issuedBy): JWTToken
    {
        $this->issuedBy = $issuedBy;
        return $this;
    }

    /**
     * setPermittedFor
     * @param string $permittedFor Route welche freigegeben sein soll. z.B. '<url>/api/v2/'
     * @return JWTToken;
     */
    public function setPermittedFor(string $permittedFor): JWTToken
    {
        $this->permittedFor = $permittedFor;
        return $this;
    }

    /**
     * setIdentifiedBy
     * @param string $identifiedBy Unique ID woran der Token unterschieden werden kann.
     * @return JWTToken;
     */
    public function setIdentifiedBy(string $identifiedBy): JWTToken
    {
        $this->identifiedBy = $identifiedBy;
        return $this;
    }

    /**
     * setIssuedAt
     * @param CarbonImmutable $issuedAt Default = NOW
     * @return JWTToken;
     */
    public function setIssuedAt(CarbonImmutable $issuedAt): JWTToken
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * setCanOnlyBeUsedAfter
     * @param CarbonImmutable $canOnlyBeUsedAfter Now plus Time
     * @return JWTToken;
     */
    public function setCanOnlyBeUsedAfter(CarbonImmutable $canOnlyBeUsedAfter): JWTToken
    {
        $this->canOnlyBeUsedAfter = $canOnlyBeUsedAfter;
        return $this;
    }

    /**
     * setExpiresAt
     * @param CarbonImmutable $expiresAt Now Plus Time
     * @return JWTToken;
     */
    public function setExpiresAt(CarbonImmutable $expiresAt): JWTToken
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * setModel
     * @param Model $model  Model/Customer or Model/User
     * @return JWTToken;
     */
    public function setModel(Model $model): JWTToken
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    public function createToken():?JWTTokenResponse{
        try {
            if(!$this->model){
                response([
                    "message"=>"Es wurde kein Model übergeben!"
                ],406)->send();
                die;
            }

            $token = $this->config->builder()
                ->issuedBy($this->issuedBy)
                ->permittedFor($this->permittedFor)
                ->identifiedBy($this->identifiedBy)
                ->issuedAt($this->issuedAt)
                ->canOnlyBeUsedAfter($this->canOnlyBeUsedAfter)
                ->expiresAt($this->expiresAt)
                ->withClaim('uid', $this->model->id)
                ->getToken($this->config->signer(), $this->config->signingKey());


            $device = Browser::deviceFamily()." - ".Browser::deviceModel();
            if(Browser::deviceFamily() == "Unknown"){
                $device = Browser::platformName()." - ".Browser::browserFamily();
            }
            try {
                $this->model->XTokens()->create([
                    "identified_by"=>$this->identifiedBy,
                    "issued_by"=>$this->issuedBy,
                    "expires_at"=>$this->expiresAt,
                    "agent"=>Browser::userAgent(),
                    "device" => $device
                ]);
            }catch (\Exception $e){
                response([
                    "message"=>"Token DB save fehlgeschlagen. Missing Trait: HasXToken ?",
                    "error"=>$e->getMessage()
                ],406)->send();
                die;
            }

        }catch (\Exception $e){
            response([
                "message"=>"Token init fehlgeschlagen!",
                "error"=>$e->getMessage()
            ],406)->send();
            die;
        }
        $this->access_token = $token->toString();
        return (new JWTTokenResponse())
            ->setAccessToken($this->access_token)
            ->setExpiresIn($this->expiresAt);
    }
}
