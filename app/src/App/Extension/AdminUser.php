<?php
/**
 *
 */
namespace App\Extension;

/**
 * Definitions for User admin
 */
use App\Extension;
use App\Session;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminUser extends Extension
{

    public static $name = 'User administration';
    public static $description = 'Add / Delete, Activate / Deactivate users, change passwords';
    public static $version = '1.0.0';
    public static $link = '';


    public function register(App $app)
    {
        if (!$this->container['can']['admin']) {
            return [];
        }

        // Define route(s)
        $app->get('/admin-user', function (Request $request, Response $response) {
            $users = array_map(
                function ($row) {
                    return [
                        'name' => $row['code'],
                        'active' => $row['active'],
                        'changed' => $row['changed'],
                        'hint' => $this['editor']->getHint('code_user', $row['code'])
                    ];
                },
                $this['editor']->paramSet('code_user')
            );

            $this['view']->render($response, 'AdminUser/list.twig', [ 'users' => $users ]);
            return $response;
        })->setName(self::ROUTE);

        /**
         *
         */
        $app->post('/admin-user', function (Request $request, Response $response) {
            $post = array_merge(
                [ 'action' => null, 'user' => null, 'pw1' => null, 'pw2' => null ],
                $request->getParsedBody()
            );

            $e = $this['editor'];

            switch ($post['action']) {
                // ---------------
                case 'save':
                    if ($post['pw1'] == '' || $post['pw2'] == '') {
                        $this['flash']('code_ext::AdminUser_PasswordEmpty', 'danger');
                    } elseif ($post['pw1'] !== $post['pw2']) {
                        $this['flash']('code_ext::AdminUser_PasswordsNotEqual', 'danger');
                    } else {
                        $e->putData('code_user', $e->native, $post['user'], sha1($post['pw1']));
                        $this['flash']('code_ext::AdminUser_UserSaved');
                    }
                    break;
                // ---------------
                case 'toggle':
                    $active = $e->toggleActive('code_user', $post['user']);
                    $this['flash']('code_ext::AdminUser_' . ($active ? 'Activated' : 'Deactivated'));
                    break;
                // ---------------
                case 'delete':
                    $e->remove('code_user', $post['user']);
                    $this['db']->query(sprintf($e->sql($this->deleteACLs), $post['user']));
                    $this['flash']('code_ext::AdminUser_UserDeleted');
            } // switch

            // Can't use self here, would be the container
            return $response->withRedirect($this->router->pathFor(\App\Extension\AdminUser::ROUTE));
        });


        $app->get(self::SCRIPT, function (Request $request, Response $response) {
            return $response
                ->withHeader('Content-type', 'application/javascript')
                ->write(file_get_contents($this['baseDir'] . '/app/src/App/Extension/AdminUser/script.js'));
        });

        // Register extension point(s)
        return [ 'nav-code-sets', 'scripts' ];
    }


    public function process($name, &$msg, Array &$args)
    {
        switch ($name) {
            case 'nav-code-sets':
                // switch default code set route to this extension
                $args['code_user']['url'] = $this->container->router->pathFor(self::ROUTE);
                break;
        } // switch
    }

    // For template extension "scripts"
    public function contentScripts()
    {
        return '<script src="' . self::SCRIPT . '"></script>';
    }

    const ROUTE = 'Extension AdminUser list';
    const SCRIPT = '/admin-user/script.js';

    protected $deleteACLs = '
        DELETE FROM `{{TABLE}}`
         WHERE `app` = {{APP}}
           AND `set` = "code_acl"
           AND ( `code` = "%1$s" OR `code` LIKE "%1$s.%%" )';
}
