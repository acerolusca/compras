<?php
namespace App\Core\Middleware;


use \Exception;
use \App\Core\Response;

class RequiredWebsiteOrigin {

    public function handle($request, $next) {

        $origin = $_SERVER["HTTP_ORIGIN"] ?? "";
        $referer = $_SERVER["HTTP_REFERER"] ?? "";

        try {
            if ($origin != getenv("HTTP_ORIGIN") || $referer != getenv("HTTP_REFERER")) {
                throw new Exception("Acesso negado.", 401);
            }

            return $next($request);

        } catch (Exception $e) { 

            $content = json_encode([
                "success" => false,
                "data" => "",
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $content, "application/json", $origin);
        }
    }



    public function handleTewste($request, $next) {
        try {

            throw new Exception("Loucura", 401);

        } catch (Exception $e) { 

            $content = json_encode([
                "success" => false,
                "data" => $_SERVER,
                "message" => $e->getMessage()
            ]);

            return new Response($e->getCode(), $content, "application/json");
        }
    }


}