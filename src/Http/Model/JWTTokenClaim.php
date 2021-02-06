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

namespace DevStorm\JWTToken\Http\Model;



use Carbon\Carbon;
use DevStorm\Response\Response;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token\Plain;

class JWTTokenClaim
{
    private $uid;
    private string $header = "X-DevStorm-Token";
    private Request $request;

    /**
     * @param Request $request Der eingehende Request von der API
     * @return JWTTokenClaim;
     */
    public function setRequest(Request $request): JWTTokenClaim
    {
        $this->request = $request;
        return $this;
    }
    /**
     * get ModelId
     * @return int|null ModelID
     */
    public function get():?int{
        $config = (new JWTToken())->getConfig();
        try {
            $jwtToken = $this->request->header($this->header);
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
