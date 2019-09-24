<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class FileUploadException extends \Exception
{
    /**
     * [ CONSTANTES DE CLASSE. ]
     *
     * Messages d'exception possibles.
     */

    const FILE_TOO_BIG = "Le fichier fourni est trop gros.";
    const INCORRECT_FILE_FORMAT = "Le format du fichier fourni est incorrect. Formats autorises : .jpeg, .jpg, .png, .bmp.";
    const COPY_NAME_ERROR = "Le nom du fichier existe deja et doit etre renomme.";
    const COPY_ERROR = "Erreur interne lors de la copie du fichier.";
    const FILE_CONTAINS_SPACES = "Le nom du fichier ne peut contenir d'espaces";

    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     * <ul>
     *      <li><b>@param string $exception_message</b>     Le message que l'exception doit renvoyer.</li>
     * </ul>
     */
    public function __construct(string $exception_message){
        parent::__construct($exception_message);
    }
}