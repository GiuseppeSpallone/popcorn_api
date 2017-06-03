<?php
// Routes

use Slim\Http\Request;
use Slim\Http\Response;

require_once 'controllers/DbController.php';
require_once 'controllers/Route.php';
require_once 'controllers/UserRoutes.php';
require_once 'controllers/NotificationsRoutes.php';

UserRoutes::register_routes($app);
NotificationsRoutes::register_routes($app);
