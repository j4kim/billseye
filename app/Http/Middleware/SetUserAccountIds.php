<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserAccountIds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('account')) {
            return $next($request);
        }
        $accounts = auth()->user()->accounts;
        $ids = $accounts->pluck('id')->toArray();
        $selected = $accounts->where('pivot.selected')->first();
        $request->session()->put('account.ids', $ids);
        $request->session()->put('account.selected', $selected);
        $request->session()->put('account.selectedId', $selected->id);
        return $next($request);
    }
}
