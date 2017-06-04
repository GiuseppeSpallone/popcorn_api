<?php

use api\routes\Route;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once 'FirebaseController.php';
require_once 'User.php';

class NotificationsRoutes extends Route
{

    public static function register_routes(App $app)
    {
        $app->post('/notification/all', self::class . ':notification_all');
        $app->post('/notification/individual', self::class . ':notification_individual');
    }

    public function notification_all(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $requestData = $request->getParsedBody();

            $title = $requestData['title'];
            $message = $requestData['message'];

            if ($message && $title) {
                $data['data']['title'] = $title;
                $data['data']['message'] = $message;

                $tokens = User::get_tokens();
                if ($tokens) {
                    $firebase = new FirebaseController();
                    $notifications = $firebase->send($tokens, $data);

                    $result = true;
                    $this->message = "messaggi inviati";
                    $response = self::get_response($response, $result, 'notifica', $notifications);
                } else {
                    $this->message = "non ci sono dispositivi registrati";
                    $response = self::get_response($response, $result, 'notifica', false);
                }
            } else {
                $this->message = "parametri mancanti";
                $response = self::get_response($response, $result, 'notifica', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'notifica', false);
        }

        return $response;
    }

    public function notification_individual(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $requestData = $request->getParsedBody();

            $username = $requestData['username'];
            $title = $requestData['title'];
            $message = $requestData['message'];

            if ($username && $title && $message) {
                $data['data']['title'] = $title;
                $data['data']['message'] = $message;

                $utente = User::username_esistente($username);
                if ($utente) {
                    $token = User::get_token_by_username($username);
                    if ($token) {
                        $firebase = new FirebaseController();
                        $notification = $firebase->send($token, $data);

                        $result = true;
                        $this->message = "messaggio inviato";
                        $response = self::get_response($response, $result, 'notifica', $notification);
                    } else {
                        $this->message = "messaggio non inviato";
                        $response = self::get_response($response, $result, 'notifica', false);
                    }
                } else {
                    $this->message = "l'utente non è registrato";
                    $response = self::get_response($response, $result, 'notifica', false);
                }
            } else {
                $this->message = "parametri mancanti";
                $response = self::get_response($response, $result, 'notifica', false);
            }

        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'notifica', false);
        }

        return $response;
    }

}