<?php
namespace App\Middleware;

use App\Core\Session;
class RoleMiddleware implements MiddlewareInterface
{
    private string $requireRole;
    public function __construct
    (
        string $requireRole = 'user',
        private Session $session
    ){
        $this->requireRole = $requireRole;
    }
    public function handle($request, callable $next)
    {
        $user = $this->session->getUser();

        if(!$user){
            header('Location:/login');
            exit;
        }
        $role = $user->getRole();
        $allowed = false;

        switch($this->requireRole){
            case 'admin':
                $allowed = $role === 'admin';
                break;
            case 'moderator':
                $allowed = $role === 'moderator' || $role === 'admin';
                break;
            case 'user':
            default:
                $allowed = true;
                break;
        }
        if(!$allowed){
            header('Location: /posts');
            exit;
        }
        return $next($request);
    }
}