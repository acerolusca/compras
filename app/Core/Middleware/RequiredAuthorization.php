<?php

namespace App\Core\Middleware;

use \App\Core\Response;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Exception;



class RequiredAuthorization {

    public function getJWTAuthorization($request){

        try{
            $headers = $request->getHeaders();
            $key = getenv("JWT_KEY");
            $jwt = isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : "";
            $decoded = (array) JWT::decode($jwt, new Key($key, 'HS384'));

            $iss = $decoded["iss"] ?? "";
            $app = $decoded["app"] ?? "";


            if( $iss != getenv("JWT_ISS") || $app != getenv("JWT_APP")){
                throw new Exception("Acesso negado. Token inválido.", 401);
            }

        } catch (InvalidArgumentException $e) {
            throw new Exception("Erro interno.", 500);
        } catch (DomainException $e) {
            throw new Exception("Erro interno.", 500);
        } catch (SignatureInvalidException $e) {
            throw new Exception("Acesso negado. Token inválido.", 401);
        } catch (BeforeValidException $e) {
            throw new Exception("Acesso negado. Token inválido.", 401);
        } catch (ExpiredException $e) {
            throw new Exception("Acesso negado. Token expirado.", 401);
        } catch (UnexpectedValueException $e) {
            throw new Exception("Acesso negado. Token inválido", 401);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    public function handle($request, $next) {

        try {

            $this->getJWTAuthorization($request);

            return $next($request);

        } catch (Exception $e) {

            $content = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $content, "application/json");
        }
    }
}