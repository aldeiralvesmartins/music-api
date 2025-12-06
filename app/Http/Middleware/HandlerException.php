<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HandlerException
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Do not attempt to wrap/transform binary or streamed responses
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        // Safely access exception only if the property exists and is set
        if (\property_exists($response, 'exception') && ($exception = $response->exception)) {
//            debug($exception);
            // Se for um erro de validação
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Erro de validação',
                    'errors' => array_merge(...array_values($exception->errors())),
                ], 422);
            }

            // Para erros HTTP (ex: rota não encontrada, método inválido)
//                if ($exception->isHttpException($exception)) {
//                    return response()->json([
//                        'message' => $exception->getMessage() ?: 'Erro HTTP',
//                        'errors' => [],
//                    ], $exception->getStatusCode());
//                }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Erro de autenticação',
                    'errors' => [$exception->getMessage()]
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'message' => 'Erro na requisição',
                    'errors' => [$exception->getMessage()]
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($exception instanceof QueryException) {
                return response()->json([
                    'message' => 'Erro de Query',
                    'errors' => [$exception->getMessage()]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($exception instanceof \Exception) {
                if (is_int($exception->getCode()) && is_http_code($exception->getCode())) {
                    return response()->json([
                        'message' => 'Ocorreu um erro',
                        'errors' => [$exception->getMessage()],
                    ], $exception->getCode());
                }
            }

            if ($exception instanceof \ErrorException) {
                return \response()->json([
                    'message' => 'Erro',
                    'errors' => [$exception->getMessage()],
                    'trace' => $exception->getTrace(),
                ]);
            }

            // Para outros erros (ex: erro de servidor, exceções genéricas)
            return response()->json([
                'message' => 'Erro no servidor',
                'errors' => config('app.debug') ? [
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                ] : [],
            ], 500);
        }

        return $response;
    }
}
