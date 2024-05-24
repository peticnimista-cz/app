<?php

namespace App\Model\API\Responses;

use App\Model\API\Status;
use Nette\Http\Request;
use Nette\Http\IResponse;
use Nette\Http\IRequest;
use Nette\Application\Response;
use Nette\Utils\Json;
use Nette\SmartObject;
use Tracy\Bar;
use Tracy\Debugger;

final class JsonResponse implements Response
{
    use SmartObject;

    /** @var mixed */
    private $payload;

    /** @var string */
    private string $contentType;


    public function __construct($payload,
                                private int $code = IResponse::S200_OK,
                                private string $message = "",
                                private string $status = Status::OK,
                                ?string $contentType = null)
    {
        $this->payload = $payload;
        $this->contentType = $contentType ?: 'application/json';
    }

    public static function error(\Exception $exception): self {
        return new self([], (int)$exception->getCode(), $exception->getMessage(), Status::ERROR);
    }


    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }


    /**
     * Returns the MIME content type of a downloaded file.
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }


    /**
     * Sends response to output.
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->contentType, 'utf-8');
        if($this->code) $httpResponse->setCode($this->code, $this->message);
        echo Json::encode([
            "status" => $this->status,
            "content" => $this->payload,
            "message" => $this->message,
            "code" => $this->code,
        ], true);
    }
}
