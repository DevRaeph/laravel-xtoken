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
 * | File:      VerifyJWTToken.php
 * | Created:   02.12.2020
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevRaeph\XToken\Http\Middleware;
use Carbon\CarbonImmutable;
use Closure;
use DevRaeph\XToken\Http\Model\DXToken;
use DevRaeph\XToken\Tokenizer;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\Clock\FrozenClock;

class VerifyJWTToken
{
    public function handle($request, Closure $next)
    {
        $config = (new Tokenizer())->getConfig();

        $validator = array(
            new Constraint\SignedWith($config->signer(),$config->signingKey()),
            new Constraint\IssuedBy(env("APP_URL")),
            new Constraint\PermittedFor(env("APP_URL")),
            new Constraint\ValidAt(new FrozenClock(CarbonImmutable::now())),
        );
        $config->setValidationConstraints(...$validator);

        $jwtToken = $request->header(config('xtoken.tokenKey'));
        if($jwtToken == null || $jwtToken == ""){
            return response([
                "message"=>"Token wurde nicht mitgegeben"
            ],403);
        }

        try {
            $token = $config->parser()->parse((string) $jwtToken);
            assert($token instanceof Plain);
        }catch (\Exception $e){
            return response([
                "message"=>"Token muss ein gültiger JWT Token sein!"
            ],403);
        }

        $constraints = $config->validationConstraints();

        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return response([
                "message"=>"Verifikation fehlgeschlagen!",
                "exception"=>$e->getMessage(),
            ],403);
        }

        //Check if identifier is valid in DB
        $tokenIdentifier = $token->claims()->get("jti");
        $dbToken = DXToken::whereIdentifiedBy($tokenIdentifier)->first();
        if(!$dbToken){
            return response([
                "message"=>"Token nicht im System gefunden!"
            ],403);
        }
        if($dbToken->is_banned){
            return response([
                "message"=>"Token wurde revoked!"
            ],401);
        }

        return $next($request);
    }
}
