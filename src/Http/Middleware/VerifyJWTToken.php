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

namespace DevStorm\JWTToken\Http\Middleware;
use Carbon\CarbonImmutable;
use Closure;
use DevStorm\JWTToken\Http\Model\DXToken;
use DevStorm\JWTToken\Http\Model\JWTToken;
use DevStorm\Response\Response;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\Clock\FrozenClock;

class VerifyJWTToken
{
    public function handle($request, Closure $next)
    {
        $config = (new JWTToken())->getConfig();

        $validator = array(
            new Constraint\SignedWith($config->signer(),$config->signingKey()),
            new Constraint\IssuedBy(env("APP_URL")),
            new Constraint\PermittedFor(env("APP_URL")),
            new Constraint\ValidAt(new FrozenClock(CarbonImmutable::now())),
        );
        $config->setValidationConstraints(...$validator);

        $jwtToken = $request->header('X-DevStorm-Token');
        if($jwtToken == null || $jwtToken == ""){
            return Response::create("<DevStorm JWT Response> Token wurde nicht mitgegeben!",Response::Failed,"Token is null or empty");
        }

        try {
            $token = $config->parser()->parse((string) $jwtToken);
            assert($token instanceof Plain);
        }catch (\Exception $e){
            return Response::create("<DevStorm JWT Response> Token muss ein gültiger JWT Token sein!",Response::Failed,$e->getMessage());
        }

        $constraints = $config->validationConstraints();

        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return Response::create("<DevStorm JWT Response> Verifikation fehlgeschlagen!",Response::Failed,$e->getMessage());
        }

        //Check if identifier is valid in DB
        $tokenIdentifier = $token->claims()->get("jti");
        $dbToken = DXToken::whereIdentifiedBy($tokenIdentifier)->first();
        if(!$dbToken){
            return response([
                "message"=>"Token nicht im System gefunden!"
            ],403)->send();
        }
        if($dbToken->is_banned){
            return response([
                "message"=>"Token wurde revoked!"
            ],403)->send();
        }

        return $next($request);
    }
}
