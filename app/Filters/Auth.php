<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;
use CodeIgniter\API\ResponseTrait;
class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    use ResponseTrait;
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('TOKEN_SECRET');
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if(!$header) return Services::response()
                            ->setJSON(['msg' => 'Token Required'])
                            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        $token = explode(' ', $header)[1];
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $response = [
				'host_id' => $decoded->host_id,
                'host_ip' => $decoded->host_ip,
				'username' => $decoded->username,
                'password' => $decoded->password,
			];
            $current_host_ip = $request->getIPAddress();
            if($current_host_ip !== $response['host_ip']) {
                return Services::response()->setJSON(['error' => '1', 'code' => '102', 'message' => 'Warning: IP conflict'])->setStatusCode(400);
            }
			$config = config('Config\App');
            $config->JWTresponse = $response;
        } catch (\Throwable $th) {
            return Services::response()->setJSON(['error' => '1', 'code' => '102', 'message' => 'Invalid Token'])->setStatusCode(400);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
