<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\filters;
use Super_Street_Dora_Grand_Championship_Turbo\exceptions\ForbiddenDataException as FDE;
final class Filter
{
    /**
     * [ CONSTANTES DE CLASSE. ]
     *
     * Ces constantes permettent de connaitre les types des donnees
     * qu'on souhaite filtrer.
     */
    const FILTER_STRING = 0;
    const FILTER_FLOAT = 1;
    const FILTER_DATE = 2;
    const FILTER_URL = 3;

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode traiter une donnee fournie d ans un fomulaire.</p>
     *
     * <ul>Des exceptions sont levees :
     *      <li> lorsque le type de la donnee fournie est incorrect. </li>
     *      <li>
     *           lorsque des caracteres suspects ou interdis sont trouves
     *          (du genre injections <i>XSS</i> ou <i>SQL</i>).
     *      </li>
     * </ul>
     *
     * <p>On ne type pas le parametre de fonction $asFilter, ni le type de retour
     * car celui-ci peut etre aussi bien un string qu'un float, et ces deux types
     * n'ont pas de type parent commun.</p>
     *
     * <ul>
     *      <li><b>@param $asFilter</b>         La donnee a fitrer et a traiter.</li>
     *      <br>
     *      <li><b>@param int $typeFilter</b>   Le type de filtre a utiliser selon le type de la donnee.</li>
     *      <br>
     *      <li><b>@return mixed</b>            La donnee filtree traitee (float ou string).</li>
     *      <br>
     *      <li><b>@throws FDE</b>              Exception levee si donnee traitee incorrecte.</li>
     * </ul>
     */
    public static function filter($asFilter, int $typeFilter){
        switch($typeFilter){
            case self::FILTER_STRING :
                $filtered = filter_var($asFilter, FILTER_SANITIZE_STRING);

                //Si le nettoyage de $asFilter a retourne une chaine diffrente de celle
                //fournie initialement, cela signifie que la chaine fournie comportait
                //des caracteres interdits ou tout du moins suspects.
                if($filtered != $asFilter)
                    throw new FDE(FDE::FORBIDDEN_CHARACTERS_FOUND);
                break;

            case self::FILTER_FLOAT :
                $filtered = filter_var($asFilter, FILTER_VALIDATE_FLOAT);

                //Si la validation a retourne false et donc un booleen au lieu de
                //de la donnee initiale, alors cela signifie que la donnee fournie
                //est invalide.
                if(is_bool($filtered))
                    throw new FDE(FDE::BAD_TYPE_GIVEN);
                break;

            case self::FILTER_DATE :
                $filtered = $asFilter;

                $date = explode('-', $filtered);
                //is_int(...) permet de verifier que ce sont bien des chiffres.
                if(is_int($date[0]) && is_int($date[1]) && is_int($date[2]))
                    //checkdate(...) permet de verifier que la date saisie est coherente.
                    if(!checkdate($date[0], $date[1], $date[2]))
                        throw new FDE(FDE::INVALID_DATE);
                else
                    throw new FDE(FDE::INVALID_SYNTAX_DATE);
                break;

            case self::FILTER_URL :
                $filtered = filter_var($asFilter, FILTER_VALIDATE_URL);

                //Si la validation a retourne false et donc un booleen au lieu de
                //de la donnee initiale, alors cela signifie que la donnee fournie
                //est invalide.
                if(is_bool($filtered))
                    throw new FDE(FDE::INVALID_URL);
                else{
                    $filtered = filter_var($asFilter, FILTER_SANITIZE_URL);

                    //Si le nettoyage de $asFilter a retourne une chaine diffrente de celle
                    //fournie initialement, cela signifie que la chaine fournie comportait
                    //des caracteres interdits ou tout du moins suspects.
                    if($filtered != $asFilter)
                        throw new FDE(FDE::FORBIDDEN_CHARACTERS_FOUND);
                }
                break;
        }
        return $filtered;
    }
}