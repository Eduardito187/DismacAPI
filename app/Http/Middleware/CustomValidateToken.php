<?php

namespace App\Http\Middleware;

use \Closure;
use \Illuminate\Http\Request;
use App\Classes\TokenAccess;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use App\Classes\Helper\Ip;
use \Illuminate\Http\Response;
use \Illuminate\Http\RedirectResponse;

class CustomValidateToken
{
    const ERROR_402 = 402;
    const ERROR_404 = 404;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Ip
     */
    protected $Ip;

    public function __construct() {
        $this->text   = new Text();
        $this->status = new Status();
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->Ip = new Ip($request->ip());
        $this->Ip->validIp();
        if ($this->Ip->validRestrict() && $request->header($this->text->getAuthorization()) != null) {
            $tokenAccess = new TokenAccess($request->header($this->text->getAuthorization()));
            if ($tokenAccess->validateAPI() == $this->status->getEnable()) {
                return $next($request);
            }else{
                return abort(self::ERROR_402, $this->text->getTokenDecline());
            }
        }else{
            return abort(self::ERROR_404, $this->text->getAccessDecline());
        }
    }
}