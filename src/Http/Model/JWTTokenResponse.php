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
 * | File:      JWTTokenResponse.php
 * | Created:   02.12.2020
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevStorm\JWTToken\Http\Model;


use Carbon\CarbonImmutable;

class JWTTokenResponse
{
    private string $access_token;
    private CarbonImmutable $expires_in;

    /**
     * @param string $access_token
     * @return JWTTokenResponse;
     */
    public function setAccessToken(string $access_token): JWTTokenResponse
    {
        $this->access_token = $access_token;
        return $this;
    }

    /**
     * @param CarbonImmutable $expires_in
     * @return JWTTokenResponse;
     */
    public function setExpiresIn(CarbonImmutable $expires_in): JWTTokenResponse
    {
        $this->expires_in = $expires_in;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @return CarbonImmutable
     */
    public function getExpiresIn(): CarbonImmutable
    {
        return $this->expires_in;
    }

    public function toArray(): array{
        return array(
            "access_token"=>$this->access_token,
            "token_type"=>"X-DevStorm-Token",
            "expires_in"=>$this->expires_in
        );
    }
    public function toJson(): string{
        return json_encode(array(
            "access_token"=>$this->access_token,
            "token_type"=>"X-DevStorm-Token",
            "expires_in"=>$this->expires_in
        ));
    }
}
