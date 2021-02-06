<?php
/*
 * ██████╗ ███████╗██╗   ██╗███████╗████████╗ ██████╗ ██████╗ ███╗   ███╗
 * ██╔══██╗██╔════╝██║   ██║██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗████╗ ████║
 * ██║  ██║█████╗  ██║   ██║███████╗   ██║   ██║   ██║██████╔╝██╔████╔██║
 * ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║   ██║   ██║   ██║██╔══██╗██║╚██╔╝██║
 * ██████╔╝███████╗ ╚████╔╝ ███████║   ██║   ╚██████╔╝██║  ██║██║ ╚═╝ ██║
 * ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝
 * ______________________________________________________________________
 * | Author:    DevStorm Solutions - rplan
 * | Project:   ds-laravel-jwttoken-project
 * | File:      XToken.php
 * | Created:   06.02.2021
 * | Todo:
 * |_____________________________________________________________________
 */
namespace DevRaeph\XToken;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DevRaeph\XToken\Http\Model\JWTToken;
use DevRaeph\XToken\Http\Model\JWTTokenResponse;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;

class Tokenizer
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
        $this->issuedBy = config('xtoken.tokenIssuedBy');
        $this->permittedFor = $this->issuedBy;
        $this->identifiedBy = uniqid("DS_").Carbon::now()->unix();
        $this->issuedAt = CarbonImmutable::now();
        $this->canOnlyBeUsedAfter = $this->issuedAt->modify("+1 seconds");
        $this->expiresAt = $this->issuedAt->addDays(1);

        $this->config = Configuration::forSymmetricSigner(
            new Sha512(),
            InMemory::plainText(config('xtoken.tokenEncKey'))
        );
    }

    /**
     * setIssuedBy
     * @param string $issuedBy Default ist config("xtoken.tokenIssuedBy")
     * @return Tokenizer;
     */
    public function setIssuedBy(string $issuedBy): Tokenizer
    {
        $this->issuedBy = $issuedBy;
        return $this;
    }

    /**
     * setPermittedFor
     * @param string $permittedFor Route welche freigegeben sein soll. z.B. '<url>/api/v2/'
     * @return Tokenizer;
     */
    public function setPermittedFor(string $permittedFor): Tokenizer
    {
        $this->permittedFor = $permittedFor;
        return $this;
    }

    /**
     * setIdentifiedBy
     * @param string $identifiedBy Unique ID woran der Token unterschieden werden kann.
     * @return Tokenizer;
     */
    public function setIdentifiedBy(string $identifiedBy): Tokenizer
    {
        $this->identifiedBy = $identifiedBy;
        return $this;
    }

    /**
     * setIssuedAt
     * @param CarbonImmutable $issuedAt Default = NOW
     * @return Tokenizer;
     */
    public function setIssuedAt(CarbonImmutable $issuedAt): Tokenizer
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * setCanOnlyBeUsedAfter
     * @param CarbonImmutable $canOnlyBeUsedAfter Now plus Time
     * @return Tokenizer;
     */
    public function setCanOnlyBeUsedAfter(CarbonImmutable $canOnlyBeUsedAfter): Tokenizer
    {
        $this->canOnlyBeUsedAfter = $canOnlyBeUsedAfter;
        return $this;
    }

    /**
     * setExpiresAt
     * @param CarbonImmutable $expiresAt Now Plus Time
     * @return Tokenizer;
     */
    public function setExpiresAt(CarbonImmutable $expiresAt): Tokenizer
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * setModel
     * @param Model $model  Model/Customer or Model/User
     * @return Tokenizer;
     */
    public function setModel(Model $model): Tokenizer
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
