<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class ForbiddenDataException extends \Exception
{
    /**
     * [ CONSTANTES DE CLASSE. ]
     *
     * Messages d'exception possibles.
     */
    const BAD_TYPE_GIVEN = "Le type de la donnee fournie est incorrect !";
    const FORBIDDEN_CHARACTERS_FOUND = "Caracteres interdits ou susptects trouves dans une donnee fournie !";
    const INVALID_DATE = "La date fournie comporte des parametres incoherents (du genre mois > 12 ou <1, etc.) !";
    const INVALID_SYNTAX_DATE = "La date comporte des caracteres imprevus, la ou des chiffres etaient attendus !";
    const INVALID_URL = "Cet url est incorrect !";

    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     *
     * <p>Constructeur de l'exception personalisee relative aux donnees fournies incorrectes.</p>
     *
     * <ul>
     *      <li><b>@param string $exception_message</b>     Le message que l'exception doit renvoyer.</li>
     * </ul>
     */
    public function __construct(string $exception_message){
        parent::__construct($exception_message);
    }
}