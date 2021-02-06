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
 * | File:      JWTTokenClaim.php
 * | Created:   02.12.2020
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevRaeph\XToken;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token\Plain;

class TokenizerClaim
{
    private $uid;
    private Request $request;

    /**
     * @param Request $request Der eingehende Request von der API
     * @return TokenizerClaim;
     */
    public function setRequest(Request $request): TokenizerClaim
    {
        $this->request = $request;
        return $this;
    }
    /**
     * get ModelId
     * @return int|null ModelID
     */
    public function get():?int{
        $config = (new Tokenizer)->getConfig();
        try {
            $jwtToken = $this->request->header(config('xtoken.tokenKey'));
            $token = $config->parser()->parse((string) $jwtToken);

            assert($token instanceof Plain);

            $this->uid = $token->claims()->get("uid");

            if($this->uid == null || $this->uid == ""){
                response([
                    "message"=>"Token Claim ist null oder leer!"
                ],403)->send();
                die;
            }else{
                return (int)$this->uid;
            }
        }catch (\Exception $e){
            response([
                "message"=>"Parse error!",
                "error"=>$e->getMessage()
            ],403)->send();
            die;
        }
    }
}
