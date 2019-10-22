<?php

namespace App\controllers;

use App\Model\Users;
use App\exception\AuthentificationException as AE;
use App\Database;
use Slim\Views\Twig as View;

class loginController
{
        const salt = "@|-°+==00001ddQ";

        public function __construct($container)
    {
      $this->container = $container;
    }

    public function accueil($request, $response,$args){
            if(self::isConnected()){
                $this->container->view->render($response, 'pages/log.html.twig');
            }else{
                $this->container->view->render($response, 'pages/login.html.twig');
                //$this->seConnecter();
            }
    }

        public static function isConnected(){
                return isset($_SESSION['pseudo']);
        }

        public function user(){
                if(self::isConnected()){
                        return Users::find($_SESSION['pseudo']);
                }
        }

        public function seDeconnecter($request, $response){
                self::disconnect();

                $this->container->view->render($response, 'pages/home.html.twig');
             }

        public function seConnecter($request, $response,$args){
           $user = Users::where('pseudo','=',$_POST["pseudo"])->first();
           $password = $_POST["mdp"];
           
           self::isGoodPassword($user,$password);
           self::loadProfile($user);
           $this->container->view->render($response, 'pages/log.html.twig');
        }

        private static function loadProfile($user){
		$_SESSION['pseudo'] = $user->id;
		if($user->pseudo != null){
		        $_SESSION['pseudo'] = strtoupper($user->pseudo);
                }
        }



        public static function isGoodPassword(Users $user,$password){
                if(!password_verify($password.self::salt, $user->mdp)){
                        throw new AE('Exception');
                }               
        }

        public static function disconnect(){
		if(self::isConnected())
			unset($_SESSION['pseudo']);
	}
        
	public function creerUtilisateur($request, $response,$args)
	{
        $user= new Users();
        $user->pseudo = $_POST["pseudo"];
        $password = $_POST["mdp"].self::salt;
        $user->mdp = password_hash($password, PASSWORD_DEFAULT);
        $user->save();
	 $this->container->view->render($response, 'pages/createCompte.html.twig', ['utilisateurs'=>$user]);
  }
  


	public function voir($request, $response,$args)
	{
        $user= Users::find(intVal($args['id']));
         $this->container->view->render($response,'pages/createCompte.html.twig', ['utilisateurs'=>$user]);

    }
}