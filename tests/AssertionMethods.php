<?php
declare(strict_types=1);

namespace FrontInterop;

trait AssertionMethods
{
    /**
     * @param string[] $headers
     */
    protected function assertResponse(
        ResponseHandler $responseHandler,
        int $code,
        array $headers,
        string $content,
    ) : void
    {
        ob_start();
        $responseHandler->handleResponse();
        $actualContent = ob_get_clean();
        $actualCode = http_response_code();
        $actualHeaders = xdebug_get_headers();

        foreach ($actualHeaders as $i => $actualHeader) {
            if (str_starts_with($actualHeader, 'Date:')) {
                unset($actualHeaders[$i]);
            }
        }

        $this->assertSame($code, $actualCode);
        $this->assertSame($headers, $actualHeaders);
        $this->assertSame($content, $actualContent);
    }
}
