<?php

namespace App\Http\Middleware;

use \Closure;
use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\Log;
use App\Classes\TokenAccess;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use App\Classes\Helper\Ip;
use \Illuminate\Http\Response;
use \Illuminate\Http\RedirectResponse;

class CustomValidateToken
{
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
                return abort(402, $this->text->getTokenDecline());
            }
        }else{
            return abort(404, $this->text->getAccessDecline());
        }
    }
}
