<?php

namespace App\Http\Middleware;

use \Closure;
use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\Log;
use App\Classes\TokenAccess;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Illuminate\Http\Response;
use \Illuminate\Http\RedirectResponse;

class CustomValidateToken
{
    protected $text;
    protected $status;

    public function __construct() {
        $this->text = new Text();
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
        if ($request->header($this->text->getAuthorization()) != null) {
            $tokenAccess = new TokenAccess($request->header($this->text->getAuthorization()));
            if ($tokenAccess->validateAPI() == $this->status->getEnable()) {
                Log::debug("Token ON => ".$request->header($this->text->getAuthorization()));
                return $next($request);
            }else{
                Log::debug("Rejected => ".$request->header($this->text->getAuthorization()));
                return abort(404, $this->text->getTokenDecline());
            }
        }else{
            return abort(404, $this->text->getAccessDecline());
        }
    }
}
