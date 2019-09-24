<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class BadPasswordException extends \Exception
{
    /**
     * [ CONSTANTES DE CLASSE. ]
     *
     * Messages d'exception possibles.
     */
     const SAME_VALUE_AS_USERNAME_AND_PASSWORD = "Le pseudo et le mot de passe sont identiques !";
     const TOO_SHORT_PASSWORD = "Mot de passe trop court ! Le mot de passe doit faire au moins 8 caracteres.";
     const PASSWORD_WITHOUT_UPPERCASE = "Mot de passe trop simple ! Le mot de passe doit comporter au moins une majuscule.";
     const PASSWORD_WITHOUT_NUMBER = "Mot de passe trop simple ! Le mot de passe doit comporter au moins un chiffre.";


    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     *
     * <p>Constructeur de l'exception personalisee relative aux mots de passe trop faibles.</p>
     *
     * <ul>
     *      <li><b>@param string $exception_message</b>     Le message que l'exception doit renvoyer.</li>
     * </ul>
     */
    public function __construct(string $exception_message){
        parent::__construct($exception_message);
    }
}